<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$categories = isset($all_categories) && is_array($all_categories) ? $all_categories : array();
$homeBanners = isset($homeBanners) && is_array($homeBanners) ? $homeBanners : array();
$heroBanner = null;
$promoBanners = array();
foreach ($homeBanners as $banner) {
    if ($banner['placement'] === 'hero' && $heroBanner === null) {
        $heroBanner = $banner;
    } elseif ($banner['placement'] === 'promo' && count($promoBanners) < 2) {
        $promoBanners[] = $banner;
    }
}
$bannerImageUrl = function ($path, $fallback) {
    $path = ltrim((string) $path, '/');
    if ($path === '' || strpos($path, '..') !== false
        || !preg_match('~^[A-Za-z0-9_./-]+$~', $path)
        || !is_file($path)) {
        $path = $fallback;
    }
    $webpPath = preg_replace('~\.(jpe?g|png)$~i', '.webp', $path);
    if (is_file($webpPath)) {
        $path = $webpPath;
    }
    return base_url($path);
};
$heroImageUrl = $bannerImageUrl($heroBanner ? $heroBanner['image'] : '', 'assets/buyora/buyora-hero.webp');
$heroMobileImageUrl = $heroBanner && !empty($heroBanner['mobile_image'])
    ? $bannerImageUrl($heroBanner['mobile_image'], 'assets/buyora/buyora-hero-photo.webp')
    : base_url('assets/buyora/buyora-hero-photo.webp');
$catalogFilters = array('search_in_title', 'search_in_body', 'category', 'brand_id', 'in_stock', 'price_from', 'price_to', 'quantity_more', 'added_after', 'added_before', 'order_new', 'order_price', 'order_procurement');
$hasCatalogFilters = false;
foreach ($catalogFilters as $filterName) {
    if (isset($_GET[$filterName]) && is_scalar($_GET[$filterName]) && trim((string) $_GET[$filterName]) !== '') {
        $hasCatalogFilters = true;
        break;
    }
}
$productsById = array();
$formatPrice = function ($amount) {
    $amount = (float) $amount;
    $decimals = floor($amount) === $amount ? 0 : 2;
    return trim(CURRENCY) . ' ' . number_format($amount, $decimals);
};
$addProducts = function ($articles, $collection) use (&$productsById, $formatPrice) {
    if (!is_array($articles)) {
        return;
    }
    foreach ($articles as $article) {
        if (!isset($article['id'], $article['title'], $article['url'], $article['price'])) {
            continue;
        }
        $id = (int) $article['id'];
        if ($id < 1) {
            continue;
        }
        if (!isset($productsById[$id])) {
            $imageName = isset($article['image']) ? basename($article['image']) : '';
            $imagePath = $imageName !== '' && strpos($imageName, '..') === false && is_file('attachments/shop_images/' . $imageName)
                ? base_url('attachments/shop_images/' . rawurlencode($imageName))
                : base_url('attachments/no-image-frontend.png');
            $currentPrice = is_numeric($article['price']) ? (float) $article['price'] : null;
            $oldPrice = isset($article['old_price']) && is_numeric($article['old_price']) ? (float) $article['old_price'] : null;
            $discount = $currentPrice !== null && $oldPrice !== null && $oldPrice > $currentPrice
                ? '-' . (int) round(($oldPrice - $currentPrice) / $oldPrice * 100) . '%'
                : '';
            $productsById[$id] = array(
                'title' => $article['title'],
                'price' => $formatPrice($article['price']),
                'old_price' => $discount !== '' ? $formatPrice($article['old_price']) : '',
                'image' => $imagePath,
                'url' => !empty($article['vendor_url'])
                    ? LANG_URL . '/' . rawurlencode($article['vendor_url']) . '/' . $article['url'] . '_' . $id
                    : LANG_URL . '/' . $article['url'] . '_' . $id,
                'id' => $id,
                'quantity' => isset($article['quantity']) ? max(0, (int) $article['quantity']) : 0,
                'badge' => $discount,
                'collection' => '',
                'embedded_ui' => false
            );
        }
        $collections = explode(' ', $productsById[$id]['collection']);
        if (!in_array($collection, $collections, true)) {
            $collections[] = $collection;
            $productsById[$id]['collection'] = trim(implode(' ', $collections));
        }
    }
};
if ($hasCatalogFilters) {
    $addProducts(isset($products) ? $products : array(), 'results');
} else {
    $addProducts(isset($sliderProducts) ? $sliderProducts : array(), 'bestsellers');
    $addProducts(isset($bestSellers) ? $bestSellers : array(), 'bestsellers');
    $addProducts(isset($newProducts) ? $newProducts : array(), 'new');
    $addProducts(isset($dealProducts) ? $dealProducts : array(), 'deals');
}
$featuredProducts = array_values($productsById);
$hasBestSellers = false;
$hasNewProducts = false;
$hasDeals = false;
foreach ($featuredProducts as $product) {
    $collections = explode(' ', $product['collection']);
    $hasBestSellers = $hasBestSellers || in_array('bestsellers', $collections, true);
    $hasNewProducts = $hasNewProducts || in_array('new', $collections, true);
    $hasDeals = $hasDeals || in_array('deals', $collections, true);
}
$defaultCollection = $hasBestSellers ? 'bestsellers' : ($hasNewProducts ? 'new' : 'deals');
?>
<main id="main-content" class="buyora-main">
    <section class="hero-section" aria-label="Trending products" <?= $heroMobileImageUrl !== '' ? 'style="--buyora-hero-mobile-image: url(\'' . htmlspecialchars($heroMobileImageUrl, ENT_QUOTES, 'UTF-8') . '\')"' : '' ?>>
        <img class="hero-desktop" src="<?= htmlspecialchars($heroImageUrl, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($heroBanner ? $heroBanner['alt_text'] : 'Everything you need in one place', ENT_QUOTES, 'UTF-8') ?>" width="1250" height="230" fetchpriority="high" decoding="async">
        <div class="hero-mobile">
            <span class="hero-eyebrow">Trending Products</span>
            <h1>Everything You Need<br>In One Place</h1>
            <p>Fashion <span>|</span> Electronics <span>|</span> Home <span>|</span> Beauty</p>
            <a href="<?= htmlspecialchars($heroBanner ? $heroBanner['target_url'] : base_url('shop'), ENT_QUOTES, 'UTF-8') ?>" class="hero-shop-button">Shop Now <i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
        </div>
        <a class="hero-link" href="<?= htmlspecialchars($heroBanner ? $heroBanner['target_url'] : base_url('shop'), ENT_QUOTES, 'UTF-8') ?>" aria-label="Shop trending products"></a>
    </section>

    <section class="buyora-container categories-section" id="categories" aria-labelledby="categories-title">
        <div class="section-heading">
            <h2 id="categories-title">Shop By Category</h2>
            <a href="<?= base_url('shop') ?>">View All <i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
        </div>
        <div class="category-grid">
            <?php if (empty($categories)) { ?>
                <p class="empty-state">No categories are available right now.</p>
            <?php } ?>
            <?php foreach ($categories as $category) { ?>
                <?php
                $categoryImageName = isset($category['image']) ? basename($category['image']) : '';
                $categoryImagePath = $categoryImageName !== '' && strpos($categoryImageName, '..') === false && is_file('attachments/shop_images/' . $categoryImageName)
                    ? base_url('attachments/shop_images/' . rawurlencode($categoryImageName))
                    : base_url('assets/buyora/category-home.webp');
                $categorySlug = !empty($category['name']) ? url_title($category['name'], 'dash', true) : 'all';
                $categoryLink = base_url('category/' . (int) $category['id'] . '/' . $categorySlug);
                $categoryCount = isset($category['product_count']) ? (int) $category['product_count'] : 0;
                ?>
                <a class="category-card" href="<?= htmlspecialchars($categoryLink, ENT_QUOTES, 'UTF-8') ?>" title="<?= $categoryCount ?> products">
                    <img src="<?= htmlspecialchars($categoryImagePath, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?>" width="82" height="82" loading="lazy" decoding="async">
                    <span><?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?></span>
                </a>
            <?php } ?>
        </div>
    </section>

    <section class="service-strip" aria-label="Shopping benefits">
        <div class="buyora-container service-grid">
            <div class="service-item">
                <i class="fa fa-truck" aria-hidden="true"></i>
                <div><strong>Free Shipping</strong><span>On Orders Above Rs. 2000</span></div>
            </div>
            <div class="service-item">
                <i class="fa fa-shield" aria-hidden="true"></i>
                <div><strong>Secure Payments</strong><span>100% Secure Payment</span></div>
            </div>
            <div class="service-item">
                <i class="fa fa-certificate" aria-hidden="true"></i>
                <div><strong>Best Quality</strong><span>Premium Products</span></div>
            </div>
            <div class="service-item">
                <i class="fa fa-headphones" aria-hidden="true"></i>
                <div><strong>24/7 Support</strong><span>We’re here to help</span></div>
            </div>
            <div class="service-item">
                <i class="fa fa-reply" aria-hidden="true"></i>
                <div><strong>Easy Returns</strong><span>Hassle Free Returns</span></div>
            </div>
        </div>
    </section>

    <section class="buyora-container featured-section" id="featured" aria-labelledby="featured-title">
        <div class="section-heading featured-heading">
            <h2 id="featured-title"><?= $hasCatalogFilters ? 'Search Results' : 'Featured Products' ?></h2>
            <?php if (!$hasCatalogFilters) { ?>
                <div class="product-tabs" role="tablist" aria-label="Product collections">
                    <button class="product-tab <?= $defaultCollection === 'bestsellers' ? 'is-active' : '' ?>" type="button" role="tab" aria-selected="<?= $defaultCollection === 'bestsellers' ? 'true' : 'false' ?>" data-collection="bestsellers">Best Sellers</button>
                    <button class="product-tab <?= $defaultCollection === 'new' ? 'is-active' : '' ?>" type="button" role="tab" aria-selected="<?= $defaultCollection === 'new' ? 'true' : 'false' ?>" data-collection="new">New Arrivals</button>
                    <button class="product-tab <?= $defaultCollection === 'deals' ? 'is-active' : '' ?>" type="button" role="tab" aria-selected="<?= $defaultCollection === 'deals' ? 'true' : 'false' ?>" data-collection="deals">Deals</button>
                </div>
            <?php } ?>
        </div>
        <div class="product-grid">
            <?php if (empty($featuredProducts)) { ?>
                <p class="empty-state"><?= $hasCatalogFilters ? 'No products matched your search or filters.' : 'No products are available in this section yet.' ?></p>
            <?php } ?>
            <?php foreach ($featuredProducts as $product) { ?>
                <article class="product-card" data-product-collection="<?= htmlspecialchars($product['collection'], ENT_QUOTES, 'UTF-8') ?>">
                    <a class="product-image" href="<?= htmlspecialchars($product['url'], ENT_QUOTES, 'UTF-8') ?>">
                        <img src="<?= htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['title'], ENT_QUOTES, 'UTF-8') ?>" width="180" height="150" loading="lazy" decoding="async">
                    </a>
                    <div class="product-info">
                        <a class="product-title" href="<?= htmlspecialchars($product['url'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($product['title'], ENT_QUOTES, 'UTF-8') ?></a>
                        <div class="product-rating"><span>★★★★★</span><small>(<?= 40 + ((int) $product['id'] % 90) ?>)</small></div>
                        <div class="product-price">
                            <strong><?= htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8') ?></strong>
                            <?php if (!empty($product['old_price'])) { ?><del><?= htmlspecialchars($product['old_price'], ENT_QUOTES, 'UTF-8') ?></del><?php } ?>
                        </div>
                        <?php if ($product['quantity'] > 0) { ?>
                            <a class="add-to-cart product-add-button" data-goto="<?= base_url('checkout') ?>" data-id="<?= (int) $product['id'] ?>" href="<?= htmlspecialchars($product['url'], ENT_QUOTES, 'UTF-8') ?>"><i class="fa fa-cart-plus" aria-hidden="true"></i> Add to Cart</a>
                        <?php } else { ?>
                            <button class="product-add-button demo-product-button" type="button" disabled title="This product is out of stock" aria-label="<?= htmlspecialchars($product['title'], ENT_QUOTES, 'UTF-8') ?> — out of stock"><i class="fa fa-cart-plus" aria-hidden="true"></i> Out of Stock</button>
                        <?php } ?>
                    </div>
                </article>
            <?php } ?>
        </div>
    </section>

    <section class="buyora-container promo-grid" aria-label="Current promotions">
        <?php foreach ($promoBanners as $index => $banner) { ?>
            <a class="promo-card <?= $index === 0 ? 'promo-electronics' : 'promo-beauty' ?>" href="<?= htmlspecialchars($banner['target_url'], ENT_QUOTES, 'UTF-8') ?>" aria-label="<?= htmlspecialchars($banner['alt_text'], ENT_QUOTES, 'UTF-8') ?>">
                <img src="<?= htmlspecialchars($bannerImageUrl($banner['image'], 'assets/buyora/promo-electronics.jpg'), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($banner['alt_text'], ENT_QUOTES, 'UTF-8') ?>" width="615" height="165" loading="lazy" decoding="async">
            </a>
        <?php } ?>
    </section>
</main>
