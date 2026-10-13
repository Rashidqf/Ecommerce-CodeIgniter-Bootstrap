<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Checkout extends MY_Controller
{

    private $orderId;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin/Orders_model');
    }

    public function index()
    {
        $data = array();
        $head = array();
        $arrSeo = $this->Public_model->getSeo('checkout');
        $head['title'] = @$arrSeo['title'];
        $head['description'] = @$arrSeo['description'];
        $head['keywords'] = $head['title'] !== null ? str_replace(" ", ",", $head['title']) : '';

        if (isset($_POST['payment_type'])) {
            $errors = $this->userInfoValidate($_POST);
            $quote = $this->checkoutQuote();
            if (isset($quote['error'])) {
                $errors[] = $quote['error'];
            }
            $paymentType = (string) $this->input->post('payment_type');
            $bankReady = $this->bankReady();
            $this->load->library('Safepay');
            if (!Order_quote::paymentAllowed($paymentType, $bankReady, $this->safepay->isConfigured())) {
                $errors[] = 'Choose Safepay or bank transfer. Cash on delivery and PayPal are not available.';
            }
            if (!empty($errors)) {
                $this->session->set_flashdata('submit_error', $errors);
            } else {
                $_POST['payment_type'] = $paymentType;
                $_POST['referrer'] = $this->session->userdata('referrer') ? $this->session->userdata('referrer') : '';
                $_POST['clean_referrer'] = cleanReferral($_POST['referrer']);
                $_POST['user_id'] = isset($_SESSION['logged_user']) ? $_SESSION['logged_user'] : 0;
                $_POST['discountCode'] = $quote['discount_code'];
                $saved = $this->Public_model->setOrder($_POST);
                if ($saved == false) {
                    log_message('error', 'Cant save order!!');
                    $this->session->set_flashdata('order_error', true);
                    redirect(LANG_URL . '/checkout/order-error');
                }
                $this->load->model('Ledger_model');
                $this->Ledger_model->saveOrderItems($saved['id'], $saved['order_id'], $quote['items']);
                $this->Public_model->setVendorOrder($_POST, $saved['order_id'], $quote['items']);
                $this->orderId = $saved['order_id'];
                $this->setActivationLink();
                $this->sendNotifications();
                $payment = $this->Ledger_model->createPayment(
                    $saved['id'],
                    $saved['order_id'],
                    $paymentType === 'Bank' ? 'bank' : 'safepay',
                    $quote['payable_minor'],
                    ($paymentType === 'Bank' ? 'bank:' : 'safepay:') . $saved['id']
                );
                $this->goToDestination($payment, $quote);
            }
        }
        $data['bank_account'] = $this->Orders_model->getBankAccountSettings();
        $this->load->library('Safepay');
        $data['safepay_ready'] = $this->safepay->isConfigured();
        $data['safepay_environment'] = $this->safepay->environment();
        $data['safepay_prepare_url'] = base_url('checkout/safepay/prepare');
        $data['safepay_start_url'] = base_url('checkout/safepay/start');
        $data['shippingAmount'] = $this->Home_admin_model->getValueStore('shippingAmount');
        $data['codeDiscounts'] = $this->Home_admin_model->getValueStore('codeDiscounts');
        $data['bestSellers'] = $this->Public_model->getbestSellers();
        $this->render('buyora-checkout', $head, $data);
    }

    public function safepayPrepare()
    {
        $this->load->library('Safepay');
        $this->load->library('Order_quote');
        if (!$this->safepay->isConfigured()) {
            return $this->jsonCheckoutError('Safepay is not configured.', 503);
        }
        $quote = $this->checkoutQuote();
        if (isset($quote['error'])) {
            return $this->jsonCheckoutError($quote['error'], 400);
        }
        $cartHash = $this->checkoutCartHash($quote);
        $prepared = isset($_SESSION['buyora_safepay_prepare']) && is_array($_SESSION['buyora_safepay_prepare'])
            ? $_SESSION['buyora_safepay_prepare']
            : array();
        // Passport / TBT tokens expire quickly; never reuse an old auth token.
        $tracker = '';
        if (
            !empty($prepared['tracker'])
            && isset($prepared['cart_hash'], $prepared['amount_minor'], $prepared['created_at'])
            && $prepared['cart_hash'] === $cartHash
            && (int) $prepared['amount_minor'] === (int) $quote['payable_minor']
            && ((time() - (int) $prepared['created_at']) < 240)
        ) {
            $tracker = (string) $prepared['tracker'];
        }
        if ($tracker === '') {
            $session = $this->safepay->createSession((int) $quote['payable_minor'], 'CART' . time());
            $tracker = isset($session['body']['data']['tracker']['token']) ? $session['body']['data']['tracker']['token'] : '';
            if ($tracker === '') {
                $detail = !empty($session['error']) ? $session['error'] : 'Safepay card fields could not be initialized.';
                return $this->jsonCheckoutError($detail, 502);
            }
        }
        $passport = $this->safepay->createPassport();
        $token = isset($passport['body']['data']) && is_string($passport['body']['data']) ? $passport['body']['data'] : '';
        if ($token === '') {
            $detail = !empty($passport['error']) ? $passport['error'] : 'Safepay authentication token could not be created.';
            return $this->jsonCheckoutError($detail, 502);
        }
        $_SESSION['buyora_safepay_prepare'] = array(
            'tracker' => $tracker,
            'auth_token' => $token,
            'amount_minor' => (int) $quote['payable_minor'],
            'cart_hash' => $cartHash,
            'created_at' => time()
        );
        return $this->jsonCheckoutOk($this->embeddedPayload($tracker, $token, (int) $quote['payable_minor'], 0));
    }

    public function safepayStart()
    {
        $this->load->library('Safepay');
        $this->load->library('Order_quote');
        if (!$this->safepay->isConfigured()) {
            return $this->jsonCheckoutError('Safepay is not configured.', 503);
        }
        $errors = $this->userInfoValidate($_POST);
        $quote = $this->checkoutQuote();
        if (isset($quote['error'])) {
            $errors[] = $quote['error'];
        }
        if (!Order_quote::paymentAllowed('Safepay', false, true)) {
            $errors[] = 'Safepay is not available.';
        }
        if (!empty($errors)) {
            return $this->jsonCheckoutError(implode(' ', $errors), 400);
        }

        $this->load->model('Ledger_model');
        $cartHash = $this->checkoutCartHash($quote);
        $payment = $this->reusePendingSafepayPayment($cartHash, (int) $quote['payable_minor']);
        if (empty($payment)) {
            $_POST['payment_type'] = 'Safepay';
            $_POST['referrer'] = $this->session->userdata('referrer') ? $this->session->userdata('referrer') : '';
            $_POST['clean_referrer'] = cleanReferral($_POST['referrer']);
            $_POST['user_id'] = isset($_SESSION['logged_user']) ? $_SESSION['logged_user'] : 0;
            $_POST['discountCode'] = $quote['discount_code'];
            $saved = $this->Public_model->setOrder($_POST);
            if ($saved == false) {
                return $this->jsonCheckoutError('Could not create the order. Try again.', 500);
            }
            $this->Ledger_model->saveOrderItems($saved['id'], $saved['order_id'], $quote['items']);
            $this->Public_model->setVendorOrder($_POST, $saved['order_id'], $quote['items']);
            $this->orderId = $saved['order_id'];
            $this->setActivationLink();
            $this->sendNotifications();
            $payment = $this->Ledger_model->createPayment(
                $saved['id'],
                $saved['order_id'],
                'safepay',
                $quote['payable_minor'],
                'safepay:' . $saved['id']
            );
            $_SESSION['buyora_safepay_checkout'] = array(
                'payment_id' => (int) $payment['id'],
                'order_row_id' => (int) $saved['id'],
                'order_id' => (int) $saved['order_id'],
                'amount_minor' => (int) $quote['payable_minor'],
                'cart_hash' => $cartHash
            );
        } else {
            $this->orderId = (int) $payment['order_number'];
        }

        $prepared = isset($_SESSION['buyora_safepay_prepare']) && is_array($_SESSION['buyora_safepay_prepare'])
            ? $_SESSION['buyora_safepay_prepare']
            : array();
        $tracker = '';
        if (
            !empty($prepared['tracker'])
            && isset($prepared['cart_hash'], $prepared['amount_minor'], $prepared['created_at'])
            && $prepared['cart_hash'] === $cartHash
            && (int) $prepared['amount_minor'] === (int) $payment['amount_minor']
            && ((time() - (int) $prepared['created_at']) < 240)
        ) {
            $tracker = (string) $prepared['tracker'];
        } else {
            $session = $this->safepay->createSession((int) $payment['amount_minor'], $this->orderId);
            $tracker = isset($session['body']['data']['tracker']['token']) ? $session['body']['data']['tracker']['token'] : '';
            if ($tracker === '') {
                $detail = !empty($session['error']) ? $session['error'] : 'Safepay could not start the payment session.';
                return $this->jsonCheckoutError($detail, 502);
            }
        }
        // Always mint a fresh passport / TBT for card capture and 3DS.
        $passport = $this->safepay->createPassport();
        $token = isset($passport['body']['data']) && is_string($passport['body']['data']) ? $passport['body']['data'] : '';
        if ($token === '') {
            $detail = !empty($passport['error']) ? $passport['error'] : 'Safepay authentication token could not be created.';
            return $this->jsonCheckoutError($detail, 502);
        }
        $_SESSION['buyora_safepay_prepare'] = array(
            'tracker' => $tracker,
            'auth_token' => $token,
            'amount_minor' => (int) $payment['amount_minor'],
            'cart_hash' => $cartHash,
            'created_at' => time()
        );
        $this->Ledger_model->attachTracker((int) $payment['id'], $tracker);
        $publicBase = $this->safepay->publicBaseUrl();
        $returnUrl = $publicBase . '/checkout/safepay/return';
        $cancelUrl = $publicBase . '/checkout/safepay/cancel';
        $mode = (string) $this->input->post('safepay_mode');
        if ($mode === 'hosted') {
            return $this->jsonCheckoutOk(array(
                'mode' => 'hosted',
                'checkout_url' => $this->safepay->checkoutUrl($tracker, $token, $returnUrl, $cancelUrl),
                'tracker' => $tracker,
                'order_id' => (int) $this->orderId,
                'amount_label' => Order_quote::formatMinor((int) $payment['amount_minor']) . ' Rs.'
            ));
        }
        $payload = $this->embeddedPayload($tracker, $token, (int) $payment['amount_minor'], (int) $this->orderId);
        $payload['billing'] = array(
            'street_1' => (string) $this->input->post('address'),
            'city' => (string) $this->input->post('city'),
            'state' => (string) $this->input->post('city'),
            'country' => 'PK',
            'postal_code' => (string) $this->input->post('post_code')
        );
        return $this->jsonCheckoutOk($payload);
    }

    private function embeddedPayload($tracker, $token, $amountMinor, $orderId)
    {
        $this->load->library('Safepay');
        $this->load->library('Order_quote');
        $publicBase = $this->safepay->publicBaseUrl();
        $returnUrl = $publicBase . '/checkout/safepay/return';
        $cancelUrl = $publicBase . '/checkout/safepay/cancel';
        return array(
            'mode' => 'embedded',
            'environment' => $this->safepay->environment(),
            'tracker' => $tracker,
            'auth_token' => $token,
            'order_id' => (int) $orderId,
            'amount_minor' => (int) $amountMinor,
            'amount_label' => Order_quote::formatMinor((int) $amountMinor) . ' Rs.',
            'return_url' => $returnUrl . '?tracker=' . rawurlencode($tracker),
            'cancel_url' => $cancelUrl,
            'checkout_url' => $this->safepay->checkoutUrl($tracker, $token, $returnUrl, $cancelUrl)
        );
    }

    private function bankReady()
    {
        $account = $this->Orders_model->getBankAccountSettings();
        return isset($account['iban']) && $account['iban'] != '';
    }

    private function checkoutQuote()
    {
        $this->load->library('Order_quote');
        $this->load->model('Ledger_model');
        $cart = $this->shoppingcart->getCartItems();
        if (!is_array($cart) || empty($_SESSION['shopping_cart']) || !is_array($_SESSION['shopping_cart'])) {
            return array('error' => 'Your cart is empty.');
        }
        if (isset($_POST['id']) && is_array($_POST['id'])) {
            $posted = array();
            $quantities = isset($_POST['quantity']) && is_array($_POST['quantity']) ? $_POST['quantity'] : array();
            foreach ($_POST['id'] as $index => $productId) {
                $qty = isset($quantities[$index]) ? (int) $quantities[$index] : 0;
                for ($copy = 0; $copy < $qty; $copy++) {
                    $posted[] = (int) $productId;
                }
            }
            $sessionCart = array_map('intval', $_SESSION['shopping_cart']);
            sort($posted);
            sort($sessionCart);
            if ($posted !== $sessionCart) {
                return array('error' => 'The cart changed before checkout. Refresh the page and try again.');
            }
        }
        return $this->Ledger_model->quoteFromCart(
            $_SESSION['shopping_cart'],
            $this->input->post('discountCode'),
            $this->Home_admin_model->getValueStore('shippingAmount'),
            $this->Home_admin_model->getValueStore('shippingOrder')
        );
    }

    /*
     * Send notifications to users that have nofify=1 in /admin/adminusers
     */

    private function sendNotifications()
    {
        $users = $this->Public_model->getNotifyUsers();
        $myDomain = $this->config->item('base_url');
        if (!empty($users)) {
            $this->sendmail->clearAddresses();
            foreach ($users as $user) {
                $this->sendmail->sendTo($user, 'Admin', 'New order in ' . $myDomain, 'Hello, you have new order. Can check it in /admin/orders');
            }
        }
    }

    private function setActivationLink()
    {
        if ($this->config->item('send_confirm_link') === true) {
            $link = md5($this->orderId . time());
            $result = $this->Public_model->setActivationLink($link, $this->orderId);
            if ($result == true) {
                $url = parse_url(base_url());
                $msg = lang('please_confirm') . base_url('confirm/' . $link);
                $this->sendmail->sendTo($_POST['email'], $_POST['first_name'] . ' ' . $_POST['last_name'], lang('confirm_order_subj') . $url['host'], $msg);
            }
        }
    }

    private function goToDestination(array $payment, array $quote)
    {
        if ($_POST['payment_type'] == 'Bank') {
            $this->shoppingcart->clearShoppingCart();
            $this->session->set_flashdata('success_order', true);
            $_SESSION['order_id'] = $this->orderId;
            $_SESSION['final_amount'] = Order_quote::formatMinor($quote['payable_minor']) . ' Rs.';
            redirect(LANG_URL . '/checkout/successbank');
        }
        // Hosted Safepay remains available as an explicit fallback from the checkout page.
        $this->load->library('Safepay');
        $session = $this->safepay->createSession((int) $payment['amount_minor'], $this->orderId);
        $tracker = isset($session['body']['data']['tracker']['token']) ? $session['body']['data']['tracker']['token'] : '';
        $passport = $tracker !== '' ? $this->safepay->createPassport() : array('ok' => false);
        $token = isset($passport['body']['data']) && is_string($passport['body']['data']) ? $passport['body']['data'] : '';
        if ($tracker === '' || $token === '') {
            $this->session->set_flashdata('submit_error', array('Safepay could not start the payment. The order is unpaid.'));
            redirect(LANG_URL . '/checkout');
        }
        $this->Ledger_model->attachTracker((int) $payment['id'], $tracker);
        $publicBase = $this->safepay->publicBaseUrl();
        redirect($this->safepay->checkoutUrl(
            $tracker,
            $token,
            $publicBase . '/checkout/safepay/return',
            $publicBase . '/checkout/safepay/cancel'
        ));
    }

    private function checkoutCartHash(array $quote)
    {
        $lines = array();
        foreach ($quote['items'] as $item) {
            $lines[] = (int) $item['product_id'] . ':' . (int) $item['quantity'] . ':' . (int) $item['unit_price_minor'];
        }
        sort($lines);
        return hash('sha256', implode('|', $lines) . '|' . (int) $quote['payable_minor'] . '|' . (string) $quote['discount_code']);
    }

    private function reusePendingSafepayPayment($cartHash, $amountMinor)
    {
        if (empty($_SESSION['buyora_safepay_checkout']) || !is_array($_SESSION['buyora_safepay_checkout'])) {
            return array();
        }
        $state = $_SESSION['buyora_safepay_checkout'];
        if (!isset($state['payment_id'], $state['cart_hash'], $state['amount_minor'])) {
            return array();
        }
        if ($state['cart_hash'] !== $cartHash || (int) $state['amount_minor'] !== (int) $amountMinor) {
            return array();
        }
        $payment = $this->Ledger_model->paymentById((int) $state['payment_id']);
        if (empty($payment) || $payment['provider'] !== 'safepay') {
            return array();
        }
        if (!in_array($payment['status'], array('pending', 'failed'), true)) {
            return array();
        }
        return $payment;
    }

    private function jsonCheckoutOk(array $payload)
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode(array('ok' => true) + $payload));
    }

    private function jsonCheckoutError($message, $status)
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        $this->output->set_status_header((int) $status);
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode(array('ok' => false, 'error' => (string) $message)));
    }

    private function userInfoValidate($post)
    {
        $errors = array();
        if (mb_strlen(trim($post['first_name'])) == 0) {
            $errors[] = lang('first_name_empty');
        }
        if (mb_strlen(trim($post['last_name'])) == 0) {
            $errors[] = lang('last_name_empty');
        }
        if (!filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = lang('invalid_email');
        }
        $post['phone'] = preg_replace("/[^0-9]/", '', $post['phone']);
        if (mb_strlen(trim($post['phone'])) == 0) {
            $errors[] = lang('invalid_phone');
        }
        if (mb_strlen(trim($post['address'])) == 0) {
            $errors[] = lang('address_empty');
        }
        if (mb_strlen(trim($post['city'])) == 0) {
            $errors[] = lang('invalid_city');
        }
        return $errors;
    }

    public function orderError()
    {
        if ($this->session->flashdata('order_error')) {
            $data = array();
            $head = array();
            $arrSeo = $this->Public_model->getSeo('checkout');
            $head['title'] = @$arrSeo['title'];
            $head['description'] = @$arrSeo['description'];
            $head['keywords'] = $head['title'] !== null ? str_replace(" ", ",", $head['title']) : '';
            $this->render('checkout_parts/order_error', $head, $data);
        } else {
            redirect(LANG_URL . '/checkout');
        }
    }

    public function paypalPayment()
    {
        $data = array();
        $head = array();
        $arrSeo = $this->Public_model->getSeo('checkout');
        $head['title'] = @$arrSeo['title'];
        $head['description'] = @$arrSeo['description'];
        $head['keywords'] = $head['title'] !== null ? str_replace(" ", ",", $head['title']) : '';
        $data['paypal_sandbox'] = $this->Home_admin_model->getValueStore('paypal_sandbox');
        $data['paypal_email'] = $this->Home_admin_model->getValueStore('paypal_email');
        $this->render('checkout_parts/paypal_payment', $head, $data);
    }

    public function successPaymentCashOnD()
    {
        if ($this->session->flashdata('success_order')) {
            $data = array();
            $head = array();
            $arrSeo = $this->Public_model->getSeo('checkout');
            $head['title'] = @$arrSeo['title'];
            $head['description'] = @$arrSeo['description'];
            $head['keywords'] = $head['title'] !== null ? str_replace(" ", ",", $head['title']) : '';
            $this->render('checkout_parts/payment_success_cash', $head, $data);
        } else {
            redirect(LANG_URL . '/checkout');
        }
    }

    public function successPaymentBank()
    {
        if ($this->session->flashdata('success_order')) {
            $data = array();
            $head = array();
            $arrSeo = $this->Public_model->getSeo('checkout');
            $head['title'] = @$arrSeo['title'];
            $head['description'] = @$arrSeo['description'];
            $head['keywords'] = $head['title'] !== null ? str_replace(" ", ",", $head['title']) : '';
            $data['bank_account'] = $this->Orders_model->getBankAccountSettings();
            $this->render('checkout_parts/payment_success_bank', $head, $data);
        } else {
            redirect(LANG_URL . '/checkout');
        }
    }

    public function paypal_cancel()
    {
        if (empty($_SESSION['paypal_order_id'])) {
            redirect(base_url());
        }
        $orderId = $_SESSION['paypal_order_id'];
        unset($_SESSION['paypal_order_id']);
        $this->Public_model->changePaypalOrderStatus($orderId, 'canceled');
        $data = array();
        $head = array();
        $head['title'] = '';
        $head['description'] = '';
        $head['keywords'] = '';
        $this->render('checkout_parts/paypal_cancel', $head, $data);
    }

    public function paypal_success()
    {
        unset($_SESSION['paypal_order_id']);
        $this->session->set_flashdata('submit_error', array('PayPal is not available. Pay with Safepay or bank transfer.'));
        redirect(LANG_URL . '/checkout');
    }

    public function safepayReturn()
    {
        $tracker = (string) $this->input->get('tracker');
        $data = array('paid' => false, 'pending' => true);
        $head = array('title' => 'Payment', 'description' => '', 'keywords' => '');
        if ($tracker !== '') {
            $this->load->model('Ledger_model');
            $this->load->library('Safepay');
            $payment = $this->Ledger_model->paymentByTracker($tracker);
            $remote = $this->safepay->fetchPayment($tracker);
            if (!empty($payment) && !empty($remote['ok'])) {
                $paid = $this->Ledger_model->confirmSafepay($payment, $remote['body']);
                $data['paid'] = $paid;
                $data['pending'] = !$paid;
                if ($paid) {
                    $this->shoppingcart->clearShoppingCart();
                }
            }
        }
        $this->render($data['paid'] ? 'checkout_parts/safepay_success' : 'checkout_parts/safepay_pending', $head, $data);
    }

    public function safepayCancel()
    {
        $data = array();
        $head = array('title' => 'Payment cancelled', 'description' => '', 'keywords' => '');
        $this->render('checkout_parts/safepay_cancel', $head, $data);
    }

    public function safepayWebhook()
    {
        $raw = file_get_contents('php://input');
        $signature = '';
        if (function_exists('getallheaders')) {
            foreach (getallheaders() as $name => $value) {
                if (strtolower($name) === 'x-sfpy-signature') {
                    $signature = $value;
                }
            }
        }
        $this->load->library('Safepay');
        if (!Safepay::signatureMatches($raw, $this->safepay->hmacSecret(), $signature)) {
            $this->output->set_status_header(400);
            echo 'invalid signature';
            return;
        }
        $payload = json_decode($raw, true);
        if (!is_array($payload)) {
            $this->output->set_status_header(400);
            echo 'invalid payload';
            return;
        }
        $tracker = Safepay::webhookTracker($payload);
        $eventType = isset($payload['type']) ? (string) $payload['type'] : '';
        $this->load->model('Ledger_model');
        $eventKey = hash('sha256', $raw);
        if ($this->db->where('event_key', $eventKey)->count_all_results('webhook_events') > 0) {
            $this->output->set_status_header(200);
            echo 'duplicate';
            return;
        }
        $payment = $tracker !== '' ? $this->Ledger_model->paymentByTracker($tracker) : array();
        if (!empty($payment) && ($eventType === 'payment.succeeded' || $this->webhookState($payload) === 'PAID')) {
            $remote = $this->safepay->fetchPayment($tracker);
            if (empty($remote['ok'])) {
                $this->output->set_status_header(500);
                echo 'verification unavailable';
                return;
            }
            $this->Ledger_model->confirmSafepay($payment, $remote['body']);
        } elseif (!empty($payment) && $eventType === 'payment.failed') {
            $this->Ledger_model->markPaymentFailed((int) $payment['id']);
        }
        $this->Ledger_model->storeWebhook($eventKey, $tracker, $eventType);
        $this->output->set_status_header(200);
        echo 'ok';
    }

    private function webhookState(array $payload)
    {
        if (isset($payload['notification']['state'])) {
            return (string) $payload['notification']['state'];
        }
        return '';
    }

}
