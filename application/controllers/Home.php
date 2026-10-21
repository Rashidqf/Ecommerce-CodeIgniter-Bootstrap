<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller
{

    private $num_rows = 20;

    public function __construct()
    {
        parent::__construct();
        $this->load->Model('admin/Brands_model');
    }

    public function index($page = 0)
    {
        $data = array();
        $head = array();
        $arrSeo = $this->Public_model->getSeo('home');
        $head['title'] = !empty($arrSeo['title']) ? $arrSeo['title'] : 'BUYORA | Shop More, Live Better — Online Marketplace';
        $head['description'] = !empty($arrSeo['description']) ? $arrSeo['description'] : 'Shop top electronics, fashion, beauty, home goods & more on BUYORA. Enjoy verified sellers, secure card payments, and fast delivery.';
        $head['keywords'] = !empty($head['title']) ? str_replace(" ", ",", $head['title']) : 'ecommerce,online shopping,marketplace,buyora';
        $all_categories = $this->Public_model->getShopCategories();
        $head['buyora_categories'] = $all_categories;

        // Dynamic category SEO title and canonical URL
        if (isset($_GET['category']) && is_numeric($_GET['category'])) {
            $catId = (int) $_GET['category'];
            foreach ($all_categories as $cat) {
                if ((int) $cat['id'] === $catId) {
                    $catName = !empty($cat['name']) ? $cat['name'] : 'Category ' . $catId;
                    $head['title'] = $catName . ' | BUYORA Marketplace';
                    $head['description'] = 'Shop the best ' . $catName . ' deals on BUYORA. Verified sellers, secure checkout, and fast shipping.';
                    $head['canonical_url'] = base_url('category/' . $catId . '/' . url_title($catName, 'dash', true));
                    break;
                }
            }
        }
        if (empty($head['canonical_url'])) {
            $head['canonical_url'] = LANG_URL;
        }
        $data['home_categories'] = $this->getHomeCategories($all_categories);
        $data['all_categories'] = $all_categories;
        $data['countQuantities'] = $this->Public_model->getCountQuantities();
        $data['bestSellers'] = $this->Public_model->getbestSellers();
        $data['newProducts'] = $this->Public_model->getNewProducts();
        $data['sliderProducts'] = $this->Public_model->getSliderProducts(6);
        $data['dealProducts'] = $this->Public_model->getHomeDealProducts();
        $this->load->model('admin/Home_banners_model');
        $data['homeBanners'] = $this->Home_banners_model->getVisibleBanners();
        $data['lastBlogs'] = $this->Public_model->getLastBlogs();
        $catalogFilters = array('search_in_title', 'search_in_body', 'category', 'brand_id', 'in_stock', 'price_from', 'price_to', 'quantity_more', 'added_after', 'added_before', 'order_new', 'order_price', 'order_procurement');
        $includeFeatured = false;
        foreach ($catalogFilters as $filterName) {
            if (isset($_GET[$filterName]) && is_scalar($_GET[$filterName]) && trim((string) $_GET[$filterName]) !== '') {
                $includeFeatured = true;
                break;
            }
        }
        $data['products'] = $this->Public_model->getProducts($this->num_rows, $page, $_GET, false, $includeFeatured);
        $rowscount = $this->Public_model->productsCount($_GET, $includeFeatured);
        $data['shippingOrder'] = $this->Home_admin_model->getValueStore('shippingOrder');
        $data['showOutOfStock'] = $this->Home_admin_model->getValueStore('outOfStock');
        $data['showBrands'] = $this->Home_admin_model->getValueStore('showBrands');
        $data['brands'] = $this->Brands_model->getBrands();
        $data['links_pagination'] = pagination('home', $rowscount, $this->num_rows);
        $this->render('buyora-home', $head, $data);
    }

    /*
     * Used from greenlabel template
     * shop page
     */

    public function shop($page = 0)
    {
        $data = array();
        $head = array();
        $arrSeo = $this->Public_model->getSeo('shop');
        $head['title'] = @$arrSeo['title'];
        $head['description'] = @$arrSeo['description'];
        $head['keywords'] = $head['title'] !== null ? str_replace(" ", ",", $head['title']) : '';
        $head['vendor_url'] = LANG_URL . '/shop';
        $all_categories = $this->Public_model->getShopCategories();
        $data['home_categories'] = $this->getHomeCategories($all_categories);
        $data['countQuantities'] = $this->Public_model->getCountQuantities();
        $data['all_categories'] = $all_categories;
        $data['showBrands'] = $this->Home_admin_model->getValueStore('showBrands');
        $data['brands'] = $this->Brands_model->getBrands();
        $data['showOutOfStock'] = $this->Home_admin_model->getValueStore('outOfStock');
        $data['shippingOrder'] = $this->Home_admin_model->getValueStore('shippingOrder');
        $data['products'] = $this->Public_model->getProducts($this->num_rows, $page, $_GET, false, true);
        $rowscount = $this->Public_model->productsCount($_GET, true);
        $data['links_pagination'] = pagination('shop', $rowscount, $this->num_rows);
        $this->render('buyora-shop', $head, $data);
    }

    private function getHomeCategories($categories)
    {

        /*
         * Tree Builder for categories menu
         */

        function buildTree(array $elements, $parentId = 0)
        {
            $branch = array();
            foreach ($elements as $element) {
                if ($element['sub_for'] == $parentId) {
                    $children = buildTree($elements, $element['id']);
                    if ($children) {
                        $element['children'] = $children;
                    }
                    $branch[] = $element;
                }
            }
            return $branch;
        }

        return buildTree($categories);
    }

    /*
     * Called to add/remove quantity from cart
     * If is ajax request send POST'S to class ShoppingCart
     */

    public function manageShoppingCart()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        $this->shoppingcart->manageShoppingCart();
    }

    /*
     * Called to remove product from cart
     * If is ajax request and send $_GET variable to the class
     */

    public function removeFromCart()
    {
        $backTo = trim((string) $this->input->get('back-to', true), '/');
        if (
            $backTo === ''
            || strpos($backTo, '..') !== false
            || preg_match('#^(?:https?:)?//#i', $backTo)
            || !preg_match('/^[a-zA-Z0-9_\/-]+$/', $backTo)
        ) {
            $backTo = 'checkout';
        }
        $this->shoppingcart->removeFromCart();
        $this->session->set_flashdata('deleted', lang('deleted_product_from_cart'));
        redirect(LANG_URL . '/' . $backTo);
    }

    public function clearShoppingCart()
    {
        $this->shoppingcart->clearShoppingCart();
    }

    public function viewProduct($id)
    {
        $data = array();
        $head = array();
        $data['product'] = $this->Public_model->getOneProduct($id);
        if (empty($data['product'])) {
            show_404();
        }
        $data['sameCagegoryProducts'] = $this->Public_model->sameCagegoryProducts(
            $data['product']['shop_categorie'],
            $id,
            (int) $data['product']['vendor_id'] > 0 ? (int) $data['product']['vendor_id'] : false
        );
        $data['storeProducts'] = (int) $data['product']['vendor_id'] > 0
            ? $this->Public_model->getProducts(8, 0, array(), (int) $data['product']['vendor_id'], true)
            : array();
        $vars['publicDateAdded'] = $this->Home_admin_model->getValueStore('publicDateAdded');
        $this->load->vars($vars);
        $head['title'] = $data['product']['title'];
        $description = url_title(character_limiter(strip_tags($data['product']['description']), 130));
        $description = str_replace("-", " ", $description) . '..';
        $head['description'] = $description;
        $head['keywords'] = str_replace(" ", ",", $data['product']['title']);
        $head['image'] = null;
        if(isset($data['product']['image'])) {
            $head['image'] = base_url('/attachments/shop_images/' . $data['product']['image']);
        }
        $head['canonical_url'] = !empty($data['product']['vendor_url'])
            ? base_url($data['product']['vendor_url'] . '/' . $data['product']['url'])
            : base_url($data['product']['url']);
        $head['og_type'] = 'product';
        $this->render('buyora-product', $head, $data);
    }

    public function confirmLink($md5)
    {
        if (preg_match('/^[a-f0-9]{32}$/', $md5)) {
            $result = $this->Public_model->confirmOrder($md5);
            if ($result === true) {
                $data = array();
                $head = array();
                $head['title'] = '';
                $head['description'] = '';
                $head['keywords'] = '';
                $this->render('confirmed', $head, $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    public function discountCodeChecker()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        $result = $this->Public_model->getValidDiscountCode($_POST['enteredCode']);
        if ($result == null) {
            echo 0;
        } else {
            echo json_encode($result);
        }
    }

    public function category($id, $slug = '', $page = 0)
    {
        $_GET['category'] = (int) $id;
        $this->index((int) $page);
    }

    public function sitemap()
    {
        $this->sitemapIndex();
    }

    public function sitemapIndex()
    {
        header("Content-Type: text/xml; charset=utf-8");
        $today = date('c');

        $sitemaps = array(
            'sitemap-products.xml',
            'sitemap-categories.xml',
            'sitemap-stores.xml',
            'sitemap-pages.xml',
            'sitemap-blog.xml',
        );

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($sitemaps as $sm) {
            $xml .= "  <sitemap>\n";
            $xml .= "    <loc>" . htmlspecialchars(base_url($sm), ENT_XML1, 'UTF-8') . "</loc>\n";
            $xml .= "    <lastmod>" . $today . "</lastmod>\n";
            $xml .= "  </sitemap>\n";
        }
        $xml .= '</sitemapindex>';
        echo $xml;
    }

    public function sitemapProducts()
    {
        header("Content-Type: text/xml; charset=utf-8");
        $products = $this->Public_model->sitemapProducts();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $xml .= '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        $today = date('Y-m-d');
        if ($products && $products->num_rows() > 0) {
            foreach ($products->result() as $row) {
                $productUrl = !empty($row->vendor_url)
                    ? base_url($row->vendor_url . '/' . $row->url)
                    : base_url($row->url);

                $lastmod = (!empty($row->time_update) && $row->time_update > 0)
                    ? date('Y-m-d', (int) $row->time_update)
                    : ((!empty($row->time) && $row->time > 0) ? date('Y-m-d', (int) $row->time) : $today);

                $xml .= "  <url>\n";
                $xml .= "    <loc>" . htmlspecialchars($productUrl, ENT_XML1, 'UTF-8') . "</loc>\n";
                $xml .= "    <lastmod>" . $lastmod . "</lastmod>\n";
                $xml .= "    <changefreq>weekly</changefreq>\n";
                $xml .= "    <priority>0.9</priority>\n";

                if (!empty($row->image)) {
                    $imgUrl = base_url('attachments/shop_images/' . $row->image);
                    $imgTitle = !empty($row->title) ? $row->title : 'Product Image';
                    $xml .= "    <image:image>\n";
                    $xml .= "      <image:loc>" . htmlspecialchars($imgUrl, ENT_XML1, 'UTF-8') . "</image:loc>\n";
                    $xml .= "      <image:title>" . htmlspecialchars($imgTitle, ENT_XML1, 'UTF-8') . "</image:title>\n";
                    $xml .= "    </image:image>\n";
                }

                $xml .= "  </url>\n";
            }
        }

        $xml .= '</urlset>';
        echo $xml;
    }

    public function sitemapCategories()
    {
        header("Content-Type: text/xml; charset=utf-8");
        $categories = $this->Public_model->sitemapCategories();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $xml .= '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        $today = date('Y-m-d');
        if ($categories && $categories->num_rows() > 0) {
            foreach ($categories->result() as $row) {
                $slug = url_title($row->name, 'dash', true);
                $catUrl = base_url('category/' . $row->id . ($slug ? '/' . $slug : ''));

                $xml .= "  <url>\n";
                $xml .= "    <loc>" . htmlspecialchars($catUrl, ENT_XML1, 'UTF-8') . "</loc>\n";
                $xml .= "    <lastmod>" . $today . "</lastmod>\n";
                $xml .= "    <changefreq>daily</changefreq>\n";
                $xml .= "    <priority>0.8</priority>\n";

                if (!empty($row->image)) {
                    $imgUrl = base_url('attachments/shop_images/' . $row->image);
                    $xml .= "    <image:image>\n";
                    $xml .= "      <image:loc>" . htmlspecialchars($imgUrl, ENT_XML1, 'UTF-8') . "</image:loc>\n";
                    $xml .= "      <image:title>" . htmlspecialchars($row->name, ENT_XML1, 'UTF-8') . "</image:title>\n";
                    $xml .= "    </image:image>\n";
                }

                $xml .= "  </url>\n";
            }
        }

        $xml .= '</urlset>';
        echo $xml;
    }

    public function sitemapStores()
    {
        header("Content-Type: text/xml; charset=utf-8");
        $stores = $this->Public_model->sitemapStores();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        $today = date('Y-m-d');
        if ($stores && $stores->num_rows() > 0) {
            foreach ($stores->result() as $row) {
                $storeUrl = base_url('vendor/view/' . $row->url);
                $lastmod = !empty($row->updated_at)
                    ? date('Y-m-d', strtotime($row->updated_at))
                    : (!empty($row->created_at) ? date('Y-m-d', strtotime($row->created_at)) : $today);

                $xml .= "  <url>\n";
                $xml .= "    <loc>" . htmlspecialchars($storeUrl, ENT_XML1, 'UTF-8') . "</loc>\n";
                $xml .= "    <lastmod>" . $lastmod . "</lastmod>\n";
                $xml .= "    <changefreq>weekly</changefreq>\n";
                $xml .= "    <priority>0.8</priority>\n";
                $xml .= "  </url>\n";
            }
        }

        $xml .= '</urlset>';
        echo $xml;
    }

    public function sitemapPages()
    {
        header("Content-Type: text/xml; charset=utf-8");
        $pages = $this->Public_model->sitemapPages();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        $today = date('Y-m-d');
        // Core static pages
        $staticPages = array(
            array('url' => base_url(), 'freq' => 'daily', 'priority' => '1.0'),
            array('url' => base_url('shop'), 'freq' => 'daily', 'priority' => '0.9'),
            array('url' => base_url('contacts'), 'freq' => 'monthly', 'priority' => '0.6'),
            array('url' => base_url('blog'), 'freq' => 'weekly', 'priority' => '0.7'),
        );

        foreach ($staticPages as $p) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($p['url'], ENT_XML1, 'UTF-8') . "</loc>\n";
            $xml .= "    <lastmod>" . $today . "</lastmod>\n";
            $xml .= "    <changefreq>" . $p['freq'] . "</changefreq>\n";
            $xml .= "    <priority>" . $p['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }

        if ($pages && $pages->num_rows() > 0) {
            foreach ($pages->result() as $row) {
                $pageUrl = base_url('page/' . $row->name);
                $xml .= "  <url>\n";
                $xml .= "    <loc>" . htmlspecialchars($pageUrl, ENT_XML1, 'UTF-8') . "</loc>\n";
                $xml .= "    <lastmod>" . $today . "</lastmod>\n";
                $xml .= "    <changefreq>monthly</changefreq>\n";
                $xml .= "    <priority>0.6</priority>\n";
                $xml .= "  </url>\n";
            }
        }

        $xml .= '</urlset>';
        echo $xml;
    }

    public function sitemapBlog()
    {
        header("Content-Type: text/xml; charset=utf-8");
        $blogPosts = $this->Public_model->sitemapBlog();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $xml .= '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        $today = date('Y-m-d');
        if ($blogPosts && $blogPosts->num_rows() > 0) {
            foreach ($blogPosts->result() as $row) {
                $postUrl = base_url('blog/' . $row->url);
                $lastmod = (!empty($row->time) && $row->time > 0) ? date('Y-m-d', (int) $row->time) : $today;

                $xml .= "  <url>\n";
                $xml .= "    <loc>" . htmlspecialchars($postUrl, ENT_XML1, 'UTF-8') . "</loc>\n";
                $xml .= "    <lastmod>" . $lastmod . "</lastmod>\n";
                $xml .= "    <changefreq>weekly</changefreq>\n";
                $xml .= "    <priority>0.7</priority>\n";

                if (!empty($row->image)) {
                    $imgUrl = base_url('attachments/blog_images/' . $row->image);
                    $imgTitle = !empty($row->title) ? $row->title : 'Blog Post';
                    $xml .= "    <image:image>\n";
                    $xml .= "      <image:loc>" . htmlspecialchars($imgUrl, ENT_XML1, 'UTF-8') . "</image:loc>\n";
                    $xml .= "      <image:title>" . htmlspecialchars($imgTitle, ENT_XML1, 'UTF-8') . "</image:title>\n";
                    $xml .= "    </image:image>\n";
                }

                $xml .= "  </url>\n";
            }
        }

        $xml .= '</urlset>';
        echo $xml;
    }

    public function sitemapAll()
    {
        header("Content-Type: text/xml; charset=utf-8");
        $today = date('Y-m-d');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $xml .= '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        // 1. Static Pages
        $staticPages = array(
            array('url' => base_url(), 'freq' => 'daily', 'priority' => '1.0'),
            array('url' => base_url('shop'), 'freq' => 'daily', 'priority' => '0.9'),
            array('url' => base_url('contacts'), 'freq' => 'monthly', 'priority' => '0.6'),
            array('url' => base_url('blog'), 'freq' => 'weekly', 'priority' => '0.7'),
        );
        foreach ($staticPages as $p) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($p['url'], ENT_XML1, 'UTF-8') . "</loc>\n";
            $xml .= "    <lastmod>" . $today . "</lastmod>\n";
            $xml .= "    <changefreq>" . $p['freq'] . "</changefreq>\n";
            $xml .= "    <priority>" . $p['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }

        // 2. Categories
        $categories = $this->Public_model->sitemapCategories();
        if ($categories && $categories->num_rows() > 0) {
            foreach ($categories->result() as $row) {
                $slug = url_title($row->name, 'dash', true);
                $catUrl = base_url('category/' . $row->id . ($slug ? '/' . $slug : ''));
                $xml .= "  <url>\n";
                $xml .= "    <loc>" . htmlspecialchars($catUrl, ENT_XML1, 'UTF-8') . "</loc>\n";
                $xml .= "    <lastmod>" . $today . "</lastmod>\n";
                $xml .= "    <changefreq>daily</changefreq>\n";
                $xml .= "    <priority>0.8</priority>\n";
                if (!empty($row->image)) {
                    $xml .= "    <image:image>\n";
                    $xml .= "      <image:loc>" . htmlspecialchars(base_url('attachments/shop_images/' . $row->image), ENT_XML1, 'UTF-8') . "</image:loc>\n";
                    $xml .= "      <image:title>" . htmlspecialchars($row->name, ENT_XML1, 'UTF-8') . "</image:title>\n";
                    $xml .= "    </image:image>\n";
                }
                $xml .= "  </url>\n";
            }
        }

        // 3. Stores
        $stores = $this->Public_model->sitemapStores();
        if ($stores && $stores->num_rows() > 0) {
            foreach ($stores->result() as $row) {
                $storeUrl = base_url('vendor/view/' . $row->url);
                $lastmod = !empty($row->updated_at)
                    ? date('Y-m-d', strtotime($row->updated_at))
                    : (!empty($row->created_at) ? date('Y-m-d', strtotime($row->created_at)) : $today);
                $xml .= "  <url>\n";
                $xml .= "    <loc>" . htmlspecialchars($storeUrl, ENT_XML1, 'UTF-8') . "</loc>\n";
                $xml .= "    <lastmod>" . $lastmod . "</lastmod>\n";
                $xml .= "    <changefreq>weekly</changefreq>\n";
                $xml .= "    <priority>0.8</priority>\n";
                $xml .= "  </url>\n";
            }
        }

        // 4. Products
        $products = $this->Public_model->sitemapProducts();
        if ($products && $products->num_rows() > 0) {
            foreach ($products->result() as $row) {
                $productUrl = !empty($row->vendor_url)
                    ? base_url($row->vendor_url . '/' . $row->url)
                    : base_url($row->url);
                $lastmod = (!empty($row->time_update) && $row->time_update > 0)
                    ? date('Y-m-d', (int) $row->time_update)
                    : ((!empty($row->time) && $row->time > 0) ? date('Y-m-d', (int) $row->time) : $today);

                $xml .= "  <url>\n";
                $xml .= "    <loc>" . htmlspecialchars($productUrl, ENT_XML1, 'UTF-8') . "</loc>\n";
                $xml .= "    <lastmod>" . $lastmod . "</lastmod>\n";
                $xml .= "    <changefreq>weekly</changefreq>\n";
                $xml .= "    <priority>0.9</priority>\n";
                if (!empty($row->image)) {
                    $xml .= "    <image:image>\n";
                    $xml .= "      <image:loc>" . htmlspecialchars(base_url('attachments/shop_images/' . $row->image), ENT_XML1, 'UTF-8') . "</image:loc>\n";
                    $xml .= "      <image:title>" . htmlspecialchars(!empty($row->title) ? $row->title : 'Product Image', ENT_XML1, 'UTF-8') . "</image:title>\n";
                    $xml .= "    </image:image>\n";
                }
                $xml .= "  </url>\n";
            }
        }

        // 5. Blog
        $blogPosts = $this->Public_model->sitemapBlog();
        if ($blogPosts && $blogPosts->num_rows() > 0) {
            foreach ($blogPosts->result() as $row) {
                $postUrl = base_url('blog/' . $row->url);
                $lastmod = (!empty($row->time) && $row->time > 0) ? date('Y-m-d', (int) $row->time) : $today;
                $xml .= "  <url>\n";
                $xml .= "    <loc>" . htmlspecialchars($postUrl, ENT_XML1, 'UTF-8') . "</loc>\n";
                $xml .= "    <lastmod>" . $lastmod . "</lastmod>\n";
                $xml .= "    <changefreq>weekly</changefreq>\n";
                $xml .= "    <priority>0.7</priority>\n";
                if (!empty($row->image)) {
                    $xml .= "    <image:image>\n";
                    $xml .= "      <image:loc>" . htmlspecialchars(base_url('attachments/blog_images/' . $row->image), ENT_XML1, 'UTF-8') . "</image:loc>\n";
                    $xml .= "      <image:title>" . htmlspecialchars(!empty($row->title) ? $row->title : 'Blog Post', ENT_XML1, 'UTF-8') . "</image:title>\n";
                    $xml .= "    </image:image>\n";
                }
                $xml .= "  </url>\n";
            }
        }

        // 6. Dynamic Pages
        $pages = $this->Public_model->sitemapPages();
        if ($pages && $pages->num_rows() > 0) {
            foreach ($pages->result() as $row) {
                $xml .= "  <url>\n";
                $xml .= "    <loc>" . htmlspecialchars(base_url('page/' . $row->name), ENT_XML1, 'UTF-8') . "</loc>\n";
                $xml .= "    <lastmod>" . $today . "</lastmod>\n";
                $xml .= "    <changefreq>monthly</changefreq>\n";
                $xml .= "    <priority>0.6</priority>\n";
                $xml .= "  </url>\n";
            }
        }

        $xml .= '</urlset>';
        echo $xml;
    }

    public function platform()
    {
        $this->load->view('main/platform/index');
    }
}
