<!DOCTYPE html>
<html lang="<?= MY_LANGUAGE_ABBR ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= htmlspecialchars($description ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <meta name="keywords" content="<?= htmlspecialchars($keywords ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:title" content="<?= htmlspecialchars($title ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($description ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:url" content="<?= LANG_URL ?>">
    <meta property="og:type" content="website">
    <title><?= htmlspecialchars($title ?? 'BUYORA | Shop More, Live Better', ENT_QUOTES, 'UTF-8') ?></title>
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
                    <a class="all-categories" href="<?= htmlspecialchars(base_url() . '#categories', ENT_QUOTES, 'UTF-8') ?>"><i class="fa fa-bars" aria-hidden="true"></i> All Categories <i class="fa fa-angle-down" aria-hidden="true"></i></a>
                    <a class="nav-link is-current" href="<?= base_url() ?>">Home</a>
                    <a class="nav-link" href="<?= htmlspecialchars(LANG_URL . '/shop', ENT_QUOTES, 'UTF-8') ?>">Shop</a>
                    <a class="nav-link" href="<?= htmlspecialchars(base_url() . '#featured', ENT_QUOTES, 'UTF-8') ?>">Deals</a>
                    <a class="nav-link" href="<?= htmlspecialchars(base_url() . '#featured', ENT_QUOTES, 'UTF-8') ?>">New Arrivals</a>
                    <?php
                    $navCategoryCount = 0;
                    foreach (isset($buyora_categories) && is_array($buyora_categories) ? $buyora_categories : array() as $navCategory) {
                        if ((int) $navCategory['sub_for'] !== 0 || $navCategoryCount >= 6) {
                            continue;
                        }
                        ?>
                        <a class="nav-link" href="<?= htmlspecialchars(LANG_URL . '?category=' . (int) $navCategory['id'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($navCategory['name'], ENT_QUOTES, 'UTF-8') ?></a>
                        <?php
                        $navCategoryCount++;
                    }
                    ?>
                    <a class="nav-link" href="<?= LANG_URL . '/contacts' ?>">Contact</a>
                </div>
            </nav>
