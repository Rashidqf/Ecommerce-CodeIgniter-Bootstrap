<?php

/*
 * Only vendors class
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Store extends MY_Controller
{

    private $num_rows = 20;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('vendor/Vendorprofile_model');
    }

    public function index($page = 0, $vendor = null)
    {
        if (empty($vendor) || (int) $this->Home_admin_model->getValueStore('multiVendor') !== 1) {
            redirect(base_url('shop'));
            return;
        }
        $vendorInfo = $this->Vendorprofile_model->getVendorByUrlAddress($vendor);
        if ($vendorInfo == null || (int) $vendorInfo['status'] !== 1) {
            redirect(base_url('shop'));
            return;
        }
        $data = array();
        $head = array();
        $head['title'] = $vendorInfo['name'] . ' Official Store | Verified Seller on BUYORA';
        $head['description'] = 'Shop authentic products from ' . $vendorInfo['name'] . ' on BUYORA Marketplace. Enjoy verified ratings, buyer protection, and fast delivery.';
        $head['keywords'] = $vendorInfo['name'] . ', official store, buyora, buy online, marketplace';
        $head['vendor_url'] = base_url('vendor/view/' . rawurlencode($vendor));
        $head['canonical_url'] = base_url('vendor/view/' . rawurlencode($vendor));
        $head['og_type'] = 'profile';
        $all_categories = $this->Public_model->getShopCategories();

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

        $data['home_categories'] = $tree = buildTree($all_categories);
        $data['all_categories'] = $all_categories;
        $data['vendorInfo'] = $vendorInfo;
        $data['products'] = $this->Public_model->getProducts($this->num_rows, $page, $_GET, $vendorInfo['id'], true);
        $rowscount = $this->Public_model->productsCount($_GET, true, $vendorInfo['id']);
        $data['links_pagination'] = pagination('vendor/view/' . $vendor, $rowscount, $this->num_rows, 4);
        $this->render('buyora-vendor', $head, $data);
    }

    public function viewProduct($vendor, $id)
    {
        if ((int) $this->Home_admin_model->getValueStore('multiVendor') !== 1) {
            show_404();
        }
        $vendorInfo = $this->Vendorprofile_model->getVendorByUrlAddress($vendor);
        if ($vendorInfo == null || (int) $vendorInfo['status'] !== 1) {
            show_404();
        }
        $data = array();
        $head = array();
        $data['product'] = $this->Public_model->getOneProduct($id);
        if ($data['product'] === null || (int) $data['product']['vendor_id'] !== (int) $vendorInfo['id']) {
            show_404();
        }
        $data['sameCagegoryProducts'] = $this->Public_model->sameCagegoryProducts($data['product']['shop_categorie'], $id, $vendorInfo['id']);
        $data['storeProducts'] = $this->Public_model->getProducts(8, 0, array(), (int) $vendorInfo['id'], true);
        $vars['publicDateAdded'] = $this->Home_admin_model->getValueStore('publicDateAdded');
        $this->load->vars($vars);
        $head['title'] = $data['product']['title'] . ' | ' . $vendorInfo['name'] . ' — BUYORA';
        $head['vendor_url'] = base_url('vendor/view/' . rawurlencode($vendorInfo['url']));
        $cleanDesc = trim(preg_replace('/\s+/', ' ', strip_tags($data['product']['description'])));
        $head['description'] = character_limiter($cleanDesc, 160);
        $head['keywords'] = str_replace(" ", ",", $data['product']['title']);
        $head['image'] = !empty($data['product']['image']) ? $data['product']['image'] : null;
        $head['og_type'] = 'product';
        $data['vendorInfo'] = $vendorInfo;
        $this->render('buyora-product', $head, $data);
    }

}
