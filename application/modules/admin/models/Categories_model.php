<?php

class Categories_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function categoriesCount()
    {
        return $this->db->count_all_results('shop_categories');
    }

    public function getShopCategories($limit = null, $start = null)
    {
        $limit_sql = '';
        if ($limit !== null && $start !== null) {
            $limit_sql = ' LIMIT ' . $start . ',' . $limit;
        }

        $query = $this->db->query('SELECT translations_first.*, (SELECT name FROM shop_categories_translations WHERE for_id = sub_for AND abbr = translations_first.abbr) as sub_is, shop_categories.position, shop_categories.image FROM shop_categories_translations as translations_first INNER JOIN shop_categories ON shop_categories.id = translations_first.for_id ORDER BY position ASC ' . $limit_sql);
        $arr = array();
        foreach ($query->result() as $shop_categorie) {
            $arr[$shop_categorie->for_id]['info'][] = array(
                'abbr' => $shop_categorie->abbr,
                'name' => $shop_categorie->name
            );
            $arr[$shop_categorie->for_id]['sub'][] = $shop_categorie->sub_is;
            $arr[$shop_categorie->for_id]['position'] = $shop_categorie->position;
            $arr[$shop_categorie->for_id]['image'] = $shop_categorie->image;
        }
        return $arr;
    }

    public function deleteShopCategorie($id)
    {
        $categoryIds = array((int) $id);
        for ($index = 0; $index < count($categoryIds); $index++) {
            $children = $this->db->select('id')->where('sub_for', $categoryIds[$index])->get('shop_categories')->result_array();
            foreach ($children as $child) {
                if (!in_array((int) $child['id'], $categoryIds, true)) {
                    $categoryIds[] = (int) $child['id'];
                }
            }
        }

        $this->db->trans_begin();
        $this->db->where_in('shop_categorie', $categoryIds);
        if (!$this->db->update('products', array('visibility' => 0))) {
            log_message('error', print_r($this->db->error(), true));
        }
        $this->db->where_in('for_id', $categoryIds);
        if (!$this->db->delete('shop_categories_translations')) {
            log_message('error', print_r($this->db->error(), true));
        }

        $this->db->where_in('id', $categoryIds);
        if (!$this->db->delete('shop_categories')) {
            log_message('error', print_r($this->db->error(), true));
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            show_error(lang('database_error'));
        } else {
            $this->db->trans_commit();
        }
        return true;
    }

    public function setShopCategorie($post, $image = '')
    {
        $this->db->trans_begin();
        if (!$this->db->insert('shop_categories', array(
                    'sub_for' => (int) $post['sub_for'],
                    'position' => $this->categoriesCount() + 1,
                    'image' => $image
                ))) {
            log_message('error', print_r($this->db->error(), true));
        }
        $id = $this->db->insert_id();

        $i = 0;
        foreach ($post['translations'] as $abbr) {
            $arr = array();
            $arr['abbr'] = $abbr;
            $arr['name'] = $post['categorie_name'][$i];
            $arr['for_id'] = $id;
            if (!$this->db->insert('shop_categories_translations', $arr)) {
                log_message('error', print_r($this->db->error(), true));
            }
            $i++;
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            show_error(lang('database_error'));
        } else {
            $this->db->trans_commit();
        }
    }

    public function categoryExists($name, $language = MY_DEFAULT_LANGUAGE_ABBR)
    {
        $this->db->join('shop_categories_translations', 'shop_categories_translations.for_id = shop_categories.id');
        $this->db->where('shop_categories_translations.abbr', $language);
        $this->db->where('shop_categories_translations.name', trim($name));
        return $this->db->count_all_results('shop_categories') > 0;
    }

    public function updateCategoryImage($id, $image)
    {
        $this->db->where('id', (int) $id);
        return $this->db->update('shop_categories', array('image' => $image));
    }

    public function categoryIdExists($id)
    {
        return $this->db->where('id', (int) $id)->count_all_results('shop_categories') > 0;
    }

    public function editShopCategorieSub($post)
    {
        $result = true;
        if ($post['editSubId'] != $post['newSubIs']) {
            $this->db->where('id', $post['editSubId']);
            if (!$this->db->update('shop_categories', array(
                        'sub_for' => $post['newSubIs']
                    ))) {
                log_message('error', print_r($this->db->error(), true));
                show_error(lang('database_error'));
            }
        } else {
            $result = false;
        }
        return $result;
    }

    public function editShopCategorie($post)
    {
        $this->db->where('abbr', $post['abbr']);
        $this->db->where('name', trim($post['name']));
        $this->db->where('for_id !=', (int) $post['for_id']);
        if ($this->db->count_all_results('shop_categories_translations') > 0) {
            return false;
        }
        $this->db->where('abbr', $post['abbr']);
        $this->db->where('for_id', $post['for_id']);
        return $this->db->update('shop_categories_translations', array(
                    'name' => $post['name']
                ));
    }

    public function editShopCategoriePosition($post)
    {
        $this->db->where('id', $post['editid']);
        if (!$this->db->update('shop_categories', array(
                    'position' => $post['new_pos']
                ))) {
            log_message('error', print_r($this->db->error(), true));
            show_error(lang('database_error'));
        }
    }

}
