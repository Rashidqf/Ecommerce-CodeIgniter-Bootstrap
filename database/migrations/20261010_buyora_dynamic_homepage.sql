-- Safe to run more than once. Existing product/category data is not changed.
SET @category_image_exists = (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'shop_categories'
    AND COLUMN_NAME = 'image'
);
SET @category_image_sql = IF(
  @category_image_exists = 0,
  'ALTER TABLE shop_categories ADD COLUMN image varchar(255) NOT NULL DEFAULT '''' AFTER position',
  'SELECT 1'
);
PREPARE buyora_category_image_stmt FROM @category_image_sql;
EXECUTE buyora_category_image_stmt;
DEALLOCATE PREPARE buyora_category_image_stmt;

CREATE TABLE IF NOT EXISTS `home_banners` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `placement` enum('hero','promo') NOT NULL DEFAULT 'promo',
  `image` varchar(255) NOT NULL,
  `mobile_image` varchar(255) DEFAULT NULL,
  `alt_text` varchar(255) NOT NULL DEFAULT '',
  `target_url` varchar(500) NOT NULL DEFAULT '#featured',
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `placement_visible_position` (`placement`,`visible`,`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
