<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';

class Customer extends REST_Controller
{
    private $langAbbr;

    public function __construct()
    {
        parent::__construct();

        // Ensure CORS is allowed for Flutter mobile apps and web
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization, X-API-KEY');

        if ($this->input->method() === 'options') {
            exit(0);
        }

        $this->langAbbr = defined('MY_LANGUAGE_ABBR') ? MY_LANGUAGE_ABBR : 'en';

        // Load models & libraries
        $this->load->model(array(
            'Public_model',
            'admin/Home_admin_model',
            'admin/Orders_model',
            'Ledger_model'
        ));
        $this->load->library(array('encryption', 'Order_quote', 'Safepay'));
    }

    /**
     * Helper to read JSON request body or POST data
     */
    private function getPayload()
    {
        $raw = $this->input->raw_input_stream;
        if (!empty($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }
        return $this->input->post() ?: array();
    }

    /**
     * Helper to turn relative image paths into absolute URLs for Flutter Image.network()
     */
    private function fullImageUrl($imagePath, $type = 'product')
    {
        if (empty($imagePath)) {
            return base_url('assets/buyora/buyora-og.jpg');
        }
        if (strpos($imagePath, 'http://') === 0 || strpos($imagePath, 'https://') === 0) {
            return $imagePath;
        }
        if ($type === 'product') {
            return base_url('attachments/shop_images/' . ltrim($imagePath, '/'));
        }
        return base_url(ltrim($imagePath, '/'));
    }

    /**
     * 1. GET /api/config
     * Returns app config, contact information, currency, payment options, and shipping rules.
     */
    public function config_get()
    {
        $phone = $this->Home_admin_model->getValueStore('footerContactPhone') ?: '0333-6108869, 0308-8503190';
        $email = $this->Home_admin_model->getValueStore('footerContactEmail') ?: 'buyoramarketplace@gmail.com';
        $address = $this->Home_admin_model->getValueStore('footerContactAddr') ?: 'Office No. 5, 1st Floor, Karim Centre, 16-Edward Road, Lahore, Pakistan';
        $shippingAmount = (float) ($this->Home_admin_model->getValueStore('shippingAmount') ?: 0);
        $shippingThreshold = (float) ($this->Home_admin_model->getValueStore('shippingOrder') ?: 2000);
        $bankAccount = $this->Orders_model->getBankAccountSettings();
        $bankReady = !empty($bankAccount['iban']);
        $safepayReady = $this->safepay->isConfigured();
        $this->load->library('BankAlfalah');
        $alfalahReady = $this->bankalfalah->isEnabled() && $this->bankalfalah->isConfigured();

        $latestVersion = $this->Home_admin_model->getValueStore('app_latest_version') ?: '1.0.0';
        $minSupportedVersion = $this->Home_admin_model->getValueStore('app_min_supported_version') ?: '1.0.0';
        $forceUpdate = ($this->Home_admin_model->getValueStore('app_force_update') == '1');
        $updateMessage = $this->Home_admin_model->getValueStore('app_update_message') ?: 
            'A new version of Buyora is available with performance improvements, bug fixes, and enhanced shopping features. Please update now to continue.';
        $playStoreUrl = $this->Home_admin_model->getValueStore('app_play_store_url') ?: 
            'https://play.google.com/store/apps/details?id=com.buyora.marketplace.buyora';

        $data = array(
            'app_name' => 'BUYORA Marketplace',
            'base_url' => rtrim(base_url(), '/'),
            'logo_url' => base_url('assets/buyora/buyora-logo.png'),
            'currency' => array(
                'symbol' => 'Rs.',
                'code' => 'PKR'
            ),
            'contact' => array(
                'cell' => $phone,
                'email' => $email,
                'address' => $address,
                'support_hours' => 'Mon - Sat: 9:00 AM - 9:00 PM PKT'
            ),
            'shipping' => array(
                'standard_fee' => $shippingAmount,
                'free_shipping_threshold' => $shippingThreshold,
                'shipping_note' => 'Free Shipping on orders above Rs. ' . number_format($shippingThreshold, 0)
            ),
            'app_version' => array(
                'latest_version' => $latestVersion,
                'min_supported_version' => $minSupportedVersion,
                'force_update' => $forceUpdate,
                'update_message' => $updateMessage,
                'play_store_url' => $playStoreUrl
            ),
            'payment_methods' => array(
                array(
                    'id' => 'cashOnDelivery',
                    'title' => 'Cash on Delivery (COD)',
                    'description' => 'Pay cash when your order is delivered to your doorstep',
                    'enabled' => true
                ),
                array(
                    'id' => 'bank',
                    'title' => 'Direct Bank Transfer',
                    'description' => 'Transfer directly to our corporate bank account',
                    'enabled' => $bankReady,
                    'bank_details' => $bankReady ? array(
                        'name' => isset($bankAccount['name']) ? $bankAccount['name'] : '',
                        'iban' => isset($bankAccount['iban']) ? $bankAccount['iban'] : '',
                        'bank' => isset($bankAccount['bank']) ? $bankAccount['bank'] : '',
                        'bic' => isset($bankAccount['bic']) ? $bankAccount['bic'] : ''
                    ) : null
                ),
                array(
                    'id' => 'safepay',
                    'title' => 'Credit / Debit Card (Safepay)',
                    'description' => 'Secure Visa & Mastercard payment via Safepay 3DS',
                    'enabled' => $safepayReady
                )
            )
        );

        $this->response(array('status' => true, 'data' => $data), REST_Controller::HTTP_OK);
    }

    /**
     * 2. GET /api/home
     * Returns home banners, featured deals, bestsellers, new arrivals, and categories for Flutter home screen.
     */
    public function home_get()
    {
        // 1. Fetch banners from home_banners
        $bannersRaw = $this->db->where('visible', 1)->order_by('position', 'ASC')->get('home_banners')->result_array();
        $banners = array();
        foreach ($bannersRaw as $b) {
            $banners[] = array(
                'id' => (int) $b['id'],
                'placement' => $b['placement'],
                'image_url' => base_url($b['image']),
                'mobile_image_url' => !empty($b['mobile_image']) ? base_url($b['mobile_image']) : base_url($b['image']),
                'alt_text' => $b['alt_text'],
                'target_url' => $b['target_url']
            );
        }

        // 2. Fetch categories
        $categoriesRaw = $this->Public_model->getShopCategories();
        $categories = array();
        foreach ($categoriesRaw as $cat) {
            if ((int) $cat['sub_for'] === 0) {
                $categories[] = array(
                    'id' => (int) $cat['id'],
                    'name' => $cat['name'],
                    'image_url' => !empty($cat['image']) ? $this->fullImageUrl($cat['image'], 'category') : null,
                    'product_count' => (int) $cat['product_count']
                );
            }
        }

        // 3. Featured Products & Best Sellers
        $bestSellersRaw = $this->Public_model->getbestSellers();
        $bestSellers = array();
        if (is_array($bestSellersRaw)) {
            foreach ($bestSellersRaw as $p) {
                $bestSellers[] = $this->formatProductSummary($p);
            }
        }

        // 4. New Arrivals
        $newProductsRaw = $this->Public_model->getNewProducts();
        $newArrivals = array();
        if (is_array($newProductsRaw)) {
            foreach ($newProductsRaw as $p) {
                $newArrivals[] = $this->formatProductSummary($p);
            }
        }

        $response = array(
            'status' => true,
            'data' => array(
                'banners' => $banners,
                'categories' => $categories,
                'best_sellers' => $bestSellers,
                'new_arrivals' => $newArrivals
            )
        );

        $this->response($response, REST_Controller::HTTP_OK);
    }

    /**
     * 3. GET /api/categories
     * Returns full category list with subcategories and product counts.
     */
    public function categories_get()
    {
        $categoriesRaw = $this->Public_model->getShopCategories();
        $parents = array();
        $childrenByParent = array();

        foreach ($categoriesRaw as $c) {
            $parentId = (int) $c['sub_for'];
            $item = array(
                'id' => (int) $c['id'],
                'name' => $c['name'],
                'image_url' => !empty($c['image']) ? $this->fullImageUrl($c['image'], 'category') : null,
                'product_count' => (int) $c['product_count'],
                'sub_for' => $parentId
            );

            if ($parentId === 0) {
                $parents[(int) $c['id']] = $item;
            } else {
                $childrenByParent[$parentId][] = $item;
            }
        }

        $tree = array();
        foreach ($parents as $pId => $parent) {
            $parent['subcategories'] = isset($childrenByParent[$pId]) ? $childrenByParent[$pId] : array();
            $tree[] = $parent;
        }

        $this->response(array(
            'status' => true,
            'count' => count($tree),
            'data' => $tree
        ), REST_Controller::HTTP_OK);
    }

    /**
     * 4. GET /api/products
     * Filterable, searchable, paginated products endpoint for Flutter catalog/search.
     * Query params:
     *   - category_id (int)
     *   - search (string)
     *   - sort (newest | price_asc | price_desc | popular)
     *   - page (default: 1)
     *   - limit (default: 20)
     */
    public function products_get()
    {
        $categoryId = $this->input->get('category_id');
        $search = trim((string) $this->input->get('search'));
        $sort = strtolower(trim((string) $this->input->get('sort')));
        $page = max(1, (int) $this->input->get('page'));
        $limit = max(1, min(100, (int) ($this->input->get('limit') ?: 20)));
        $offset = ($page - 1) * $limit;

        $this->db->from('products');
        $this->db->join('products_translations', 'products_translations.for_id = products.id AND products_translations.abbr = \'' . $this->langAbbr . '\'', 'inner');
        $this->db->join('shop_categories_translations', 'shop_categories_translations.for_id = products.shop_categorie AND shop_categories_translations.abbr = \'' . $this->langAbbr . '\'', 'left');
        $this->db->join('vendors', 'vendors.id = products.vendor_id', 'left');
        $this->db->where('products.visibility', 1);

        if ($categoryId !== null && $categoryId !== '' && ctype_digit((string) $categoryId)) {
            $this->db->where('products.shop_categorie', (int) $categoryId);
        }

        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('products_translations.title', $search);
            $this->db->or_like('products_translations.basic_description', $search);
            $this->db->or_like('products_translations.description', $search);
            $this->db->group_end();
        }

        // Count total matching items
        $totalCount = $this->db->count_all_results('', false);

        // Sort order
        if ($sort === 'price_asc') {
            $this->db->order_by('CAST(products_translations.price AS DECIMAL(10,2))', 'ASC');
        } elseif ($sort === 'price_desc') {
            $this->db->order_by('CAST(products_translations.price AS DECIMAL(10,2))', 'DESC');
        } elseif ($sort === 'popular') {
            $this->db->order_by('products.procurement', 'DESC');
        } else {
            $this->db->order_by('products.id', 'DESC');
        }

        $this->db->limit($limit, $offset);
        $this->db->select('products.id, products.image, products.quantity, products.procurement, products.shop_categorie as category_id, products_translations.title, products_translations.price, products_translations.old_price, products_translations.basic_description, shop_categories_translations.name as category_name, vendors.name as vendor_name');
        $query = $this->db->get();
        $rows = $query->result_array();

        $products = array();
        foreach ($rows as $r) {
            $products[] = $this->formatProductSummary($r);
        }

        $this->response(array(
            'status' => true,
            'total' => (int) $totalCount,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => (int) ceil($totalCount / $limit),
            'data' => $products
        ), REST_Controller::HTTP_OK);
    }

    /**
     * 5. GET /api/product/{id}
     * Returns complete single product details including image gallery.
     */
    public function product_get($id = null)
    {
        $id = (int) $id;
        if ($id <= 0) {
            $this->response(array('status' => false, 'message' => 'Invalid product ID'), REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $product = $this->Public_model->getOneProduct($id);
        if (empty($product)) {
            $this->response(array('status' => false, 'message' => 'Product not found'), REST_Controller::HTTP_NOT_FOUND);
            return;
        }

        // Gallery images
        $gallery = array();
        if (!empty($product['folder']) && is_dir('./attachments/shop_images/' . $product['folder'])) {
            $files = scandir('./attachments/shop_images/' . $product['folder']);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && is_file('./attachments/shop_images/' . $product['folder'] . '/' . $file)) {
                    $gallery[] = base_url('attachments/shop_images/' . $product['folder'] . '/' . $file);
                }
            }
        }
        if (empty($gallery) && !empty($product['image'])) {
            $gallery[] = $this->fullImageUrl($product['image']);
        }

        // Related items
        $relatedRaw = $this->Public_model->sameCagegoryProducts(
            $product['shop_categorie'],
            $id,
            (int) $product['vendor_id'] > 0 ? (int) $product['vendor_id'] : false
        );
        $related = array();
        if (is_array($relatedRaw)) {
            foreach ($relatedRaw as $rp) {
                $related[] = $this->formatProductSummary($rp);
            }
        }

        $price = (float) $product['price'];
        $oldPrice = !empty($product['old_price']) ? (float) $product['old_price'] : null;
        $discount = ($oldPrice !== null && $oldPrice > $price) ? (int) round((($oldPrice - $price) / $oldPrice) * 100) : null;

        $data = array(
            'id' => (int) $product['id'],
            'title' => (string) $product['title'],
            'price' => $price,
            'old_price' => $oldPrice,
            'discount_percent' => $discount,
            'currency' => 'PKR',
            'in_stock' => (int) $product['quantity'] > 0,
            'stock_quantity' => (int) $product['quantity'],
            'image_url' => $this->fullImageUrl($product['image']),
            'gallery' => $gallery,
            'basic_description' => isset($product['basic_description']) ? (string) $product['basic_description'] : '',
            'description' => isset($product['description']) ? (string) $product['description'] : '',
            'category_id' => (int) $product['shop_categorie'],
            'category_name' => isset($product['categorie_name']) ? (string) $product['categorie_name'] : '',
            'vendor_id' => (int) $product['vendor_id'],
            'vendor_name' => isset($product['vendor_name']) ? (string) $product['vendor_name'] : 'BUYORA Official Store',
            'related_products' => $related
        );

        $this->response(array('status' => true, 'data' => $data), REST_Controller::HTTP_OK);
    }

    /**
     * 6. POST /api/discount/check
     * Checks if a promo / coupon code is valid and returns the discount amount.
     * Request body: { "code": "SAVE10" }
     */
    public function discountCheck_post()
    {
        $payload = $this->getPayload();
        $code = isset($payload['code']) ? trim((string) $payload['code']) : '';

        if ($code === '') {
            $this->response(array('status' => false, 'message' => 'Please enter a coupon code'), REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $validCode = $this->Public_model->getValidDiscountCode($code);
        if ($validCode == null) {
            $this->response(array('status' => false, 'message' => 'Invalid or expired coupon code'), REST_Controller::HTTP_NOT_FOUND);
            return;
        }

        $this->response(array(
            'status' => true,
            'code' => $validCode['code'],
            'type' => $validCode['type'], // 'percent' or 'fixed'
            'amount' => (float) $validCode['amount'],
            'message' => 'Coupon code applied successfully!'
        ), REST_Controller::HTTP_OK);
    }

    /**
     * 7. POST /api/order/create
     * Core endpoint for buying products! Validates items, calculates totals from database, and places order.
     */
    public function orderCreate_post()
    {
        $payload = $this->getPayload();

        // Validate customer delivery info
        $firstName = isset($payload['first_name']) ? trim((string) $payload['first_name']) : '';
        $lastName = isset($payload['last_name']) ? trim((string) $payload['last_name']) : '';
        $phone = isset($payload['phone']) ? trim((string) $payload['phone']) : '';
        $email = isset($payload['email']) ? trim((string) $payload['email']) : '';
        $address = isset($payload['address']) ? trim((string) $payload['address']) : '';
        $city = isset($payload['city']) ? trim((string) $payload['city']) : '';
        $postCode = isset($payload['post_code']) ? trim((string) $payload['post_code']) : '';
        $notes = isset($payload['notes']) ? trim((string) $payload['notes']) : '';
        $paymentType = isset($payload['payment_type']) ? trim((string) $payload['payment_type']) : 'cashOnDelivery';
        $discountCode = isset($payload['discount_code']) ? trim((string) $payload['discount_code']) : '';
        $userId = isset($payload['user_id']) ? (int) $payload['user_id'] : 0;
        $items = isset($payload['items']) && is_array($payload['items']) ? $payload['items'] : array();

        $errors = array();
        if ($firstName === '') {
            $errors[] = 'First name is required.';
        }
        if ($phone === '') {
            $errors[] = 'Phone number is required.';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email address is required.';
        }
        if ($address === '') {
            $errors[] = 'Delivery address is required.';
        }
        if ($city === '') {
            $errors[] = 'City is required.';
        }
        if (empty($items)) {
            $errors[] = 'Cart items cannot be empty.';
        }

        // Normalize payment type
        $normalizedPayment = 'cashOnDelivery';
        $paymentLower = strtolower($paymentType);
        if ($paymentLower === 'bank' || $paymentLower === 'bankaccount') {
            $normalizedPayment = 'Bank';
        } elseif ($paymentLower === 'safepay' || $paymentLower === 'card') {
            $normalizedPayment = 'Safepay';
        } elseif ($paymentLower === 'cashondelivery' || $paymentLower === 'cod') {
            $normalizedPayment = 'cashOnDelivery';
        } else {
            $errors[] = 'Invalid payment type. Choose cashOnDelivery, bank, or safepay.';
        }

        if (!empty($errors)) {
            $this->response(array('status' => false, 'message' => implode(' ', $errors)), REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Build cart IDs array to feed quote calculation
        $cartIds = array();
        $postedQuantities = array();
        $postedIds = array();
        foreach ($items as $item) {
            $pId = isset($item['product_id']) ? (int) $item['product_id'] : 0;
            $qty = isset($item['quantity']) ? max(1, (int) $item['quantity']) : 1;
            if ($pId <= 0) {
                continue;
            }
            $postedIds[] = $pId;
            $postedQuantities[] = $qty;
            for ($c = 0; $c < $qty; $c++) {
                $cartIds[] = $pId;
            }
        }

        if (empty($cartIds)) {
            $this->response(array('status' => false, 'message' => 'No valid product items provided.'), REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Calculate exact server-side quote & verify availability
        $shippingAmount = $this->Home_admin_model->getValueStore('shippingAmount');
        $shippingThreshold = $this->Home_admin_model->getValueStore('shippingOrder');
        $quote = $this->Ledger_model->quoteFromCart($cartIds, $discountCode, $shippingAmount, $shippingThreshold);

        if (isset($quote['error'])) {
            $this->response(array('status' => false, 'message' => $quote['error']), REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Prepare order array for Public_model->setOrder
        $orderPost = array(
            'id' => $postedIds,
            'quantity' => $postedQuantities,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'city' => $city,
            'post_code' => $postCode,
            'notes' => $notes,
            'payment_type' => $normalizedPayment,
            'referrer' => 'Flutter Mobile App',
            'clean_referrer' => 'mobile_app',
            'user_id' => $userId,
            'discountCode' => $quote['discount_code']
        );

        $saved = $this->Public_model->setOrder($orderPost);
        if ($saved === false) {
            $this->response(array('status' => false, 'message' => 'Could not save the order. Please try again.'), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            return;
        }

        // Save order items ledger & vendor routing
        $this->Ledger_model->saveOrderItems($saved['id'], $saved['order_id'], $quote['items']);
        $this->Public_model->setVendorOrder($orderPost, $saved['order_id'], $quote['items']);

        // Create payment transaction
        $paymentProvider = strtolower($normalizedPayment) === 'bankalfalah' ? 'bankalfalah' : ($normalizedPayment === 'Bank' ? 'bank' : ($normalizedPayment === 'Safepay' ? 'safepay' : 'cod'));
        $payment = $this->Ledger_model->createPayment(
            $saved['id'],
            $saved['order_id'],
            $paymentProvider,
            $quote['payable_minor'],
            $paymentProvider . ':' . $saved['id']
        );

        // If payment is Safepay, generate checkout session / URL for Flutter Webview
        $safepayDetails = null;
        if ($normalizedPayment === 'Safepay' && $this->safepay->isConfigured()) {
            $session = $this->safepay->createSession((int) $quote['payable_minor'], (int) $saved['order_id']);
            $tracker = isset($session['body']['data']['tracker']['token']) ? $session['body']['data']['tracker']['token'] : '';
            $passport = $tracker !== '' ? $this->safepay->createPassport() : array();
            $token = isset($passport['body']['data']) && is_string($passport['body']['data']) ? $passport['body']['data'] : '';

            if ($tracker !== '') {
                $this->Ledger_model->attachTracker((int) $payment['id'], $tracker);
                $publicBase = $this->safepay->publicBaseUrl();
                $returnUrl = $publicBase . '/checkout/safepay/return';
                $cancelUrl = $publicBase . '/checkout/safepay/cancel';
                $safepayDetails = array(
                    'tracker' => $tracker,
                    'auth_token' => $token,
                    'checkout_url' => $this->safepay->checkoutUrl($tracker, $token, $returnUrl, $cancelUrl)
                );
            }
        }

        $totalPayable = (float) Order_quote::formatMinor($quote['payable_minor']);

        $this->response(array(
            'status' => true,
            'message' => 'Order placed successfully!',
            'order_id' => (int) $saved['order_id'],
            'order_row_id' => (int) $saved['id'],
            'subtotal' => (float) Order_quote::formatMinor(isset($quote['merchandise_minor']) ? $quote['merchandise_minor'] : 0),
            'shipping_amount' => (float) Order_quote::formatMinor(isset($quote['shipping_minor']) ? $quote['shipping_minor'] : 0),
            'discount_amount' => (float) Order_quote::formatMinor(isset($quote['discount_minor']) ? $quote['discount_minor'] : 0),
            'total_amount' => $totalPayable,
            'currency' => 'PKR',
            'payment_type' => $normalizedPayment,
            'safepay_details' => $safepayDetails,
            'alfalah_details' => $alfalahDetails
        ), REST_Controller::HTTP_CREATED);
    }

    /**
     * 8. GET /api/order/track/{order_id} or GET /api/order/track?order_id=...
     * Allows customer to check real-time order status, tracking, items, and delivery info.
     */
    public function orderTrack_get($orderId = null)
    {
        if ($orderId === null) {
            $orderId = $this->input->get('order_id');
        }
        $orderId = (int) $orderId;

        if ($orderId <= 0) {
            $this->response(array('status' => false, 'message' => 'Please provide a valid Order ID'), REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $this->db->where('orders.order_id', $orderId);
        $this->db->select('orders.*, orders_clients.first_name, orders_clients.last_name, orders_clients.email, orders_clients.phone, orders_clients.address, orders_clients.city, orders_clients.post_code');
        $this->db->join('orders_clients', 'orders_clients.for_id = orders.id', 'left');
        $order = $this->db->get('orders')->row_array();

        if (empty($order)) {
            $this->response(array('status' => false, 'message' => 'No order found with ID #' . $orderId), REST_Controller::HTTP_NOT_FOUND);
            return;
        }

        // Decrypt customer info
        $decrypt = function ($val) {
            if (empty($val)) return '';
            $d = $this->encryption->decrypt($val);
            return $d !== false ? $d : $val;
        };

        $clientName = trim($decrypt($order['first_name']) . ' ' . $decrypt($order['last_name']));
        $clientPhone = $decrypt($order['phone']);
        $clientCity = $decrypt($order['city']);
        $clientAddress = $decrypt($order['address']);

        // Determine human-readable status
        $statusKey = 'pending';
        $statusTitle = 'Order Received';
        $statusDescription = 'Your order has been received and is awaiting merchant confirmation.';

        if ((int) $order['confirmed'] === 1) {
            $statusKey = 'delivered';
            $statusTitle = 'Delivered & Confirmed';
            $statusDescription = 'The package was successfully delivered and confirmed.';
        } elseif ((int) $order['processed'] === 1) {
            $statusKey = 'processed';
            $statusTitle = 'Dispatched / In Transit';
            $statusDescription = 'Your parcel is packed and on its way with the courier service.';
        }

        // Decode products
        $decodedProducts = @unserialize($order['products']);
        $items = array();
        $totalAmount = 0.0;

        if (is_array($decodedProducts)) {
            foreach ($decodedProducts as $item) {
                if (isset($item['product_info']) && is_array($item['product_info'])) {
                    $pInfo = $item['product_info'];
                    $qty = (int) $item['product_quantity'];
                    $unitPrice = (float) $pInfo['price'];
                    $lineTotal = $unitPrice * $qty;
                    $totalAmount += $lineTotal;

                    $items[] = array(
                        'product_id' => (int) $pInfo['id'],
                        'title' => $this->resolveProductTitle($pInfo),
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'total_price' => $lineTotal,
                        'image_url' => $this->fullImageUrl(isset($pInfo['image']) ? $pInfo['image'] : '')
                    );
                }
            }
        }

        $data = array(
            'order_id' => (int) $order['order_id'],
            'date' => date('Y-m-d H:i:s', $order['date']),
            'status' => array(
                'code' => $statusKey,
                'title' => $statusTitle,
                'description' => $statusDescription
            ),
            'payment' => array(
                'type' => $order['payment_type'],
                'paypal_status' => $order['paypal_status']
            ),
            'customer' => array(
                'name' => $clientName,
                'phone' => $clientPhone,
                'city' => $clientCity,
                'address' => $clientAddress
            ),
            'items' => $items,
            'total_amount' => $totalAmount,
            'currency' => 'PKR'
        );

        $this->response(array('status' => true, 'data' => $data), REST_Controller::HTTP_OK);
    }

    /**
     * 9. POST /api/customer/register
     * Customer registration from Flutter app.
     * Request body: { "name": "...", "email": "...", "password": "...", "phone": "..." }
     */
    public function register_post()
    {
        $payload = $this->getPayload();
        $name = isset($payload['name']) ? trim((string) $payload['name']) : '';
        $email = isset($payload['email']) ? trim((string) $payload['email']) : '';
        $password = isset($payload['password']) ? trim((string) $payload['password']) : '';
        $phone = isset($payload['phone']) ? trim((string) $payload['phone']) : '';

        $errors = array();
        if ($name === '') {
            $errors[] = 'Please enter your name.';
        }
        if ($phone === '') {
            $errors[] = 'Please enter your phone number.';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }
        if (strlen($password) < 4) {
            $errors[] = 'Password must be at least 4 characters long.';
        }

        if (empty($errors)) {
            $exists = $this->Public_model->countPublicUsersWithEmail($email, 0);
            if ($exists > 0) {
                $errors[] = 'An account with this email address already exists.';
            }
        }

        if (!empty($errors)) {
            $this->response(array('status' => false, 'message' => implode(' ', $errors)), REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $newUserId = $this->Public_model->registerUser(array(
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'pass' => $password
        ));

        if (!$newUserId) {
            $this->response(array('status' => false, 'message' => 'Failed to create account. Try again.'), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            return;
        }

        $this->response(array(
            'status' => true,
            'message' => 'Account created successfully!',
            'user' => array(
                'id' => (int) $newUserId,
                'name' => $name,
                'email' => $email,
                'phone' => $phone
            )
        ), REST_Controller::HTTP_CREATED);
    }

    /**
     * 10. POST /api/customer/login
     * Customer login from Flutter app.
     * Request body: { "email": "...", "password": "..." }
     */
    public function login_post()
    {
        $payload = $this->getPayload();
        $email = isset($payload['email']) ? trim((string) $payload['email']) : '';
        $password = isset($payload['password']) ? trim((string) $payload['password']) : '';

        if ($email === '' || $password === '') {
            $this->response(array('status' => false, 'message' => 'Email and password are required.'), REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $userId = $this->Public_model->checkPublicUserIsValid(array(
            'email' => $email,
            'pass' => $password
        ));

        if ($userId === false) {
            $this->response(array('status' => false, 'message' => 'Invalid email or password.'), REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }

        $userProfile = $this->Public_model->getUserProfileInfo($userId);

        $this->response(array(
            'status' => true,
            'message' => 'Login successful',
            'user' => array(
                'id' => (int) $userId,
                'name' => isset($userProfile['name']) ? $userProfile['name'] : '',
                'email' => isset($userProfile['email']) ? $userProfile['email'] : $email,
                'phone' => isset($userProfile['phone']) ? $userProfile['phone'] : ''
            )
        ), REST_Controller::HTTP_OK);
    }

    /**
     * 11. GET /api/customer/orders/{user_id}
     * Returns previous orders for a logged-in customer.
     */
    public function orders_get($userId = null)
    {
        $userId = (int) $userId;
        if ($userId <= 0) {
            $this->response(array('status' => false, 'message' => 'Invalid user ID'), REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $ordersRaw = $this->Public_model->getUserOrdersHistory($userId, 50, 0);
        $orders = array();

        foreach ($ordersRaw as $o) {
            $decodedProducts = @unserialize($o['products']);
            $items = array();
            $orderTotal = 0.0;

            if (is_array($decodedProducts)) {
                foreach ($decodedProducts as $item) {
                    if (isset($item['product_info']) && is_array($item['product_info'])) {
                        $pInfo = $item['product_info'];
                        $qty = (int) $item['product_quantity'];
                        $unitPrice = (float) $pInfo['price'];
                        $orderTotal += ($unitPrice * $qty);

                        $items[] = array(
                            'product_id' => (int) $pInfo['id'],
                            'title' => $this->resolveProductTitle($pInfo),
                            'quantity' => $qty,
                            'unit_price' => $unitPrice,
                            'image_url' => $this->fullImageUrl(isset($pInfo['image']) ? $pInfo['image'] : '')
                        );
                    }
                }
            }

            $orders[] = array(
                'order_id' => (int) $o['order_id'],
                'date' => date('Y-m-d H:i:s', $o['date']),
                'confirmed' => (int) $o['confirmed'] === 1,
                'processed' => (int) $o['processed'] === 1,
                'payment_type' => $o['payment_type'],
                'total_amount' => $orderTotal,
                'currency' => 'PKR',
                'items_count' => count($items),
                'items' => $items
            );
        }

        $this->response(array('status' => true, 'count' => count($orders), 'data' => $orders), REST_Controller::HTTP_OK);
    }

    /**
     * 12. POST /api/customer/forgot-password
     * Request 6-digit verification code to reset password.
     * Request body: { "email": "user@example.com" }
     */
    public function forgotPassword_post()
    {
        $payload = $this->getPayload();
        $email = isset($payload['email']) ? trim((string) $payload['email']) : '';

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->response(array('status' => false, 'message' => 'Please provide a valid email address.'), REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $user = $this->db->where('email', $email)->get('users_public')->row_array();
        if (!$user) {
            $this->response(array('status' => false, 'message' => 'No account found with this email address.'), REST_Controller::HTTP_NOT_FOUND);
            return;
        }

        $this->db->query("CREATE TABLE IF NOT EXISTS `password_resets` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `email` VARCHAR(255) NOT NULL,
            `code` VARCHAR(10) NOT NULL,
            `created_at` INT NOT NULL,
            INDEX (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $code = (string) mt_rand(100000, 999999);

        // Delete any existing codes for this email
        $this->db->where('email', $email)->delete('password_resets');

        // Store new code with timestamp
        $this->db->insert('password_resets', array(
            'email' => $email,
            'code' => $code,
            'created_at' => time()
        ));

        $customerName = !empty($user['name']) ? $user['name'] : 'Customer';
        $subject = 'BUYORA Marketplace - Password Reset Code: ' . $code;
        $html = '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 24px; border: 1px solid #E2E8F0; border-radius: 8px;">';
        $html .= '<h2 style="color: #0F172A; margin-top: 0;">BUYORA Marketplace</h2>';
        $html .= '<p>Hello ' . htmlspecialchars($customerName) . ',</p>';
        $html .= '<p>We received a request to reset your BUYORA account password. Use the verification code below to complete the reset process:</p>';
        $html .= '<div style="background: #F1F5F9; padding: 18px; text-align: center; border-radius: 6px; margin: 24px 0;">';
        $html .= '<span style="font-size: 32px; font-weight: bold; letter-spacing: 6px; color: #FF6B00;">' . $code . '</span>';
        $html .= '</div>';
        $html .= '<p style="font-size: 13px; color: #64748B;">This code will expire in 15 minutes. If you did not request a password reset, please ignore this email or contact support.</p>';
        $html .= '<p style="margin-top: 30px; font-size: 12px; color: #94A3B8; border-top: 1px solid #E2E8F0; padding-top: 12px;">BUYORA Marketplace • Shop More. Live Better.</p>';
        $html .= '</div>';

        $sent = $this->sendSmtpEmail($email, $subject, $html);

        $this->response(array(
            'status' => true,
            'message' => 'Password reset verification code has been sent to ' . $email . '.',
            'email_sent' => $sent
        ), REST_Controller::HTTP_OK);
    }

    /**
     * 13. POST /api/customer/reset-password
     * Verify 6-digit code and set new password.
     * Request body: { "email": "...", "code": "123456", "new_password": "..." }
     */
    public function resetPassword_post()
    {
        $payload = $this->getPayload();
        $email = isset($payload['email']) ? trim((string) $payload['email']) : '';
        $code = isset($payload['code']) ? trim((string) $payload['code']) : '';
        $newPassword = isset($payload['new_password']) ? trim((string) $payload['new_password']) : '';

        if ($email === '' || $code === '' || $newPassword === '') {
            $this->response(array('status' => false, 'message' => 'Email, verification code, and new password are required.'), REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        if (strlen($newPassword) < 6) {
            $this->response(array('status' => false, 'message' => 'New password must be at least 6 characters long.'), REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $reset = $this->db->where('email', $email)
            ->where('code', $code)
            ->order_by('id', 'DESC')
            ->get('password_resets')
            ->row_array();

        if (!$reset) {
            $this->response(array('status' => false, 'message' => 'Invalid verification code.'), REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Check expiration (15 minutes = 900 seconds)
        if ((time() - (int) $reset['created_at']) > 900) {
            $this->response(array('status' => false, 'message' => 'Verification code has expired. Please request a new code.'), REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Update user password in users_public
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->db->where('email', $email)->update('users_public', array('password' => $hashed));

        // Delete used reset code
        $this->db->where('email', $email)->delete('password_resets');

        $this->response(array(
            'status' => true,
            'message' => 'Password has been updated successfully! You can now sign in with your new password.'
        ), REST_Controller::HTTP_OK);
    }

    /**
     * 14. POST /api/customer/delete-account
     * In-app Google Play compliant account deletion.
     * Request body: { "user_id": 123, "password": "...", "reason": "..." }
     */
    public function deleteAccount_post()
    {
        $payload = $this->getPayload();
        $userId = isset($payload['user_id']) ? (int) $payload['user_id'] : 0;
        $password = isset($payload['password']) ? trim((string) $payload['password']) : '';
        $reason = isset($payload['reason']) ? trim((string) $payload['reason']) : '';

        if ($userId <= 0) {
            $this->response(array('status' => false, 'message' => 'User ID is required.'), REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $user = $this->db->where('id', $userId)->get('users_public')->row_array();
        if (!$user) {
            $this->response(array('status' => false, 'message' => 'User not found.'), REST_Controller::HTTP_NOT_FOUND);
            return;
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            $this->response(array('status' => false, 'message' => 'Incorrect password. Account deletion cancelled.'), REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }

        $this->db->query("CREATE TABLE IF NOT EXISTS `account_deletions` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NULL,
            `email` VARCHAR(255) NOT NULL,
            `reason` TEXT NULL,
            `status` VARCHAR(50) DEFAULT 'requested',
            `created_at` INT NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        // Record deletion log
        $this->db->insert('account_deletions', array(
            'user_id' => $userId,
            'email' => $user['email'],
            'reason' => $reason,
            'status' => 'completed',
            'created_at' => time()
        ));

        // Anonymize user personal data in users_public
        $anonymizedEmail = 'deleted_' . $userId . '_' . time() . '@anonymized.buyoramarketplace.com';
        $this->db->where('id', $userId)->update('users_public', array(
            'name' => 'Deactivated Customer',
            'phone' => '',
            'email' => $anonymizedEmail,
            'password' => ''
        ));

        $this->response(array(
            'status' => true,
            'message' => 'Your BUYORA account and personal details have been permanently deleted.'
        ), REST_Controller::HTTP_OK);
    }

    /**
     * Send email using Gmail SMTP credentials
     */
    private function sendSmtpEmail($to, $subject, $htmlMessage)
    {
        $this->load->library('email');

        $smtpHost = getenv('MAIL_HOST') ?: 'smtp.gmail.com';
        if (strpos($smtpHost, 'ssl://') === false && (getenv('MAIL_ENCRYPTION') === 'ssl' || getenv('MAIL_PORT') == '465')) {
            $smtpHost = 'ssl://' . $smtpHost;
        }

        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => $smtpHost,
            'smtp_port' => (int) (getenv('MAIL_PORT') ?: 465),
            'smtp_user' => getenv('MAIL_USERNAME') ?: 'rashidkg01@gmail.com',
            'smtp_pass' => getenv('MAIL_PASSWORD') ?: 'xzmjwtenzasmkgzv',
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'newline' => "\r\n",
            'crlf' => "\r\n",
            'wordwrap' => TRUE
        );

        $this->email->initialize($config);
        $fromEmail = getenv('MAIL_FROM_ADDRESS') ?: 'rashidkg01@gmail.com';
        $fromName = getenv('MAIL_FROM_NAME') ?: 'BUYORA Marketplace';

        $this->email->from($fromEmail, $fromName);
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($htmlMessage);

        return @$this->email->send();
    }

    /**
     * Helper to format a product for Flutter listing
     */
    private function formatProductSummary(array $p)
    {
        $price = (float) $p['price'];
        $oldPrice = !empty($p['old_price']) ? (float) $p['old_price'] : null;
        $discount = ($oldPrice !== null && $oldPrice > $price) ? (int) round((($oldPrice - $price) / $oldPrice) * 100) : null;

        return array(
            'id' => (int) $p['id'],
            'title' => (string) $p['title'],
            'price' => $price,
            'old_price' => $oldPrice,
            'discount_percent' => $discount,
            'currency' => 'PKR',
            'in_stock' => isset($p['quantity']) ? (int) $p['quantity'] > 0 : true,
            'quantity' => isset($p['quantity']) ? (int) $p['quantity'] : 0,
            'image_url' => $this->fullImageUrl(isset($p['image']) ? $p['image'] : ''),
            'category_id' => isset($p['category_id']) ? (int) $p['category_id'] : (isset($p['shop_categorie']) ? (int) $p['shop_categorie'] : null),
            'category_name' => isset($p['category_name']) ? (string) $p['category_name'] : null,
            'vendor_name' => isset($p['vendor_name']) ? (string) $p['vendor_name'] : 'BUYORA'
        );
    }

    /**
     * Resolve product title safely
     */
     private function resolveProductTitle($pInfo)
     {
         if (is_array($pInfo) && isset($pInfo['title']) && !empty($pInfo['title'])) {
             return (string) $pInfo['title'];
         }
         $productId = is_array($pInfo) && isset($pInfo['id']) ? (int) $pInfo['id'] : 0;
         if ($productId > 0) {
             $trans = $this->db->select('title')
                 ->where('for_id', $productId)
                 ->where('abbr', MY_DEFAULT_LANGUAGE_ABBR)
                 ->get('products_translations')
                 ->row_array();
             if ($trans && !empty($trans['title'])) {
                 return (string) $trans['title'];
             }
         }
         return 'Product #' . $productId;
     }

    /**
     * 15. GET/POST /api/version/check
     * Remote App Version Control Check
     */
    public function versionCheck_get()
    {
        $this->processVersionCheck();
    }

    public function versionCheck_post()
    {
        $this->processVersionCheck();
    }

    private function processVersionCheck()
    {
        $payload = $this->getPayload();
        $clientVersion = isset($payload['version']) ? trim($payload['version']) : ($this->input->get_post('version') ?: '');
        $platform = isset($payload['platform']) ? strtolower(trim($payload['platform'])) : strtolower($this->input->get_post('platform') ?: 'android');

        $latestVersion = $this->Home_admin_model->getValueStore('app_latest_version') ?: '1.0.0';
        $minSupportedVersion = $this->Home_admin_model->getValueStore('app_min_supported_version') ?: '1.0.0';
        $forceUpdate = ($this->Home_admin_model->getValueStore('app_force_update') == '1');
        $updateMessage = $this->Home_admin_model->getValueStore('app_update_message') ?: 
            'A new version of Buyora is available with performance improvements, bug fixes, and enhanced shopping features. Please update now to continue.';
        $playStoreUrl = $this->Home_admin_model->getValueStore('app_play_store_url') ?: 
            'https://play.google.com/store/apps/details?id=com.buyora.marketplace.buyora';

        // Version evaluation
        $isBelowMin = false;
        $isBelowLatest = false;
        if (!empty($clientVersion)) {
            $normalizedClient = $this->normalizeVersion($clientVersion);
            $normalizedMin = $this->normalizeVersion($minSupportedVersion);
            $normalizedLatest = $this->normalizeVersion($latestVersion);

            $isBelowMin = version_compare($normalizedClient, $normalizedMin, '<');
            $isBelowLatest = version_compare($normalizedClient, $normalizedLatest, '<');
        }

        // Force update applies if version is strictly below minimum supported, OR if global forceUpdate flag is set while below latest
        $isUpdateRequired = $isBelowMin || ($forceUpdate && $isBelowLatest);
        $isUpdateAvailable = $isBelowLatest;

        $response = array(
            'status' => true,
            'data' => array(
                'client_version' => $clientVersion,
                'platform' => $platform,
                'latest_version' => $latestVersion,
                'min_supported_version' => $minSupportedVersion,
                'force_update' => $forceUpdate,
                'update_message' => $updateMessage,
                'play_store_url' => $playStoreUrl,
                'is_update_available' => $isUpdateAvailable,
                'is_update_required' => $isUpdateRequired
            )
        );

        $this->response($response, REST_Controller::HTTP_OK);
    }

    /**
     * Clean version string (e.g. "1.0.0+1" -> "1.0.0.1" for standard PHP version_compare)
     */
    private function normalizeVersion($version)
    {
        $clean = preg_replace('/[^0-9\.]/', '.', str_replace('+', '.', trim($version)));
        $clean = preg_replace('/\.+/', '.', $clean);
        return trim($clean, '.');
    }
}
