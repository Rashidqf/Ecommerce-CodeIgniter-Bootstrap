<?php

class MY_Controller extends MX_Controller
{

    public $nonDynPages = array();
    private $dynPages = array();
    protected $template;

    public function __construct()
    {
        parent::__construct();
        $this->getActivePages();
        $this->checkForPostRequests();
        $this->setReferrer();
        //set selected template
        $this->loadTemplate();
    }

    /*
     * Render page from controller
     * it loads header and footer auto
     */

    public function render($view, $head, $data = null, $footer = null)
    {
        $head['cartItems'] = $this->shoppingcart->getCartItems();
        $head['sumOfItems'] = $this->shoppingcart->sumValues;
        $vars = $this->loadVars();
        $vars['cartItems'] = $head['cartItems'];
        $vars['sumOfItems'] = $head['sumOfItems'];
        $this->load->vars($vars);
        $all_categories = $this->Public_model->getShopCategories();

        function buildTree1(array $elements, $parentId = 0)
        {
            $branch = array();
            foreach ($elements as $element) {
                if ($element['sub_for'] == $parentId) {
                    $children = buildTree1($elements, $element['id']);
                    if ($children) {
                        $element['children'] = $children;
                    }
                    $branch[] = $element;
                }
            }
            return $branch;
        }

        $head['nav_categories'] = $tree = buildTree1($all_categories);
        $this->load->vars(array(
            'buyora_category_tree' => $tree,
            'buyora_header_categories' => $this->categoriesForPins(
                $all_categories,
                $this->pinnedCategoryIds(isset($vars['pin_header_categories']) ? $vars['pin_header_categories'] : null),
                6
            ),
            'buyora_footer_categories' => $this->categoriesForPins(
                $all_categories,
                $this->pinnedCategoryIds(isset($vars['pin_footer_categories']) ? $vars['pin_footer_categories'] : null),
                6
            )
        ));
        $buyoraViews = array(
            'buyora-home',
            'buyora-product',
            'buyora-cart',
            'buyora-checkout',
            'buyora-shop',
            'buyora-vendor',
            'login',
            'signup',
            'user',
            'contacts',
            'blog',
            'view_blog_post',
            'dynPage',
            'confirmed',
            'checkout_parts/order_error',
            'checkout_parts/payment_success_cash',
            'checkout_parts/payment_success_bank',
            'checkout_parts/paypal_payment',
            'checkout_parts/paypal_success',
            'checkout_parts/paypal_cancel',
            'checkout_parts/safepay_success',
            'checkout_parts/safepay_pending',
            'checkout_parts/safepay_cancel'
        );
        if (in_array($view, $buyoraViews, true)) {
            $head['buyora_categories'] = $all_categories;
            $this->load->view('templates/buyora/_parts/header', $head);
            $this->load->view('templates/buyora/' . $view, $data);
            $this->load->view('templates/buyora/_parts/footer', $footer);
            return;
        }

        $this->load->view($this->template . '_parts/header', $head);
        $this->load->view($this->template . $view, $data);
        $this->load->view($this->template . '_parts/footer', $footer);
    }

    private function pinnedCategoryIds($raw)
    {
        if ($raw === null) {
            return null;
        }
        $ids = array();
        foreach (explode(',', (string) $raw) as $part) {
            if (ctype_digit($part) && (int) $part > 0) {
                $ids[] = (int) $part;
            }
        }
        return $ids;
    }

    private function categoriesForPins($categories, $ids, $fallbackLimit)
    {
        if ($ids === null) {
            $picked = array();
            foreach ($categories as $category) {
                if ((int) $category['sub_for'] !== 0) {
                    continue;
                }
                $picked[] = $category;
                if (count($picked) >= $fallbackLimit) {
                    break;
                }
            }
            return $picked;
        }

        $byId = array();
        foreach ($categories as $category) {
            $byId[(int) $category['id']] = $category;
        }
        $picked = array();
        foreach ($ids as $id) {
            if (isset($byId[$id])) {
                $picked[] = $byId[$id];
            }
        }
        return $picked;
    }

    /*
     * Load variables from values-store
     * texts, social media links, logos, etc.
     */

    private function loadVars()
    {
        $vars = array();
        $vars['nonDynPages'] = $this->nonDynPages;
        $vars['dynPages'] = $this->dynPages;
        $vars['footerCategories'] = $this->Public_model->getFooterCategories();

        $this->load->model('admin/Settings_model');
        $values = $this->Settings_model->getValueStores();
        if(is_array($values) && count($values) > 0) {
            foreach($values as $value) {
                if (!array_key_exists($value['thekey'], $vars)) {
                    $vars[$value['thekey']] = htmlentities($value['value']);
                }
            }
        }
        
        if (empty($vars['footerContactPhone'])) {
            $vars['footerContactPhone'] = '03088503190';
        }
        if (empty($vars['footerContactEmail'])) {
            $vars['footerContactEmail'] = 'alitrader97@gmail.com';
        }

        $vars['allLanguages'] = $this->getAllLangs();
        $vars['load'] = $this->loop;
        $vars['cookieLaw'] = $this->Public_model->getCookieLaw();
        return $vars;
    }

    /*
     * Get all added languages from administration
     */

    private function getAllLangs()
    {
        $arr = array();
        $this->load->model('admin/Languages_model');
        $langs = $this->Languages_model->getLanguages();
        foreach ($langs as $lang) {
            $arr[$lang->abbr]['name'] = $lang->name;
            $arr[$lang->abbr]['flag'] = $lang->flag;
        }
        return $arr;
    }

    /*
     * Active pages for navigation
     * Managed from administration
     */

    private function getActivePages()
    {
        $this->load->model('admin/Pages_model');
        $activeP = $this->Pages_model->getPages(true);
        $dynPages = $this->config->item('no_dynamic_pages');
        $actDynPages = [];
        foreach ($activeP as $acp) {
            if (($key = array_search($acp, $dynPages)) !== false) {
                $actDynPages[] = $acp;
            }
        }
        $this->nonDynPages = $actDynPages;
        $dynPages = getTextualPages($activeP);
        $this->dynPages = $this->Public_model->getDynPagesLangs($dynPages);
    }

    /*
     * Email subscribe form from footer
     */

    private function checkForPostRequests()
    {
        if (isset($_POST['subscribeEmail'])) {
            $arr = array();
            $arr['browser'] = substr(strip_tags($_SERVER['HTTP_USER_AGENT']), 0, 500);
            $arr['ip'] = $_SERVER['REMOTE_ADDR'];
            $arr['time'] = time();
            $arr['email'] = $_POST['subscribeEmail'];
            if (filter_var($arr['email'], FILTER_VALIDATE_EMAIL) && !$this->session->userdata('email_added')) {
                $this->session->set_userdata('email_added', 1);
                $this->Public_model->setSubscribe($arr);
                $this->session->set_flashdata('emailAdded', lang('email_added'));
            }
            if (!headers_sent()) {
                redirect();
            } else {
                echo 'window.location = "' . base_url() . '"';
            }
        }
    }

    /*
     * Set referrer to save it in orders
     */

    private function setReferrer()
    {
        if ($this->session->userdata('referrer') == null) {
            if (!isset($_SERVER['HTTP_REFERER'])) {
                $ref = 'Direct';
            } else {
                $raw = $_SERVER['HTTP_REFERER'];
                $parsed = parse_url($raw);
                $scheme = isset($parsed['scheme']) ? strtolower($parsed['scheme']) : '';
                if (in_array($scheme, ['http', 'https'], true) && strlen($raw) <= 500) {
                    $ref = $raw;
                } else {
                    $ref = 'Direct';
                }
            }
            $this->session->set_userdata('referrer', $ref);
        }
    }

    /*
     * Check for selected template 
     * and set it in config if exists
     */

    private function loadTemplate()
    {
        $template = $this->Home_admin_model->getValueStore('template');
        if ($template == null) {
            $template = $this->config->item('template');
        } else {
            $this->config->set_item('template', $template);
        }
        if (!is_dir(TEMPLATES_DIR . $template)) {
            show_error('The selected template does not exists!');
        }
        $this->template = 'templates' . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR;
    }

}
