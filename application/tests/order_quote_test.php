<?php
define('BASEPATH', __DIR__);
require dirname(__DIR__) . '/libraries/Order_quote.php';

function assertSameValue($expected, $actual, $label)
{
    if ($expected !== $actual) {
        fwrite(STDERR, $label . ' expected ' . var_export($expected, true) . ' got ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}

assertSameValue(false, Order_quote::paymentAllowed('cashOnDelivery', true, true), 'cod rejected');
assertSameValue(false, Order_quote::paymentAllowed('PayPal', true, true), 'paypal rejected');
assertSameValue(true, Order_quote::paymentAllowed('Safepay', false, true), 'safepay allowed when configured');
assertSameValue(false, Order_quote::paymentAllowed('Safepay', true, false), 'safepay blocked without keys');
assertSameValue(true, Order_quote::paymentAllowed('Bank', true, false), 'bank allowed when an account exists');

$quote = Order_quote::build(array(
    array('product_id' => 1, 'vendor_id' => 4, 'title' => 'A', 'unit_price_minor' => 1000, 'quantity' => 1),
    array('product_id' => 2, 'vendor_id' => 5, 'title' => 'B', 'unit_price_minor' => 3000, 'quantity' => 1),
    array('product_id' => 3, 'vendor_id' => 0, 'title' => 'Marketplace', 'unit_price_minor' => 2000, 'quantity' => 1)
), 'percent', 10, Order_quote::percentToBasisPoints('10'), 0, 0);

assertSameValue(6000, $quote['merchandise_minor'], 'merchandise');
assertSameValue(600, $quote['discount_minor'], 'ten percent discount');
assertSameValue(100, $quote['items'][0]['discount_minor'], 'discount share a');
assertSameValue(300, $quote['items'][1]['discount_minor'], 'discount share b');
assertSameValue(200, $quote['items'][2]['discount_minor'], 'discount share marketplace');
assertSameValue(90, $quote['items'][0]['commission_minor'], 'commission a');
assertSameValue(810, $quote['items'][0]['vendor_net_minor'], 'vendor net a');
assertSameValue(0, $quote['items'][2]['vendor_net_minor'], 'marketplace product has no vendor net');
assertSameValue(0, $quote['items'][2]['commission_minor'], 'marketplace product has no commission');
assertSameValue(5400, $quote['payable_minor'], 'payable ignores browser total');

$tampered = Order_quote::build(array(
    array('product_id' => 1, 'vendor_id' => 4, 'title' => 'A', 'unit_price_minor' => 2500, 'quantity' => 2)
), null, 0, 0, 50000, 100000);
assertSameValue(5000, $tampered['merchandise_minor'], 'quantity times database price');
assertSameValue(50000, $tampered['shipping_minor'], 'shipping below threshold');
assertSameValue(55000, $tampered['payable_minor'], 'payable includes shipping');

$split = Order_quote::refundSplit(1000, 900, 100, 500);
assertSameValue(450, $split['vendor_minor'], 'partial refund vendor share');
assertSameValue(50, $split['commission_minor'], 'partial refund commission share');
assertSameValue(null, Order_quote::refundSplit(1000, 900, 100, 1001), 'refund above taxable rejected');
assertSameValue(null, Order_quote::percentToBasisPoints('101'), 'commission above 100 rejected');
assertSameValue(0, Order_quote::percentToBasisPoints('0'), 'commission starts unset at zero');

require dirname(__DIR__) . '/libraries/Safepay.php';
$payload = '{"type":"payment.succeeded","tracker":"track_test"}';
$secret = 'sandbox-secret';
$signature = hash_hmac('sha512', $payload, $secret);
assertSameValue(true, Safepay::signatureMatches($payload, $secret, $signature), 'webhook signature matches');
assertSameValue(false, Safepay::signatureMatches($payload, $secret, 'nope'), 'bad webhook signature rejected');
assertSameValue(false, Safepay::signatureMatches($payload . ' ', $secret, $signature), 'changed webhook body rejected');
assertSameValue(
    'track_06ee38cb-981d-4158-819f-7231f28314e4',
    Safepay::webhookTracker(array('type' => 'payment.failed', 'data' => array('tracker' => 'track_06ee38cb-981d-4158-819f-7231f28314e4'))),
    'string tracker from webhook'
);
assertSameValue(
    'track_nested',
    Safepay::webhookTracker(array('data' => array('tracker' => array('token' => 'track_nested')))),
    'nested tracker token'
);

echo "order quote tests passed\n";
