CREATE TABLE IF NOT EXISTS `payment_transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_row_id` int(10) unsigned NOT NULL,
  `order_number` int(11) NOT NULL,
  `provider` varchar(20) NOT NULL,
  `tracker` varchar(80) DEFAULT NULL,
  `amount_minor` int(10) unsigned NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'PKR',
  `status` varchar(32) NOT NULL DEFAULT 'pending',
  `idempotency_key` varchar(80) NOT NULL,
  `verified_at` int(10) unsigned DEFAULT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tracker` (`tracker`),
  UNIQUE KEY `idempotency_key` (`idempotency_key`),
  KEY `order_row_id` (`order_row_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_row_id` int(10) unsigned NOT NULL,
  `order_number` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL DEFAULT '0',
  `product_id` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `quantity` int(10) unsigned NOT NULL,
  `unit_price_minor` int(10) unsigned NOT NULL,
  `line_gross_minor` int(10) unsigned NOT NULL,
  `discount_minor` int(10) unsigned NOT NULL DEFAULT '0',
  `commission_rate_bps` int(10) unsigned NOT NULL DEFAULT '0',
  `commission_minor` int(10) unsigned NOT NULL DEFAULT '0',
  `vendor_net_minor` int(10) unsigned NOT NULL DEFAULT '0',
  `currency` char(3) NOT NULL DEFAULT 'PKR',
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_product` (`order_row_id`,`product_id`),
  KEY `vendor_id` (`vendor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `vendor_ledger` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` int(10) unsigned NOT NULL,
  `order_item_id` int(10) unsigned DEFAULT NULL,
  `payout_id` int(10) unsigned DEFAULT NULL,
  `entry_type` varchar(32) NOT NULL,
  `amount_minor` int(11) NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'PKR',
  `idempotency_key` varchar(120) NOT NULL,
  `note` varchar(255) NOT NULL DEFAULT '',
  `admin_user` varchar(80) NOT NULL DEFAULT '',
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idempotency_key` (`idempotency_key`),
  KEY `vendor_id` (`vendor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `vendor_payouts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` int(10) unsigned NOT NULL,
  `amount_minor` int(10) unsigned NOT NULL,
  `method` varchar(40) NOT NULL,
  `reference_code` varchar(80) NOT NULL,
  `notes` varchar(500) NOT NULL DEFAULT '',
  `paid_on` date NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'recorded',
  `admin_user` varchar(80) NOT NULL DEFAULT '',
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vendor_reference` (`vendor_id`,`reference_code`),
  KEY `vendor_id` (`vendor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `webhook_events` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_key` varchar(191) NOT NULL,
  `tracker` varchar(80) NOT NULL DEFAULT '',
  `event_type` varchar(80) NOT NULL DEFAULT '',
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_key` (`event_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
