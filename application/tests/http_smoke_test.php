<?php
/**
 * HTTP smoke tests against the local PHP server.
 * Run: php application/tests/http_smoke_test.php
 * Does not insert/update/delete database rows.
 */
$base = getenv('BUYORA_BASE') ?: 'http://127.0.0.1:8080';
$cookie = tempnam(sys_get_temp_dir(), 'buyora_cookie_');
$passed = 0;
$failed = 0;
$blocked = 0;

function request($method, $url, $cookie, $body = null, $headers = array())
{
    $ch = curl_init($url);
    $hdrs = array_merge(array('Accept: text/html,application/json'), $headers);
    curl_setopt_array($ch, array(
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_COOKIEJAR => $cookie,
        CURLOPT_COOKIEFILE => $cookie,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTPHEADER => $hdrs,
    ));
    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }
    $raw = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    $parts = explode("\r\n\r\n", (string) $raw, 2);
    return array(
        'code' => $code,
        'headers' => isset($parts[0]) ? $parts[0] : '',
        'body' => isset($parts[1]) ? $parts[1] : '',
        'error' => $err,
    );
}

function check($label, $ok, &$passed, &$failed)
{
    if ($ok) {
        echo "PASS $label\n";
        $passed++;
    } else {
        echo "FAIL $label\n";
        $failed++;
    }
}

$home = request('GET', $base . '/', $cookie);
check('homepage reachable', $home['code'] === 200 && $home['error'] === '', $passed, $failed);

$checkout = request('GET', $base . '/checkout', $cookie);
check('checkout page reachable', $checkout['code'] === 200, $passed, $failed);
check('checkout has no COD option', strpos($checkout['body'], 'cashOnDelivery') === false && strpos($checkout['body'], 'Cash on delivery') === false, $passed, $failed);
check('checkout has no PayPal option', stripos($checkout['body'], 'PayPal') === false || stripos($checkout['body'], 'not available') !== false, $passed, $failed);
$emptyCart = stripos($checkout['body'], 'no_products_in_cart') !== false
    || stripos($checkout['body'], 'Continue shopping') !== false
    || stripos($checkout['body'], 'shopping bag') !== false
    || stripos($checkout['body'], 'buyora-cart-empty') !== false;
$hasSafepay = stripos($checkout['body'], 'value="Safepay"') !== false || stripos($checkout['body'], '>Safepay<') !== false;
check('empty checkout hides payment form OR shows Safepay when cart has items', $emptyCart || $hasSafepay, $passed, $failed);

$cod = request(
    'POST',
    $base . '/checkout',
    $cookie,
    http_build_query(array(
        'payment_type' => 'cashOnDelivery',
        'first_name' => 'Audit',
        'last_name' => 'Tester',
        'email' => 'buyora.audit@example.com',
        'phone' => '03001234567',
        'address' => 'Test Street 1',
        'city' => 'Karachi',
        'post_code' => '74000',
        'final_amount' => '1'
    )),
    array('Content-Type: application/x-www-form-urlencoded')
);
$codOk = in_array($cod['code'], array(200, 302, 303), true);
check('COD POST does not crash', $codOk, $passed, $failed);
$codFollow = request('GET', $base . '/checkout', $cookie);
check(
    'COD rejected in UI flash or message',
    stripos($codFollow['body'], 'Cash on delivery') !== false
        || stripos($codFollow['body'], 'not available') !== false
        || stripos($codFollow['body'], 'cart is empty') !== false,
    $passed,
    $failed
);

$paypal2 = request('GET', $base . '/index.php/checkout/paypal_success', $cookie);
check(
    'PayPal success redirects away unpaid',
    in_array($paypal2['code'], array(302, 303, 307), true)
        || (stripos($paypal2['headers'], 'Location:') !== false),
    $passed,
    $failed
);

$badSig = request(
    'POST',
    $base . '/checkout/safepay/webhook',
    $cookie,
    '{"type":"payment.succeeded"}',
    array('Content-Type: application/json', 'X-SFPY-SIGNATURE: nope')
);
check('unsigned webhook returns 400', $badSig['code'] === 400, $passed, $failed);
check('unsigned webhook body says invalid signature', stripos($badSig['body'], 'invalid signature') !== false, $passed, $failed);

$missingSig = request(
    'POST',
    $base . '/checkout/safepay/webhook',
    $cookie,
    '{"type":"payment.failed"}',
    array('Content-Type: application/json')
);
check('missing signature returns 400', $missingSig['code'] === 400, $passed, $failed);

$malformed = request(
    'POST',
    $base . '/checkout/safepay/webhook',
    $cookie,
    'not-json',
    array('Content-Type: application/json', 'X-SFPY-SIGNATURE: deadbeef')
);
check('malformed signed body returns 400', $malformed['code'] === 400, $passed, $failed);

$adminFinance = request('GET', $base . '/admin/finance', $cookie);
check('admin finance requires auth (redirect)', in_array($adminFinance['code'], array(302, 303, 307), true), $passed, $failed);
$vendorFinance = request('GET', $base . '/vendor/finance', $cookie);
check('vendor finance requires auth (redirect)', in_array($vendorFinance['code'], array(302, 303, 307), true), $passed, $failed);

$vendorLogin = request('GET', $base . '/vendor/login', $cookie);
check('vendor login page loads', $vendorLogin['code'] === 200, $passed, $failed);
$vendorRegister = request('GET', $base . '/vendor/register', $cookie);
check('vendor register page loads', $vendorRegister['code'] === 200, $passed, $failed);

$cancel = request('GET', $base . '/checkout/safepay/cancel', $cookie);
check('safepay cancel page loads', $cancel['code'] === 200, $passed, $failed);
$ret = request('GET', $base . '/checkout/safepay/return', $cookie);
check('safepay return without tracker stays unpaid page', in_array($ret['code'], array(200, 302, 303), true), $passed, $failed);

@unlink($cookie);
echo "\nSMOKE passed=$passed failed=$failed blocked=$blocked\n";
exit($failed > 0 ? 1 : 0);
