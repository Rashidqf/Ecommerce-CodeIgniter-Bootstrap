<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Safepay
{
    public function environment()
    {
        $environment = getenv('SAFEPAY_ENVIRONMENT');
        return ($environment === 'production' || $environment === 'live') ? 'production' : 'sandbox';
    }

    public function isConfigured()
    {
        return $this->secret() !== '' && $this->publicKey() !== '';
    }

    public function secret()
    {
        $value = getenv('SAFEPAY_SECRET_KEY');
        return is_string($value) ? trim($value) : '';
    }

    public function publicKey()
    {
        $value = getenv('SAFEPAY_PUBLIC_KEY');
        return is_string($value) ? trim($value) : '';
    }

    public function hmacSecret()
    {
        $value = getenv('SAFEPAY_HMAC_SECRET');
        return is_string($value) ? trim($value) : '';
    }

    public function intent()
    {
        $intent = getenv('SAFEPAY_INTENT');
        $intent = is_string($intent) ? strtoupper(trim($intent)) : '';
        return in_array($intent, array('CYBERSOURCE', 'MPGS'), true) ? $intent : 'CYBERSOURCE';
    }

    public function apiHost()
    {
        return $this->environment() === 'production'
            ? 'https://api.getsafepay.com'
            : 'https://sandbox.api.getsafepay.com';
    }

    public static function signatureMatches($payload, $secret, $signature)
    {
        if (!is_string($payload) || !is_string($secret) || $secret === '' || !is_string($signature) || trim($signature) === '') {
            return false;
        }
        $expected = hash_hmac('sha512', $payload, $secret);
        return hash_equals($expected, strtolower(trim($signature)));
    }

    public static function webhookTracker(array $payload)
    {
        if (isset($payload['data']['tracker']) && is_string($payload['data']['tracker'])) {
            return $payload['data']['tracker'];
        }
        if (isset($payload['data']['tracker']['token'])) {
            return (string) $payload['data']['tracker']['token'];
        }
        if (isset($payload['notification']['tracker'])) {
            return (string) $payload['notification']['tracker'];
        }
        if (isset($payload['tracker']) && is_string($payload['tracker'])) {
            return $payload['tracker'];
        }
        return '';
    }

    public function createSession($amountMinor, $orderNumber)
    {
        return $this->request('POST', '/order/payments/v3/', array(
            'merchant_api_key' => $this->publicKey(),
            'intent' => $this->intent(),
            'mode' => 'payment',
            'entry_mode' => 'raw',
            'currency' => 'PKR',
            'amount' => (int) $amountMinor,
            'metadata' => array('order_id' => (string) $orderNumber),
            'include_fees' => false
        ));
    }

    public function createPassport()
    {
        // Safepay expects a JSON object body (`{}`), not an empty array (`[]`).
        return $this->request('POST', '/client/passport/v1/token', new stdClass());
    }

    public function fetchPayment($tracker)
    {
        return $this->request('GET', '/reporter/api/v1/payments/' . rawurlencode($tracker), null);
    }

    public function checkoutUrl($tracker, $authenticationToken, $redirectUrl, $cancelUrl)
    {
        // Official sfpy-php Checkout::constructURL uses /embedded (not /checkout/pay).
        $base = $this->environment() === 'production'
            ? 'https://getsafepay.com/embedded'
            : 'https://sandbox.api.getsafepay.com/embedded';
        return $base . '?' . http_build_query(array(
            'environment' => $this->environment(),
            'tracker' => $tracker,
            'source' => 'hosted',
            'tbt' => $authenticationToken,
            'redirect_url' => $redirectUrl,
            'cancel_url' => $cancelUrl
        ), '', '&', PHP_QUERY_RFC3986);
    }

    public function publicBaseUrl()
    {
        $configured = getenv('SAFEPAY_PUBLIC_BASE');
        if (is_string($configured) && trim($configured) !== '') {
            return rtrim(trim($configured), '/');
        }
        return rtrim((string) base_url(), '/');
    }

    public function trackerState(array $response)
    {
        if (isset($response['data']['tracker']['state'])) {
            return (string) $response['data']['tracker']['state'];
        }
        return '';
    }

    public function trackerAmount(array $response)
    {
        if (isset($response['data']['tracker']['purchase_totals']['quote_amount']['amount'])) {
            return (int) $response['data']['tracker']['purchase_totals']['quote_amount']['amount'];
        }
        return null;
    }

    public function trackerCurrency(array $response)
    {
        if (isset($response['data']['tracker']['purchase_totals']['quote_amount']['currency'])) {
            return (string) $response['data']['tracker']['purchase_totals']['quote_amount']['currency'];
        }
        return '';
    }

    private function request($method, $path, $body)
    {
        if (!$this->isConfigured() || !function_exists('curl_init')) {
            return array('ok' => false, 'error' => 'Safepay sandbox is not configured.');
        }
        $ch = curl_init($this->apiHost() . $path);
        $headers = array(
            'X-SFPY-MERCHANT-SECRET: ' . $this->secret(),
            'Accept: application/json'
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        if ($body !== null) {
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $raw = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        $decoded = is_string($raw) ? json_decode($raw, true) : null;
        if ($error !== '' || $status < 200 || $status >= 300 || !is_array($decoded)) {
            return array('ok' => false, 'error' => $error !== '' ? $error : 'Safepay returned HTTP ' . $status);
        }
        return array('ok' => true, 'body' => $decoded);
    }
}
