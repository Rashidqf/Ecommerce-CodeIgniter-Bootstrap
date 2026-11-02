<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BankAlfalah
{
    private $ci;

    // Bank Alfalah Alfa Payment Gateway (APG) Endpoints
    const SANDBOX_HANDSHAKE_URL = 'https://sandbox.bankalfalah.com/HS/HS/HS';
    const SANDBOX_REDIRECT_URL  = 'https://sandbox.bankalfalah.com/Page/Page/Page';
    const SANDBOX_INQUIRY_URL   = 'https://sandbox.bankalfalah.com/api/v1/Order/Status';

    const LIVE_HANDSHAKE_URL    = 'https://payments.bankalfalah.com/HS/HS/HS';
    const LIVE_REDIRECT_URL     = 'https://payments.bankalfalah.com/Page/Page/Page';
    const LIVE_INQUIRY_URL      = 'https://payments.bankalfalah.com/api/v1/Order/Status';

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->model('admin/Home_admin_model');
    }

    public function environment()
    {
        $env = getenv('ALFALAH_ENVIRONMENT');
        if ($env !== false && $env !== '') {
            return strtolower(trim($env)) === 'production' || strtolower(trim($env)) === 'live' ? 'production' : 'sandbox';
        }
        $stored = $this->ci->Home_admin_model->getValueStore('alfalah_environment');
        return ($stored === 'production' || $stored === 'live') ? 'production' : 'sandbox';
    }

    public function isEnabled()
    {
        $env = getenv('ALFALAH_ENABLED');
        if ($env !== false && $env !== '') {
            return (bool) $env;
        }
        $stored = $this->ci->Home_admin_model->getValueStore('alfalah_enabled');
        if ($stored !== null && $stored !== '') {
            return $stored === '1' || $stored === 1 || $stored === true;
        }
        return true; // Enabled by default if configured
    }

    public function isConfigured()
    {
        return $this->merchantId() !== '' && $this->storeId() !== '' && $this->key1() !== '';
    }

    public function merchantId()
    {
        if ($this->environment() === 'production') {
            $liveVal = getenv('ALFALAH_LIVE_MERCHANT_ID');
            if ($liveVal !== false && $liveVal !== '') return trim((string) $liveVal);
        }
        $val = getenv('ALFALAH_MERCHANT_ID');
        if ($val !== false && $val !== '') return trim((string) $val);
        $stored = $this->ci->Home_admin_model->getValueStore('alfalah_merchant_id');
        return $stored ? trim((string) $stored) : '27275';
    }

    public function storeId()
    {
        if ($this->environment() === 'production') {
            $liveVal = getenv('ALFALAH_LIVE_STORE_ID');
            if ($liveVal !== false && $liveVal !== '') return trim((string) $liveVal);
        }
        $val = getenv('ALFALAH_STORE_ID');
        if ($val !== false && $val !== '') return trim((string) $val);
        $stored = $this->ci->Home_admin_model->getValueStore('alfalah_store_id');
        return $stored ? trim((string) $stored) : '038491';
    }

    public function channelId()
    {
        $val = getenv('ALFALAH_CHANNEL_ID');
        if ($val !== false && $val !== '') return trim((string) $val);
        $stored = trim((string) $this->ci->Home_admin_model->getValueStore('alfalah_channel_id'));
        return $stored !== '' ? $stored : '1001';
    }

    public function merchantUsername()
    {
        if ($this->environment() === 'production') {
            $liveVal = getenv('ALFALAH_LIVE_MERCHANT_USERNAME');
            if ($liveVal !== false && $liveVal !== '') return trim((string) $liveVal);
        }
        $val = getenv('ALFALAH_MERCHANT_USERNAME');
        if ($val !== false && $val !== '') return trim((string) $val);
        $stored = $this->ci->Home_admin_model->getValueStore('alfalah_merchant_username');
        return $stored ? trim((string) $stored) : 'tikimu';
    }

    public function merchantPassword()
    {
        if ($this->environment() === 'production') {
            $liveVal = getenv('ALFALAH_LIVE_MERCHANT_PASSWORD');
            if ($liveVal !== false && $liveVal !== '') return trim((string) $liveVal);
        }
        $val = getenv('ALFALAH_MERCHANT_PASSWORD');
        if ($val !== false && $val !== '') return trim((string) $val);
        $stored = $this->ci->Home_admin_model->getValueStore('alfalah_merchant_password');
        return $stored ? trim((string) $stored) : 'e3a2Ozgcr21vFzk4yqF7CA==';
    }

    public function key1()
    {
        $val = getenv('ALFALAH_KEY_1');
        if ($val !== false && $val !== '') return trim((string) $val);
        $stored = $this->ci->Home_admin_model->getValueStore('alfalah_key1');
        return $stored ? trim((string) $stored) : 'hqmuaVc36sQDqcAc';
    }

    public function key2()
    {
        $val = getenv('ALFALAH_KEY_2');
        if ($val !== false && $val !== '') return trim((string) $val);
        $stored = $this->ci->Home_admin_model->getValueStore('alfalah_key2');
        return $stored ? trim((string) $stored) : '8453964436470719';
    }

    public function merchantHash()
    {
        if ($this->environment() === 'production') {
            $liveVal = getenv('ALFALAH_LIVE_MERCHANT_HASH');
            if ($liveVal !== false && $liveVal !== '') return trim((string) $liveVal);
        }
        $val = getenv('ALFALAH_MERCHANT_HASH');
        if ($val !== false && $val !== '') return trim((string) $val);
        $stored = $this->ci->Home_admin_model->getValueStore('alfalah_merchant_hash');
        return $stored ? trim((string) $stored) : 'OUU362MB1uo8k0czzNv4NIcTgaOALDLRIxDyGypyiUm2r8/W86jBuEcjTAqRvvNfL82ysYRYDsP/9YsfvY02rg==';
    }

    public function handshakeUrl()
    {
        return $this->environment() === 'production' ? self::LIVE_HANDSHAKE_URL : self::SANDBOX_HANDSHAKE_URL;
    }

    public function redirectUrl()
    {
        return $this->environment() === 'production' ? self::LIVE_REDIRECT_URL : self::SANDBOX_REDIRECT_URL;
    }

    public function returnUrl()
    {
        return base_url('checkout/alfalah/return');
    }

    public function cancelUrl()
    {
        return base_url('checkout/alfalah/cancel');
    }

    public function webhookUrl()
    {
        return base_url('checkout/alfalah/webhook');
    }

    /**
     * Generate Bank Alfalah APG Request Hash using AES-128-CBC encryption
     * Sorting parameter keys alphabetically (ksort) is required by Bank Alfalah APG
     */
    public function generateRequestHash(array $params)
    {
        ksort($params);
        $pairs = array();
        foreach ($params as $k => $v) {
            if ($k === 'HS_RequestHash' || $k === 'RequestHash' || $v === null) {
                continue;
            }
            $pairs[] = $k . '=' . trim((string) $v);
        }
        $dataString = implode('&', $pairs);
        $key1 = $this->key1();
        $key2 = $this->key2();

        // Bank Alfalah AES-128-CBC encryption using Key 1 (Cipher Key) and Key 2 (IV)
        return openssl_encrypt($dataString, 'AES-128-CBC', $key1, 0, $key2);
    }

    /**
     * Create Handshake Token from Bank Alfalah APG
     */
    public function createHandshakeToken($transactionRef)
    {
        $params = array(
            'HS_ChannelId'                 => $this->channelId(),
            'HS_IsRedirectionRequest'      => '0',
            'HS_MerchantId'                => $this->merchantId(),
            'HS_StoreId'                   => $this->storeId(),
            'HS_ReturnURL'                 => $this->returnUrl(),
            'HS_MerchantUsername'          => $this->merchantUsername(),
            'HS_MerchantPassword'          => $this->merchantPassword(),
            'HS_TransactionReferenceNumber'=> (string) $transactionRef,
            'HS_MerchantHash'              => $this->merchantHash()
        );

        $params['HS_RequestHash'] = $this->generateRequestHash($params);

        $ch = curl_init($this->handshakeUrl());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded'
        ));

        $rawResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError !== '') {
            return array('ok' => false, 'error' => 'Connection to Bank Alfalah failed: ' . $curlError);
        }

        $decoded = json_decode($rawResponse, true);
        if (is_array($decoded)) {
            if (!empty($decoded['AuthToken'])) {
                return array('ok' => true, 'auth_token' => $decoded['AuthToken'], 'raw' => $decoded);
            }
            $errMsg = !empty($decoded['ErrorMessage']) ? $decoded['ErrorMessage'] : (!empty($decoded['ResponseDescription']) ? $decoded['ResponseDescription'] : 'Auth token missing');
            return array('ok' => false, 'error' => $errMsg, 'raw' => $decoded);
        }

        parse_str($rawResponse, $parsed);
        if (!empty($parsed['AuthToken'])) {
            return array('ok' => true, 'auth_token' => $parsed['AuthToken'], 'raw' => $parsed);
        }

        return array('ok' => false, 'error' => 'Unexpected response from Bank Alfalah APG (' . $httpCode . ')', 'response' => $rawResponse);
    }

    /**
     * Build Hosted Checkout Redirection Form Data
     */
    public function buildRedirectPayload($authToken, $transactionRef, $amountFormatted, array $customer = array())
    {
        $payload = array(
            'AuthToken'                  => (string) $authToken,
            'RequestHash'                => '',
            'ChannelId'                  => $this->channelId(),
            'Currency'                   => 'PKR',
            'IsRedirectionRequest'       => '1',
            'MerchantId'                 => $this->merchantId(),
            'StoreId'                    => $this->storeId(),
            'MerchantUsername'           => $this->merchantUsername(),
            'MerchantPassword'           => $this->merchantPassword(),
            'MerchantHash'               => $this->merchantHash(),
            'ReturnURL'                  => $this->returnUrl(),
            'TransactionAmount'          => number_format((float) $amountFormatted, 2, '.', ''),
            'TransactionReferenceNumber' => (string) $transactionRef,
            'OrderDate'                  => date('Y-m-d H:i:s'),
            'OrderDescription'           => 'BUYORA Order ' . $transactionRef
        );

        if (!empty($customer['first_name'])) {
            $payload['CustomerName'] = trim($customer['first_name'] . ' ' . (isset($customer['last_name']) ? $customer['last_name'] : ''));
        }
        if (!empty($customer['email'])) {
            $payload['CustomerEmail'] = trim($customer['email']);
        }
        if (!empty($customer['phone'])) {
            $payload['CustomerMobile'] = trim($customer['phone']);
        }

        // Generate RequestHash for page redirect
        $payload['RequestHash'] = $this->generateRequestHash($payload);

        return $payload;
    }

    /**
     * Verify Return Payload from Bank Alfalah
     * ResponseCode '00' or '0' means transaction was approved/successful
     */
    public function isPaymentSuccessful(array $response)
    {
        $code = isset($response['ResponseCode']) ? trim((string) $response['ResponseCode']) : (isset($response['RC']) ? trim((string) $response['RC']) : '');
        return $code === '00' || $code === '0';
    }

    public function getTransactionReference(array $response)
    {
        if (!empty($response['TransactionReferenceNumber'])) {
            return trim((string) $response['TransactionReferenceNumber']);
        }
        if (!empty($response['TransactionRefNumber'])) {
            return trim((string) $response['TransactionRefNumber']);
        }
        if (!empty($response['OrderNumber'])) {
            return trim((string) $response['OrderNumber']);
        }
        return '';
    }

    public function getAuthCode(array $response)
    {
        return !empty($response['AuthCode']) ? trim((string) $response['AuthCode']) : (!empty($response['ApprovalCode']) ? trim((string) $response['ApprovalCode']) : '');
    }

    public function getAmount(array $response)
    {
        $amt = isset($response['TransactionAmount']) ? $response['TransactionAmount'] : (isset($response['Amount']) ? $response['Amount'] : 0);
        return (float) $amt;
    }
}
