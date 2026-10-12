<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$storeUrl = LANG_URL . '/vendor/view/' . rawurlencode($vendorInfo['url']);
?>
<main class="buyora-container buyora-page buyora-vendor-page">
    <nav class="buyora-breadcrumbs" aria-label="Breadcrumb"><a href="<?= base_url() ?>">Home</a><span>/</span><a href="<?= htmlspecialchars(LANG_URL . '/shop', ENT_QUOTES, 'UTF-8') ?>">Shops</a><span>/</span><span><?= htmlspecialchars($vendorInfo['name'], ENT_QUOTES, 'UTF-8') ?></span></nav>
    <section class="buyora-store-banner">
        <div class="buyora-store-avatar"><i class="fa fa-shopping-bag" aria-hidden="true"></i></div>
        <div><div class="buyora-eyebrow">BUYORA marketplace</div><h1><?= htmlspecialchars($vendorInfo['name'], ENT_QUOTES, 'UTF-8') ?></h1><p>Shop products from this seller.</p></div>
        <a class="buyora-button buyora-button-light" href="<?= htmlspecialchars(LANG_URL . '/shop', ENT_QUOTES, 'UTF-8') ?>"><i class="fa fa-arrow-left" aria-hidden="true"></i> Browse all products</a>
    </section>
    <section class="buyora-store-products">
        <div class="buyora-page-heading">
            <div><div class="buyora-eyebrow">Store catalogue</div><h2>Products by <?= htmlspecialchars($vendorInfo['name'], ENT_QUOTES, 'UTF-8') ?></h2></div>
            <form class="buyora-catalog-search" method="get" action="<?= htmlspecialchars($storeUrl, ENT_QUOTES, 'UTF-8') ?>">
                <input type="search" name="search_in_title" value="<?= htmlspecialchars(isset($_GET['search_in_title']) && is_scalar($_GET['search_in_title']) ? $_GET['search_in_title'] : '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Search this shop">
                <button type="submit" aria-label="Search this shop"><i class="fa fa-search" aria-hidden="true"></i></button>
            </form>
        </div>
        <form class="buyora-sort-form" method="get" action="<?= htmlspecialchars($storeUrl, ENT_QUOTES, 'UTF-8') ?>">
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
                    $itemUrl = $storeUrl . '/' . $item['url'] . '_' . (int) $item['id'];
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
            <p class="buyora-empty-state">This shop has no products available right now.</p>
        <?php } ?>
        <?php if (!empty($links_pagination)) { ?><div class="buyora-pagination"><?= $links_pagination ?></div><?php } ?>
    </section>
</main>
