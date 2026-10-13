-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table shop.active_pages
DROP TABLE IF EXISTS `active_pages`;
CREATE TABLE IF NOT EXISTS `active_pages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.active_pages: ~1 rows (approximately)
INSERT INTO `active_pages` (`id`, `name`, `enabled`) VALUES
	(1, 'blog', 1);

-- Dumping structure for table shop.bank_accounts
DROP TABLE IF EXISTS `bank_accounts`;
CREATE TABLE IF NOT EXISTS `bank_accounts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `iban` varchar(255) NOT NULL,
  `bank` varchar(255) NOT NULL,
  `bic` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.bank_accounts: ~0 rows (approximately)

-- Dumping structure for table shop.blog_posts
DROP TABLE IF EXISTS `blog_posts`;
CREATE TABLE IF NOT EXISTS `blog_posts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `image` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `time` int unsigned NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.blog_posts: 0 rows
/*!40000 ALTER TABLE `blog_posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `blog_posts` ENABLE KEYS */;

-- Dumping structure for table shop.blog_translations
DROP TABLE IF EXISTS `blog_translations`;
CREATE TABLE IF NOT EXISTS `blog_translations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `abbr` varchar(5) NOT NULL,
  `for_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.blog_translations: ~0 rows (approximately)

-- Dumping structure for table shop.brands
DROP TABLE IF EXISTS `brands`;
CREATE TABLE IF NOT EXISTS `brands` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.brands: ~0 rows (approximately)

-- Dumping structure for table shop.confirm_links
DROP TABLE IF EXISTS `confirm_links`;
CREATE TABLE IF NOT EXISTS `confirm_links` (
  `id` int NOT NULL AUTO_INCREMENT,
  `link` char(32) NOT NULL,
  `for_order` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.confirm_links: ~2 rows (approximately)
INSERT INTO `confirm_links` (`id`, `link`, `for_order`) VALUES
	(1, '1f33df7e51685b4bc0cf14e9840a82d4', 1234),
	(2, '71a09e4d41b685ace87cf33c08a2ac98', 1235);

-- Dumping structure for table shop.cookie_law
DROP TABLE IF EXISTS `cookie_law`;
CREATE TABLE IF NOT EXISTS `cookie_law` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `link` varchar(255) NOT NULL,
  `theme` varchar(20) NOT NULL,
  `visibility` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.cookie_law: ~0 rows (approximately)

-- Dumping structure for table shop.cookie_law_translations
DROP TABLE IF EXISTS `cookie_law_translations`;
CREATE TABLE IF NOT EXISTS `cookie_law_translations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `message` varchar(255) NOT NULL,
  `button_text` varchar(50) NOT NULL,
  `learn_more` varchar(50) NOT NULL,
  `abbr` varchar(5) NOT NULL,
  `for_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQUE` (`abbr`,`for_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.cookie_law_translations: ~0 rows (approximately)

-- Dumping structure for table shop.discount_codes
DROP TABLE IF EXISTS `discount_codes`;
CREATE TABLE IF NOT EXISTS `discount_codes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(10) NOT NULL,
  `code` varchar(10) NOT NULL,
  `amount` varchar(20) NOT NULL,
  `valid_from_date` int unsigned NOT NULL,
  `valid_to_date` int unsigned NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-enabled, 0-disabled',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.discount_codes: ~0 rows (approximately)

-- Dumping structure for table shop.history
DROP TABLE IF EXISTS `history`;
CREATE TABLE IF NOT EXISTS `history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `activity` varchar(255) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `time` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.history: ~0 rows (approximately)

-- Dumping structure for table shop.home_banners
DROP TABLE IF EXISTS `home_banners`;
CREATE TABLE IF NOT EXISTS `home_banners` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `placement` enum('hero','promo') NOT NULL DEFAULT 'promo',
  `image` varchar(255) NOT NULL,
  `mobile_image` varchar(255) DEFAULT NULL,
  `alt_text` varchar(255) NOT NULL DEFAULT '',
  `target_url` varchar(500) NOT NULL DEFAULT '#featured',
  `position` int unsigned NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `placement_visible_position` (`placement`,`visible`,`position`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.home_banners: ~3 rows (approximately)
INSERT INTO `home_banners` (`id`, `placement`, `image`, `mobile_image`, `alt_text`, `target_url`, `position`, `visible`) VALUES
	(1, 'hero', 'assets/buyora/buyora-hero.jpg', 'assets/buyora/buyora-hero-photo.jpg', 'Everything you need in one place', '#featured', 1, 1),
	(2, 'promo', 'assets/buyora/promo-electronics.jpg', NULL, 'Limited-time offers on selected electronics', '/?category=1', 1, 1),
	(3, 'promo', 'assets/buyora/promo-beauty.jpg', NULL, 'Beauty and personal care', '/?category=3', 2, 1);

-- Dumping structure for table shop.keys
DROP TABLE IF EXISTS `keys`;
CREATE TABLE IF NOT EXISTS `keys` (
  `id` int NOT NULL AUTO_INCREMENT,
  `key` varchar(40) NOT NULL,
  `level` int NOT NULL,
  `ignore_limits` tinyint(1) NOT NULL DEFAULT '0',
  `date_created` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.keys: ~0 rows (approximately)

-- Dumping structure for table shop.languages
DROP TABLE IF EXISTS `languages`;
CREATE TABLE IF NOT EXISTS `languages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `abbr` varchar(5) NOT NULL,
  `name` varchar(30) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `currencyKey` varchar(5) NOT NULL,
  `flag` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.languages: ~9 rows (approximately)
INSERT INTO `languages` (`id`, `abbr`, `name`, `currency`, `currencyKey`, `flag`) VALUES
	(1, 'bg', 'bulgarian', 'лв', 'BGN', 'bg.jpg'),
	(2, 'en', 'english', '$', 'USD', 'en.jpg'),
	(3, 'gr', 'greek', 'EUR', 'EUR', 'gr.png'),
	(4, 'id', 'indonesian', 'RP', 'IDR', 'id.jpg'),
	(5, 'fr', 'francais', 'EUR', 'EUR', 'fr.jpg'),
	(6, 'hi', 'hindi', '₹', 'INR', 'hi.svg'),
	(7, 'de', 'german', '€', 'EUR', 'de.svg'),
	(8, 'es', 'spanish', '€', 'EUR', 'es.svg'),
	(9, 'zh', 'chinese', '¥', 'CNY', 'zh.svg');

-- Dumping structure for table shop.orders
DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `user_id` int unsigned DEFAULT NULL COMMENT 'point to public_users ID',
  `products` text NOT NULL,
  `date` int unsigned NOT NULL,
  `referrer` varchar(255) NOT NULL,
  `clean_referrer` varchar(255) NOT NULL,
  `payment_type` varchar(255) NOT NULL,
  `paypal_status` varchar(10) DEFAULT NULL,
  `processed` tinyint(1) NOT NULL DEFAULT '0',
  `viewed` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'viewed status is change when change processed status',
  `confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `discount_code` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.orders: 2 rows
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` (`id`, `order_id`, `user_id`, `products`, `date`, `referrer`, `clean_referrer`, `payment_type`, `paypal_status`, `processed`, `viewed`, `confirmed`, `discount_code`) VALUES
	(1, 1234, 0, 'a:1:{i:0;a:2:{s:12:"product_info";a:17:{s:11:"vendor_name";s:8:"TechNest";s:9:"vendor_id";s:1:"1";s:2:"id";s:1:"1";s:6:"folder";N;s:5:"image";s:20:"buyora-product-1.jpg";s:4:"time";s:10:"1791891766";s:11:"time_update";s:10:"1791891766";s:10:"visibility";s:1:"1";s:14:"shop_categorie";s:1:"1";s:8:"quantity";s:3:"124";s:11:"procurement";s:1:"0";s:9:"in_slider";s:1:"1";s:3:"url";s:16:"wireless_earbuds";s:16:"virtual_products";N;s:8:"brand_id";N;s:8:"position";s:1:"1";s:5:"price";s:7:"3999.00";}s:16:"product_quantity";s:1:"1";}}', 1791900925, 'Direct', 'Direct', 'Safepay', NULL, 0, 0, 0, ''),
	(2, 1235, 0, 'a:1:{i:0;a:2:{s:12:"product_info";a:17:{s:11:"vendor_name";s:8:"TechNest";s:9:"vendor_id";s:1:"1";s:2:"id";s:1:"1";s:6:"folder";N;s:5:"image";s:20:"buyora-product-1.jpg";s:4:"time";s:10:"1791891766";s:11:"time_update";s:10:"1791891766";s:10:"visibility";s:1:"1";s:14:"shop_categorie";s:1:"1";s:8:"quantity";s:3:"124";s:11:"procurement";s:1:"0";s:9:"in_slider";s:1:"1";s:3:"url";s:16:"wireless_earbuds";s:16:"virtual_products";N;s:8:"brand_id";N;s:8:"position";s:1:"1";s:5:"price";s:7:"3999.00";}s:16:"product_quantity";s:1:"2";}}', 1791901971, 'Direct', 'Direct', 'Safepay', NULL, 0, 0, 0, '');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;

-- Dumping structure for table shop.orders_clients
DROP TABLE IF EXISTS `orders_clients`;
CREATE TABLE IF NOT EXISTS `orders_clients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(500) NOT NULL,
  `last_name` varchar(500) NOT NULL,
  `email` varchar(500) NOT NULL,
  `phone` varchar(500) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(500) NOT NULL,
  `post_code` varchar(500) NOT NULL,
  `notes` text NOT NULL,
  `for_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.orders_clients: ~2 rows (approximately)
INSERT INTO `orders_clients` (`id`, `first_name`, `last_name`, `email`, `phone`, `address`, `city`, `post_code`, `notes`, `for_id`) VALUES
	(1, '891be57250237e764efec92b930c8b5d735b58754afee096543b504f7f884e812d2010b28a2eb9c18d7a4b577f62e857b22dc56ee78e2119a472f65666de7d2dtqiJsppe30srjoggAHmK+1UMNj/+6Zcjrz9JAxz5azU=', '57894a0e811af176e81cc405abc96461fbe0162b3ebd30860c5afede44cc6947ce933fc00249fd77828bac3fc0da46f3d40d4fe84cb03de42f895798e30bc917HLaqkwMg7+pq9Xfz4yaSPjMhQmR0eNof69LZ+1ZnO+A=', 'f3894bf1235632419c923213a8ec43908bcff2384026ec5aadec51fb01130230faa869d32539e10725ba0c28fa57644a4495d54c46c52d6859b1caf657c4e9c7Kd8jaqN7nmILLrvW/HrVilHUp9WuEqBAeLaUcnUyiinAj9m4dJUxAp9za3vWbqbHQVQReZWESGvC9xUF2dWk/g==', 'e7701e39690db0057ff0d0b2b5df0d87a44e69bb0ba93b32247ca8c21e12be526a74d007c5e72eaa779831b475f70cae4ac6f5c01d16008b8ae0a8f3e02b1228ULK91WiJH/st3UZx0yijMli7i9VgzHEL9Qew79kYkFM=', '3e168b4da5a1eca21b43bafba5a0cb6e7ce5312e26af2620459bc1563f69cb48fe225129e4525f438c8cf1a94e454ee240c3bec8ccabd7461e5d1e7aadcc3960ettlmJslyd1xxjlvz76i9netFwm0ZLyVrpPmiJGcgzo=', '1965f34471f06b0a29c1db5d49a82825523f97464970ee9491643b8fc9de83dab8692e6b7b9ca40b7aaa6d40da57f2149a5b0e6e11b7cf36239c97c14b41f66eeOoXck+I2Ixlp/KLOwImIbguh3orrxHzoguRNnjl/tI=', '4748dc169c09ae5b8e4951c58521b0aa1f3c1a1529e4a6c7fe6761180a37d20e4250f3e3bb788517838f4988c5708e5475364a4ddcbe3f963296ce3f255109ecjZVT3j6gAjmPWogLWcdjf65ItDT2GqSfhLmz3Ff3oiQ=', '415423b05877db51946e8a9cc393a9e46db4d6ad2480c056b995204bcf85cd130f47a789a0b320c36d29405789f7184f6def330963c55cbda6c33fb3d30e5eebwp7YIaGYnsvtfUGu+NbAbBYOSgHbAzICYFU/pGcAbtSkYNbr99jOvph+N2KOiFiA', 1),
	(2, '6b3affab77e3faaba6046524c3cd77ff9e0261ab981287fc74ad91b6256598f6f8afcf2b3e7701215bca08d7ae8fa81816794359c29c69f7ade9d80f961f771fcVSq9+XZOA06mrSMSnUlxJO7dmJOGJvhvK5Area+LwY=', '4adfce2246fcff832c0e4c9ead5802dd884b7d9b423ceac39abf56d467be1e10d39d0194a82e54088fbceac6b88b54088718514ecc10f285fa47ef57d8f590baCFtPNHxuDRhkOoSE2a+sgF20WQmSn9NRjK8P1JeaDvY=', 'e86313d03d2c6fad3a9813cb464fda0c4695330376f5f9630ab1a3d74d4bf8ec9e658f3441e5906affbbef3bba51fc10c0b305bf3e4be10a73f6eaa4bb0d70dbfUJd9CGU74Ams/u7RFuOW4Bkw5JygCkAPOSQpmagCcMK+cQlOfejnNBe8V+Bc91o', 'dd65e0fa1e817c35cc517e12d816419d32153cee8300f10d68cd737103c59cb8c795ad2f66f8c63172a71e0dcf14ffe170441e0e4c9f14b34b36da2a61bacc8chC1feSxrisGhz6Ed46cGsO4ltGO/BlUGn5sKsFREOmo=', '9eec68adabc580d81535d5b9c7a29bc9f633e33e0ee09e6f847b75dd825ac7f884760ae27a8e31b78ef95be795618e50d5c2f47e78840be5dcb2593a01e7ab6dnS5prqGY7EIyALNJDq875GOOqJVcV/lK1/MRCuKJph0eJCJ4/nguAs6qWKQTSb4x', '16156ba3567435f3106258fbfa01a82b392a85e80988485b1a3ea3043f4531724ee1d1f4ef9970c8f327fbf67382dcf8fc19e0b79c25f5cda820e238fd225db3R+jnjtI7Qq4I7NXH580qKlr9vFPCFVJcTnxxu19YFbw=', '8fd38bc1f21f29758c4eaf440dd222262c95a9f38946362e92f8b23ecca55ae6b8dd954345177cbc285a99dd42151c1769ee091cdb166bc7d101edceb0015894UIbPqomkKFzLoKe33Q6Jcgll+5OG0x8ImImu16wO2Vk=', '669a9d2d38d54f8ac795683c345f97064ae7340621fa1f1ee6652b35770b8ec7574e92455ab3839201dbc20b0f8f51c2e9c7a441a44a3cfcf7e12269b7845844UXrYcwpKBUPB/CZq394v98FLGMTETBIsST8wZ24J8tI=', 2);

-- Dumping structure for table shop.order_items
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_row_id` int unsigned NOT NULL,
  `order_number` int NOT NULL,
  `vendor_id` int NOT NULL DEFAULT '0',
  `product_id` int unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `quantity` int unsigned NOT NULL,
  `unit_price_minor` int unsigned NOT NULL,
  `line_gross_minor` int unsigned NOT NULL,
  `discount_minor` int unsigned NOT NULL DEFAULT '0',
  `commission_rate_bps` int unsigned NOT NULL DEFAULT '0',
  `commission_minor` int unsigned NOT NULL DEFAULT '0',
  `vendor_net_minor` int unsigned NOT NULL DEFAULT '0',
  `currency` char(3) NOT NULL DEFAULT 'PKR',
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_product` (`order_row_id`,`product_id`),
  KEY `vendor_id` (`vendor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.order_items: ~2 rows (approximately)
INSERT INTO `order_items` (`id`, `order_row_id`, `order_number`, `vendor_id`, `product_id`, `title`, `quantity`, `unit_price_minor`, `line_gross_minor`, `discount_minor`, `commission_rate_bps`, `commission_minor`, `vendor_net_minor`, `currency`, `created_at`) VALUES
	(1, 1, 1234, 1, 1, 'Wireless Earbuds', 1, 399900, 399900, 0, 0, 0, 399900, 'PKR', 1791900925),
	(2, 2, 1235, 1, 1, 'Wireless Earbuds', 2, 399900, 799800, 0, 0, 0, 799800, 'PKR', 1791901971);

-- Dumping structure for table shop.payment_transactions
DROP TABLE IF EXISTS `payment_transactions`;
CREATE TABLE IF NOT EXISTS `payment_transactions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_row_id` int unsigned NOT NULL,
  `order_number` int NOT NULL,
  `provider` varchar(20) NOT NULL,
  `tracker` varchar(80) DEFAULT NULL,
  `amount_minor` int unsigned NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'PKR',
  `status` varchar(32) NOT NULL DEFAULT 'pending',
  `idempotency_key` varchar(80) NOT NULL,
  `verified_at` int unsigned DEFAULT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idempotency_key` (`idempotency_key`),
  UNIQUE KEY `tracker` (`tracker`),
  KEY `order_row_id` (`order_row_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.payment_transactions: ~2 rows (approximately)
INSERT INTO `payment_transactions` (`id`, `order_row_id`, `order_number`, `provider`, `tracker`, `amount_minor`, `currency`, `status`, `idempotency_key`, `verified_at`, `created_at`) VALUES
	(1, 1, 1234, 'safepay', 'track_8f4e2db7-ee29-473c-ad2a-a7acc6869137', 399900, 'PKR', 'pending', 'safepay:1', NULL, 1791900927),
	(2, 2, 1235, 'safepay', 'track_e2ba7d55-3a1f-4198-9bfb-da4ab9649f96', 799800, 'PKR', 'pending', 'safepay:2', NULL, 1791901973);

-- Dumping structure for table shop.products
DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `folder` int unsigned DEFAULT NULL COMMENT 'folder with images',
  `image` varchar(255) NOT NULL,
  `time` int unsigned NOT NULL COMMENT 'time created',
  `time_update` int unsigned NOT NULL COMMENT 'time updated',
  `visibility` tinyint(1) NOT NULL DEFAULT '1',
  `shop_categorie` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `procurement` int unsigned NOT NULL,
  `in_slider` tinyint(1) NOT NULL DEFAULT '0',
  `url` varchar(255) NOT NULL,
  `virtual_products` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `brand_id` int DEFAULT NULL,
  `position` int unsigned NOT NULL,
  `vendor_id` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.products: ~6 rows (approximately)
INSERT INTO `products` (`id`, `folder`, `image`, `time`, `time_update`, `visibility`, `shop_categorie`, `quantity`, `procurement`, `in_slider`, `url`, `virtual_products`, `brand_id`, `position`, `vendor_id`) VALUES
	(1, NULL, 'buyora-product-1.jpg', 1791891766, 1791891766, 1, 1, 124, 0, 1, 'wireless_earbuds', NULL, NULL, 1, 1),
	(2, NULL, 'buyora-product-2.jpg', 1791891766, 1791891766, 1, 2, 89, 0, 1, 'men_s_running_shoes', NULL, NULL, 2, 2),
	(3, NULL, 'buyora-product-3.jpg', 1791891766, 1791891766, 1, 1, 67, 0, 1, 'smart_watch', NULL, NULL, 3, 1),
	(4, NULL, 'buyora-product-4.jpg', 1791891766, 1791891766, 1, 3, 112, 0, 1, 'makeup_kit', NULL, NULL, 4, 3),
	(5, NULL, 'buyora-product-5.jpg', 1791891766, 1791891766, 1, 2, 76, 0, 0, 'backpack', NULL, NULL, 5, 2),
	(6, NULL, 'buyora-product-6.jpg', 1791891766, 1791891766, 1, 4, 45, 0, 0, 'home_decor_lamp', NULL, NULL, 6, 4);

-- Dumping structure for table shop.products_translations
DROP TABLE IF EXISTS `products_translations`;
CREATE TABLE IF NOT EXISTS `products_translations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `basic_description` text NOT NULL,
  `price` varchar(20) NOT NULL,
  `old_price` varchar(20) NOT NULL,
  `abbr` varchar(5) NOT NULL,
  `for_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.products_translations: ~54 rows (approximately)
INSERT INTO `products_translations` (`id`, `title`, `description`, `basic_description`, `price`, `old_price`, `abbr`, `for_id`) VALUES
	(1, 'Wireless Earbuds', 'Compact wireless earbuds with clear sound, a charging case and all-day comfort.', 'Compact wireless earbuds with clear sound, a charging case and all-day comfort.', '3999.00', '4999.00', 'bg', 1),
	(2, 'Wireless Earbuds', 'Compact wireless earbuds with clear sound, a charging case and all-day comfort.', 'Compact wireless earbuds with clear sound, a charging case and all-day comfort.', '3999.00', '4999.00', 'en', 1),
	(3, 'Wireless Earbuds', 'Compact wireless earbuds with clear sound, a charging case and all-day comfort.', 'Compact wireless earbuds with clear sound, a charging case and all-day comfort.', '3999.00', '4999.00', 'gr', 1),
	(4, 'Wireless Earbuds', 'Compact wireless earbuds with clear sound, a charging case and all-day comfort.', 'Compact wireless earbuds with clear sound, a charging case and all-day comfort.', '3999.00', '4999.00', 'id', 1),
	(5, 'Wireless Earbuds', 'Compact wireless earbuds with clear sound, a charging case and all-day comfort.', 'Compact wireless earbuds with clear sound, a charging case and all-day comfort.', '3999.00', '4999.00', 'fr', 1),
	(6, 'Wireless Earbuds', 'Compact wireless earbuds with clear sound, a charging case and all-day comfort.', 'Compact wireless earbuds with clear sound, a charging case and all-day comfort.', '3999.00', '4999.00', 'hi', 1),
	(7, 'Wireless Earbuds', 'Compact wireless earbuds with clear sound, a charging case and all-day comfort.', 'Compact wireless earbuds with clear sound, a charging case and all-day comfort.', '3999.00', '4999.00', 'de', 1),
	(8, 'Wireless Earbuds', 'Compact wireless earbuds with clear sound, a charging case and all-day comfort.', 'Compact wireless earbuds with clear sound, a charging case and all-day comfort.', '3999.00', '4999.00', 'es', 1),
	(9, 'Wireless Earbuds', 'Compact wireless earbuds with clear sound, a charging case and all-day comfort.', 'Compact wireless earbuds with clear sound, a charging case and all-day comfort.', '3999.00', '4999.00', 'zh', 1),
	(10, 'Men’s Running Shoes', 'Lightweight running shoes with breathable uppers and cushioned everyday support.', 'Lightweight running shoes with breathable uppers and cushioned everyday support.', '5499.00', '6999.00', 'bg', 2),
	(11, 'Men’s Running Shoes', 'Lightweight running shoes with breathable uppers and cushioned everyday support.', 'Lightweight running shoes with breathable uppers and cushioned everyday support.', '5499.00', '6999.00', 'en', 2),
	(12, 'Men’s Running Shoes', 'Lightweight running shoes with breathable uppers and cushioned everyday support.', 'Lightweight running shoes with breathable uppers and cushioned everyday support.', '5499.00', '6999.00', 'gr', 2),
	(13, 'Men’s Running Shoes', 'Lightweight running shoes with breathable uppers and cushioned everyday support.', 'Lightweight running shoes with breathable uppers and cushioned everyday support.', '5499.00', '6999.00', 'id', 2),
	(14, 'Men’s Running Shoes', 'Lightweight running shoes with breathable uppers and cushioned everyday support.', 'Lightweight running shoes with breathable uppers and cushioned everyday support.', '5499.00', '6999.00', 'fr', 2),
	(15, 'Men’s Running Shoes', 'Lightweight running shoes with breathable uppers and cushioned everyday support.', 'Lightweight running shoes with breathable uppers and cushioned everyday support.', '5499.00', '6999.00', 'hi', 2),
	(16, 'Men’s Running Shoes', 'Lightweight running shoes with breathable uppers and cushioned everyday support.', 'Lightweight running shoes with breathable uppers and cushioned everyday support.', '5499.00', '6999.00', 'de', 2),
	(17, 'Men’s Running Shoes', 'Lightweight running shoes with breathable uppers and cushioned everyday support.', 'Lightweight running shoes with breathable uppers and cushioned everyday support.', '5499.00', '6999.00', 'es', 2),
	(18, 'Men’s Running Shoes', 'Lightweight running shoes with breathable uppers and cushioned everyday support.', 'Lightweight running shoes with breathable uppers and cushioned everyday support.', '5499.00', '6999.00', 'zh', 2),
	(19, 'Smart Watch', 'A versatile smart watch with activity tracking, notifications and a bright display.', 'A versatile smart watch with activity tracking, notifications and a bright display.', '8499.00', '9999.00', 'bg', 3),
	(20, 'Smart Watch', 'A versatile smart watch with activity tracking, notifications and a bright display.', 'A versatile smart watch with activity tracking, notifications and a bright display.', '8499.00', '9999.00', 'en', 3),
	(21, 'Smart Watch', 'A versatile smart watch with activity tracking, notifications and a bright display.', 'A versatile smart watch with activity tracking, notifications and a bright display.', '8499.00', '9999.00', 'gr', 3),
	(22, 'Smart Watch', 'A versatile smart watch with activity tracking, notifications and a bright display.', 'A versatile smart watch with activity tracking, notifications and a bright display.', '8499.00', '9999.00', 'id', 3),
	(23, 'Smart Watch', 'A versatile smart watch with activity tracking, notifications and a bright display.', 'A versatile smart watch with activity tracking, notifications and a bright display.', '8499.00', '9999.00', 'fr', 3),
	(24, 'Smart Watch', 'A versatile smart watch with activity tracking, notifications and a bright display.', 'A versatile smart watch with activity tracking, notifications and a bright display.', '8499.00', '9999.00', 'hi', 3),
	(25, 'Smart Watch', 'A versatile smart watch with activity tracking, notifications and a bright display.', 'A versatile smart watch with activity tracking, notifications and a bright display.', '8499.00', '9999.00', 'de', 3),
	(26, 'Smart Watch', 'A versatile smart watch with activity tracking, notifications and a bright display.', 'A versatile smart watch with activity tracking, notifications and a bright display.', '8499.00', '9999.00', 'es', 3),
	(27, 'Smart Watch', 'A versatile smart watch with activity tracking, notifications and a bright display.', 'A versatile smart watch with activity tracking, notifications and a bright display.', '8499.00', '9999.00', 'zh', 3),
	(28, 'Makeup Kit', 'A practical beauty kit with everyday shades for an easy, polished look.', 'A practical beauty kit with everyday shades for an easy, polished look.', '2799.00', '3499.00', 'bg', 4),
	(29, 'Makeup Kit', 'A practical beauty kit with everyday shades for an easy, polished look.', 'A practical beauty kit with everyday shades for an easy, polished look.', '2799.00', '3499.00', 'en', 4),
	(30, 'Makeup Kit', 'A practical beauty kit with everyday shades for an easy, polished look.', 'A practical beauty kit with everyday shades for an easy, polished look.', '2799.00', '3499.00', 'gr', 4),
	(31, 'Makeup Kit', 'A practical beauty kit with everyday shades for an easy, polished look.', 'A practical beauty kit with everyday shades for an easy, polished look.', '2799.00', '3499.00', 'id', 4),
	(32, 'Makeup Kit', 'A practical beauty kit with everyday shades for an easy, polished look.', 'A practical beauty kit with everyday shades for an easy, polished look.', '2799.00', '3499.00', 'fr', 4),
	(33, 'Makeup Kit', 'A practical beauty kit with everyday shades for an easy, polished look.', 'A practical beauty kit with everyday shades for an easy, polished look.', '2799.00', '3499.00', 'hi', 4),
	(34, 'Makeup Kit', 'A practical beauty kit with everyday shades for an easy, polished look.', 'A practical beauty kit with everyday shades for an easy, polished look.', '2799.00', '3499.00', 'de', 4),
	(35, 'Makeup Kit', 'A practical beauty kit with everyday shades for an easy, polished look.', 'A practical beauty kit with everyday shades for an easy, polished look.', '2799.00', '3499.00', 'es', 4),
	(36, 'Makeup Kit', 'A practical beauty kit with everyday shades for an easy, polished look.', 'A practical beauty kit with everyday shades for an easy, polished look.', '2799.00', '3499.00', 'zh', 4),
	(37, 'Backpack', 'A roomy everyday backpack with organized storage for work, travel or school.', 'A roomy everyday backpack with organized storage for work, travel or school.', '4299.00', '5499.00', 'bg', 5),
	(38, 'Backpack', 'A roomy everyday backpack with organized storage for work, travel or school.', 'A roomy everyday backpack with organized storage for work, travel or school.', '4299.00', '5499.00', 'en', 5),
	(39, 'Backpack', 'A roomy everyday backpack with organized storage for work, travel or school.', 'A roomy everyday backpack with organized storage for work, travel or school.', '4299.00', '5499.00', 'gr', 5),
	(40, 'Backpack', 'A roomy everyday backpack with organized storage for work, travel or school.', 'A roomy everyday backpack with organized storage for work, travel or school.', '4299.00', '5499.00', 'id', 5),
	(41, 'Backpack', 'A roomy everyday backpack with organized storage for work, travel or school.', 'A roomy everyday backpack with organized storage for work, travel or school.', '4299.00', '5499.00', 'fr', 5),
	(42, 'Backpack', 'A roomy everyday backpack with organized storage for work, travel or school.', 'A roomy everyday backpack with organized storage for work, travel or school.', '4299.00', '5499.00', 'hi', 5),
	(43, 'Backpack', 'A roomy everyday backpack with organized storage for work, travel or school.', 'A roomy everyday backpack with organized storage for work, travel or school.', '4299.00', '5499.00', 'de', 5),
	(44, 'Backpack', 'A roomy everyday backpack with organized storage for work, travel or school.', 'A roomy everyday backpack with organized storage for work, travel or school.', '4299.00', '5499.00', 'es', 5),
	(45, 'Backpack', 'A roomy everyday backpack with organized storage for work, travel or school.', 'A roomy everyday backpack with organized storage for work, travel or school.', '4299.00', '5499.00', 'zh', 5),
	(46, 'Home Decor Lamp', 'A warm accent lamp designed to bring a soft, comfortable glow to your room.', 'A warm accent lamp designed to bring a soft, comfortable glow to your room.', '3199.00', '3549.00', 'bg', 6),
	(47, 'Home Decor Lamp', 'A warm accent lamp designed to bring a soft, comfortable glow to your room.', 'A warm accent lamp designed to bring a soft, comfortable glow to your room.', '3199.00', '3549.00', 'en', 6),
	(48, 'Home Decor Lamp', 'A warm accent lamp designed to bring a soft, comfortable glow to your room.', 'A warm accent lamp designed to bring a soft, comfortable glow to your room.', '3199.00', '3549.00', 'gr', 6),
	(49, 'Home Decor Lamp', 'A warm accent lamp designed to bring a soft, comfortable glow to your room.', 'A warm accent lamp designed to bring a soft, comfortable glow to your room.', '3199.00', '3549.00', 'id', 6),
	(50, 'Home Decor Lamp', 'A warm accent lamp designed to bring a soft, comfortable glow to your room.', 'A warm accent lamp designed to bring a soft, comfortable glow to your room.', '3199.00', '3549.00', 'fr', 6),
	(51, 'Home Decor Lamp', 'A warm accent lamp designed to bring a soft, comfortable glow to your room.', 'A warm accent lamp designed to bring a soft, comfortable glow to your room.', '3199.00', '3549.00', 'hi', 6),
	(52, 'Home Decor Lamp', 'A warm accent lamp designed to bring a soft, comfortable glow to your room.', 'A warm accent lamp designed to bring a soft, comfortable glow to your room.', '3199.00', '3549.00', 'de', 6),
	(53, 'Home Decor Lamp', 'A warm accent lamp designed to bring a soft, comfortable glow to your room.', 'A warm accent lamp designed to bring a soft, comfortable glow to your room.', '3199.00', '3549.00', 'es', 6),
	(54, 'Home Decor Lamp', 'A warm accent lamp designed to bring a soft, comfortable glow to your room.', 'A warm accent lamp designed to bring a soft, comfortable glow to your room.', '3199.00', '3549.00', 'zh', 6);

-- Dumping structure for table shop.seo_pages
DROP TABLE IF EXISTS `seo_pages`;
CREATE TABLE IF NOT EXISTS `seo_pages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.seo_pages: ~4 rows (approximately)
INSERT INTO `seo_pages` (`id`, `name`) VALUES
	(1, 'home'),
	(2, 'checkout'),
	(3, 'contacts'),
	(4, 'blog');

-- Dumping structure for table shop.seo_pages_translations
DROP TABLE IF EXISTS `seo_pages_translations`;
CREATE TABLE IF NOT EXISTS `seo_pages_translations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` varchar(200) NOT NULL,
  `abbr` varchar(5) NOT NULL,
  `page_type` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.seo_pages_translations: ~0 rows (approximately)

-- Dumping structure for table shop.shop_categories
DROP TABLE IF EXISTS `shop_categories`;
CREATE TABLE IF NOT EXISTS `shop_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sub_for` int NOT NULL,
  `position` int unsigned NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.shop_categories: 9 rows
/*!40000 ALTER TABLE `shop_categories` DISABLE KEYS */;
INSERT INTO `shop_categories` (`id`, `sub_for`, `position`, `image`) VALUES
	(1, 0, 1, 'buyora-electronics.jpg'),
	(2, 0, 2, 'buyora-fashion.jpg'),
	(3, 0, 3, 'buyora-beauty-and-health.jpg'),
	(4, 0, 4, 'buyora-home-and-living.jpg'),
	(5, 0, 5, 'buyora-toys-and-games.jpg'),
	(6, 0, 6, 'buyora-sports-and-fitness.jpg'),
	(7, 0, 7, 'buyora-kids-and-baby.jpg'),
	(8, 0, 8, 'buyora-groceries.jpg'),
	(9, 0, 9, 'buyora-automotive.jpg');
/*!40000 ALTER TABLE `shop_categories` ENABLE KEYS */;

-- Dumping structure for table shop.shop_categories_translations
DROP TABLE IF EXISTS `shop_categories_translations`;
CREATE TABLE IF NOT EXISTS `shop_categories_translations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `abbr` varchar(5) NOT NULL,
  `for_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.shop_categories_translations: ~81 rows (approximately)
INSERT INTO `shop_categories_translations` (`id`, `name`, `abbr`, `for_id`) VALUES
	(1, 'Electronics', 'bg', 1),
	(2, 'Electronics', 'en', 1),
	(3, 'Electronics', 'gr', 1),
	(4, 'Electronics', 'id', 1),
	(5, 'Electronics', 'fr', 1),
	(6, 'Electronics', 'hi', 1),
	(7, 'Electronics', 'de', 1),
	(8, 'Electronics', 'es', 1),
	(9, 'Electronics', 'zh', 1),
	(10, 'Fashion', 'bg', 2),
	(11, 'Fashion', 'en', 2),
	(12, 'Fashion', 'gr', 2),
	(13, 'Fashion', 'id', 2),
	(14, 'Fashion', 'fr', 2),
	(15, 'Fashion', 'hi', 2),
	(16, 'Fashion', 'de', 2),
	(17, 'Fashion', 'es', 2),
	(18, 'Fashion', 'zh', 2),
	(19, 'Beauty & Health', 'bg', 3),
	(20, 'Beauty & Health', 'en', 3),
	(21, 'Beauty & Health', 'gr', 3),
	(22, 'Beauty & Health', 'id', 3),
	(23, 'Beauty & Health', 'fr', 3),
	(24, 'Beauty & Health', 'hi', 3),
	(25, 'Beauty & Health', 'de', 3),
	(26, 'Beauty & Health', 'es', 3),
	(27, 'Beauty & Health', 'zh', 3),
	(28, 'Home & Living', 'bg', 4),
	(29, 'Home & Living', 'en', 4),
	(30, 'Home & Living', 'gr', 4),
	(31, 'Home & Living', 'id', 4),
	(32, 'Home & Living', 'fr', 4),
	(33, 'Home & Living', 'hi', 4),
	(34, 'Home & Living', 'de', 4),
	(35, 'Home & Living', 'es', 4),
	(36, 'Home & Living', 'zh', 4),
	(37, 'Toys & Games', 'bg', 5),
	(38, 'Toys & Games', 'en', 5),
	(39, 'Toys & Games', 'gr', 5),
	(40, 'Toys & Games', 'id', 5),
	(41, 'Toys & Games', 'fr', 5),
	(42, 'Toys & Games', 'hi', 5),
	(43, 'Toys & Games', 'de', 5),
	(44, 'Toys & Games', 'es', 5),
	(45, 'Toys & Games', 'zh', 5),
	(46, 'Sports & Fitness', 'bg', 6),
	(47, 'Sports & Fitness', 'en', 6),
	(48, 'Sports & Fitness', 'gr', 6),
	(49, 'Sports & Fitness', 'id', 6),
	(50, 'Sports & Fitness', 'fr', 6),
	(51, 'Sports & Fitness', 'hi', 6),
	(52, 'Sports & Fitness', 'de', 6),
	(53, 'Sports & Fitness', 'es', 6),
	(54, 'Sports & Fitness', 'zh', 6),
	(55, 'Kids & Baby', 'bg', 7),
	(56, 'Kids & Baby', 'en', 7),
	(57, 'Kids & Baby', 'gr', 7),
	(58, 'Kids & Baby', 'id', 7),
	(59, 'Kids & Baby', 'fr', 7),
	(60, 'Kids & Baby', 'hi', 7),
	(61, 'Kids & Baby', 'de', 7),
	(62, 'Kids & Baby', 'es', 7),
	(63, 'Kids & Baby', 'zh', 7),
	(64, 'Groceries', 'bg', 8),
	(65, 'Groceries', 'en', 8),
	(66, 'Groceries', 'gr', 8),
	(67, 'Groceries', 'id', 8),
	(68, 'Groceries', 'fr', 8),
	(69, 'Groceries', 'hi', 8),
	(70, 'Groceries', 'de', 8),
	(71, 'Groceries', 'es', 8),
	(72, 'Groceries', 'zh', 8),
	(73, 'Automotive', 'bg', 9),
	(74, 'Automotive', 'en', 9),
	(75, 'Automotive', 'gr', 9),
	(76, 'Automotive', 'id', 9),
	(77, 'Automotive', 'fr', 9),
	(78, 'Automotive', 'hi', 9),
	(79, 'Automotive', 'de', 9),
	(80, 'Automotive', 'es', 9),
	(81, 'Automotive', 'zh', 9);

-- Dumping structure for table shop.subscribed
DROP TABLE IF EXISTS `subscribed`;
CREATE TABLE IF NOT EXISTS `subscribed` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `browser` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.subscribed: ~0 rows (approximately)

-- Dumping structure for table shop.textual_pages_tanslations
DROP TABLE IF EXISTS `textual_pages_tanslations`;
CREATE TABLE IF NOT EXISTS `textual_pages_tanslations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `abbr` varchar(5) NOT NULL,
  `for_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.textual_pages_tanslations: ~0 rows (approximately)

-- Dumping structure for table shop.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `notify` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'notifications by email',
  `last_login` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.users: ~1 rows (approximately)
INSERT INTO `users` (`id`, `username`, `password`, `email`, `notify`, `last_login`) VALUES
	(1, 'admin', '$2y$12$bUS.5X2sHbJlKtYpE5uQFOuKRVQEYs8XEFbLxZF2RsDdVBqLgEGcW', 'your@email.com', 0, 1791894770);

-- Dumping structure for table shop.users_public
DROP TABLE IF EXISTS `users_public`;
CREATE TABLE IF NOT EXISTS `users_public` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.users_public: ~0 rows (approximately)

-- Dumping structure for table shop.value_store
DROP TABLE IF EXISTS `value_store`;
CREATE TABLE IF NOT EXISTS `value_store` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `thekey` varchar(50) NOT NULL,
  `value` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `key` (`thekey`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.value_store: ~35 rows (approximately)
INSERT INTO `value_store` (`id`, `thekey`, `value`) VALUES
	(1, 'sitelogo', 'NewLogo.jpg'),
	(2, 'navitext', ''),
	(3, 'footercopyright', 'Your organization.'),
	(4, 'contactspage', 'Hello dear client'),
	(5, 'footerContactAddr', ''),
	(6, 'footerContactEmail', 'support@shop.dev'),
	(7, 'footerContactPhone', ''),
	(8, 'googleMaps', '42.671840, 83.279163'),
	(9, 'footerAboutUs', ''),
	(10, 'footerSocialFacebook', ''),
	(11, 'footerSocialTwitter', ''),
	(12, 'footerSocialGooglePlus', ''),
	(13, 'footerSocialPinterest', ''),
	(14, 'footerSocialYoutube', ''),
	(16, 'contactsEmailTo', 'contacts@shop.dev'),
	(17, 'shippingOrder', '1'),
	(18, 'addJs', ''),
	(19, 'publicQuantity', '0'),
	(20, 'paypal_email', ''),
	(21, 'paypal_sandbox', '0'),
	(22, 'publicDateAdded', '0'),
	(23, 'googleApi', ''),
	(24, 'template', 'wind2026'),
	(25, 'cashondelivery_visibility', '1'),
	(26, 'showBrands', '0'),
	(27, 'showInSlider', '0'),
	(28, 'codeDiscounts', '1'),
	(29, 'virtualProducts', '0'),
	(30, 'multiVendor', '1'),
	(31, 'outOfStock', '0'),
	(32, 'hideBuyButtonsOfOutOfStock', '0'),
	(33, 'moreInfoBtn', ''),
	(34, 'refreshAfterAddToCart', '0'),
	(35, 'requireVendorApproval', '0'),
	(36, 'requireProductApproval', '0');

-- Dumping structure for table shop.vendors
DROP TABLE IF EXISTS `vendors`;
CREATE TABLE IF NOT EXISTS `vendors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `url` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-approved, 0-pending',
  `password` varchar(100) NOT NULL,
  `remember_token` varchar(64) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`email`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.vendors: ~7 rows (approximately)
INSERT INTO `vendors` (`id`, `name`, `url`, `email`, `status`, `password`, `remember_token`, `updated_at`, `created_at`) VALUES
	(1, 'TechNest', 'technest', 'technest@buyora.test', 1, '$2y$10$OMzBCw1doob76P5esNlHsOAdT8etWD7Yj7Wgw6jGLunzK8UHf5YB6', NULL, '2026-10-13 11:42:46', '2026-10-13 11:42:46'),
	(2, 'StepStyle', 'stepstyle', 'stepstyle@buyora.test', 1, '$2y$10$OMzBCw1doob76P5esNlHsOAdT8etWD7Yj7Wgw6jGLunzK8UHf5YB6', NULL, '2026-10-13 11:42:46', '2026-10-13 11:42:46'),
	(3, 'GlowHouse', 'glowhouse', 'glowhouse@buyora.test', 1, '$2y$10$OMzBCw1doob76P5esNlHsOAdT8etWD7Yj7Wgw6jGLunzK8UHf5YB6', NULL, '2026-10-13 11:42:46', '2026-10-13 11:42:46'),
	(4, 'LumaHome', 'lumahome', 'lumahome@buyora.test', 1, '$2y$10$OMzBCw1doob76P5esNlHsOAdT8etWD7Yj7Wgw6jGLunzK8UHf5YB6', NULL, '2026-10-13 11:42:46', '2026-10-13 11:42:46'),
	(5, NULL, '', 'buyora.audit.1791900897@example.com', 1, '$2y$10$DHrPdd6NEh8.fbPG/mjmoOcD9eTG9ClPqutyhKZHHW9H7IWOX/Xxu', NULL, '2026-10-13 14:14:57', '2026-10-13 14:14:57'),
	(6, NULL, '', 'buyora.audit.1791900925@example.com', 1, '$2y$10$Qjy2veAVUKH7k0bEdxUBOeYtgzAUgCL5DQxyE9rji1GBtma/MYWWO', NULL, '2026-10-13 14:15:30', '2026-10-13 14:15:30'),
	(7, NULL, '', 'buyora.audit.1791901217@example.com', 1, '$2y$10$1fe0EX6nXxYnLwQE/zs6ouEC.2730oKCH7qkSnM9rsxzT13sN181u', NULL, '2026-10-13 14:20:18', '2026-10-13 14:20:18');

-- Dumping structure for table shop.vendors_orders
DROP TABLE IF EXISTS `vendors_orders`;
CREATE TABLE IF NOT EXISTS `vendors_orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `products` text NOT NULL,
  `date` int unsigned NOT NULL,
  `referrer` varchar(255) NOT NULL,
  `clean_referrer` varchar(255) NOT NULL,
  `payment_type` varchar(255) NOT NULL,
  `paypal_status` varchar(10) DEFAULT NULL,
  `processed` tinyint(1) NOT NULL DEFAULT '0',
  `viewed` tinyint(1) NOT NULL DEFAULT '0',
  `confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `discount_code` varchar(20) NOT NULL,
  `vendor_id` int unsigned NOT NULL,
  `parent_order_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.vendors_orders: 2 rows
/*!40000 ALTER TABLE `vendors_orders` DISABLE KEYS */;
INSERT INTO `vendors_orders` (`id`, `order_id`, `products`, `date`, `referrer`, `clean_referrer`, `payment_type`, `paypal_status`, `processed`, `viewed`, `confirmed`, `discount_code`, `vendor_id`, `parent_order_id`) VALUES
	(1, 1234, 'a:1:{i:1;i:1;}', 1791900925, 'Direct', 'Direct', 'Safepay', NULL, 0, 0, 0, '', 1, 1234),
	(2, 1235, 'a:1:{i:1;i:2;}', 1791901971, 'Direct', 'Direct', 'Safepay', NULL, 0, 0, 0, '', 1, 1235);
/*!40000 ALTER TABLE `vendors_orders` ENABLE KEYS */;

-- Dumping structure for table shop.vendors_orders_clients
DROP TABLE IF EXISTS `vendors_orders_clients`;
CREATE TABLE IF NOT EXISTS `vendors_orders_clients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(500) NOT NULL,
  `last_name` varchar(500) NOT NULL,
  `email` varchar(500) NOT NULL,
  `phone` varchar(500) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(500) NOT NULL,
  `post_code` varchar(500) NOT NULL,
  `notes` text NOT NULL,
  `for_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.vendors_orders_clients: ~2 rows (approximately)
INSERT INTO `vendors_orders_clients` (`id`, `first_name`, `last_name`, `email`, `phone`, `address`, `city`, `post_code`, `notes`, `for_id`) VALUES
	(1, 'ffe7b3687d0d8d66799a7c20bc0c69ce94303719dc3e62661e27ee0191570492f24e0f544767fe77232193a6caa57e4abadf578f48cf024176fd77f8d3ef534elQLStrh3VRDw49Wrf5erXYxoYKRkgAP5ALflWW5H9vw=', 'd7c61629887c0d58cef82db4d11e42b4f1a655584556a6676b535de2264938b9ed81fe05d02172915d936648e6db0e94d669ac415540f57472bcb71c41c8a6f8oTRzVZKmStffU8S5vSmbHFc/OXjOB1QbC2Z97rUo4Nw=', 'a6a55497852463f4a845f0b0fa820d7152b2cc90c459f6e6e34d1c6701c5eab56f0de30766d59fa72675cf3797d81886a874623205c50173ba61063b8bd05c7brEkYW6x7Neb6C2is4wDn62WsmqKGmQuj3xBQwii7kCORWA3QcHp5sPXsSFunlLg63CffvcRUavxCfSD1fGC/KA==', '2f3886d4f8c69335366290eb24ccbad0ae0f737de55932a1fb3395ef12895040f8bcb505b6ff0d51f037bda84fdde80111681f45ae9db269d53ef7b3988992ef9vfDMwe+/YfwVkIvRoL9qkQZPe9QKSwHoGNp0gILfhg=', '63e3a6d95426743cfdb3b01733520ef9d8876ad55107c53cc75ee1573087f01976f95d333e6fd0d64e31876df35f1484c23e7c3f36897c4387530fc8ef6b0bf5NGAHXq4W8wblKwISiaeWqr6n29bk+Sl6jg8xJ7pk7Ew=', 'a86cad902c47c8294e4cadd69698868691c6b745129ed3aa2bc313df6e07b106d0b50d0823034a5c06676e4f0ec2504d37723b2d4ce7cbf4bca936750b464550cxsvpzvzn5cOBeD3QWR/p6woGg17mNSI+1v1nJl/Eoo=', '756ee1e069386e2398ce438131963d3f8bdd4b1f94de4b82a7248d1ae1a483e9318796065b1445b203a67982348a6c19d77564652c21663fbb59d73448e142c4c6G01q0JXCdfFPZn2bRANMY1Gcv4ocZdazfBn5q0IWo=', '1c85a91f658adc041c971b502330848de178f48ee3620804cf1122ec49bd9a2b54401143d47aa2d0ec0f920be0c50c0b6ad71e71dc45190afffb77cdf4954585Ar3hwOgSKjTjCe1Aj34szz1yD/pdFB1QcwHhhQSu3HZBc+rPbRuM9Bp3QVTOjHeI', 1),
	(2, 'dd6e3646db919e199761d6455d6b72bf4efb303baf22ec681f5dfdfe1ec2357e9ca28bc87bc919460451076c2e4940d4ccf9acb7099fe978c87529446727228axpa9NVAZ5iaCbNXIa95KwARdmojj5kvZt/zMQz7Ij28=', '29c75aff800a294921c2c63709cf16b14d0b68c916c64d6046fd02fc217801e6ada6d2e6c5da01284c7d278a9a4096cd86565ba0f6d1e5e0c5e4e9d7119fa0f8+g1es3JbNmj6xe7FsHPsBAr3ygkTo+g+H64nrOkbtSM=', 'aa0e1ab9d585deb2e1b41813db996c6e8ff704c6ed1bd37e0c8c008cbaee9b089449dd12906fbdbebf3be5437ff193461c166d9ec2c6e85b2b61e044d3286d26RRSY9rU7Kw+4axtgaBvtjSVsXcysFbr75E9QBf5UkLbdflXvCRyoWz5j33//BKwF', '3e491c554ecda9918c224e7fbdb2fffc25324f463bd52d192474745c174abe15572ac362189b00d81dbb50654744de0fca65386f7f5292a9d0ebd644687c7e34uitkdsLD+IN8yPNQHo8G5qXn+1F1SC5noKsUMGRkhy8=', '30c08cf24d6b0b4f2f4a30478fd58e5e6f5ce991582f86b82e09996a381ff635798042e58c226426bfb642e20275e2ee6ab2261b8ecc2446a7380c84378c348cXPGaKYYz0L1cjY/R0t6WqyeXjHiVuXaRvSR+D4yVR4IaGa+2brGuuwW+2zFu3/K6', 'f32837b3f91ed32efe2cea754f3b30229e9dea6b7badd73e9cef1f3ed582a81d14c0d5b5c8f1fecb19091900849e4aace84cb98bf7af5593c5508cb9e178a7d0iL+Yd8gHrmmsuqaDbEm6pF3Ivl4LTM9tVIJcQi7e9+g=', '39fccbbb2f2fc77183d704df7fe06e3b63b91780d7683bb92e5d2d9de4a29d835746c5f07c15073c45ab0333316f83248a8c5eebc2d6acb683a9c0e4952e8e1b9k0lEjqjgxHGdVG5D3pY8IEyGbTudhTDkofnxnr+lJU=', '1bcbbd7c4b2321a7c50b9136bf9a73b52925afa63b78e15fa74fd07520c4a4d86c8559a0f75c1645b2abfa8345366a61300c01a858ebb9074cec19af499c3ff4ebcwawh2OaWjI+rwmI6UqyF6OLw0KNkShVofqOuOHG8=', 2);

-- Dumping structure for table shop.vendor_ledger
DROP TABLE IF EXISTS `vendor_ledger`;
CREATE TABLE IF NOT EXISTS `vendor_ledger` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` int unsigned NOT NULL,
  `order_item_id` int unsigned DEFAULT NULL,
  `payout_id` int unsigned DEFAULT NULL,
  `entry_type` varchar(32) NOT NULL,
  `amount_minor` int NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'PKR',
  `idempotency_key` varchar(120) NOT NULL,
  `note` varchar(255) NOT NULL DEFAULT '',
  `admin_user` varchar(80) NOT NULL DEFAULT '',
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idempotency_key` (`idempotency_key`),
  KEY `vendor_id` (`vendor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.vendor_ledger: ~0 rows (approximately)

-- Dumping structure for table shop.vendor_payouts
DROP TABLE IF EXISTS `vendor_payouts`;
CREATE TABLE IF NOT EXISTS `vendor_payouts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` int unsigned NOT NULL,
  `amount_minor` int unsigned NOT NULL,
  `method` varchar(40) NOT NULL,
  `reference_code` varchar(80) NOT NULL,
  `notes` varchar(500) NOT NULL DEFAULT '',
  `paid_on` date NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'recorded',
  `admin_user` varchar(80) NOT NULL DEFAULT '',
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vendor_reference` (`vendor_id`,`reference_code`),
  KEY `vendor_id` (`vendor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.vendor_payouts: ~0 rows (approximately)

-- Dumping structure for table shop.webhook_events
DROP TABLE IF EXISTS `webhook_events`;
CREATE TABLE IF NOT EXISTS `webhook_events` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `event_key` varchar(191) NOT NULL,
  `tracker` varchar(80) NOT NULL DEFAULT '',
  `event_type` varchar(80) NOT NULL DEFAULT '',
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_key` (`event_key`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table shop.webhook_events: ~3 rows (approximately)
INSERT INTO `webhook_events` (`id`, `event_key`, `tracker`, `event_type`, `created_at`) VALUES
	(1, '234dc5b573f5d314879c967b6947d69181a352c79205b52110603517fa6fdc0d', 'track_webhook_setup_probe', 'payment.failed', 1791900020),
	(2, '3eba7ec0c2d5689cd54508274380dfe233200d8749bfe4ce86872006c4d46330', '', 'payment.failed', 1791900073),
	(3, '062a0883b631b8b49f0036d8456932dbabec60d3d0a775f6ec15e489f7b206b0', 'track_webhook_shape_probe', 'payment.failed', 1791900246);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
