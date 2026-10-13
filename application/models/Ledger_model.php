<?php

class Ledger_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('Order_quote');
    }

    public function commissionBasisPoints()
    {
        $stored = $this->db->query('SELECT value FROM value_store WHERE thekey = ? LIMIT 1', array('commission_rate_percent'));
        $row = $stored ? $stored->row_array() : null;
        $points = Order_quote::percentToBasisPoints($row ? $row['value'] : '0');
        return $points === null ? 0 : $points;
    }

    public function saveCommissionPercent($percent)
    {
        $points = Order_quote::percentToBasisPoints($percent);
        if ($points === null) {
            return false;
        }
        $value = number_format($points / 100, 2, '.', '');
        $this->load->model('admin/Home_admin_model');
        $this->Home_admin_model->setValueStore('commission_rate_percent', $value);
        return true;
    }

    public function quoteFromCart(array $cartIds, $discountCode, $shippingAmount, $shippingThreshold)
    {
        $counts = array_count_values(array_map('intval', $cartIds));
        $ids = array_keys($counts);
        if (empty($ids)) {
            return array('error' => 'Your cart is empty.');
        }
        $this->db->select('products.id, products.vendor_id, products.quantity, products.visibility, products_translations.price, products_translations.title');
        $this->db->from('products');
        $this->db->join('products_translations', 'products_translations.for_id = products.id', 'inner');
        $this->db->where('products_translations.abbr', MY_DEFAULT_LANGUAGE_ABBR);
        $this->db->where_in('products.id', $ids);
        $rows = $this->db->get()->result_array();
        $byId = array();
        foreach ($rows as $row) {
            $byId[(int) $row['id']] = $row;
        }
        $lines = array();
        foreach ($counts as $id => $quantity) {
            if (!isset($byId[$id]) || (int) $byId[$id]['visibility'] !== 1) {
                return array('error' => 'A product in the cart is no longer available.');
            }
            if ((int) $byId[$id]['quantity'] < $quantity) {
                return array('error' => 'A product in the cart does not have enough stock.');
            }
            $lines[] = array(
                'product_id' => $id,
                'vendor_id' => (int) $byId[$id]['vendor_id'],
                'title' => (string) $byId[$id]['title'],
                'unit_price_minor' => Order_quote::toMinor($byId[$id]['price']),
                'quantity' => $quantity
            );
        }
        $discountType = null;
        $discountAmount = 0;
        if (is_string($discountCode) && trim($discountCode) !== '') {
            $code = $this->Public_model->getValidDiscountCode(trim($discountCode));
            if ($code == null) {
                return array('error' => 'The discount code is not valid.');
            }
            $discountType = $code['type'];
            $discountAmount = $code['amount'];
        }
        $quote = Order_quote::build(
            $lines,
            $discountType,
            $discountAmount,
            $this->commissionBasisPoints(),
            Order_quote::toMinor($shippingAmount),
            Order_quote::toMinor($shippingThreshold)
        );
        $quote['discount_code'] = is_string($discountCode) ? trim($discountCode) : '';
        return $quote;
    }

    public function saveOrderItems($orderRowId, $orderNumber, array $items)
    {
        foreach ($items as $item) {
            $exists = $this->db->where('order_row_id', (int) $orderRowId)->where('product_id', (int) $item['product_id'])->count_all_results('order_items');
            if ($exists > 0) {
                continue;
            }
            $this->db->insert('order_items', array(
                'order_row_id' => (int) $orderRowId,
                'order_number' => (int) $orderNumber,
                'vendor_id' => (int) $item['vendor_id'],
                'product_id' => (int) $item['product_id'],
                'title' => substr($item['title'], 0, 255),
                'quantity' => (int) $item['quantity'],
                'unit_price_minor' => (int) $item['unit_price_minor'],
                'line_gross_minor' => (int) $item['line_gross_minor'],
                'discount_minor' => (int) $item['discount_minor'],
                'commission_rate_bps' => (int) $item['commission_rate_bps'],
                'commission_minor' => (int) $item['commission_minor'],
                'vendor_net_minor' => (int) $item['vendor_net_minor'],
                'currency' => 'PKR',
                'created_at' => time()
            ));
        }
        return true;
    }

    public function createPayment($orderRowId, $orderNumber, $provider, $amountMinor, $idempotencyKey)
    {
        $existing = $this->db->where('idempotency_key', $idempotencyKey)->get('payment_transactions')->row_array();
        if (!empty($existing)) {
            return $existing;
        }
        $this->db->insert('payment_transactions', array(
            'order_row_id' => (int) $orderRowId,
            'order_number' => (int) $orderNumber,
            'provider' => $provider,
            'tracker' => null,
            'amount_minor' => (int) $amountMinor,
            'currency' => 'PKR',
            'status' => 'pending',
            'idempotency_key' => $idempotencyKey,
            'verified_at' => null,
            'created_at' => time()
        ));
        return $this->db->where('id', (int) $this->db->insert_id())->get('payment_transactions')->row_array();
    }

    public function attachTracker($paymentId, $tracker)
    {
        $this->db->where('id', (int) $paymentId);
        $this->db->where_in('status', array('pending', 'failed'));
        return $this->db->update('payment_transactions', array(
            'tracker' => $tracker,
            'status' => 'pending'
        ));
    }

    public function paymentById($paymentId)
    {
        return $this->db->where('id', (int) $paymentId)->get('payment_transactions')->row_array();
    }

    public function paymentByTracker($tracker)
    {
        return $this->db->where('tracker', $tracker)->get('payment_transactions')->row_array();
    }

    public function markPaymentFailed($paymentId)
    {
        $this->db->where('id', (int) $paymentId);
        $this->db->where('status', 'pending');
        $this->db->update('payment_transactions', array('status' => 'failed'));
    }

    public function recordVerifiedSale($paymentId, $adminUser)
    {
        $payment = $this->db->where('id', (int) $paymentId)->get('payment_transactions')->row_array();
        if (empty($payment) || !in_array($payment['status'], array('pending', 'failed'), true)) {
            return !empty($payment) && $payment['status'] === 'paid';
        }
        $items = $this->db->where('order_row_id', (int) $payment['order_row_id'])->get('order_items')->result_array();
        $this->db->trans_begin();
        foreach ($items as $item) {
            if ((int) $item['vendor_id'] < 1) {
                continue;
            }
            $taxable = (int) $item['line_gross_minor'] - (int) $item['discount_minor'];
            $this->insertLedger(array(
                'vendor_id' => (int) $item['vendor_id'],
                'order_item_id' => (int) $item['id'],
                'entry_type' => 'sale',
                'amount_minor' => $taxable,
                'idempotency_key' => 'sale:' . (int) $item['id'],
                'note' => 'Order ' . (int) $payment['order_number'],
                'admin_user' => $adminUser
            ));
            $this->insertLedger(array(
                'vendor_id' => (int) $item['vendor_id'],
                'order_item_id' => (int) $item['id'],
                'entry_type' => 'commission',
                'amount_minor' => 0 - (int) $item['commission_minor'],
                'idempotency_key' => 'commission:' . (int) $item['id'],
                'note' => 'Commission on order ' . (int) $payment['order_number'],
                'admin_user' => $adminUser
            ));
        }
        $this->db->where('id', (int) $paymentId);
        $this->db->where_in('status', array('pending', 'failed'));
        $this->db->update('payment_transactions', array('status' => 'paid', 'verified_at' => time()));
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }
        $this->db->trans_commit();
        return true;
    }

    public function confirmSafepay(array $payment, array $remote)
    {
        $this->load->library('Safepay');
        $state = $this->safepay->trackerState($remote);
        $amount = $this->safepay->trackerAmount($remote);
        $currency = $this->safepay->trackerCurrency($remote);
        if ($state === 'TRACKER_ENDED' && $currency === 'PKR' && $amount === (int) $payment['amount_minor']) {
            return $this->recordVerifiedSale((int) $payment['id'], 'safepay');
        }
        if ($state === 'TRACKER_ENDED') {
            $this->db->where('id', (int) $payment['id'])->where('status', 'pending')->update('payment_transactions', array('status' => 'review'));
        }
        return false;
    }

    public function storeWebhook($eventKey, $tracker, $eventType)
    {
        $exists = $this->db->where('event_key', $eventKey)->count_all_results('webhook_events');
        if ($exists > 0) {
            return false;
        }
        $this->db->insert('webhook_events', array(
            'event_key' => $eventKey,
            'tracker' => substr((string) $tracker, 0, 80),
            'event_type' => substr((string) $eventType, 0, 80),
            'created_at' => time()
        ));
        return $this->db->affected_rows() > 0;
    }

    public function payableBalance($vendorId)
    {
        $row = $this->db->query(
            'SELECT COALESCE(SUM(amount_minor), 0) AS balance FROM vendor_ledger WHERE vendor_id = ?',
            array((int) $vendorId)
        )->row_array();
        return (int) $row['balance'];
    }

    public function vendorSummary($vendorId)
    {
        $rows = $this->db->query(
            'SELECT entry_type, COALESCE(SUM(amount_minor), 0) AS total FROM vendor_ledger WHERE vendor_id = ? GROUP BY entry_type',
            array((int) $vendorId)
        )->result_array();
        $totals = array('sale' => 0, 'commission' => 0, 'refund' => 0, 'commission_reversal' => 0, 'adjustment' => 0, 'payout' => 0);
        foreach ($rows as $row) {
            $totals[$row['entry_type']] = (int) $row['total'];
        }
        $totals['balance'] = $this->payableBalance($vendorId);
        return $totals;
    }

    public function recordPayout($vendorId, $amountMinor, $method, $reference, $paidOn, $notes, $adminUser)
    {
        $vendorId = (int) $vendorId;
        $amountMinor = (int) $amountMinor;
        $reference = trim((string) $reference);
        if ($vendorId < 1 || $amountMinor < 1 || $reference === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $paidOn)) {
            return 'Enter the vendor, amount, date, method, and reference.';
        }
        if (!in_array($method, array('bank', 'cash', 'other'), true)) {
            return 'Choose a manual payout method.';
        }
        if ($this->db->where('id', $vendorId)->count_all_results('vendors') < 1) {
            return 'That vendor does not exist.';
        }
        $this->db->trans_begin();
        $this->db->query('SELECT id FROM vendors WHERE id = ? FOR UPDATE', array($vendorId));
        $balance = $this->payableBalance($vendorId);
        if ($amountMinor > $balance) {
            $this->db->trans_rollback();
            return 'The payout is higher than the payable balance.';
        }
        $duplicate = $this->db->where('vendor_id', $vendorId)->where('reference_code', $reference)->count_all_results('vendor_payouts');
        if ($duplicate > 0) {
            $this->db->trans_rollback();
            return 'That payout reference was already recorded.';
        }
        $this->db->insert('vendor_payouts', array(
            'vendor_id' => $vendorId,
            'amount_minor' => $amountMinor,
            'method' => $method,
            'reference_code' => substr($reference, 0, 80),
            'notes' => substr((string) $notes, 0, 500),
            'paid_on' => $paidOn,
            'status' => 'recorded',
            'admin_user' => substr((string) $adminUser, 0, 80),
            'created_at' => time()
        ));
        $payoutId = (int) $this->db->insert_id();
        $saved = $this->insertLedger(array(
            'vendor_id' => $vendorId,
            'payout_id' => $payoutId,
            'entry_type' => 'payout',
            'amount_minor' => 0 - $amountMinor,
            'idempotency_key' => 'payout:' . $vendorId . ':' . $reference,
            'note' => 'Manual payout ' . $reference,
            'admin_user' => $adminUser
        ));
        if (!$saved || $this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return 'The payout could not be recorded.';
        }
        $this->db->trans_commit();
        return true;
    }

    public function recordRefund($orderItemId, $refundTaxable, $reference, $adminUser)
    {
        $item = $this->db->where('id', (int) $orderItemId)->get('order_items')->row_array();
        if (empty($item) || (int) $item['vendor_id'] < 1) {
            return 'Choose a vendor order item.';
        }
        $reference = trim((string) $reference);
        if ($reference === '') {
            return 'Enter a refund reference.';
        }
        $taxable = (int) $item['line_gross_minor'] - (int) $item['discount_minor'];
        $already = $this->refundedTaxable((int) $item['id']);
        $remaining = $taxable - $already;
        $refundTaxable = (int) $refundTaxable;
        if ($refundTaxable < 1 || $refundTaxable > $remaining) {
            return 'The refund is higher than the remaining item amount.';
        }
        $split = Order_quote::refundSplit($taxable, (int) $item['vendor_net_minor'], (int) $item['commission_minor'], $refundTaxable);
        if ($split === null) {
            return 'The refund could not be calculated.';
        }
        $this->db->trans_begin();
        $refundSaved = $this->insertLedger(array(
            'vendor_id' => (int) $item['vendor_id'],
            'order_item_id' => (int) $item['id'],
            'entry_type' => 'refund',
            'amount_minor' => 0 - $split['vendor_minor'],
            'idempotency_key' => 'refund:' . (int) $item['id'] . ':' . $reference,
            'note' => 'Refund ' . $reference,
            'admin_user' => $adminUser
        ));
        $reversalSaved = true;
        if ($split['commission_minor'] > 0) {
            $reversalSaved = $this->insertLedger(array(
                'vendor_id' => (int) $item['vendor_id'],
                'order_item_id' => (int) $item['id'],
                'entry_type' => 'commission_reversal',
                'amount_minor' => $split['commission_minor'],
                'idempotency_key' => 'commission-reversal:' . (int) $item['id'] . ':' . $reference,
                'note' => 'Commission reversal ' . $reference,
                'admin_user' => $adminUser
            ));
        }
        if (!$refundSaved || !$reversalSaved || $this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return 'That refund reference was already recorded.';
        }
        $this->db->trans_commit();
        return true;
    }

    public function recordAdjustment($vendorId, $amountMinor, $reference, $note, $adminUser)
    {
        $vendorId = (int) $vendorId;
        $reference = trim((string) $reference);
        if ($vendorId < 1 || (int) $amountMinor === 0 || $reference === '') {
            return 'Enter the vendor, a non-zero amount, and a reference.';
        }
        if ($this->db->where('id', $vendorId)->count_all_results('vendors') < 1) {
            return 'That vendor does not exist.';
        }
        $saved = $this->insertLedger(array(
            'vendor_id' => $vendorId,
            'entry_type' => 'adjustment',
            'amount_minor' => (int) $amountMinor,
            'idempotency_key' => 'adjustment:' . $vendorId . ':' . $reference,
            'note' => substr((string) $note, 0, 255),
            'admin_user' => $adminUser
        ));
        return $saved ? true : 'That adjustment reference was already recorded.';
    }

    public function transactions($limit)
    {
        $this->db->order_by('id', 'DESC');
        return $this->db->get('payment_transactions', (int) $limit)->result_array();
    }

    public function vendorBalances()
    {
        return $this->db->query(
            'SELECT vendors.id, vendors.name, COALESCE(SUM(vendor_ledger.amount_minor), 0) AS balance
             FROM vendors
             LEFT JOIN vendor_ledger ON vendor_ledger.vendor_id = vendors.id
             GROUP BY vendors.id, vendors.name
             ORDER BY vendors.name ASC'
        )->result_array();
    }

    public function payoutsForVendor($vendorId)
    {
        $this->db->where('vendor_id', (int) $vendorId);
        $this->db->order_by('id', 'DESC');
        return $this->db->get('vendor_payouts')->result_array();
    }

    public function allPayouts()
    {
        return $this->db->query(
            'SELECT vendor_payouts.*, vendors.name FROM vendor_payouts
             INNER JOIN vendors ON vendors.id = vendor_payouts.vendor_id
             ORDER BY vendor_payouts.id DESC'
        )->result_array();
    }

    public function ledgerForVendor($vendorId)
    {
        $this->db->where('vendor_id', (int) $vendorId);
        $this->db->order_by('id', 'DESC');
        return $this->db->get('vendor_ledger', 100)->result_array();
    }

    public function payableItems()
    {
        return $this->db->query(
            'SELECT order_items.*, payment_transactions.status AS payment_status
             FROM order_items
             INNER JOIN payment_transactions ON payment_transactions.order_row_id = order_items.order_row_id
             WHERE order_items.vendor_id > 0 AND payment_transactions.status = "paid"
             ORDER BY order_items.id DESC
             LIMIT 100'
        )->result_array();
    }

    public function pendingBankPayments()
    {
        $this->db->where('provider', 'bank');
        $this->db->where('status', 'pending');
        $this->db->order_by('id', 'DESC');
        return $this->db->get('payment_transactions')->result_array();
    }

    private function refundedTaxable($orderItemId)
    {
        $row = $this->db->query(
            'SELECT COALESCE(SUM(CASE WHEN entry_type = "refund" THEN -amount_minor ELSE 0 END), 0)
                + COALESCE(SUM(CASE WHEN entry_type = "commission_reversal" THEN amount_minor ELSE 0 END), 0) AS refunded
             FROM vendor_ledger WHERE order_item_id = ?',
            array((int) $orderItemId)
        )->row_array();
        return (int) $row['refunded'];
    }

    private function insertLedger(array $entry)
    {
        $exists = $this->db->where('idempotency_key', $entry['idempotency_key'])->count_all_results('vendor_ledger');
        if ($exists > 0) {
            return false;
        }
        return $this->db->insert('vendor_ledger', array(
            'vendor_id' => (int) $entry['vendor_id'],
            'order_item_id' => isset($entry['order_item_id']) ? (int) $entry['order_item_id'] : null,
            'payout_id' => isset($entry['payout_id']) ? (int) $entry['payout_id'] : null,
            'entry_type' => $entry['entry_type'],
            'amount_minor' => (int) $entry['amount_minor'],
            'currency' => 'PKR',
            'idempotency_key' => substr($entry['idempotency_key'], 0, 120),
            'note' => substr((string) $entry['note'], 0, 255),
            'admin_user' => substr((string) $entry['admin_user'], 0, 80),
            'created_at' => time()
        ));
    }
}
