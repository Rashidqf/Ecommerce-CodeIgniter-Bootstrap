<?php

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Run this sample-data seeder from the command line.\n");
    exit(1);
}

$root = dirname(__DIR__, 2);
define('BASEPATH', $root . '/system/');
define('ENVIRONMENT', 'development');
require $root . '/application/config/database.php';
$connection = $db[$active_group];
$store = new mysqli(
    $connection['hostname'],
    $connection['username'],
    $connection['password'],
    $connection['database']
);

if ($store->connect_errno) {
    fwrite(STDERR, "Could not connect to the configured store database.\n");
    exit(1);
}

$store->set_charset($connection['char_set']);

function buyoraSeedStatement($store, $sql, $types = '', $values = array())
{
    $statement = $store->prepare($sql);
    if ($statement === false) {
        throw new RuntimeException($store->error);
    }

    if ($types !== '') {
        $arguments = array($types);
        foreach ($values as $key => &$value) {
            $arguments[] = &$value;
        }
        call_user_func_array(array($statement, 'bind_param'), $arguments);
    }

    if (!$statement->execute()) {
        throw new RuntimeException($statement->error);
    }

    return $statement;
}

function buyoraSeedImage($root, $source, $targetDirectory, $targetName)
{
    $sourcePath = $root . '/assets/buyora/' . $source;
    if (!is_file($sourcePath)) {
        throw new RuntimeException('Sample image is missing: ' . $source);
    }

    if (!is_dir($targetDirectory) && !mkdir($targetDirectory, 0755, true) && !is_dir($targetDirectory)) {
        throw new RuntimeException('Could not create the sample image directory.');
    }

    $targetPath = $targetDirectory . DIRECTORY_SEPARATOR . $targetName;
    if (!is_file($targetPath) && !copy($sourcePath, $targetPath)) {
        throw new RuntimeException('Could not copy sample image: ' . $source);
    }

    return $targetName;
}

try {
    $languages = array();
    $languageQuery = $store->query('SELECT abbr FROM languages ORDER BY id');
    while ($language = $languageQuery->fetch_assoc()) {
        $languages[] = $language['abbr'];
    }
    if (!in_array('en', $languages, true)) {
        throw new RuntimeException('The store needs an English language entry before seeding the sample catalog.');
    }

    $imageDirectory = $root . '/attachments/shop_images';
    $categories = array(
        array('Fashion', 'category-fashion.jpg'),
        array('Electronics', 'category-electronics.jpg'),
        array('Beauty & Health', 'category-beauty.jpg'),
        array('Home & Living', 'category-home.jpg'),
        array('Toys & Games', 'category-toys.jpg'),
        array('Sports & Fitness', 'category-sports.jpg'),
        array('Kids & Baby', 'category-baby.jpg'),
        array('Groceries', 'category-groceries.jpg'),
        array('Automotive', 'category-auto.jpg')
    );

    $categoryIds = array();
    foreach ($categories as $position => $category) {
        $imageName = buyoraSeedImage(
            $root,
            $category[1],
            $imageDirectory,
            'buyora-' . strtolower(str_replace(array(' ', '&'), array('-', 'and'), $category[0])) . '.jpg'
        );
        $lookup = buyoraSeedStatement(
            $store,
            'SELECT c.id FROM shop_categories c INNER JOIN shop_categories_translations t ON t.for_id = c.id WHERE t.abbr = ? AND t.name = ? ORDER BY c.id LIMIT 1',
            'ss',
            array('en', $category[0])
        );
        $lookup->bind_result($categoryId);
        if ($lookup->fetch()) {
            $categoryIds[$category[0]] = (int) $categoryId;
            $lookup->close();
            foreach ($languages as $languageAbbr) {
                buyoraSeedStatement(
                    $store,
                    'INSERT INTO shop_categories_translations (name, abbr, for_id) SELECT ?, ?, ? FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM shop_categories_translations WHERE abbr = ? AND for_id = ?)',
                    'ssisi',
                    array($category[0], $languageAbbr, $categoryIds[$category[0]], $languageAbbr, $categoryIds[$category[0]])
                )->close();
            }
            continue;
        }
        $lookup->close();

        buyoraSeedStatement(
            $store,
            'INSERT INTO shop_categories (sub_for, position, image) VALUES (0, ?, ?)',
            'is',
            array($position + 1, $imageName)
        )->close();
        $categoryId = (int) $store->insert_id;

        foreach ($languages as $languageAbbr) {
            buyoraSeedStatement(
                $store,
                'INSERT INTO shop_categories_translations (name, abbr, for_id) VALUES (?, ?, ?)',
                'ssi',
                array($category[0], $languageAbbr, $categoryId)
            )->close();
        }
        $categoryIds[$category[0]] = $categoryId;
    }

    $products = array(
        array('Wireless Earbuds', 'Electronics', 'product-earbuds.jpg', '3999.00', '4999.00', 124, 0, 1, 'Compact wireless earbuds with clear sound, a charging case and all-day comfort.'),
        array('Men’s Running Shoes', 'Fashion', 'product-shoes.jpg', '5499.00', '6999.00', 89, 0, 1, 'Lightweight running shoes with breathable uppers and cushioned everyday support.'),
        array('Smart Watch', 'Electronics', 'product-watch.jpg', '8499.00', '9999.00', 67, 0, 1, 'A versatile smart watch with activity tracking, notifications and a bright display.'),
        array('Makeup Kit', 'Beauty & Health', 'product-makeup.jpg', '2799.00', '3499.00', 112, 0, 1, 'A practical beauty kit with everyday shades for an easy, polished look.'),
        array('Backpack', 'Fashion', 'product-backpack.jpg', '4299.00', '5499.00', 76, 0, 0, 'A roomy everyday backpack with organized storage for work, travel or school.'),
        array('Home Decor Lamp', 'Home & Living', 'product-lamp.jpg', '3199.00', '3549.00', 45, 0, 0, 'A warm accent lamp designed to bring a soft, comfortable glow to your room.')
    );

    foreach ($products as $position => $product) {
        if (!isset($categoryIds[$product[1]])) {
            throw new RuntimeException('Missing sample category: ' . $product[1]);
        }

        $imageName = buyoraSeedImage(
            $root,
            $product[2],
            $imageDirectory,
            'buyora-product-' . ($position + 1) . '.jpg'
        );
        $categoryId = $categoryIds[$product[1]];
        $lookup = buyoraSeedStatement(
            $store,
            'SELECT p.id FROM products p INNER JOIN products_translations t ON t.for_id = p.id WHERE p.shop_categorie = ? AND t.abbr = ? AND t.title = ? ORDER BY p.id LIMIT 1',
            'iss',
            array($categoryId, 'en', $product[0])
        );
        $lookup->bind_result($productId);
        if ($lookup->fetch()) {
            $productId = (int) $productId;
            $lookup->close();
            buyoraSeedStatement(
                $store,
                'UPDATE products SET procurement = 0 WHERE id = ? AND image LIKE ?',
                'is',
                array($productId, 'buyora-product-%')
            )->close();
            continue;
        }
        $lookup->close();

        $created = time();
        buyoraSeedStatement(
            $store,
            'INSERT INTO products (folder, image, time, time_update, visibility, shop_categorie, quantity, procurement, in_slider, url, virtual_products, brand_id, position, vendor_id) VALUES (NULL, ?, ?, ?, 1, ?, ?, ?, ?, ?, NULL, NULL, ?, 0)',
            'siiiiiisi',
            array($imageName, $created, $created, $categoryId, $product[5], $product[6], $product[7], '', $position + 1)
        )->close();

        $productId = (int) $store->insert_id;
        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '_', $product[0]), '_'));
        buyoraSeedStatement(
            $store,
            'UPDATE products SET url = ? WHERE id = ?',
            'si',
            array($slug . '_' . $productId, $productId)
        )->close();

        foreach ($languages as $languageAbbr) {
            buyoraSeedStatement(
                $store,
                'INSERT INTO products_translations (title, description, basic_description, price, old_price, abbr, for_id) VALUES (?, ?, ?, ?, ?, ?, ?)',
                'ssssssi',
                array($product[0], $product[8], $product[8], $product[3], $product[4], $languageAbbr, $productId)
            )->close();
        }
    }

    $heroImage = 'assets/buyora/buyora-hero.jpg';
    $heroMobileImage = 'assets/buyora/buyora-hero-photo.jpg';
    $bannerRows = array(
        array('hero', $heroImage, $heroMobileImage, 'Everything you need in one place', '#featured', 1),
        array('promo', 'assets/buyora/promo-electronics.jpg', null, 'Limited-time offers on selected electronics', '/?category=' . $categoryIds['Electronics'], 1),
        array('promo', 'assets/buyora/promo-beauty.jpg', null, 'Beauty and personal care', '/?category=' . $categoryIds['Beauty & Health'], 2)
    );
    foreach ($bannerRows as $banner) {
        $lookup = buyoraSeedStatement(
            $store,
            'SELECT id FROM home_banners WHERE placement = ? AND alt_text = ? LIMIT 1',
            'ss',
            array($banner[0], $banner[3])
        );
        $lookup->bind_result($bannerId);
        if ($lookup->fetch()) {
            $lookup->close();
            continue;
        }
        $lookup->close();
        buyoraSeedStatement(
            $store,
            'INSERT INTO home_banners (placement, image, mobile_image, alt_text, target_url, position, visible) VALUES (?, ?, ?, ?, ?, ?, 1)',
            'sssssi',
            array($banner[0], $banner[1], $banner[2], $banner[3], $banner[4], $banner[5])
        )->close();
    }

    echo "Sample store is ready. Existing matching records were kept unchanged.\n";
    echo count($categories) . " categories and " . count($products) . " sample products are available.\n";
} catch (Throwable $error) {
    fwrite(STDERR, 'Sample data was not completed: ' . $error->getMessage() . "\n");
    exit(1);
} finally {
    $store->close();
}
