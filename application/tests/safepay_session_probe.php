<?php
define('BASEPATH', __DIR__);
$envFile = dirname(__DIR__) . '/../.env';
foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
        continue;
    }
    list($k, $v) = explode('=', $line, 2);
    if (getenv($k) === false) {
        putenv($k . '=' . $v);
        $_ENV[$k] = $v;
    }
}
require dirname(__DIR__) . '/libraries/Safepay.php';
$sp = new Safepay();
$session = $sp->createSession(10000, 'AUDIT' . time());
$tracker = isset($session['body']['data']['tracker']['token']) ? $session['body']['data']['tracker']['token'] : '';
$passport = $sp->createPassport();
$token = isset($passport['body']['data']) && is_string($passport['body']['data']) ? $passport['body']['data'] : '';
$ngrok = 'https://gloater-roman-spool.ngrok-free.dev';
$url = $sp->checkoutUrl($tracker, $token, $ngrok . '/checkout/safepay/return', $ngrok . '/checkout/safepay/cancel');
echo 'path_ok=' . (strpos($url, '/embedded?') !== false ? '1' : '0') . PHP_EOL;
echo 'env_ok=' . (strpos($url, 'environment=sandbox') !== false ? '1' : '0') . PHP_EOL;
file_put_contents(sys_get_temp_dir() . '/buyora-safepay-checkout-url.txt', $url);
$ch = curl_init($url);
curl_setopt_array($ch, array(CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => false, CURLOPT_HEADER => true, CURLOPT_TIMEOUT => 30));
$raw = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo 'first_hop=' . $code . PHP_EOL;
if (preg_match('/^Location:\s*(.+)$/mi', (string) $raw, $m)) {
    echo 'location=' . trim($m[1]) . PHP_EOL;
}
echo substr((string) $raw, 0, 200) . PHP_EOL;
