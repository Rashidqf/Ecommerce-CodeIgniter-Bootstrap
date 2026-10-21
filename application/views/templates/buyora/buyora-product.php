<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$productImage = base_url('attachments/no-image-frontend.png');
if (!empty($product['image']) && is_file('attachments/shop_images/' . $product['image'])) {
    $productImage = base_url('attachments/shop_images/' . rawurlencode($product['image']));
}
$vendorUrl = !empty($product['vendor_url']) ? LANG_URL . '/vendor/view/' . rawurlencode($product['vendor_url']) : '';
$addedCount = 0;
if (isset($_SESSION['shopping_cart']) && is_array($_SESSION['shopping_cart'])) {
    $cartCounts = array_count_values($_SESSION['shopping_cart']);
    $addedCount = isset($cartCounts[$product['id']]) ? $cartCounts[$product['id']] : 0;
}
?>
<main class="buyora-container buyora-page buyora-product-page">
    <nav class="buyora-breadcrumbs" aria-label="Breadcrumb">
        <a href="<?= base_url() ?>">Home</a><span>/</span>
        <a href="<?= base_url('category/' . (int) $product['shop_categorie'] . '/' . url_title($product['categorie_name'], 'dash', true)) ?>"><?= htmlspecialchars($product['categorie_name'], ENT_QUOTES, 'UTF-8') ?></a>
        <span>/</span><span><?= htmlspecialchars($product['title'], ENT_QUOTES, 'UTF-8') ?></span>
    </nav>
    <div class="buyora-product-layout">
        <section class="buyora-product-gallery" aria-label="Product images">
            <div class="buyora-product-image">
                <img id="buyora-product-main-image" src="<?= htmlspecialchars($productImage, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['title'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <?php
            $otherImagesDir = !empty($product['folder']) ? 'attachments/shop_images/' . basename($product['folder']) . '/' : '';
            $otherImages = array();
            if ($otherImagesDir !== '' && is_dir($otherImagesDir)) {
                foreach (scandir($otherImagesDir) as $imageFile) {
                    if ($imageFile !== '.' && $imageFile !== '..' && is_file($otherImagesDir . $imageFile)
                        && preg_match('/\.(jpe?g|png|webp|gif)$/i', $imageFile)) {
                        $otherImages[] = $imageFile;
                    }
                }
            }
            ?>
            <?php if (!empty($otherImages)) { ?>
                <div class="buyora-product-thumbnails">
                    <button type="button" class="is-selected" data-product-image="<?= htmlspecialchars($productImage, ENT_QUOTES, 'UTF-8') ?>" aria-label="Show main product image"><img src="<?= htmlspecialchars($productImage, ENT_QUOTES, 'UTF-8') ?>" alt=""></button>
                    <?php foreach ($otherImages as $imageFile) { $imageUrl = base_url($otherImagesDir . rawurlencode($imageFile)); ?>
                        <button type="button" data-product-image="<?= htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') ?>" aria-label="Show another product image"><img src="<?= htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') ?>" alt=""></button>
                    <?php } ?>
                </div>
            <?php } ?>
        </section>

        <section class="buyora-product-details">
            <div class="buyora-eyebrow"><?= htmlspecialchars($product['categorie_name'], ENT_QUOTES, 'UTF-8') ?></div>
            <h1><?= htmlspecialchars($product['title'], ENT_QUOTES, 'UTF-8') ?></h1>
            <?php if ((int) $product['vendor_id'] > 0 && $vendorUrl !== '') { ?>
                <a class="buyora-store-card" href="<?= htmlspecialchars($vendorUrl, ENT_QUOTES, 'UTF-8') ?>">
                    <span class="buyora-store-mark"><i class="fa fa-shopping-bag" aria-hidden="true"></i></span>
                    <span>
                        <small>Sold by</small>
                        <strong><?= htmlspecialchars($product['vendor_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                        <em>Visit store for details and more products</em>
                    </span>
                    <span class="buyora-store-go">Visit Store <i class="fa fa-angle-right" aria-hidden="true"></i></span>
                </a>
            <?php } else { ?>
                <div class="buyora-seller-link buyora-official-seller"><i class="fa fa-check-circle" aria-hidden="true"></i> Sold by <strong><?= htmlspecialchars(isset($navitext) && $navitext !== '' ? $navitext : 'BUYORA', ENT_QUOTES, 'UTF-8') ?></strong></div>
            <?php } ?>
            <div class="buyora-product-price">
                <strong><?= trim(CURRENCY) ?> <?= htmlspecialchars(number_format((float) $product['price'], floor((float) $product['price']) === (float) $product['price'] ? 0 : 2), ENT_QUOTES, 'UTF-8') ?></strong>
                <?php if (isset($product['old_price']) && is_numeric($product['old_price']) && (float) $product['old_price'] > (float) $product['price']) { ?>
                    <del><?= trim(CURRENCY) ?> <?= htmlspecialchars(number_format((float) $product['old_price'], floor((float) $product['old_price']) === (float) $product['old_price'] ? 0 : 2), ENT_QUOTES, 'UTF-8') ?></del>
                    <span class="buyora-discount-badge">-<?= (int) round(((float) $product['old_price'] - (float) $product['price']) / (float) $product['old_price'] * 100) ?>%</span>
                <?php } ?>
            </div>
            <div class="buyora-stock-state <?= (int) $product['quantity'] > 0 ? 'is-available' : 'is-unavailable' ?>">
                <i class="fa <?= (int) $product['quantity'] > 0 ? 'fa-check-circle' : 'fa-times-circle' ?>" aria-hidden="true"></i>
                <?= (int) $product['quantity'] > 0 ? htmlspecialchars(lang('in_stock'), ENT_QUOTES, 'UTF-8') : htmlspecialchars(lang('out_of_stock_product'), ENT_QUOTES, 'UTF-8') ?>
                <?php if ((int) $publicQuantity === 1 && (int) $product['quantity'] > 0) { ?> · <?= (int) $product['quantity'] ?> available<?php } ?>
            </div>
            <div class="buyora-product-meta"><?= htmlspecialchars(lang('num_added_to_cart'), ENT_QUOTES, 'UTF-8') ?>: <?= (int) $addedCount ?></div>
            <?php if ((int) $product['quantity'] > 0) { ?>
                <div class="buyora-product-actions">
                    <a href="javascript:void(0);" data-id="<?= (int) $product['id'] ?>" data-goto="<?= htmlspecialchars(LANG_URL . '/shopping-cart', ENT_QUOTES, 'UTF-8') ?>" class="add-to-cart buyora-button buyora-button-primary"><i class="fa fa-cart-plus" aria-hidden="true"></i> <?= htmlspecialchars(lang('add_to_cart'), ENT_QUOTES, 'UTF-8') ?></a>
                    <a href="javascript:void(0);" data-id="<?= (int) $product['id'] ?>" data-goto="<?= htmlspecialchars(LANG_URL . '/checkout', ENT_QUOTES, 'UTF-8') ?>" class="add-to-cart buyora-button buyora-button-outline"><i class="fa fa-bolt" aria-hidden="true"></i> <?= htmlspecialchars(lang('buy_now'), ENT_QUOTES, 'UTF-8') ?></a>
                </div>
            <?php } ?>
            <div class="buyora-description">
                <h2><?= htmlspecialchars(lang('description'), ENT_QUOTES, 'UTF-8') ?></h2>
                <div><?= $product['description'] ?></div>
            </div>
            <?php include rtrim(APPPATH, '/') . '/views/main/social_share.php'; ?>
        </section>
    </div>

    <?php if ((int) $product['vendor_id'] > 0 && !empty($storeProducts)) { ?>
        <section class="buyora-related-products">
            <div class="buyora-page-heading">
                <h2>More from <?= htmlspecialchars($product['vendor_name'], ENT_QUOTES, 'UTF-8') ?></h2>
                <?php if ($vendorUrl !== '') { ?><a href="<?= htmlspecialchars($vendorUrl, ENT_QUOTES, 'UTF-8') ?>">Visit store <i class="fa fa-angle-right" aria-hidden="true"></i></a><?php } ?>
            </div>
            <div class="buyora-catalog-grid">
                <?php foreach ($storeProducts as $item) {
                    if ((int) $item['id'] === (int) $product['id']) {
                        continue;
                    }
                    $itemImage = !empty($item['image']) && is_file('attachments/shop_images/' . $item['image'])
                        ? base_url('attachments/shop_images/' . rawurlencode($item['image']))
                        : base_url('attachments/no-image-frontend.png');
                    $itemUrl = $vendorUrl !== ''
                        ? $vendorUrl . '/' . $item['url'] . '_' . (int) $item['id']
                        : LANG_URL . '/' . $item['url'] . '_' . (int) $item['id'];
                    ?>
                    <article class="buyora-catalog-card">
                        <a class="buyora-catalog-image" href="<?= htmlspecialchars($itemUrl, ENT_QUOTES, 'UTF-8') ?>"><img src="<?= htmlspecialchars($itemImage, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>"></a>
                        <div class="buyora-catalog-info">
                            <a class="buyora-catalog-title" href="<?= htmlspecialchars($itemUrl, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></a>
                            <strong><?= trim(CURRENCY) ?> <?= htmlspecialchars(number_format((float) $item['price'], floor((float) $item['price']) === (float) $item['price'] ? 0 : 2), ENT_QUOTES, 'UTF-8') ?></strong>
                        </div>
                    </article>
                <?php } ?>
            </div>
        </section>
    <?php } ?>

    <section class="buyora-related-products">
        <div class="buyora-page-heading"><h2><?= htmlspecialchars(lang('oder_from_category'), ENT_QUOTES, 'UTF-8') ?></h2></div>
        <?php if (!empty($sameCagegoryProducts)) { ?>
            <div class="buyora-catalog-grid">
                <?php foreach ($sameCagegoryProducts as $item) {
                    $itemImage = !empty($item['image']) && is_file('attachments/shop_images/' . $item['image'])
                        ? base_url('attachments/shop_images/' . rawurlencode($item['image']))
                        : base_url('attachments/no-image-frontend.png');
                    $itemUrl = !empty($item['vendor_url'])
                        ? LANG_URL . '/' . rawurlencode($item['vendor_url']) . '/' . $item['url'] . '_' . (int) $item['id']
                        : LANG_URL . '/' . $item['url'] . '_' . (int) $item['id'];
                    ?>
                    <article class="buyora-catalog-card">
                        <a class="buyora-catalog-image" href="<?= htmlspecialchars($itemUrl, ENT_QUOTES, 'UTF-8') ?>"><img src="<?= htmlspecialchars($itemImage, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>"></a>
                        <div class="buyora-catalog-info">
                            <a class="buyora-catalog-title" href="<?= htmlspecialchars($itemUrl, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></a>
                            <strong><?= trim(CURRENCY) ?> <?= htmlspecialchars(number_format((float) $item['price'], floor((float) $item['price']) === (float) $item['price'] ? 0 : 2), ENT_QUOTES, 'UTF-8') ?></strong>
                        </div>
                    </article>
                <?php } ?>
            </div>
        <?php } else { ?>
            <p class="buyora-empty-state"><?= htmlspecialchars(lang('no_same_category_products'), ENT_QUOTES, 'UTF-8') ?></p>
        <?php } ?>
    </section>
</main>
<script>
document.querySelectorAll('[data-product-image]').forEach(function (thumbnail) {
    thumbnail.addEventListener('click', function () {
        document.getElementById('buyora-product-main-image').src = thumbnail.getAttribute('data-product-image');
        document.querySelectorAll('.buyora-product-thumbnails button').forEach(function (button) {
            button.classList.toggle('is-selected', button === thumbnail);
        });
    });
});
</script>
