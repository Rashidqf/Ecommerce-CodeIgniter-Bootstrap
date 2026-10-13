<?php
/**
 * Vendor auth HTTP checks without mutating durable data beyond registration attempt uniqueness.
 * Uses a unique email each run. Does not delete vendors.
 * Run: php application/tests/auth_flow_test.php
 */
$base = getenv('BUYORA_BASE') ?: 'http://127.0.0.1:8080';
$cookie = tempnam(sys_get_temp_dir(), 'buyora_auth_');
$passed = 0;
$failed = 0;
$email = 'buyora.audit.' . time() . '@example.com';

function req($method, $url, $cookie, $body = null)
{
    $ch = curl_init($url);
    $opts = array(
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_COOKIEJAR => $cookie,
        CURLOPT_COOKIEFILE => $cookie,
        CURLOPT_TIMEOUT => 30,
    );
    if ($body !== null) {
        $opts[CURLOPT_POSTFIELDS] = $body;
        $opts[CURLOPT_HTTPHEADER] = array('Content-Type: application/x-www-form-urlencoded');
    }
    curl_setopt_array($ch, $opts);
    $raw = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $parts = explode("\r\n\r\n", (string) $raw, 2);
    return array('code' => $code, 'headers' => $parts[0], 'body' => isset($parts[1]) ? $parts[1] : '');
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

function loc($headers)
{
    if (preg_match('/^Location:\s*(.+)$/mi', $headers, $m)) {
        return trim($m[1]);
    }
    return '';
}

$badLogin = req('POST', $base . '/vendor/login', $cookie, http_build_query(array(
    'email' => 'nobody@example.com',
    'pass' => 'wrong-password',
    'login' => '1'
)));
check('invalid vendor login redirects or stays on login', in_array($badLogin['code'], array(200, 302, 303), true), $passed, $failed);

$regMissing = req('POST', $base . '/vendor/register', $cookie, http_build_query(array(
    'u_email' => 'not-an-email',
    'u_password' => '1',
    'u_password_repeat' => '2',
    'register' => '1'
)));
check('invalid register rejected without redirect to me', strpos(loc($regMissing['headers']), '/vendor/me') === false, $passed, $failed);

$reg = req('POST', $base . '/vendor/register', $cookie, http_build_query(array(
    'u_email' => $email,
    'u_password' => 'AuditPass123!',
    'u_password_repeat' => 'AuditPass123!',
    'register' => '1'
)));
$regLoc = loc($reg['headers']);
$regOk = in_array($reg['code'], array(302, 303, 307), true)
    && (strpos($regLoc, '/vendor/me') !== false || strpos($regLoc, '/vendor/login') !== false);
check('register with valid data redirects (me or login/pending)', $regOk, $passed, $failed);

$dupCookie = tempnam(sys_get_temp_dir(), 'buyora_dup_');
$dup = req('POST', $base . '/vendor/register', $dupCookie, http_build_query(array(
    'u_email' => $email,
    'u_password' => 'AuditPass123!',
    'u_password_repeat' => 'AuditPass123!',
    'register' => '1'
)));
$dupLoc = loc($dup['headers']);
check(
    'duplicate email registration does not auto-login as new session',
    strpos($dupLoc, '/vendor/me') === false,
    $passed,
    $failed
);

$techCookie = tempnam(sys_get_temp_dir(), 'buyora_tv_');
$tech = req('POST', $base . '/vendor/login', $techCookie, http_build_query(array(
    'u_email' => 'technest@buyora.test',
    'u_password' => 'vendor123',
    'login' => '1'
)));
$techLoc = loc($tech['headers']);
check('sample vendor login redirects to me', in_array($tech['code'], array(302, 303, 307), true) && strpos($techLoc, '/vendor/me') !== false, $passed, $failed);
if (strpos($techLoc, 'http') === 0) {
    req('GET', $techLoc, $techCookie);
} elseif ($techLoc !== '') {
    req('GET', $base . $techLoc, $techCookie);
}
$finance = req('GET', $base . '/vendor/finance', $techCookie);
if (in_array($finance['code'], array(302, 303, 307), true) && strpos(loc($finance['headers']), '/vendor/finance') !== false) {
    $finance = req('GET', $base . '/vendor/finance', $techCookie);
}
check('logged-in vendor can open finance', $finance['code'] === 200, $passed, $failed);
if ($finance['code'] === 200) {
    check('vendor finance page is read-only (no commission edit form)', stripos($finance['body'], 'commission_rate_percent') === false, $passed, $failed);
}

@unlink($cookie);
echo "AUTH passed=$passed failed=$failed email=$email\n";
exit($failed > 0 ? 1 : 0);
