<!DOCTYPE html>
<html lang="<?= MY_LANGUAGE_ABBR ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<?php
$siteName = 'BUYORA Marketplace';
$defaultTitle = 'BUYORA | Shop More, Live Better — Online Marketplace';
$defaultDesc = 'Shop top electronics, fashion, beauty, home goods & more on BUYORA. Enjoy verified sellers, secure card payments, and fast delivery.';
$defaultKeywords = 'ecommerce, online shopping, buyora, marketplace, electronics, fashion, beauty, deals';

$pageTitle = !empty($title) ? trim($title) : $defaultTitle;
if (!empty($title) && stripos($title, 'BUYORA') === false) {
    $metaTitle = $pageTitle . ' | BUYORA';
} else {
    $metaTitle = $pageTitle;
}

$pageDesc = !empty($description) ? trim(strip_tags($description)) : $defaultDesc;
if (mb_strlen($pageDesc) > 165) {
    $pageDesc = mb_substr($pageDesc, 0, 162) . '...';
}

$pageKeywords = !empty($keywords) ? trim($keywords) : $defaultKeywords;

if (!empty($og_image)) {
    $shareImage = (strpos($og_image, 'http://') === 0 || strpos($og_image, 'https://') === 0) ? $og_image : base_url($og_image);
} elseif (!empty($image)) {
    $shareImage = (strpos($image, 'http://') === 0 || strpos($image, 'https://') === 0) ? $image : base_url('attachments/shop_images/' . $image);
} else {
    $shareImage = base_url('assets/buyora/buyora-og.jpg');
}

$canonicalUrl = function_exists('current_url') ? current_url() : LANG_URL;
if (empty($canonicalUrl) || $canonicalUrl === base_url()) {
    $canonicalUrl = rtrim(base_url(), '/') . '/';
}
?>
    <!-- Primary SEO Meta Tags -->
    <title><?= htmlspecialchars($metaTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="title" content="<?= htmlspecialchars($metaTitle, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="description" content="<?= htmlspecialchars($pageDesc, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="keywords" content="<?= htmlspecialchars($pageKeywords, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="author" content="BUYORA Marketplace">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8') ?>">

    <!-- Favicon & Mobile Icons -->
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>" type="image/x-icon">
    <link rel="icon" href="<?= base_url('favicon.ico') ?>" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/buyora/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/buyora/favicon-16x16.png') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/buyora/apple-touch-icon.png') ?>">
    <link rel="manifest" href="<?= base_url('site.webmanifest') ?>">
    <meta name="theme-color" content="#10b981">
    <meta name="msapplication-TileColor" content="#0f172a">

    <!-- Open Graph / Facebook / WhatsApp / LinkedIn -->
    <meta property="og:type" content="<?= !empty($og_type) ? htmlspecialchars($og_type, ENT_QUOTES, 'UTF-8') : 'website' ?>">
    <meta property="og:site_name" content="<?= htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:title" content="<?= htmlspecialchars($metaTitle, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDesc, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:image" content="<?= htmlspecialchars($shareImage, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:image:secure_url" content="<?= htmlspecialchars($shareImage, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="<?= htmlspecialchars($metaTitle, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:locale" content="en_US">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?= htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:title" content="<?= htmlspecialchars($metaTitle, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($pageDesc, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($shareImage, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:image:alt" content="<?= htmlspecialchars($metaTitle, ENT_QUOTES, 'UTF-8') ?>">

    <!-- Schema.org JSON-LD Structured Data for Google SEO -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "name": "BUYORA Marketplace",
      "url": "<?= rtrim(base_url(), '/') ?>/",
      "potentialAction": {
        "@type": "SearchAction",
        "target": "<?= rtrim(base_url(), '/') ?>/?search_in_title={search_term_string}",
        "query-input": "required name=search_term_string"
      }
    }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/font-awesome.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/buyora/buyora.css') ?>">
    <script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/buyora/lang.js') ?>"></script>
</head>
<body class="buyora-site">
    <a class="buyora-skip-link" href="#main-content">Skip to content</a>
    <div id="wrapper">
        <div id="content">
            <div class="buyora-utility">
                <div class="buyora-container utility-inner">
                    <span class="shipping-note"><i class="fa fa-truck" aria-hidden="true"></i> Free Shipping on Orders Above Rs. 2000</span>
                    <div class="utility-links">
                        <a href="<?= LANG_URL . '/checkout' ?>"><i class="fa fa-map-marker" aria-hidden="true"></i> Track Order</a>
                        <a href="<?= LANG_URL . '/contacts' ?>"><i class="fa fa-question-circle-o" aria-hidden="true"></i> Help</a>
                        <a href="<?= LANG_URL . '/login' ?>"><i class="fa fa-user" aria-hidden="true"></i> Login / Register</a>
                    </div>
                </div>
            </div>
            <header class="buyora-header">
                <div class="buyora-container masthead">
                    <a class="buyora-brand" href="<?= base_url() ?>" aria-label="BUYORA home">
                        <img src="<?= base_url('assets/buyora/buyora-logo.png') ?>" alt="BUYORA — Shop More, Live Better">
                    </a>
                    <form class="buyora-search" id="bigger-search" method="get" action="<?= htmlspecialchars(isset($vendor_url) && $vendor_url !== '' ? $vendor_url : LANG_URL, ENT_QUOTES, 'UTF-8') ?>" role="search">
                        <label class="sr-only" for="search_in_title">Search for products</label>
                        <input type="search" name="search_in_title" id="search_in_title" value="<?= isset($_GET['search_in_title']) ? htmlspecialchars($_GET['search_in_title'], ENT_QUOTES, 'UTF-8') : '' ?>" placeholder="Search for products, brands and more...">
                        <button type="submit" aria-label="Search"><i class="fa fa-search" aria-hidden="true"></i></button>
                    </form>
                    <div class="header-actions">
                        <button class="icon-action favorite-header" type="button" aria-label="Your favourites"><i class="fa fa-heart-o" aria-hidden="true"></i></button>
                        <a class="icon-action account-action" href="<?= LANG_URL . '/myaccount' ?>" aria-label="Your account"><i class="fa fa-user-o" aria-hidden="true"></i></a>
                        <a class="icon-action cart-action" href="<?= LANG_URL . '/shopping-cart' ?>" aria-label="Shopping cart">
                            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                            <span class="cart-count sumOfItems"><?= is_numeric($cartItems) && (int) $cartItems === 0 ? 0 : (int) $sumOfItems ?></span>
                        </a>
                        <button class="mobile-menu-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation" aria-label="Open menu">
                            <i class="fa fa-bars" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </header>
            <nav class="buyora-navigation" id="primary-navigation" aria-label="Main navigation">
                <div class="buyora-container nav-inner">
                    <div class="all-categories-menu">
                        <button class="all-categories" type="button" aria-expanded="false" aria-controls="all-categories-list">
                            <i class="fa fa-bars" aria-hidden="true"></i> All Categories <i class="fa fa-angle-down" aria-hidden="true"></i>
                        </button>
                        <div class="categories-dropdown" id="all-categories-list">
                            <?php
                            $categoryTree = isset($buyora_category_tree) && is_array($buyora_category_tree) ? $buyora_category_tree : array();
                            if (empty($categoryTree)) { ?>
                                <p class="categories-dropdown-empty">No categories yet.</p>
                            <?php }
                            foreach ($categoryTree as $parentCategory) { ?>
                                <div class="categories-dropdown-group">
                                    <a href="<?= htmlspecialchars(LANG_URL . '?category=' . (int) $parentCategory['id'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($parentCategory['name'], ENT_QUOTES, 'UTF-8') ?></a>
                                    <?php if (!empty($parentCategory['children'])) { ?>
                                        <?php foreach ($parentCategory['children'] as $childCategory) { ?>
                                            <a class="is-child" href="<?= htmlspecialchars(LANG_URL . '?category=' . (int) $childCategory['id'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($childCategory['name'], ENT_QUOTES, 'UTF-8') ?></a>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <a class="nav-link is-current" href="<?= base_url() ?>">Home</a>
                    <a class="nav-link" href="<?= htmlspecialchars(LANG_URL . '/shop', ENT_QUOTES, 'UTF-8') ?>">Shop</a>
                    <a class="nav-link" href="<?= htmlspecialchars(base_url() . '#featured', ENT_QUOTES, 'UTF-8') ?>">Deals</a>
                    <a class="nav-link" href="<?= htmlspecialchars(base_url() . '#featured', ENT_QUOTES, 'UTF-8') ?>">New Arrivals</a>
                    <?php foreach (isset($buyora_header_categories) && is_array($buyora_header_categories) ? $buyora_header_categories : array() as $navCategory) { ?>
                        <a class="nav-link" href="<?= htmlspecialchars(LANG_URL . '?category=' . (int) $navCategory['id'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($navCategory['name'], ENT_QUOTES, 'UTF-8') ?></a>
                    <?php } ?>
                    <a class="nav-link" href="<?= LANG_URL . '/contacts' ?>">Contact</a>
                </div>
            </nav>
