<?php

class Products_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function deleteProduct($id)
    {
        $this->db->trans_begin();
        $this->db->where('for_id', $id);
        if (!$this->db->delete('products_translations')) {
            log_message('error', print_r($this->db->error(), true));
        }

        $this->db->where('id', $id);
        if (!$this->db->delete('products')) {
            log_message('error', print_r($this->db->error(), true));
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            show_error(lang('database_error'));
        } else {
            $this->db->trans_commit();
        }
    }

    public function productsCount($search_title = null, $category = null, $vendor = null, $status = null)
    {
        if ($search_title != null) {
            $search_title = trim($this->db->escape_like_str($search_title));
            $this->db->where("(products_translations.title LIKE '%$search_title%')");
        }
        if ($category != null) {
            $this->db->where('shop_categorie', $category);
        }
        if ($vendor != null) {
            $this->db->where('vendor_id', $vendor);
        }
        if ($status !== null) {
            $this->db->where('visibility', $status);
        }
        $this->db->join('products_translations', 'products_translations.for_id = products.id', 'left');
        $this->db->where('products_translations.abbr', MY_DEFAULT_LANGUAGE_ABBR);
        return $this->db->count_all_results('products');
    }

    public function getProducts($limit, $page, $search_title = null, $orderby = null, $category = null, $vendor = null, $status = null)
    {
        if ($search_title != null) {
            $search_title = trim($this->db->escape_like_str($search_title));
            $this->db->where("(products_translations.title LIKE '%$search_title%')");
        }
        if ($orderby !== null) {
            $ord = explode('=', $orderby);
            if (isset($ord[0]) && isset($ord[1])) {
                $this->db->order_by('products.' . $ord[0], $ord[1]);
            }
        } else {
            $this->db->order_by('products.position', 'asc');
        }
        if ($category != null) {
            $this->db->where('shop_categorie', $category);
        }
        if ($vendor != null) {
            $this->db->where('vendor_id', $vendor);
        }
        if ($status !== null) {
            $this->db->where('visibility', $status);
        }
        $this->db->join('vendors', 'vendors.id = products.vendor_id', 'left');
        $this->db->join('products_translations', 'products_translations.for_id = products.id', 'left');
        $this->db->where('products_translations.abbr', MY_DEFAULT_LANGUAGE_ABBR);
        $query = $this->db->select('vendors.name as vendor_name, vendors.id as vendor_id, products.*, products_translations.title, products_translations.description, products_translations.price, products_translations.old_price, products_translations.abbr, products.url, products_translations.for_id, products_translations.basic_description')->get('products', $limit, $page);
        return $query->result();
    }

    public function numShopProducts()
    {
        return $this->db->count_all_results('products');
    }

    public function getOneProduct($id)
    {
        $this->db->select('vendors.name as vendor_name, vendors.id as vendor_id, products.*, products_translations.price');
        $this->db->where('products.id', $id);
        $this->db->join('vendors', 'vendors.id = products.vendor_id', 'left');
        $this->db->join('products_translations', 'products_translations.for_id = products.id', 'inner');
        $this->db->where('products_translations.abbr', MY_DEFAULT_LANGUAGE_ABBR);
        $query = $this->db->get('products');
        if ($query->num_rows() > 0) {
            return $query->row_array();
        } else {
            return false;
        }
    }

    public function productStatusChange($id, $to_status)
    {
        $this->db->where('id', $id);
        $result = $this->db->update('products', array('visibility' => $to_status));
        return $result;
    }

    public function setProduct($post, $id = 0, $allowDuplicateTitle = false)
    {
        if (!isset($post['position']) || trim((string) $post['position']) === '') {
            $post['position'] = 0;
        }
        $this->validateProductInput($post, $id, $allowDuplicateTitle);
        if (!isset($post['brand_id'])) {
            $post['brand_id'] = null;
        }
        if (!isset($post['virtual_products'])) {
            $post['virtual_products'] = null;
        }
        $image = isset($post['image']) && $post['image'] !== '' ? $post['image'] : null;
        $old_image = isset($post['old_image']) ? $post['old_image'] : null;
        $this->db->trans_begin();
        $is_update = false;
        if ($id > 0) {
            $is_update = true;
            if (!$this->db->where('id', $id)->update('products', array(
                        'image' => $image !== null ? $image : $old_image,
                        'shop_categorie' => $post['shop_categorie'],
                        'quantity' => $post['quantity'],
                        'in_slider' => $post['in_slider'],
                        'position' => $post['position'],
                        'virtual_products' => $post['virtual_products'],
                        'brand_id' => $post['brand_id'],
                        'visibility' => isset($post['visibility']) ? $post['visibility'] : 1,
                        'time_update' => time()
                    ))) {
                log_message('error', print_r($this->db->error(), true));
            }
        } else {
            /*
             * Lets get what is default tranlsation number
             * in titles and convert it to url
             * We want our plaform public ulrs to be in default 
             * language that we use
             */
            $myTranslationNum = 0;
            $i = 0;
            foreach ($_POST['translations'] as $translation) {
                if ($translation == MY_DEFAULT_LANGUAGE_ABBR) {
                    $myTranslationNum = $i;
                }
                $i++;
            }
            if (!$this->db->insert('products', array(
                        'image' => $image,
                        'shop_categorie' => $post['shop_categorie'],
                        'quantity' => $post['quantity'],
                        'in_slider' => $post['in_slider'],
                        'position' => $post['position'],
                        'virtual_products' => $post['virtual_products'],
                        'folder' => $post['folder'],
                        'brand_id' => $post['brand_id'],
                        'visibility' => isset($post['visibility']) ? $post['visibility'] : 1,
                        'time' => time()
                    ))) {
                log_message('error', print_r($this->db->error(), true));
            }
            $id = $this->db->insert_id();

            $this->db->where('id', $id);
            if (!$this->db->update('products', array(
                        'url' => except_letters($_POST['title'][$myTranslationNum]) . '_' . $id
                    ))) {
                log_message('error', print_r($this->db->error(), true));
            }
        }
        $this->setProductTranslation($post, $id, $is_update);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            show_error(lang('database_error'));
        } else {
            $this->db->trans_commit();
        }
    }

    private function validateProductInput($post, $id, $allowDuplicateTitle)
    {
        $quantity = isset($post['quantity']) ? trim((string) $post['quantity']) : '';
        $categoryId = isset($post['shop_categorie']) ? trim((string) $post['shop_categorie']) : '';
        if (!ctype_digit($quantity) || (int) $quantity > 2147483647
            || !ctype_digit($categoryId) || (int) $categoryId < 1
            || !$this->db->where('id', (int) $categoryId)->count_all_results('shop_categories')) {
            show_error('Choose a valid category and enter a non-negative whole-number stock quantity.', 400);
        }
        if (!isset($post['position']) || !ctype_digit(trim((string) $post['position']))
            || (float) $post['position'] > 4294967295
            || !isset($post['in_slider']) || !in_array((string) $post['in_slider'], array('0', '1'), true)) {
            show_error('Enter a valid display position and featured setting.', 400);
        }

        if (!isset($post['translations'], $post['price'], $post['old_price'], $post['title'])
            || !is_array($post['translations']) || !is_array($post['price'])
            || !is_array($post['old_price']) || !is_array($post['title'])) {
            show_error('Product names and prices are required for each language.', 400);
        }

        $defaultLanguageIsValid = false;
        foreach ($post['translations'] as $index => $language) {
            $title = isset($post['title'][$index]) ? trim((string) $post['title'][$index]) : '';
            $price = isset($post['price'][$index]) ? str_replace(',', '.', trim((string) $post['price'][$index])) : '';
            $oldPrice = isset($post['old_price'][$index]) ? str_replace(',', '.', trim((string) $post['old_price'][$index])) : '';
            if ($language !== MY_DEFAULT_LANGUAGE_ABBR && $title === '' && $price === '' && $oldPrice === '') {
                continue;
            }
            $validPrice = preg_match('/^(?:\d+(?:\.\d*)?|\.\d+)$/', $price) === 1;
            $validOldPrice = $oldPrice === '' || preg_match('/^(?:\d+(?:\.\d*)?|\.\d+)$/', $oldPrice) === 1;
            if ($title === '' || !$validPrice || strlen($price) > 20) {
                show_error('Enter a product name and a valid non-negative price for every language.', 400);
            }
            if (!$validOldPrice || ($oldPrice !== '' && (float) $oldPrice <= (float) $price)
                || strlen($oldPrice) > 20) {
                show_error('A previous price must be greater than the current price.', 400);
            }
            if ($language === MY_DEFAULT_LANGUAGE_ABBR) {
                $defaultLanguageIsValid = true;
            }
        }
        if (!$defaultLanguageIsValid) {
            show_error('The default-language product name and price are required.', 400);
        }

        $defaultTitle = '';
        foreach ($post['translations'] as $index => $language) {
            if ($language === MY_DEFAULT_LANGUAGE_ABBR && isset($post['title'][$index])) {
                $defaultTitle = trim((string) $post['title'][$index]);
                break;
            }
        }
        if (!$allowDuplicateTitle) {
            $this->db->join('products_translations', 'products_translations.for_id = products.id', 'inner');
            $this->db->where('products_translations.abbr', MY_DEFAULT_LANGUAGE_ABBR);
            $this->db->where('products_translations.title', $defaultTitle);
            $this->db->where('products.shop_categorie', (int) $categoryId);
            if ($id > 0) {
                $this->db->where('products.id !=', (int) $id);
            }
            if ($this->db->count_all_results('products') > 0) {
                show_error('A product with this name already exists in the selected category.', 400);
            }
        }

        if (isset($post['visibility']) && !in_array((string) $post['visibility'], array('0', '1', '2'), true)) {
            show_error('Choose a valid product publication status.', 400);
        }
    }

    private function setProductTranslation($post, $id, $is_update)
    {
        $i = 0;
        $current_trans = $this->getTranslations($id);
        foreach ($post['translations'] as $abbr) {
            $arr = array();
            $emergency_insert = false;
            if (!isset($current_trans[$abbr])) {
                $emergency_insert = true;
            }
            $post['title'][$i] = str_replace('"', "'", $post['title'][$i]);
            $post['price'][$i] = str_replace(' ', '', $post['price'][$i]);
            $post['price'][$i] = str_replace(',', '.', $post['price'][$i]);
            $post['price'][$i] = preg_replace("/[^0-9,.]/", "", $post['price'][$i]);
            $post['old_price'][$i] = str_replace(' ', '', $post['old_price'][$i]);
            $post['old_price'][$i] = str_replace(',', '.', $post['old_price'][$i]);
            $post['old_price'][$i] = preg_replace("/[^0-9,.]/", "", $post['old_price'][$i]);
            $arr = array(
                'title' => $post['title'][$i],
                'basic_description' => $post['basic_description'][$i],
                'description' => $post['description'][$i],
                'price' => $post['price'][$i],
                'old_price' => $post['old_price'][$i],
                'abbr' => $abbr,
                'for_id' => $id
            );
            if ($is_update === true && $emergency_insert === false) {
                $abbr = $arr['abbr'];
                unset($arr['for_id'], $arr['abbr'], $arr['url']);
                if (!$this->db->where('abbr', $abbr)->where('for_id', $id)->update('products_translations', $arr)) {
                    log_message('error', print_r($this->db->error(), true));
                }
            } else {
                if (!$this->db->insert('products_translations', $arr)) {
                    log_message('error', print_r($this->db->error(), true));
                }
            }
            $i++;
        }
    }

    public function getTranslations($id)
    {
        $this->db->where('for_id', $id);
        $query = $this->db->get('products_translations');
        $arr = array();
        foreach ($query->result() as $row) {
            $arr[$row->abbr]['title'] = $row->title;
            $arr[$row->abbr]['basic_description'] = $row->basic_description;
            $arr[$row->abbr]['description'] = $row->description;
            $arr[$row->abbr]['price'] = $row->price;
            $arr[$row->abbr]['old_price'] = $row->old_price;
        }
        return $arr;
    }

}
