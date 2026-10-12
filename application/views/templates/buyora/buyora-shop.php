<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<main class="buyora-container buyora-page">
    <nav class="buyora-breadcrumbs" aria-label="Breadcrumb"><a href="<?= base_url() ?>">Home</a><span>/</span><span>Shop</span></nav>
    <div class="buyora-page-heading">
        <div><div class="buyora-eyebrow">Explore BUYORA</div><h1><?= htmlspecialchars(lang('products'), ENT_QUOTES, 'UTF-8') ?></h1></div>
        <form class="buyora-catalog-search" method="get" action="<?= htmlspecialchars(LANG_URL . '/shop', ENT_QUOTES, 'UTF-8') ?>">
            <input type="search" name="search_in_title" value="<?= htmlspecialchars(isset($_GET['search_in_title']) && is_scalar($_GET['search_in_title']) ? $_GET['search_in_title'] : '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Search products">
            <button type="submit" aria-label="Search"><i class="fa fa-search" aria-hidden="true"></i></button>
        </form>
    </div>
    <div class="buyora-shop-layout">
        <aside class="buyora-filter-panel">
            <h2><?= htmlspecialchars(lang('categories'), ENT_QUOTES, 'UTF-8') ?></h2>
            <a class="buyora-filter-link <?= empty($_GET['category']) ? 'is-active' : '' ?>" href="<?= htmlspecialchars(LANG_URL . '/shop', ENT_QUOTES, 'UTF-8') ?>">All products</a>
            <?php foreach (isset($all_categories) && is_array($all_categories) ? $all_categories : array() as $category) { ?>
                <?php if ((int) $category['sub_for'] === 0) { ?>
                    <a class="buyora-filter-link <?= isset($_GET['category']) && (int) $_GET['category'] === (int) $category['id'] ? 'is-active' : '' ?>" href="<?= htmlspecialchars(LANG_URL . '/shop?category=' . (int) $category['id'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?></a>
                <?php } ?>
            <?php } ?>
        </aside>
        <section class="buyora-shop-results">
            <div class="buyora-results-heading">
                <h2><?= empty($_GET['category']) ? 'All products' : 'Products in selected category' ?></h2>
                <span><?= count(isset($products) && is_array($products) ? $products : array()) ?> products</span>
            </div>
            <form class="buyora-sort-form" method="get" action="<?= htmlspecialchars(LANG_URL . '/shop', ENT_QUOTES, 'UTF-8') ?>">
                <?php foreach ($_GET as $filterName => $filterValue) {
                    if (
                        is_string($filterName)
                        && !in_array($filterName, array('order_new', 'order_price', 'order_procurement'), true)
                        && is_scalar($filterValue)
                    ) { ?>
                        <input type="hidden" name="<?= htmlspecialchars($filterName, ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars((string) $filterValue, ENT_QUOTES, 'UTF-8') ?>">
                    <?php }
                } ?>
                <label>
                    <span><?= htmlspecialchars(lang('new'), ENT_QUOTES, 'UTF-8') ?></span>
                    <select name="order_new" onchange="this.form.submit()">
                        <option value="desc" <?= !isset($_GET['order_new']) || $_GET['order_new'] === 'desc' ? 'selected' : '' ?>><?= htmlspecialchars(lang('new'), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="asc" <?= isset($_GET['order_new']) && $_GET['order_new'] === 'asc' ? 'selected' : '' ?>><?= htmlspecialchars(lang('old'), ENT_QUOTES, 'UTF-8') ?></option>
                    </select>
                </label>
                <label>
                    <span><?= htmlspecialchars(lang('price_title'), ENT_QUOTES, 'UTF-8') ?></span>
                    <select name="order_price" onchange="this.form.submit()">
                        <option value=""><?= htmlspecialchars(lang('not_selected'), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="asc" <?= isset($_GET['order_price']) && $_GET['order_price'] === 'asc' ? 'selected' : '' ?>><?= htmlspecialchars(lang('price_low'), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="desc" <?= isset($_GET['order_price']) && $_GET['order_price'] === 'desc' ? 'selected' : '' ?>><?= htmlspecialchars(lang('price_high'), ENT_QUOTES, 'UTF-8') ?></option>
                    </select>
                </label>
                <label>
                    <span><?= htmlspecialchars(lang('procurement_title'), ENT_QUOTES, 'UTF-8') ?></span>
                    <select name="order_procurement" onchange="this.form.submit()">
                        <option value=""><?= htmlspecialchars(lang('not_selected'), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="desc" <?= isset($_GET['order_procurement']) && $_GET['order_procurement'] === 'desc' ? 'selected' : '' ?>><?= htmlspecialchars(lang('procurement_desc'), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="asc" <?= isset($_GET['order_procurement']) && $_GET['order_procurement'] === 'asc' ? 'selected' : '' ?>><?= htmlspecialchars(lang('procurement_asc'), ENT_QUOTES, 'UTF-8') ?></option>
                    </select>
                </label>
            </form>
            <?php if (!empty($products)) { ?>
                <div class="buyora-catalog-grid">
                    <?php foreach ($products as $item) {
                        $itemImage = !empty($item['image']) && is_file('attachments/shop_images/' . $item['image'])
                            ? base_url('attachments/shop_images/' . rawurlencode($item['image']))
                            : base_url('attachments/no-image-frontend.png');
                        $itemUrl = !empty($item['vendor_url'])
                            ? LANG_URL . '/' . rawurlencode($item['vendor_url']) . '/' . $item['url'] . '_' . (int) $item['id']
                            : LANG_URL . '/' . $item['url'] . '_' . (int) $item['id'];
                        $discounted = is_numeric($item['old_price']) && (float) $item['old_price'] > (float) $item['price'];
                        ?>
                        <article class="buyora-catalog-card">
                            <a class="buyora-catalog-image" href="<?= htmlspecialchars($itemUrl, ENT_QUOTES, 'UTF-8') ?>"><img src="<?= htmlspecialchars($itemImage, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>"></a>
                            <div class="buyora-catalog-info">
                                <a class="buyora-catalog-title" href="<?= htmlspecialchars($itemUrl, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></a>
                                <div class="buyora-catalog-price"><strong><?= htmlspecialchars($item['price'], ENT_QUOTES, 'UTF-8') . CURRENCY ?></strong><?php if ($discounted) { ?><del><?= htmlspecialchars($item['old_price'], ENT_QUOTES, 'UTF-8') . CURRENCY ?></del><?php } ?></div>
                                <?php if ((int) $item['quantity'] > 0) { ?>
                                    <a class="buyora-button buyora-button-primary buyora-card-cart add-to-cart" data-id="<?= (int) $item['id'] ?>" href="javascript:void(0);"><i class="fa fa-cart-plus" aria-hidden="true"></i> <?= htmlspecialchars(lang('add_to_cart'), ENT_QUOTES, 'UTF-8') ?></a>
                                <?php } else { ?>
                                    <span class="buyora-out-of-stock"><?= htmlspecialchars(lang('out_of_stock_product'), ENT_QUOTES, 'UTF-8') ?></span>
                                <?php } ?>
                            </div>
                        </article>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <p class="buyora-empty-state"><?= htmlspecialchars(lang('no_results'), ENT_QUOTES, 'UTF-8') ?></p>
            <?php } ?>
            <?php if (!empty($links_pagination)) { ?><div class="buyora-pagination"><?= $links_pagination ?></div><?php } ?>
        </section>
    </div>
</main>
