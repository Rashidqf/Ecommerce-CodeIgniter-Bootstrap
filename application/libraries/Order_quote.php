<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_quote
{
    public static function toMinor($amount)
    {
        if (!is_numeric($amount)) {
            return 0;
        }
        return (int) round(((float) $amount) * 100, 0, PHP_ROUND_HALF_UP);
    }

    public static function formatMinor($minor)
    {
        $sign = ((int) $minor) < 0 ? '-' : '';
        $abs = abs((int) $minor);
        return $sign . number_format($abs / 100, 2, '.', '');
    }

    public static function percentToBasisPoints($percent)
    {
        if (!is_numeric($percent)) {
            return null;
        }
        $value = round((float) $percent, 2, PHP_ROUND_HALF_UP);
        if ($value < 0 || $value > 100) {
            return null;
        }
        return (int) round($value * 100, 0, PHP_ROUND_HALF_UP);
    }

    public static function paymentAllowed($type, $bankReady, $safepayReady)
    {
        if ($type === 'Safepay') {
            return (bool) $safepayReady;
        }
        if ($type === 'Bank') {
            return (bool) $bankReady;
        }
        return false;
    }

    public static function allocate($total, array $weights)
    {
        $total = (int) $total;
        $count = count($weights);
        $shares = array_fill(0, $count, 0);
        $sum = 0;
        foreach ($weights as $weight) {
            $sum += (int) $weight;
        }
        if ($total <= 0 || $sum <= 0) {
            return $shares;
        }
        if ($total > $sum) {
            $total = $sum;
        }
        $remainders = array();
        $assigned = 0;
        foreach ($weights as $index => $weight) {
            $weight = (int) $weight;
            $exact = ($total * $weight) / $sum;
            $base = (int) floor($exact);
            if ($base > $weight) {
                $base = $weight;
            }
            $shares[$index] = $base;
            $assigned += $base;
            $remainders[$index] = $exact - $base;
        }
        $left = $total - $assigned;
        while ($left > 0) {
            arsort($remainders);
            $moved = false;
            foreach (array_keys($remainders) as $index) {
                if ($shares[$index] < (int) $weights[$index]) {
                    $shares[$index]++;
                    $remainders[$index] = 0;
                    $left--;
                    $moved = true;
                    break;
                }
                $remainders[$index] = 0;
            }
            if (!$moved) {
                break;
            }
        }
        return $shares;
    }

    public static function build(array $lines, $discountType, $discountAmount, $commissionBps, $shippingMinor, $shippingThresholdMinor)
    {
        $commissionBps = (int) $commissionBps;
        $weights = array();
        $normalized = array();
        foreach ($lines as $line) {
            $quantity = (int) $line['quantity'];
            $unit = (int) $line['unit_price_minor'];
            $gross = $unit * $quantity;
            $normalized[] = array(
                'product_id' => (int) $line['product_id'],
                'vendor_id' => (int) $line['vendor_id'],
                'title' => (string) $line['title'],
                'quantity' => $quantity,
                'unit_price_minor' => $unit,
                'line_gross_minor' => $gross
            );
            $weights[] = $gross;
        }
        $merchandise = array_sum($weights);
        $discountMinor = 0;
        if ($discountType === 'percent' && is_numeric($discountAmount)) {
            $discountMinor = (int) round($merchandise * ((float) $discountAmount) / 100, 0, PHP_ROUND_HALF_UP);
        } elseif ($discountType === 'float' && is_numeric($discountAmount)) {
            $discountMinor = self::toMinor($discountAmount);
        }
        if ($discountMinor < 0) {
            $discountMinor = 0;
        }
        if ($discountMinor > $merchandise) {
            $discountMinor = $merchandise;
        }
        $shares = self::allocate($discountMinor, $weights);
        $items = array();
        $vendorNet = 0;
        $commissionTotal = 0;
        foreach ($normalized as $index => $line) {
            $discount = $shares[$index];
            $taxable = $line['line_gross_minor'] - $discount;
            $commission = 0;
            $net = 0;
            if ($line['vendor_id'] > 0) {
                $commission = (int) round($taxable * $commissionBps / 10000, 0, PHP_ROUND_HALF_UP);
                if ($commission > $taxable) {
                    $commission = $taxable;
                }
                $net = $taxable - $commission;
                $vendorNet += $net;
                $commissionTotal += $commission;
            }
            $line['discount_minor'] = $discount;
            $line['commission_rate_bps'] = $line['vendor_id'] > 0 ? $commissionBps : 0;
            $line['commission_minor'] = $commission;
            $line['vendor_net_minor'] = $net;
            $items[] = $line;
        }
        $shipping = 0;
        if ((int) $shippingMinor > 0 && (int) $shippingThresholdMinor > $merchandise) {
            $shipping = (int) $shippingMinor;
        }
        return array(
            'items' => $items,
            'merchandise_minor' => $merchandise,
            'discount_minor' => $discountMinor,
            'shipping_minor' => $shipping,
            'payable_minor' => $merchandise - $discountMinor + $shipping,
            'commission_minor' => $commissionTotal,
            'vendor_net_minor' => $vendorNet,
            'currency' => 'PKR'
        );
    }

    public static function refundSplit($taxable, $vendorNet, $commission, $refundTaxable)
    {
        $taxable = (int) $taxable;
        $refundTaxable = (int) $refundTaxable;
        if ($taxable < 1 || $refundTaxable < 1 || $refundTaxable > $taxable) {
            return null;
        }
        $vendorShare = (int) round($refundTaxable * ((int) $vendorNet) / $taxable, 0, PHP_ROUND_HALF_UP);
        if ($vendorShare > (int) $vendorNet) {
            $vendorShare = (int) $vendorNet;
        }
        if ($vendorShare > $refundTaxable) {
            $vendorShare = $refundTaxable;
        }
        $commissionShare = $refundTaxable - $vendorShare;
        if ($commissionShare > (int) $commission) {
            $commissionShare = (int) $commission;
            $vendorShare = $refundTaxable - $commissionShare;
        }
        return array(
            'vendor_minor' => $vendorShare,
            'commission_minor' => $commissionShare
        );
    }
}
