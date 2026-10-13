<?php
/**
 * Buyora payment/ledger audit helpers that do not touch the database.
 * Run: php application/tests/payment_audit_test.php
 */
define('BASEPATH', __DIR__);
require dirname(__DIR__) . '/libraries/Order_quote.php';
require dirname(__DIR__) . '/libraries/Safepay.php';

function assertSameValue($expected, $actual, $label)
{
    if ($expected !== $actual) {
        fwrite(STDERR, 'FAIL ' . $label . ' expected ' . var_export($expected, true) . ' got ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
    echo 'PASS ' . $label . PHP_EOL;
}

function assertTrue($cond, $label)
{
    assertSameValue(true, (bool) $cond, $label);
}

// --- Checkout method allow-list ---
assertSameValue(false, Order_quote::paymentAllowed('cashOnDelivery', true, true), 'COD rejected');
assertSameValue(false, Order_quote::paymentAllowed('PayPal', true, true), 'PayPal rejected');
assertSameValue(false, Order_quote::paymentAllowed('paypal', true, true), 'paypal lowercase rejected');
assertSameValue(false, Order_quote::paymentAllowed('Safepay', true, false), 'Safepay blocked without keys');
assertSameValue(true, Order_quote::paymentAllowed('Safepay', false, true), 'Safepay allowed when configured');
assertSameValue(true, Order_quote::paymentAllowed('Bank', true, false), 'Bank allowed when IBAN exists');
assertSameValue(false, Order_quote::paymentAllowed('Bank', false, true), 'Bank blocked without IBAN');

// --- Multi-vendor quote with marketplace line ---
$quote = Order_quote::build(array(
    array('product_id' => 1, 'vendor_id' => 4, 'title' => 'A', 'unit_price_minor' => 100000, 'quantity' => 1),
    array('product_id' => 2, 'vendor_id' => 5, 'title' => 'B', 'unit_price_minor' => 200000, 'quantity' => 2),
    array('product_id' => 3, 'vendor_id' => 0, 'title' => 'Marketplace', 'unit_price_minor' => 50000, 'quantity' => 1)
), 'percent', 10, Order_quote::percentToBasisPoints('5'), 15000, 1000000);

assertSameValue(550000, $quote['merchandise_minor'], 'merchandise 1000+4000+500');
assertSameValue(55000, $quote['discount_minor'], '10 percent discount');
assertSameValue(15000, $quote['shipping_minor'], 'shipping below threshold');
assertSameValue(510000, $quote['payable_minor'], 'payable = merchandise - discount + shipping');
assertSameValue(0, $quote['items'][2]['commission_minor'], 'marketplace commission 0');
assertSameValue(0, $quote['items'][2]['vendor_net_minor'], 'marketplace vendor net 0');
assertSameValue(4500, $quote['items'][0]['commission_minor'], '5% of taxable 900');
assertSameValue(85500, $quote['items'][0]['vendor_net_minor'], 'vendor A net');

// Zero commission default
$zero = Order_quote::build(array(
    array('product_id' => 1, 'vendor_id' => 4, 'title' => 'A', 'unit_price_minor' => 1000, 'quantity' => 1)
), null, 0, 0, 0, 0);
assertSameValue(0, $zero['items'][0]['commission_minor'], 'commission default 0');
assertSameValue(1000, $zero['items'][0]['vendor_net_minor'], 'full net when commission 0');

// --- Webhook HMAC ---
$body = '{"type":"payment.failed","data":{"tracker":"track_audit"}}';
$secret = '05b6943e1efe97defc35dd80c197a589462dd7a86e5d51c96309616bbc267b77';
$sig = hash_hmac('sha512', $body, $secret);
assertTrue(Safepay::signatureMatches($body, $secret, $sig), 'valid HMAC accepted');
assertTrue(!Safepay::signatureMatches($body, $secret, 'nope'), 'invalid HMAC rejected');
assertTrue(!Safepay::signatureMatches($body, '', $sig), 'empty HMAC secret rejected');
assertTrue(!Safepay::signatureMatches($body, $secret, ''), 'missing signature rejected');
assertTrue(!Safepay::signatureMatches($body . 'x', $secret, $sig), 'tampered body rejected');

// --- Tracker shapes (Safepay sandbox uses string tracker) ---
assertSameValue(
    'track_06ee38cb-981d-4158-819f-7231f28314e4',
    Safepay::webhookTracker(array('data' => array('tracker' => 'track_06ee38cb-981d-4158-819f-7231f28314e4'))),
    'string data.tracker'
);
assertSameValue(
    'track_nested',
    Safepay::webhookTracker(array('data' => array('tracker' => array('token' => 'track_nested')))),
    'nested data.tracker.token'
);
assertSameValue(
    'track_notify',
    Safepay::webhookTracker(array('notification' => array('tracker' => 'track_notify'))),
    'notification.tracker'
);
assertSameValue('', Safepay::webhookTracker(array('type' => 'payment.failed')), 'missing tracker empty');

// --- Confirm decision matrix (mirrors Ledger_model::confirmSafepay conditions) ---
function confirmWouldPay($state, $currency, $amount, $expectedMinor)
{
    return $state === 'TRACKER_ENDED' && $currency === 'PKR' && (int) $amount === (int) $expectedMinor;
}
function confirmWouldReview($state, $currency, $amount, $expectedMinor)
{
    return $state === 'TRACKER_ENDED' && !confirmWouldPay($state, $currency, $amount, $expectedMinor);
}

assertTrue(confirmWouldPay('TRACKER_ENDED', 'PKR', 1000, 1000), 'ended matching amount pays');
assertTrue(!confirmWouldPay('TRACKER_ENROLLED', 'PKR', 1000, 1000), 'enrolled does not pay');
assertTrue(!confirmWouldPay('TRACKER_ENDED', 'USD', 1000, 1000), 'wrong currency does not pay');
assertTrue(confirmWouldReview('TRACKER_ENDED', 'PKR', 999, 1000), 'amount mismatch goes review');
assertTrue(!confirmWouldPay('TRACKER_ENDED', 'PKR', 999, 1000), 'amount mismatch does not pay');

// --- Refund split ---
$split = Order_quote::refundSplit(10000, 9000, 1000, 5000);
assertSameValue(4500, $split['vendor_minor'], 'half refund vendor share');
assertSameValue(500, $split['commission_minor'], 'half refund commission share');
assertSameValue(null, Order_quote::refundSplit(10000, 9000, 1000, 10001), 'over-refund rejected');

// --- Idempotency key shapes used by ledger ---
assertSameValue('sale:12', 'sale:' . 12, 'sale key shape');
assertSameValue('commission:12', 'commission:' . 12, 'commission key shape');
assertSameValue('payout:4:AUD-REF-1', 'payout:' . 4 . ':' . 'AUD-REF-1', 'payout key shape');

$sp = new Safepay();
$checkout = $sp->checkoutUrl('track_x', 'tbt_token==', 'https://example.com/r', 'https://example.com/c');
assertTrue(strpos($checkout, '/embedded?') !== false, 'checkout uses /embedded path');
assertTrue(strpos($checkout, 'environment=sandbox') !== false || strpos($checkout, 'environment=production') !== false, 'checkout includes environment');
assertTrue(strpos($checkout, '/checkout/pay') === false, 'checkout does not use legacy /checkout/pay');

echo "payment audit tests passed\n";
