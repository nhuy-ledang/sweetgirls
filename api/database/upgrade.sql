-- 2023-05-29
DROP TABLE `order__transactions`;
DROP TABLE `order__webhooks`;
DROP TABLE `order__reviews`;
DROP TABLE `order__histories`;

DELETE FROM `migrations` WHERE `migration` = '2022_10_27_092254_create_order_options_table';

DROP TABLE `pd__order_histories`;
DROP TABLE `pd__order_options`;
DROP TABLE `pd__order_products`;
DROP TABLE `pd__order_totals`;

DROP TABLE `pd__orders`;

DROP TABLE `orders`;

ALTER TABLE `pd__products` ADD `coins` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `price`;
DELETE FROM `migrations` WHERE `migration` = '2022_07_28_230038_update_product_table';
DELETE FROM `migrations` WHERE `migration` = '2023_04_12_230038_update_product0412_table';
ALTER TABLE `pd__products` CHANGE `master_id` `master_id` INT(10) UNSIGNED NULL;
DELETE FROM `migrations` WHERE `migration` = '2022_10_04_030038_update_categories_table';
DELETE FROM `migrations` WHERE `migration` = '2022_10_11_030038_update_categories_table';

-- 2023-05-30
ALTER TABLE `users` CHANGE `username` `username` VARCHAR(63) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `last_name`, CHANGE `avatar` `avatar` VARCHAR(191) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `username`, CHANGE `is_notify` `is_notify` TINYINT(1) NOT NULL DEFAULT '1' AFTER `avatar`, CHANGE `is_sms` `is_sms` TINYINT(1) NOT NULL DEFAULT '1' AFTER `is_notify`, CHANGE `phone_number` `phone_number` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `is_sms`, CHANGE `gender` `gender` SMALLINT(6) NOT NULL DEFAULT '0' AFTER `phone_number`, CHANGE `birthday` `birthday` DATE NULL DEFAULT NULL AFTER `gender`, CHANGE `address_id` `address_id` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `birthday`, CHANGE `address` `address` VARCHAR(191) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `address_id`, CHANGE `latitude` `latitude` DECIMAL(10,8) NULL DEFAULT NULL AFTER `address`, CHANGE `longitude` `longitude` DECIMAL(11,8) NULL DEFAULT NULL AFTER `latitude`, CHANGE `status` `status` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'starter' AFTER `longitude`, CHANGE `password_failed` `password_failed` SMALLINT(6) NOT NULL DEFAULT '0' AFTER `status`, CHANGE `ip` `ip` VARCHAR(40) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `password_failed`, CHANGE `completed` `completed` TINYINT(1) NOT NULL DEFAULT '0' AFTER `ip`, CHANGE `completed_at` `completed_at` TIMESTAMP NULL DEFAULT NULL AFTER `completed`, CHANGE `device_platform` `device_platform` VARCHAR(7) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `completed_at`, CHANGE `device_token` `device_token` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `device_platform`, CHANGE `last_provider` `last_provider` VARCHAR(31) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `device_token`;
ALTER TABLE `users` ADD `fullname` VARCHAR(191) NULL DEFAULT NULL AFTER `last_name`;
ALTER TABLE `users` ADD `calling_code` VARCHAR(7) NOT NULL DEFAULT '84' AFTER `is_sms`;
ALTER TABLE `users` CHANGE `phone_number` `phone_number` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE `users` ADD `avatar_url` VARCHAR(255) NULL DEFAULT NULL AFTER `avatar`;
ALTER TABLE `users` ADD `prefix` SMALLINT(6) NOT NULL DEFAULT '0' AFTER `last_login`;
ALTER TABLE `users` ADD `email_verified` BOOLEAN NOT NULL DEFAULT FALSE AFTER `status`;
ALTER TABLE `users` ADD `phone_verified` BOOLEAN NOT NULL DEFAULT FALSE AFTER `email_verified`;
ALTER TABLE `users` ADD `device_id` VARCHAR(63) NULL DEFAULT NULL AFTER `completed_at`;

-- 2023-05-30
DROP TABLE `user`;
UPDATE `users` SET `fullname` = `first_name` WHERE 1;
ALTER TABLE `users` ADD `id_no` VARCHAR(15) NULL DEFAULT NULL AFTER `last_provider`;
ALTER TABLE `users` ADD `id_date` DATE NULL DEFAULT NULL AFTER `id_no`;
ALTER TABLE `users` ADD `id_provider` VARCHAR(191) NULL DEFAULT NULL AFTER `id_date`;
ALTER TABLE `users` ADD `id_address` VARCHAR(191) NULL DEFAULT NULL AFTER `id_provider`;
ALTER TABLE `users` ADD `id_front` VARCHAR(255) NULL DEFAULT NULL AFTER `id_address`;
ALTER TABLE `users` ADD `id_behind` VARCHAR(255) NULL DEFAULT NULL AFTER `id_front`;
ALTER TABLE `users` ADD `tax` VARCHAR(191) NULL DEFAULT NULL AFTER `id_behind`;
ALTER TABLE `users` ADD `card_holder` VARCHAR(191) NULL DEFAULT NULL AFTER `tax`;
ALTER TABLE `users` ADD `bank_number` VARCHAR(191) NULL DEFAULT NULL AFTER `card_holder`;
ALTER TABLE `users` ADD `bank_name` VARCHAR(191) NULL DEFAULT NULL AFTER `bank_number`;
ALTER TABLE `users` ADD `bank_id` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `bank_name`;
ALTER TABLE `users` ADD `bank_branch` VARCHAR(191) NULL DEFAULT NULL AFTER `bank_id`;
ALTER TABLE `users` ADD `paypal_number` VARCHAR(191) NULL DEFAULT NULL AFTER `bank_branch`;

-- 2023-05-31
UPDATE `pd__products` SET `master_id` = NULL WHERE `master_id` = 0;

-- 2023-06-01
ALTER TABLE `orders` CHANGE `tax_code` `company_tax` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `orders` CHANGE `address` `company_address` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `orders` ADD `company_email` VARCHAR(96) NULL DEFAULT NULL AFTER `company_tax`;
ALTER TABLE `orders` ADD `note` VARCHAR(255) NULL DEFAULT NULL AFTER `shipping_at`;

-- 2023-06-01
ALTER TABLE `aff__agents` ADD `id_date` DATE NULL DEFAULT NULL AFTER `id_no`;
ALTER TABLE `aff__agents` ADD `id_provider` VARCHAR(191) NULL DEFAULT NULL AFTER `id_date`;
ALTER TABLE `aff__agents` ADD `card_holder` VARCHAR(191) NULL DEFAULT NULL AFTER `tax`;
ALTER TABLE `aff__agents` ADD `bank_name` VARCHAR(191) NULL DEFAULT NULL AFTER `bank_number`;
ALTER TABLE `aff__agents` ADD `bank_id` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `bank_name`;

-- 2023-06-01
ALTER TABLE `pd__product_reviews` ADD `files` TEXT NULL AFTER `review`;

-- 2023-06-05 - Huy
ALTER TABLE `aff__agents` ADD `points` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `website`;
ALTER TABLE `pd__products` CHANGE `banner` `banner` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `image_alt`;
ALTER TABLE `pd__products` ADD `is_gift` BOOLEAN NOT NULL DEFAULT FALSE AFTER `price`;
ALTER TABLE `pd__products` ADD `is_included` BOOLEAN NOT NULL DEFAULT FALSE AFTER `is_gift`;
ALTER TABLE `pd__products` ADD `num_of_child` SMALLINT(6) NOT NULL DEFAULT '0' AFTER `model`;
ALTER TABLE `users` ADD `coins` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `password_failed`;

-- 2023-06-06 - Huy
ALTER TABLE `orders` ADD `payment_status` ENUM('pending', 'in_process', 'paid', 'failed', 'unknown', 'refunded', 'canceled') NULL DEFAULT NULL AFTER `order_status`;

-- 2023-06-06 - Huy
ALTER TABLE `aff__agent_points` CHANGE `points` `points` INT(10) NOT NULL DEFAULT '0';
ALTER TABLE `aff__agent_points` ADD `amount` DECIMAL(15,0) NOT NULL DEFAULT '0' AFTER `points`;
ALTER TABLE `aff__agents` ADD `points` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `website`;
ALTER TABLE `aff__agents` ADD `amount` DECIMAL(15,0) NOT NULL DEFAULT '0' AFTER `points`;

-- 2023-06-09 - Huy
ALTER TABLE `aff__agents` ADD `commission` SMALLINT(6) UNSIGNED NOT NULL DEFAULT '0' AFTER `website`;
ALTER TABLE `aff__agents` CHANGE `points` `balance` DECIMAL(15,0) NOT NULL DEFAULT '0';
ALTER TABLE `aff__agent_points` ADD `commission` SMALLINT(6) UNSIGNED NOT NULL DEFAULT '0' AFTER `withdrawal_id`;
ALTER TABLE `aff__agent_points` CHANGE `points` `points` DECIMAL(15,0) NOT NULL DEFAULT '0';
ALTER TABLE `aff__agent_withdrawals` CHANGE `points` `points` DECIMAL(15,0) NOT NULL DEFAULT '0';

-- 2023-06-09 - Huy
ALTER TABLE `pd__products` CHANGE `stock_status` `stock_status` ENUM('in_stock','2_3_days','out_of_stock','pre_order') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `pd__products` CHANGE `model` `model` VARCHAR(63) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `name`, CHANGE `num_of_child` `num_of_child` SMALLINT(6) NOT NULL DEFAULT '0' AFTER `model`, CHANGE `warranty` `warranty` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `num_of_child`, CHANGE `quantity` `quantity` SMALLINT(6) NOT NULL DEFAULT '0' AFTER `warranty`, CHANGE `date_available` `date_available` DATE NULL DEFAULT NULL AFTER `quantity`, CHANGE `subtract` `subtract` TINYINT(1) NOT NULL DEFAULT '1' AFTER `date_available`, CHANGE `minimum` `minimum` INT(11) NOT NULL DEFAULT '1' AFTER `subtract`, CHANGE `stock_status` `stock_status` ENUM('in_stock','2_3_days','out_of_stock','pre_order') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `minimum`, CHANGE `price` `price` DECIMAL(15,2) NOT NULL DEFAULT '0.00' AFTER `stock_status`, CHANGE `is_gift` `is_gift` TINYINT(1) NOT NULL DEFAULT '0' AFTER `price`, CHANGE `is_included` `is_included` TINYINT(1) NOT NULL DEFAULT '0' AFTER `is_gift`, CHANGE `coins` `coins` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `is_included`, CHANGE `image` `image` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `coins`, CHANGE `image_alt` `image_alt` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `image`, CHANGE `banner` `banner` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `image_alt`, CHANGE `top` `top` TINYINT(1) NOT NULL DEFAULT '0' AFTER `banner`, CHANGE `viewed` `viewed` SMALLINT(6) NOT NULL DEFAULT '0' AFTER `top`, CHANGE `sort_order` `sort_order` SMALLINT(6) NOT NULL DEFAULT '1' AFTER `viewed`, CHANGE `status` `status` TINYINT(1) NOT NULL DEFAULT '0' AFTER `sort_order`, CHANGE `alias` `alias` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `status`;
ALTER TABLE `pd__products` CHANGE `quantity` `quantity` SMALLINT(6) NOT NULL DEFAULT '0' AFTER `num_of_child`, CHANGE `date_available` `date_available` DATE NULL DEFAULT NULL AFTER `quantity`, CHANGE `subtract` `subtract` TINYINT(1) NOT NULL DEFAULT '1' AFTER `date_available`, CHANGE `minimum` `minimum` INT(11) NOT NULL DEFAULT '1' AFTER `subtract`, CHANGE `stock_status` `stock_status` ENUM('in_stock','2_3_days','out_of_stock','pre_order') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `minimum`, CHANGE `price` `price` DECIMAL(15,2) NOT NULL DEFAULT '0.00' AFTER `stock_status`, CHANGE `is_gift` `is_gift` TINYINT(1) NOT NULL DEFAULT '0' AFTER `price`, CHANGE `is_included` `is_included` TINYINT(1) NOT NULL DEFAULT '0' AFTER `is_gift`, CHANGE `coins` `coins` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `is_included`, CHANGE `image` `image` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `coins`, CHANGE `image_alt` `image_alt` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `image`, CHANGE `banner` `banner` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `image_alt`, CHANGE `top` `top` TINYINT(1) NOT NULL DEFAULT '0' AFTER `banner`, CHANGE `viewed` `viewed` SMALLINT(6) NOT NULL DEFAULT '0' AFTER `top`, CHANGE `sort_order` `sort_order` SMALLINT(6) NOT NULL DEFAULT '1' AFTER `viewed`, CHANGE `status` `status` TINYINT(1) NOT NULL DEFAULT '0' AFTER `sort_order`, CHANGE `alias` `alias` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `status`, CHANGE `meta_title` `meta_title` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `alias`, CHANGE `meta_description` `meta_description` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `meta_title`, CHANGE `meta_keyword` `meta_keyword` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `meta_description`, CHANGE `short_description` `short_description` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `meta_keyword`, CHANGE `description` `description` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `short_description`, CHANGE `tag` `tag` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `description`, CHANGE `link` `link` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `tag`, CHANGE `properties` `properties` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `link`;
ALTER TABLE `pd__products` CHANGE `stock_status` `stock_status` ENUM('in_stock','2_3_days','out_of_stock','pre_order') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'in_stock';

ALTER TABLE `pd__products` ADD `weight` DECIMAL(15,0) NOT NULL DEFAULT '0' AFTER `coins`;
ALTER TABLE `pd__products` ADD `length` DECIMAL(15,0) NOT NULL DEFAULT '0' AFTER `weight`;
ALTER TABLE `pd__products` ADD `width` DECIMAL(15,0) NOT NULL DEFAULT '0' AFTER `length`;
ALTER TABLE `pd__products` ADD `height` DECIMAL(15,0) NOT NULL DEFAULT '0' AFTER `width`;
ALTER TABLE `pd__products` DROP `warranty`;

ALTER TABLE `bus__products` CHANGE `weight` `weight` DECIMAL(15,0) NOT NULL DEFAULT '0';
ALTER TABLE `bus__products` ADD `length` DECIMAL(15,0) NOT NULL DEFAULT '0' AFTER `weight`;
ALTER TABLE `bus__products` ADD `width` DECIMAL(15,0) NOT NULL DEFAULT '0' AFTER `length`;
ALTER TABLE `bus__products` ADD `height` DECIMAL(15,0) NOT NULL DEFAULT '0' AFTER `width`;

-- 2023-06-10 - Nhật
ALTER TABLE `loc__locations` ADD `link` TEXT NULL AFTER `address`;

-- 2023-06-11 - Huy
ALTER TABLE `pd__products` CHANGE `price` `price` DECIMAL(15,0) NOT NULL DEFAULT '0';
ALTER TABLE `pd__products` ADD `price_min` DECIMAL(15,0) NOT NULL DEFAULT '0' AFTER `height`;
ALTER TABLE `pd__products` ADD `price_max` DECIMAL(15,0) NOT NULL DEFAULT '0' AFTER `price_min`;

-- 2023-06-12 - Huy
ALTER TABLE `pd__products` ADD `is_free` BOOLEAN NOT NULL DEFAULT FALSE AFTER `is_gift`;
ALTER TABLE `pd__products` ADD `gift_set_id` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `price_max`;

-- 2023-06-17 - Huy
UPDATE `pd__products` SET `weight` = '300' WHERE `weight` = 0;
DELETE FROM `migrations` WHERE `migration` = '2022_04_03_053444_create_location_shipping_fees_table';
DROP TABLE `location__industrial_parks`;
DROP TABLE `location__locations`;
-- Run seed
-- php artisan module:seed location

-- 2023-06-20 - Huy
ALTER TABLE `crt__sessions` ADD `shipping_code` VARCHAR(31) NULL DEFAULT NULL AFTER `coupon`;
ALTER TABLE `crt__sessions` ADD `shipping_fee` DECIMAL(15,0) NULL DEFAULT '0' AFTER `shipping_code`;

-- 2023-06-21 - Truong
ALTER TABLE `pd__product_reviews` CHANGE `user_id` `user_id` INT(10) UNSIGNED NULL;
ALTER TABLE `pd__product_reviews` CHANGE `product_id` `product_id` INT(10) UNSIGNED NULL;
ALTER TABLE `pd__product_reviews` ADD `status` BOOLEAN NOT NULL DEFAULT TRUE AFTER `review`;
ALTER TABLE `pd__product_reviews` ADD `link` VARCHAR(255) NULL AFTER `review`;

-- 2023-06-25 - Truong
UPDATE `pg__page_contents` SET `layout` = 'layout1' WHERE `pg__page_contents`.`id` = 106 AND `pg__page_contents`.`code` = 'banners';

-- 2023-29-06 - Nhật
UPDATE `pd__product_specials` SET `date_start` = NULL WHERE `date_start` = '0000-00-00';
UPDATE `pd__product_specials` SET `date_end` = NULL WHERE `date_end` = '0000-00-00';
ALTER TABLE `pd__product_specials` CHANGE `date_start` `start_date` TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE `pd__product_specials` CHANGE `date_end` `end_date` TIMESTAMP NULL DEFAULT NULL;

-- 2023-07-03 - Truong
ALTER TABLE `user__coins` ADD `type` ENUM('order','product','review','referral') NULL DEFAULT NULL AFTER `user_id`;
ALTER TABLE `user__coins` CHANGE `order_id` `obj_id` INT(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `pd__product_reviews` CHANGE `status` `status` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `pd__product_reviews` ADD `approved_at` TIMESTAMP NULL AFTER `status`;

-- 2023-07-04 - Huy
DROP TABLE IF EXISTS `pd__order_options`;
DROP TABLE IF EXISTS `pd__option_values`;
DROP TABLE IF EXISTS `pd__options`;
DELETE FROM `migrations` WHERE `migration` = '2022_10_02_092251_create_options_table';
DELETE FROM `migrations` WHERE `migration` = '2022_10_02_092252_create_option_values_table';

DROP TABLE IF EXISTS `pd__product_option`;
DROP TABLE IF EXISTS `pd__product_options`;
DROP TABLE IF EXISTS `pd__product_option_value`;
DROP TABLE IF EXISTS `pd__product_option_values`;
DELETE FROM `migrations` WHERE `migration` = '2022_10_12_092253_create_product_options_table';
DELETE FROM `migrations` WHERE `migration` = '2022_10_12_092254_create_product_option_values_table';

-- 2023-07-04
UPDATE `pd__products` SET `properties` = NULL WHERE `properties` = '"{}"';
ALTER TABLE `pd__products` ADD `user_guide` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `properties`;
DROP TABLE IF EXISTS `pd__product_properties`;
DROP TABLE IF EXISTS `pd__property_desc`;
DROP TABLE IF EXISTS `pd__property_value_desc`;
DROP TABLE IF EXISTS `pd__property_values`;
DROP TABLE IF EXISTS `pd__properties`;
DROP TABLE IF EXISTS `pd__property_group_desc`;
DROP TABLE IF EXISTS `pd__property_groups`;
DELETE FROM `migrations` WHERE `migration` = '2020_11_10_073611_create_product_tab_desc_table';
DELETE FROM `migrations` WHERE `migration` = '2020_11_10_073610_create_product_tabs_table';
DELETE FROM `migrations` WHERE `migration` = '2022_02_17_114206_create_property_desc_table';
DELETE FROM `migrations` WHERE `migration` = '2022_02_17_114216_create_product_properties_table';
DELETE FROM `migrations` WHERE `migration` = '2022_08_09_153447_create_property_group_desc_table';
DELETE FROM `migrations` WHERE `migration` = '2022_02_17_114110_create_property_groups_table';
DELETE FROM `migrations` WHERE `migration` = '2022_09_06_115110_create_property_values_table';
DELETE FROM `migrations` WHERE `migration` = '2022_09_06_115206_create_property_value_desc_table';
DELETE FROM `migrations` WHERE `migration` = '2023_01_05_030038_update_property_value_230105_table';
DELETE FROM `migrations` WHERE `migration` = '2022_09_21_230038_update_property_group1213_table';
DELETE FROM `migrations` WHERE `migration` = '2022_10_19_030038_update_property_group_table';

-- 2023-07-05 - Truong
ALTER TABLE `pd__options` ADD `type` CHAR(15) NOT NULL AFTER `name`;
UPDATE `pd__options` SET `type` = 'vol' WHERE `pd__options`.`id` = 1;
UPDATE `pd__options` SET `type` = 'color' WHERE `pd__options`.`id` = 2;
ALTER TABLE `pd__option_values` ADD `value` VARCHAR(255) NULL AFTER `name`;

-- Deployed

-- DROP TABLE IF EXISTS `pd__product_tab_desc`;
-- DROP TABLE IF EXISTS `pd__product_tabs`;

-- 2023-07-06 Huy Cart
ALTER TABLE `crt__carts` CHANGE `user_id` `user_id` INT(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `crt__carts` ADD `type` ENUM('T', 'G', 'I') NOT NULL DEFAULT 'T' AFTER `session_id`;
ALTER TABLE `order__products` ADD `type` ENUM('T', 'G', 'I') NOT NULL DEFAULT 'T' AFTER `model`;
ALTER TABLE `order__products` ADD `coins` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `total`;
ALTER TABLE `orders` ADD `total_coins` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `total`;
ALTER TABLE `users` CHANGE `status` `status` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'starter';

-- 2023-07-06
ALTER TABLE `user__coins` ADD `total` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `coins`;

-- 2023-07-10
ALTER TABLE `users` ADD `share_code` VARCHAR(63) NULL DEFAULT NULL AFTER `device_id`;
ALTER TABLE `users` ADD `points` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `coins`;

-- 2023-07-11
ALTER TABLE `orders` ADD `referral_code` VARCHAR(20) NULL DEFAULT NULL AFTER `ip`;
ALTER TABLE `orders` ADD `voucher_code` VARCHAR(10) NULL DEFAULT NULL AFTER `discount_total`;
ALTER TABLE `orders` ADD `voucher_total` DECIMAL(15,0) NOT NULL DEFAULT '0' AFTER `voucher_code`;
ALTER TABLE `crt__sessions` ADD `voucher` VARCHAR(10) NULL DEFAULT NULL AFTER `coupon`;

-- 2023-07-12
ALTER TABLE `mkt__vouchers` CHANGE `order_id` `order_id` INT(10) UNSIGNED NULL DEFAULT NULL;

-- 2023-07-13
ALTER TABLE `order__histories` ADD `payment_status` ENUM('pending', 'in_process', 'paid', 'failed', 'unknown', 'refunded', 'canceled') NULL DEFAULT NULL AFTER `order_status`;
ALTER TABLE `orders` ADD `shipping_status` ENUM('create_order', 'delivering', 'delivered', 'return') NULL DEFAULT NULL AFTER `payment_status`;

-- Deployed
-- 2023-07-13 - Truong
ALTER TABLE `orders` ADD `shipping_time` VARCHAR(64) NULL AFTER `shipping_method`;

-- 2023-07-14
INSERT INTO `setting` (`id`, `code`, `key`, `value`, `serialized`) VALUES (NULL, 'config', 'config_onepay_terms', '{\"vi\":\"\\u003Ch1\\u003E\\u0110I\\u1ec0U KHO\\u1ea2N D\\u1ecaCH V\\u1ee4\\u003C\\/h1\\u003E\\n\\u003Cp\\u003ED\\u01b0\\u1edbi \\u0111&acirc;y l&agrave; nh\\u1eefng \\u0111i\\u1ec1u kho\\u1ea3n \\u0111\\u01b0\\u1ee3c &aacute;p d\\u1ee5ng cho kh&aacute;ch h&agrave;ng v&agrave; \\u0111\\u1ed1i t&aacute;c c\\u1ee7a MiharuBeauty. Xin h&atilde;y \\u0111\\u1ecdc k\\u1ef9 to&agrave;n b\\u1ed9 th\\u1ecfa thu\\u1eadn tr\\u01b0\\u1edbc khi tham gia.\\u003C\\/p\\u003E\\n\\u003Cp\\u003EM\\u1ed9t khi b\\u1ea1n \\u0111&atilde; \\u0111\\u0103ng k&yacute; tham gia tr&ecirc;n MiharuBeauty.com, ch&uacute;ng t&ocirc;i s\\u1ebd hi\\u1ec3u r\\u1eb1ng b\\u1ea1n \\u0111&atilde; \\u0111\\u1ecdc v&agrave; \\u0111\\u1ed3ng &yacute; to&agrave;n b\\u1ed9 \\u0111i\\u1ec1u kho\\u1ea3n \\u0111\\u01b0\\u1ee3c \\u0111\\u01b0a ra trong b\\u1ea3n th\\u1ecfa thu\\u1eadn n&agrave;y.\\u003C\\/p\\u003E\\n\\u003Cp\\u003EB\\u1ea3n c\\u1eadp nh\\u1eadt m\\u1edbi nh\\u1ea5t (n\\u1ebfu c&oacute;) s\\u1ebd \\u0111\\u01b0\\u1ee3c \\u0111\\u0103ng t\\u1ea1i t\\u1ea1i \\u0111&acirc;y v&agrave; MiharuBeauty s\\u1ebd kh&ocirc;ng th&ocirc;ng b&aacute;o \\u0111\\u1ebfn t\\u1eebng \\u0111\\u1ed1i t&aacute;c, v&igrave; v\\u1eady b\\u1ea1n h&atilde;y quay l\\u1ea1i trang n&agrave;y th\\u01b0\\u1eddng xuy&ecirc;n \\u0111\\u1ec3 c\\u1eadp nh\\u1eadt ch&iacute;nh s&aacute;ch m\\u1edbi nh\\u1ea5t.\\u003C\\/p\\u003E\\n\\u003Ch3\\u003E\\u0110I\\u1ec0U KHO\\u1ea2N CHUNG\\u003C\\/h3\\u003E\\n\\u003Ch4\\u003E\\u0110i\\u1ec1u 1: Th&ocirc;ng tin t&agrave;i kho\\u1ea3n c&aacute; nh&acirc;n\\u003C\\/h4\\u003E\\n\\u003Cul\\u003E\\n\\u003Cli\\u003EKhi \\u0111\\u0103ng k&yacute; t&agrave;i kho\\u1ea3n MiharuBeauty, \\u0111\\u1ec3 \\u0111\\u01b0\\u1ee3c h\\u1ed7 tr\\u1ee3 nhanh ch&oacute;ng, b\\u1ea1n c\\u1ea7n cung c\\u1ea5p \\u0111\\u1ea7y \\u0111\\u1ee7 v&agrave; ch&iacute;nh x&aacute;c c&aacute;c th&ocirc;ng tin: H\\u1ecd t&ecirc;n, Email, Phone, Gi\\u1edbi t&iacute;nh, Ng&agrave;y sinh, Th&agrave;nh ph\\u1ed1,..\\u003C\\/li\\u003E\\n\\u003Cli\\u003ECh&uacute;ng t&ocirc;i s\\u1eed d\\u1ee5ng th&ocirc;ng tin li&ecirc;n l\\u1ea1c c\\u1ee7a b\\u1ea1n \\u0111\\u1ec3 g\\u1eedi m&atilde; k&iacute;ch ho\\u1ea1t s\\u1ea3n ph\\u1ea9m, th&ocirc;ng b&aacute;o ch\\u01b0\\u01a1ng tr&igrave;nh khuy\\u1ebfn m&atilde;i, x&aacute;c nh\\u1eadn \\u0111\\u1ed5i m\\u1eadt kh\\u1ea9u, c&aacute;c th\\u1ea3o lu\\u1eadn trong l\\u1edbp h\\u1ecdc,..\\u003C\\/li\\u003E\\n\\u003Cli\\u003ETh&ocirc;ng tin ng&agrave;y sinh v&agrave; gi\\u1edbi t&iacute;nh d&ugrave;ng \\u0111\\u1ec3 g\\u1ee3i &yacute; \\u0111\\u1ebfn b\\u1ea1n nh\\u1eefng s\\u1ea3n ph\\u1ea9m ph&ugrave; h\\u1ee3p, c\\u0169ng nh\\u01b0 g\\u1eedi qu&agrave; t\\u1eb7ng \\u0111\\u1ebfn b\\u1ea1n trong ng&agrave;y sinh nh\\u1eadt.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EB\\u1ea1n c&oacute; th\\u1ec3 \\u0111\\u0103ng nh\\u1eadp b\\u1eb1ng t&agrave;i kho\\u1ea3n MiharuBeauty (email + m\\u1eadt kh\\u1ea9u) ho\\u1eb7c \\u0111\\u0103ng nh\\u1eadp b\\u1eb1ng Google, Facebook.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EB\\u1ea1n c&oacute; th\\u1ec3 c\\u1eadp nh\\u1eadt th&ocirc;ng tin c&aacute; nh&acirc;n ho\\u1eb7c h\\u1ee7y (x&oacute;a) t&agrave;i kho\\u1ea3n b\\u1ea5t k\\u1ef3 l&uacute;c n&agrave;o khi kh&ocirc;ng c&ograve;n nhu c\\u1ea7u s\\u1eed d\\u1ee5ng\\u003C\\/li\\u003E\\n\\u003C\\/ul\\u003E\\n\\u003Ch4\\u003E\\u0110i\\u1ec1u 2: Vi\\u1ec7c b\\u1ea3o m\\u1eadt th&ocirc;ng tin\\u003C\\/h4\\u003E\\n\\u003Cul\\u003E\\n\\u003Cli\\u003EB\\u1ea1n c&oacute; tr&aacute;ch nhi\\u1ec7m t\\u1ef1 m&igrave;nh b\\u1ea3o qu\\u1ea3n m\\u1eadt kh\\u1ea9u, n\\u1ebfu m\\u1eadt kh\\u1ea9u b\\u1ecb l\\u1ed9 ra ngo&agrave;i d\\u01b0\\u1edbi b\\u1ea5t k\\u1ef3 h&igrave;nh th\\u1ee9c n&agrave;o, MiharuBeauty s\\u1ebd kh&ocirc;ng ch\\u1ecbu tr&aacute;ch nhi\\u1ec7m v\\u1ec1 m\\u1ecdi t\\u1ed5n th\\u1ea5t ph&aacute;t sinh.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EM\\u1ecdi th&ocirc;ng tin c&aacute; nh&acirc;n c\\u1ee7a b\\u1ea1n s\\u1ebd \\u0111\\u01b0\\u1ee3c ch&uacute;ng t&ocirc;i b\\u1ea3o m\\u1eadt, kh&ocirc;ng ti\\u1ebft l\\u1ed9 ra ngo&agrave;i. Ch&uacute;ng t&ocirc;i kh&ocirc;ng b&aacute;n hay trao \\u0111\\u1ed5i nh\\u1eefng th&ocirc;ng tin n&agrave;y v\\u1edbi b\\u1ea5t k\\u1ef3 m\\u1ed9t b&ecirc;n th\\u1ee9 ba n&agrave;o kh&aacute;c. Tuy nhi&ecirc;n, trong tr\\u01b0\\u1eddng h\\u1ee3p c\\u01a1 quan ch\\u1ee9c n\\u0103ng y&ecirc;u c\\u1ea7u, MiharuBeauty bu\\u1ed9c ph\\u1ea3i cung c\\u1ea5p nh\\u1eefng th&ocirc;ng tin n&agrave;y theo quy \\u0111\\u1ecbnh ph&aacute;p lu\\u1eadt.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EB\\u1ea1n c&oacute; quy\\u1ec1n s\\u1edf h\\u1eefu tr\\u1ecdn \\u0111\\u1eddi c&aacute;c s\\u1ea3n ph\\u1ea9m \\u0111&atilde; \\u0111\\u0103ng k&yacute;: kh&ocirc;ng gi\\u1edbi h\\u1ea1n s\\u1ed1 l\\u1ea7n tham gia h\\u1ecdc v&agrave; th\\u1eddi gian h\\u1ecdc.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EB\\u1ea1n kh&ocirc;ng \\u0111\\u01b0\\u1ee3c download video, kh&ocirc;ng \\u0111\\u01b0\\u1ee3c chia s\\u1ebb video l&ecirc;n Internet v\\u1edbi b\\u1ea5t k\\u1ef3 h&igrave;nh th\\u1ee9c n&agrave;o. N\\u1ebfu vi ph\\u1ea1m, t&agrave;i kho\\u1ea3n c\\u1ee7a b\\u1ea1n s\\u1ebd b\\u1ecb kho&aacute; v&agrave; b\\u1ea1n ph\\u1ea3i ch\\u1ecbu tr&aacute;ch nhi\\u1ec7m tr\\u01b0\\u1edbc ph&aacute;p lu\\u1eadt v\\u1ec1 h&agrave;nh vi x&acirc;m ph\\u1ea1m s\\u1edf h\\u1eefu tr&iacute; tu\\u1ec7.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EMiharuBeauty c&oacute; th\\u1ec3 g\\u1eedi th&ocirc;ng b&aacute;o t&igrave;nh h&igrave;nh h\\u1ecdc t\\u1eadp, ch\\u01b0\\u01a1ng tr&igrave;nh khuy\\u1ebfn m&atilde;i (n\\u1ebfu c&oacute;), th&ocirc;ng b&aacute;o s\\u1ea3n ph\\u1ea9m m\\u1edbi s\\u1eafp ra m\\u1eaft \\u0111\\u1ec3 kh&aacute;ch h&agrave;ng quan t&acirc;m c&oacute; th\\u1ec3 \\u0111\\u0103ng k&yacute; ngay \\u0111\\u1ec3 \\u0111\\u01b0\\u1ee3c \\u01b0u \\u0111&atilde;i. N\\u1ebfu b\\u1ea1n kh&ocirc;ng mu\\u1ed1n nh\\u1eadn email c&oacute; th\\u1ec3 b\\u1ea5m v&agrave;o link \\\"Ng\\u1eebng nh\\u1eadn email\\\" \\u1edf cu\\u1ed1i email.\\u003C\\/li\\u003E\\n\\u003C\\/ul\\u003E\\n\\u003Ch4\\u003E\\u0110i\\u1ec1u 3: \\u0110&aacute;nh gi&aacute; s\\u1ea3n ph\\u1ea9m v&agrave; th\\u1ea3o lu\\u1eadn\\u003C\\/h4\\u003E\\n\\u003Cul\\u003E\\n\\u003Cli\\u003EH\\u1ecdc vi&ecirc;n khi tham gia s\\u1ea3n ph\\u1ea9m tr&ecirc;n MiharuBeauty c&oacute; quy\\u1ec1n \\u0111&aacute;nh gi&aacute; v\\u1ec1 ch\\u1ea5t l\\u01b0\\u1ee3ng s\\u1ea3n ph\\u1ea9m.\\u003C\\/li\\u003E\\n\\u003Cli\\u003ETrong qu&aacute; tr&igrave;nh h\\u1ecdc, kh&aacute;ch h&agrave;ng c&oacute; b\\u1ea5t k\\u1ef3 th\\u1eafc m\\u1eafc hay g&oacute;p &yacute; n&agrave;o c&oacute; th\\u1ec3 \\u0111\\u0103ng b&igrave;nh lu\\u1eadn c\\u1ee7a m&igrave;nh l&ecirc;n ph\\u1ea7n Th\\u1ea3o lu\\u1eadn - ngay trong giao di\\u1ec7n b&agrave;i h\\u1ecdc \\u0111\\u1ec3 \\u0111\\u01b0\\u1ee3c chuy&ecirc;n vi&ecirc;n MiharuBeauty v&agrave; Gi\\u1ea3ng vi&ecirc;n h\\u1ed7 tr\\u1ee3 gi\\u1ea3i \\u0111&aacute;p.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EB&ecirc;n c\\u1ea1nh \\u0111&oacute;, c&aacute;c s\\u1ea3n ph\\u1ea9m l\\u1edbn tr&ecirc;n MiharuBeauty \\u0111\\u1ec1u c&oacute; Group Th\\u1ea3o lu\\u1eadn ri&ecirc;ng cho c&aacute;c kh&aacute;ch h&agrave;ng v&agrave; gi\\u1ea3ng vi&ecirc;n \\u0111\\u1ec3 trao \\u0111\\u1ed5i c&aacute;c v\\u1ea5n \\u0111\\u1ec1 chuy&ecirc;n m&ocirc;n.\\u003C\\/li\\u003E\\n\\u003C\\/ul\\u003E\\n\\u003Ch4\\u003E\\u0110i\\u1ec1u 4: Nghi&ecirc;m c\\u1ea5m s\\u1eed d\\u1ee5ng d\\u1ecbch v\\u1ee5 v\\u1edbi c&aacute;c h&agrave;nh vi d\\u01b0\\u1edbi \\u0111&acirc;y\\u003C\\/h4\\u003E\\n\\u003Cul\\u003E\\n\\u003Cli\\u003ES\\u1eed d\\u1ee5ng b\\u1ea5t k\\u1ef3 c&ocirc;ng c\\u1ee5 hay h&igrave;nh th\\u1ee9c n&agrave;o \\u0111\\u1ec3 can thi\\u1ec7p v&agrave;o c&aacute;c d\\u1ecbch v\\u1ee5, s\\u1ea3n ph\\u1ea9m trong h\\u1ec7 th\\u1ed1ng MiharuBeauty.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EPh&aacute;t t&aacute;n ho\\u1eb7c tuy&ecirc;n truy\\u1ec1n c\\u1ed5 v\\u0169 c&aacute;c ho\\u1ea1t \\u0111\\u1ed9ng ph&aacute;t t&aacute;n, can thi\\u1ec7p v&agrave; ph&aacute; ho\\u1ea1i n\\u1ed9i dung c&aacute;c b&agrave;i h\\u1ecdc tr&ecirc;n h\\u1ec7 th\\u1ed1ng c\\u1ee7a MiharuBeauty ra b&ecirc;n ngo&agrave;i. M\\u1ecdi vi ph\\u1ea1m khi b\\u1ecb ph&aacute;t hi\\u1ec7n s\\u1ebd b\\u1ecb x&oacute;a t&agrave;i kho\\u1ea3n v&agrave; c&oacute; th\\u1ec3 x\\u1eed l&yacute; theo quy \\u0111\\u1ecbnh c\\u1ee7a ph&aacute;p lu\\u1eadt v\\u1ec1 vi\\u1ec7c vi ph\\u1ea1m b\\u1ea3n quy\\u1ec1n.\\u003C\\/li\\u003E\\n\\u003Cli\\u003ES\\u1eed d\\u1ee5ng chung t&agrave;i kho\\u1ea3n: v\\u1edbi vi\\u1ec7c tr&ecirc;n 2 ng\\u01b0\\u1eddi c&ugrave;ng s\\u1eed d\\u1ee5ng chung m\\u1ed9t t&agrave;i kho\\u1ea3n khi b\\u1ecb ph&aacute;t hi\\u1ec7n s\\u1ebd b\\u1ecb x&oacute;a t&agrave;i kho\\u1ea3n ngay l\\u1eadp t\\u1ee9c.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EX&uacute;c ph\\u1ea1m, nh\\u1ea1o b&aacute;ng ng\\u01b0\\u1eddi kh&aacute;c d\\u01b0\\u1edbi b\\u1ea5t k\\u1ef3 h&igrave;nh th\\u1ee9c n&agrave;o: ch&ecirc; bai, k\\u1ef3 th\\u1ecb t&ocirc;n gi&aacute;o, gi\\u1edbi t&iacute;nh, s\\u1eafc t\\u1ed9c..\\u003C\\/li\\u003E\\n\\u003Cli\\u003EH&agrave;nh vi m\\u1ea1o nh\\u1eadn hay c\\u1ed1 &yacute; l&agrave;m ng\\u01b0\\u1eddi kh&aacute;c t\\u01b0\\u1edfng l\\u1ea7m m&igrave;nh l&agrave; m\\u1ed9t ng\\u01b0\\u1eddi s\\u1eed d\\u1ee5ng kh&aacute;c trong h\\u1ec7 th\\u1ed1ng d\\u1ecbch v\\u1ee5 c\\u1ee7a MiharuBeauty.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EB&agrave;n lu\\u1eadn v\\u1ec1 c&aacute;c v\\u1ea5n \\u0111\\u1ec1 ch&iacute;nh tr\\u1ecb, k\\u1ef3 th\\u1ecb t&ocirc;n gi&aacute;o, k\\u1ef3 th\\u1ecb s\\u1eafc t\\u1ed9c.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EH&agrave;nh vi, th&aacute;i \\u0111\\u1ed9 l&agrave;m t\\u1ed5n h\\u1ea1i \\u0111\\u1ebfn uy t&iacute;n c\\u1ee7a c&aacute;c s\\u1ea3n ph\\u1ea9m, d\\u1ecbch v\\u1ee5, s\\u1ea3n ph\\u1ea9m trong h\\u1ec7 th\\u1ed1ng MiharuBeauty d\\u01b0\\u1edbi b\\u1ea5t k\\u1ef3 h&igrave;nh th\\u1ee9c n&agrave;o, ph\\u01b0\\u01a1ng th\\u1ee9c n&agrave;o.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EMua b&aacute;n chuy\\u1ec3n nh\\u01b0\\u1ee3ng t&agrave;i kho\\u1ea3n, s\\u1ea3n ph\\u1ea9m c\\u1ee7a MiharuBeauty.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EM\\u1ea1o danh MiharuBeauty \\u1ea3nh h\\u01b0\\u1edfng \\u0111\\u1ebfn uy t&iacute;n c\\u1ee7a MiharuBeauty, g&acirc;y s\\u1ef1 nh\\u1ea7m l\\u1eabn cho c&aacute;c kh&aacute;ch h&agrave;ng v&agrave; \\u0111\\u1ed1i t&aacute;c theo b\\u1ea5t k\\u1ef3 ph\\u01b0\\u01a1ng th\\u1ee9c n&agrave;o (d&ugrave;ng \\u0111\\u1ecba ch\\u1ec9 email, t&ecirc;n mi\\u1ec1n website, fanpage c&oacute; ch\\u1eef MiharuBeauty, C\\u1eeda S\\u1ed5 V&agrave;ng, Nguy\\u1ec5n Duy C\\u01b0\\u01a1ng...)\\u003C\\/li\\u003E\\n\\u003Cli\\u003EKhi ph&aacute;t hi\\u1ec7n nh\\u1eefng h&agrave;nh vi tr&ecirc;n t\\u1eeb t&agrave;i kho\\u1ea3n c\\u1ee7a b\\u1ea1n, MiharuBeauty c&oacute; quy\\u1ec1n t\\u01b0\\u1edbc b\\u1ecf m\\u1ecdi quy\\u1ec1n l\\u1ee3i li&ecirc;n quan \\u0111\\u1ed1i v\\u1edbi t&agrave;i kho\\u1ea3n (bao g\\u1ed3m vi\\u1ec7c kh&oacute;a t&agrave;i kho\\u1ea3n) ho\\u1eb7c s\\u1eed d\\u1ee5ng nh\\u1eefng th&ocirc;ng tin m&agrave; b\\u1ea1n cung c\\u1ea5p khi \\u0111\\u0103ng k&yacute; t&agrave;i kho\\u1ea3n \\u0111\\u1ec3 chuy\\u1ec3n cho c\\u01a1 quan ch\\u1ee9c n\\u0103ng gi\\u1ea3i quy\\u1ebft theo quy \\u0111\\u1ecbnh c\\u1ee7a ph&aacute;p lu\\u1eadt.\\u003C\\/li\\u003E\\n\\u003C\\/ul\\u003E\\n\\u003Ch4\\u003E\\u0110i\\u1ec1u 5: Ch&iacute;nh s&aacute;ch ho&agrave;n tr\\u1ea3 h\\u1ecdc ph&iacute;\\u003C\\/h4\\u003E\\n\\u003Cul\\u003E\\n\\u003Cli\\u003EKhi th\\u1ef1c hi\\u1ec7n thanh to&aacute;n online th&agrave;nh c&ocirc;ng tr&ecirc;n website sweetgirlbeauty.com th&igrave; s\\u1ea3n ph\\u1ea9m c\\u1ea5p t\\u1ef1 \\u0111\\u1ed9ng t\\u1ea1i app MiharuBeauty v\\u1edbi c&ugrave;ng t&agrave;i kho\\u1ea3n tr&ecirc;n website&nbsp; sweetgirlbeauty.com. Trong tr\\u01b0\\u1eddng h\\u1ee3p n&agrave;y qu&yacute; kh&aacute;ch \\u0111&atilde; nh\\u1eadn \\u0111\\u01b0\\u1ee3c s\\u1ea3n ph\\u1ea9m t\\u1eeb MiharuBeauty, th&igrave; ch&iacute;nh s&aacute;ch ho&agrave;n ph&iacute; kh&ocirc;ng \\u0111\\u01b0\\u1ee3c &aacute;p d\\u1ee5ng.\\u003C\\/li\\u003E\\n\\u003C\\/ul\\u003E\\n\\u003Ch3\\u003EB\\u1ea2O M\\u1eacT THANH TO&Aacute;N\\u003C\\/h3\\u003E\\n\\u003Ch4\\u003E\\u0110i\\u1ec1u 6: Quy Ch\\u1ebf Thanh To&aacute;n\\u003C\\/h4\\u003E\\n\\u003Cul\\u003E\\n\\u003Cli\\u003E\\u0110\\u1ec3 \\u0111\\u1ea3m b\\u1ea3o an to&agrave;n b\\u1ea3o m\\u1eadt thanh to&aacute;n cho kh&aacute;ch h&agrave;ng, \\u1edf&nbsp; sweetgirlbeauty.com b\\u1ea1n s\\u1ebd ch\\u1ec9 \\u0111\\u1ec3 l\\u1ea1i th&ocirc;ng tin \\u0111\\u1eb7t h&agrave;ng nh\\u01b0 t&ecirc;n, s\\u1ed1 \\u0111i\\u1ec7n tho\\u1ea1i, \\u0111\\u1ecba ch\\u1ec9 nh\\u1eadn h&agrave;ng, email v&agrave; thanh to&aacute;n b\\u1eb1ng h&igrave;nh th\\u1ee9c chuy\\u1ec3n kho\\u1ea3n.\\u003C\\/li\\u003E\\n\\u003Cli\\u003E\\u0110\\u1ed1i v\\u1edbi c&aacute;c h&igrave;nh th\\u1ee9c thanh to&aacute;n tr\\u1ef1c tuy\\u1ebfn, sau khi \\u0111\\u01a1n h&agrave;ng \\u0111\\u01b0\\u1ee3c kh\\u1edfi t\\u1ea1o v&agrave; \\u0111\\u1ec3 l\\u1ea1i th&ocirc;ng tin \\u0111\\u1eb7t h&agrave;ng nh\\u01b0 t&ecirc;n, s\\u1ed1 \\u0111i\\u1ec7n tho\\u1ea1i, \\u0111\\u1ecba ch\\u1ec9 nh\\u1eadn h&agrave;ng, email, v&agrave; ch\\u1ecdn h&igrave;nh th\\u1ee9c thanh to&aacute;n tr\\u1ef1c tuy\\u1ebfn b\\u1ea1n s\\u1ebd \\u0111\\u01b0\\u1ee3c chuy\\u1ec3n v\\u1ec1 trang onepay.vn \\u0111\\u1ec3 th\\u1ef1c hi\\u1ec7n c&aacute;c giao d\\u1ecbch b\\u1eb1ng th\\u1ebb qu\\u1ed1c t\\u1ebf, n\\u1ed9i \\u0111\\u1ecba, internet banking, v&iacute; \\u0111i\\u1ec7n t\\u1eed,&hellip; sau khi giao d\\u1ecbch th&agrave;nh c&ocirc;ng t\\u1ea1i onepay.vn th&igrave; cha m\\u1eb9 s\\u1ebd \\u0111\\u01b0\\u1ee3c tr\\u1ea3 th&ocirc;ng tin v\\u1ec1&nbsp; sweetgirlbeauty.com.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EL\\u01b0u &yacute;: khi thanh \\u0111\\u1eb7t h&agrave;ng t\\u1ea1i sweetgirlbeauty.com, b\\u1ea1n ch\\u1ec9 thanh to&aacute;n tr\\u1ef1c tuy\\u1ebfn (b\\u1eb1ng th\\u1ebb qu\\u1ed1c t\\u1ebf, n\\u1ed9i \\u0111\\u1ecba, v&iacute; \\u0111i\\u1ec7n t\\u1eed,&hellip;) khi v&agrave; ch\\u1ec9 khi \\u0111&atilde; \\u0111\\u01b0\\u1ee3c chuy\\u1ec3n qua trang web onepay.vn v&agrave; tuy\\u1ec7t \\u0111\\u1ed1i KH&Ocirc;NG cung c\\u1ea5p th&ocirc;ng tin cho b\\u1ea5t c\\u1ee9 c&aacute; nh&acirc;n, \\u0111\\u01a1n v\\u1ecb n&agrave;o kh&aacute;c.\\u003C\\/li\\u003E\\n\\u003C\\/ul\\u003E\\n\\u003Ch4\\u003E\\u0110i\\u1ec1u 7: Cam K\\u1ebft B\\u1ea3o M\\u1eadt T\\u1ea1i ONEPAY.VN\\u003C\\/h4\\u003E\\n\\u003Cp\\u003EH\\u1ec7 th\\u1ed1ng thanh to&aacute;n th\\u1ebb \\u0111\\u01b0\\u1ee3c cung c\\u1ea5p b\\u1edfi c&aacute;c \\u0111\\u1ed1i t&aacute;c c\\u1ed5ng thanh to&aacute;n ONEPAY (&ldquo;\\u0110\\u1ed1i T&aacute;c C\\u1ed5ng Thanh To&aacute;n&rdquo;) \\u0111&atilde; \\u0111\\u01b0\\u1ee3c c\\u1ea5p ph&eacute;p ho\\u1ea1t \\u0111\\u1ed9ng h\\u1ee3p ph&aacute;p t\\u1ea1i Vi\\u1ec7t Nam. Theo \\u0111&oacute;, c&aacute;c ti&ecirc;u chu\\u1ea9n b\\u1ea3o m\\u1eadt thanh to&aacute;n th\\u1ebb t\\u1ea1i onepay.vn \\u0111\\u1ea3m b\\u1ea3o tu&acirc;n th\\u1ee7 theo c&aacute;c ti&ecirc;u chu\\u1ea9n b\\u1ea3o m\\u1eadt ng&agrave;nh.\\u003C\\/p\\u003E\\n\\u003Cp\\u003EGi\\u1ea5y Ch\\u1ee9ng Nh\\u1eadn Tu&acirc;n Th\\u1ee7 PCI DSS\\u003C\\/p\\u003E\\n\\u003Cul\\u003E\\n\\u003Cli\\u003EPCI DSS l&agrave; ti&ecirc;u chu\\u1ea9n b\\u1ea3o m\\u1eadt qu\\u1ed1c t\\u1ebf c&oacute; gi&aacute; tr\\u1ecb to&agrave;n c\\u1ea7u do H\\u1ed9i \\u0111\\u1ed3ng ti&ecirc;u chu\\u1ea9n b\\u1ea3o m\\u1eadt SSC thi\\u1ebft l\\u1eadp (Security Standards Council).\\u003C\\/li\\u003E\\n\\u003Cli\\u003EV\\u1edbi vi\\u1ec7c tu&acirc;n th\\u1ee7 PCI DSS, OnePay tham gia c&aacute;c ch\\u01b0\\u01a1ng tr&igrave;nh b\\u1ea3o v\\u1ec7 nh\\u01b0 Verified by Visa c\\u1ee7a VISA, MasterCard SecureCode c\\u1ee7a MasterCard, J\\/Secure c\\u1ee7a JCB, Safe Key c\\u1ee7a American Express.\\u003C\\/li\\u003E\\n\\u003C\\/ul\\u003E\\n\\u003Cp\\u003ECh\\u1ee9ng Ch\\u1ec9 TLS\\u003C\\/p\\u003E\\n\\u003Cul\\u003E\\n\\u003Cli\\u003ETh&ocirc;ng tin th\\u1ebb c\\u1ee7a b\\u1ea1n s\\u1ebd \\u0111\\u01b0\\u1ee3c b\\u1ea3o v\\u1ec7 trong su\\u1ed1t qu&aacute; tr&igrave;nh giao d\\u1ecbch b\\u1eb1ng giao th\\u1ee9c TLS. Giao th\\u1ee9c TLS s\\u1ebd m&atilde; h&oacute;a th&ocirc;ng tin b\\u1ea1n cung c\\u1ea5p trong su\\u1ed1t qu&aacute; tr&igrave;nh giao d\\u1ecbch.\\u003C\\/li\\u003E\\n\\u003Cli\\u003ENgo&agrave;i ra, OnePay c&ograve;n s\\u1eed d\\u1ee5ng nhi\\u1ec1u ph\\u1ea7n m\\u1ec1m v&agrave; thu\\u1eadt to&aacute;n kh&aacute;c \\u0111\\u1ec3 \\u0111\\u1ea3m b\\u1ea3o an ninh d\\u1eef li\\u1ec7u, an to&agrave;n tr&ecirc;n m&ocirc;i tr\\u01b0\\u1eddng Internet.\\u003C\\/li\\u003E\\n\\u003C\\/ul\\u003E\\n\\u003Cp\\u003EC\\u01a1 S\\u1edf H\\u1ea1 T\\u1ea7ng Ti&ecirc;n Ti\\u1ebfn\\u003C\\/p\\u003E\\n\\u003Cul\\u003E\\n\\u003Cli\\u003ETrung t&acirc;m d\\u1eef li\\u1ec7u ti&ecirc;u chu\\u1ea9n qu\\u1ed1c t\\u1ebf. Ti&ecirc;u chu\\u1ea9n b\\u1ea3o m\\u1eadt ISO\\/IEC 27001:2005. B\\u1ea3o \\u0111\\u1ea3m v\\u1eadn h&agrave;nh trung t&acirc;m kh&ocirc;i ph\\u1ee5c d\\u1eef li\\u1ec7u ch&iacute;nh.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EONEPAY cam k\\u1ebft \\u0111\\u1ea3m b\\u1ea3o th\\u1ef1c hi\\u1ec7n nghi&ecirc;m t&uacute;c c&aacute;c bi\\u1ec7n ph&aacute;p b\\u1ea3o m\\u1eadt c\\u1ea7n thi\\u1ebft cho m\\u1ecdi ho\\u1ea1t \\u0111\\u1ed9ng thanh to&aacute;n th\\u1ef1c hi\\u1ec7n tr&ecirc;n onepay.vn.\\u003C\\/li\\u003E\\n\\u003C\\/ul\\u003E\\n\\u003Ch3\\u003E\\u0110I\\u1ec0U KHO\\u1ea2N \\u0110\\u1ed0I V\\u1edaI NG\\u01af\\u1edcI CHIA S\\u1eba&nbsp;\\u003C\\/h3\\u003E\\n\\u003Ch4\\u003E\\u0110i\\u1ec1u 8. Quy \\u0111\\u1ecbnh v\\u1ec1 vi\\u1ec7c ph&acirc;n ph\\u1ed1i s\\u1ea3n ph\\u1ea9m v\\u1edbi M&atilde; chia s\\u1ebb&nbsp;\\u003C\\/h4\\u003E\\n\\u003Cul\\u003E\\n\\u003Cli\\u003EB\\u1ea1n c&oacute; th\\u1ec3 truy c\\u1eadp v&agrave;o ph\\u1ea7n T&agrave;i kho\\u1ea3n c\\u1ee7a t&ocirc;i &gt; \\u0110i\\u1ec3m chia s\\u1ebb t\\u1ea1i website sweetgirlbeauty.com \\u0111\\u1ec3 l\\u1ea5y m&atilde; chia s\\u1ebb (ch&iacute;nh l&agrave; s\\u1ed1 \\u0111i\\u1ec7n tho\\u1ea1i b\\u1ea1n d&ugrave;ng \\u0111\\u1ec3 \\u0111\\u0103ng k&yacute; t&agrave;i kho\\u1ea3n t\\u1ea1i ollearning.com v&agrave; app MiharuBeauty).\\u003C\\/li\\u003E\\n\\u003Cli\\u003E\\u0110\\u1ed1i t&aacute;c kh&ocirc;ng \\u0111\\u01b0\\u1ee3c m\\u1ea1o danh MiharuBeauty \\u0111\\u1ec3 truy\\u1ec1n th&ocirc;ng s\\u1ea3n ph\\u1ea9m g&acirc;y hi\\u1ec3u nh\\u1ea7m cho kh&aacute;ch h&agrave;ng (kh&ocirc;ng d&ugrave;ng MiharuBeauty, C\\u1eeda S\\u1ed5 V&agrave;ng, Nguy\\u1ec5n Duy C\\u01b0\\u01a1ng trong t&ecirc;n ng\\u01b0\\u1eddi g\\u1eedi email, trong fanpage, tr&ecirc;n k&ecirc;nh Youtube&hellip;)\\u003C\\/li\\u003E\\n\\u003Cli\\u003E\\u0110\\u1ed1i t&aacute;c kh&ocirc;ng l\\u1ea5y danh ngh\\u0129a MiharuBeauty, C\\u1eeda S\\u1ed5 V&agrave;ng, Nguy\\u1ec5n Duy C\\u01b0\\u01a1ng \\u0111\\u1ec3 l&agrave;m vi\\u1ec7c v\\u1edbi kh&aacute;ch h&agrave;ng.\\u003C\\/li\\u003E\\n\\u003Cli\\u003E\\u0110\\u1ed1i t&aacute;c kh&ocirc;ng \\u0111\\u01b0\\u1ee3c ph&eacute;p mua c&aacute;c l\\u01b0\\u1ee3t t&igrave;m ki\\u1ebfm v\\u1ec1 t\\u1eeb kh&oacute;a (nh\\u01b0 \\u003Ca href=\\\"https:\\/\\/unica.vn\\/tag\\/google-ads\\\"\\u003EGoogle Adwords\\u003C\\/a\\u003E), hay mua c&aacute;c t&ecirc;n mi\\u1ec1n li&ecirc;n quan \\u0111\\u1ebfn MiharuBeauty, MiharuBeauty.com, C\\u1eeda S\\u1ed5 V&agrave;ng hay Nguy\\u1ec5n Duy C\\u01b0\\u01a1ng. Trong tr\\u01b0\\u1eddng h\\u1ee3p b\\u1ecb ph&aacute;t hi\\u1ec7n, \\u0111\\u1ed1i t&aacute;c s\\u1ebd b\\u1ecb ph\\u1ea1t doanh thu, m\\u1ee9c ph\\u1ea1t c&oacute; th\\u1ec3 t\\u1eeb 15 ng&agrave;y, 30 ng&agrave;y ho\\u1eb7c 3 th&aacute;ng tu\\u1ef3 m\\u1ee9c \\u0111\\u1ed9 vi ph\\u1ea1m. Trong tr\\u01b0\\u1eddng h\\u1ee3p vi ph\\u1ea1m nhi\\u1ec1u h\\u01a1n 1 l\\u1ea7n, \\u0111\\u1ed1i t&aacute;c s\\u1ebd b\\u1ecb d\\u1eebng t&agrave;i kho\\u1ea3n (chung t&agrave;i kho\\u1ea3n h\\u1ecdc) c&oacute; th\\u1eddi h\\u1ea1n ho\\u1eb7c v&ocirc; th\\u1eddi h\\u1ea1n.\\u003C\\/li\\u003E\\n\\u003Cli\\u003E\\u0110\\u1ed1i t&aacute;c kh&ocirc;ng \\u0111\\u01b0\\u1ee3c ch\\u1ea1y qu\\u1ea3ng c&aacute;o n&oacute;i x\\u1ea5u c&aacute;c \\u0111\\u01a1n v\\u1ecb kinh doanh kh&aacute;c nh\\u1eb1m l&ocirc;i k&eacute;o ng\\u01b0\\u1eddi d&ugrave;ng\\u003C\\/li\\u003E\\n\\u003Cli\\u003E\\u0110\\u1ed1i t&aacute;c khi s\\u1eed d\\u1ee5ng Email Marketing, th&ocirc;ng tin truy\\u1ec1n th&ocirc;ng \\u0111i ph\\u1ea3i \\u0111\\u1ea1t c&aacute;c chu\\u1ea9n chung v\\u1ec1 k&ecirc;nh marketing n&agrave;y (nh\\u01b0 lu&ocirc;n \\u0111\\u1ec3 ch\\u1eef t\\u1eeb ch\\u1ed1i nh\\u1eadn email, kh&ocirc;ng ch\\u1ee9a ph\\u1ea7n m\\u1ec1m gi&aacute;n \\u0111i\\u1ec7p v&agrave; kh&ocirc;ng \\u0111\\u01b0\\u1ee3c Spam kh&aacute;ch h&agrave;ng).\\u003C\\/li\\u003E\\n\\u003Cli\\u003EKh&ocirc;ng \\u0111\\u01b0\\u1ee3c l&ocirc;i k&eacute;o kh&aacute;ch h&agrave;ng mua s\\u1ea3n ph\\u1ea9m b\\u1eb1ng c&aacute;ch t\\u1eb7ng ti\\u1ec1n ho\\u1eb7c c&aacute;c v\\u1eadt ph\\u1ea9m c&oacute; gi&aacute; tr\\u1ecb kh&aacute;c nh\\u01b0 th\\u1ebb c&agrave;o, s\\u1ea3n ph\\u1ea9m v\\u1eadt ch\\u1ea5t.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EKh&ocirc;ng \\u0111\\u01b0\\u1ee3c l&ocirc;i k&eacute;o kh&aacute;ch h&agrave;ng t\\u1eeb c&aacute;c fanpage do MiharuBeauty v&agrave; C\\u1eeda S\\u1ed5 V&agrave;ng qu\\u1ea3n l&yacute;, t\\u1eeb website v&agrave; t\\u1eeb landing page (c&aacute;c h&agrave;nh vi nh\\u01b0 inbox cho kh&aacute;ch h&agrave;ng t\\u1eeb c&aacute;c qu\\u1ea3ng c&aacute;o c\\u1ee7a MiharuBeauty, tr\\u1ea3 l\\u1eddi comment g\\u1eafn m&atilde; chia s\\u1ebb tr&ecirc;n website v&agrave; landing page c\\u1ee7a MiharuBeauty, C\\u1eeda S\\u1ed5 V&agrave;ng \\u0111\\u1ec1u b\\u1ecb nghi&ecirc;m c\\u1ea5m).\\u003C\\/li\\u003E\\n\\u003Cli\\u003ECh\\u01b0\\u01a1ng tr&igrave;nh \\u0110i\\u1ec3m chia s\\u1ebb kh&ocirc;ng &aacute;p d\\u1ee5ng v\\u1edbi kh&aacute;ch h&agrave;ng \\u0110\\u1ea1i L&yacute;: Mua l\\u1ebb, Mua s\\u1ec9 b\\u1eb1ng ti\\u1ec1n k&yacute; qu\\u1ef9.\\u003C\\/li\\u003E\\n\\u003C\\/ul\\u003E\\n\\u003Ch4\\u003E\\u0110i\\u1ec1u 9. Thanh to&aacute;n \\u0111i\\u1ec3m chia s\\u1ebb (hoa h\\u1ed3ng)\\u003C\\/h4\\u003E\\n\\u003Cp\\u003E\\u0110\\u1ed1i T&aacute;c c\\u1ee7a MiharuBeauty s\\u1ebd \\u0111\\u01b0\\u1ee3c chia s\\u1ebb doanh thu theo th\\u1ecfa thu\\u1eadn c\\u1ee7a MiharuBeauty v\\u1edbi Ng\\u01b0\\u1eddi chia s\\u1ebb.\\u003C\\/p\\u003E\\n\\u003Cul\\u003E\\n\\u003Cli\\u003EDoanh thu t\\u1eeb ch\\u01b0\\u01a1ng tr&igrave;nh \\u0111\\u1ed1i t&aacute;c s\\u1ebd \\u0111\\u01b0\\u1ee3c h\\u1ea1ch to&aacute;n theo th&aacute;ng v&agrave; chi tr\\u1ea3 ch\\u1eadm nh\\u1ea5t v&agrave;o ng&agrave;y 10-20 c\\u1ee7a th&aacute;ng th\\u1ee9 2. V&iacute; d\\u1ee5: doanh thu th&aacute;ng 01 s\\u1ebd chi tr\\u1ea3 v&agrave;o t\\u1eeb ng&agrave;y 10-20\\/03 (c&oacute; tr\\u01b0\\u1eddng h\\u1ee3p tr\\u1ec5 do h\\u1ec7 th\\u1ed1ng ng&acirc;n h&agrave;ng ho\\u1eb7c tr&ugrave;ng v&agrave;o c&aacute;c ng&agrave;y ngh\\u1ec9).&nbsp;\\u003C\\/li\\u003E\\n\\u003Cli\\u003EM\\u1ee9c chi\\u1ebft kh\\u1ea5u c\\u1ee5 th\\u1ec3 cho ng\\u01b0\\u1eddi chia s\\u1ebb l&agrave;:\\u003C\\/li\\u003E\\n\\u003C\\/ul\\u003E\\n\\u003Cul\\u003E\\n\\u003Cli\\u003EMiharuBeauty s\\u1ebd kh\\u1ea5u tr\\u1eeb thu\\u1ebf thu nh\\u1eadp c&aacute; nh&acirc;n 10% \\u0111\\u1ed1i v\\u1edbi doanh thu \\u0111\\u1ed1i t&aacute;c \\u0111\\u1ea1t tr&ecirc;n 500,000\\u0111 (bao g\\u1ed3m c\\u1ea3 thu nh\\u1eadp t\\u1eeb th\\u01b0\\u1edfng n\\u1ebfu c&oacute;).\\u003C\\/li\\u003E\\n\\u003C\\/ul\\u003E\\n\\u003Ch3\\u003EH\\u1ee6Y TH\\u1eceA THU\\u1eacN V&Agrave; C\\u1eacP NH\\u1eacT \\u0110I\\u1ec0U KHO\\u1ea2N&nbsp;\\u003C\\/h3\\u003E\\n\\u003Ch4\\u003E\\u0110i\\u1ec1u 10. Hu\\u1ef7 tho\\u1ea3 thu\\u1eadn h\\u1ee3p t&aacute;c&nbsp;\\u003C\\/h4\\u003E\\n\\u003Cul\\u003E\\n\\u003Cli\\u003EB\\u1ea5t k\\u1ef3 ho\\u1ea1t \\u0111\\u1ed9ng gian l\\u1eadn hay c&aacute;c h&agrave;nh vi vi ph\\u1ea1m m\\u1ed9t trong c&aacute;c \\u0111i\\u1ec1u kho\\u1ea3n n&oacute;i tr&ecirc;n s\\u1ebd d\\u1eabn \\u0111\\u1ebfn vi\\u1ec7c d\\u1eebng th\\u1ecfa thu\\u1eadn h\\u1ee3p t&aacute;c, ho\\u1eb7c cao h\\u01a1n l&agrave; kh&oacute;a t&agrave;i kho\\u1ea3n v&agrave; h\\u1ee7y m\\u1ecdi k\\u1ebft qu\\u1ea3 \\u0111\\u1ea1t \\u0111\\u01b0\\u1ee3c t\\u1ea1i MiharuBeauty.com m&agrave; kh&ocirc;ng c\\u1ea7n th&ocirc;ng b&aacute;o tr\\u01b0\\u1edbc.\\u003C\\/li\\u003E\\n\\u003C\\/ul\\u003E\\n\\u003Ch4\\u003E\\u0110i\\u1ec1u 11. V\\u1ec1 vi\\u1ec7c c\\u1eadp nh\\u1eadt \\u0111i\\u1ec1u kho\\u1ea3n\\u003C\\/h4\\u003E\\n\\u003Cp\\u003EMiharuBeauty c&oacute; th\\u1ec3 thay \\u0111\\u1ed5i, b\\u1ed5 sung ho\\u1eb7c s\\u1eeda ch\\u1eefa th\\u1ecfa thu\\u1eadn n&agrave;y b\\u1ea5t c\\u1ee9 l&uacute;c n&agrave;o nh\\u1eb1m c\\u1eadp nh\\u1eadt nh\\u1eefng ch&iacute;nh s&aacute;ch m\\u1edbi nh\\u1ea5t. C&aacute;c c\\u1eadp nh\\u1eadt m\\u1edbi, quan tr\\u1ecdng s\\u1ebd \\u0111\\u01b0\\u1ee3c th&ocirc;ng b&aacute;o v&agrave; g\\u1eedi email t\\u1edbi c&aacute;c \\u0111\\u1ed1i t&aacute;c li&ecirc;n quan.&nbsp;\\u003C\\/p\\u003E\\n\\u003Cp\\u003E\\u003Cbr \\/\\u003E\\u003Cbr \\/\\u003E\\u003C\\/p\\u003E\",\"en\":\"\\u003Ch1\\u003E\\u0110I\\u1ec0U KHO\\u1ea2N D\\u1ecaCH V\\u1ee4\\u003C\\/h1\\u003E\\n\\u003Cp\\u003ED\\u01b0\\u1edbi \\u0111&acirc;y l&agrave; nh\\u1eefng \\u0111i\\u1ec1u kho\\u1ea3n \\u0111\\u01b0\\u1ee3c &aacute;p d\\u1ee5ng cho kh&aacute;ch h&agrave;ng v&agrave; \\u0111\\u1ed1i t&aacute;c c\\u1ee7a MiharuBeauty. Xin h&atilde;y \\u0111\\u1ecdc k\\u1ef9 to&agrave;n b\\u1ed9 th\\u1ecfa thu\\u1eadn tr\\u01b0\\u1edbc khi tham gia.\\u003C\\/p\\u003E\\n\\u003Cp\\u003EM\\u1ed9t khi b\\u1ea1n \\u0111&atilde; \\u0111\\u0103ng k&yacute; tham gia tr&ecirc;n MiharuBeauty.com, ch&uacute;ng t&ocirc;i s\\u1ebd hi\\u1ec3u r\\u1eb1ng b\\u1ea1n \\u0111&atilde; \\u0111\\u1ecdc v&agrave; \\u0111\\u1ed3ng &yacute; to&agrave;n b\\u1ed9 \\u0111i\\u1ec1u kho\\u1ea3n \\u0111\\u01b0\\u1ee3c \\u0111\\u01b0a ra trong b\\u1ea3n th\\u1ecfa thu\\u1eadn n&agrave;y.\\u003C\\/p\\u003E\\n\\u003Cp\\u003EB\\u1ea3n c\\u1eadp nh\\u1eadt m\\u1edbi nh\\u1ea5t (n\\u1ebfu c&oacute;) s\\u1ebd \\u0111\\u01b0\\u1ee3c \\u0111\\u0103ng t\\u1ea1i t\\u1ea1i \\u0111&acirc;y v&agrave; MiharuBeauty s\\u1ebd kh&ocirc;ng th&ocirc;ng b&aacute;o \\u0111\\u1ebfn t\\u1eebng \\u0111\\u1ed1i t&aacute;c, v&igrave; v\\u1eady b\\u1ea1n h&atilde;y quay l\\u1ea1i trang n&agrave;y th\\u01b0\\u1eddng xuy&ecirc;n \\u0111\\u1ec3 c\\u1eadp nh\\u1eadt ch&iacute;nh s&aacute;ch m\\u1edbi nh\\u1ea5t.\\u003C\\/p\\u003E\\n\\u003Ch3\\u003E\\u0110I\\u1ec0U KHO\\u1ea2N CHUNG\\u003C\\/h3\\u003E\\n\\u003Ch4\\u003E\\u0110i\\u1ec1u 1: Th&ocirc;ng tin t&agrave;i kho\\u1ea3n c&aacute; nh&acirc;n\\u003C\\/h4\\u003E\\n\\u003Cul\\u003E\\n\\u003Cli\\u003EKhi \\u0111\\u0103ng k&yacute; t&agrave;i kho\\u1ea3n MiharuBeauty, \\u0111\\u1ec3 \\u0111\\u01b0\\u1ee3c h\\u1ed7 tr\\u1ee3 nhanh ch&oacute;ng, b\\u1ea1n c\\u1ea7n cung c\\u1ea5p \\u0111\\u1ea7y \\u0111\\u1ee7 v&agrave; ch&iacute;nh x&aacute;c c&aacute;c th&ocirc;ng tin: H\\u1ecd t&ecirc;n, Email, Phone, Gi\\u1edbi t&iacute;nh, Ng&agrave;y sinh, Th&agrave;nh ph\\u1ed1,..\\u003C\\/li\\u003E\\n\\u003Cli\\u003ECh&uacute;ng t&ocirc;i s\\u1eed d\\u1ee5ng th&ocirc;ng tin li&ecirc;n l\\u1ea1c c\\u1ee7a b\\u1ea1n \\u0111\\u1ec3 g\\u1eedi m&atilde; k&iacute;ch ho\\u1ea1t s\\u1ea3n ph\\u1ea9m, th&ocirc;ng b&aacute;o ch\\u01b0\\u01a1ng tr&igrave;nh khuy\\u1ebfn m&atilde;i, x&aacute;c nh\\u1eadn \\u0111\\u1ed5i m\\u1eadt kh\\u1ea9u, c&aacute;c th\\u1ea3o lu\\u1eadn trong l\\u1edbp h\\u1ecdc,..\\u003C\\/li\\u003E\\n\\u003Cli\\u003ETh&ocirc;ng tin ng&agrave;y sinh v&agrave; gi\\u1edbi t&iacute;nh d&ugrave;ng \\u0111\\u1ec3 g\\u1ee3i &yacute; \\u0111\\u1ebfn b\\u1ea1n nh\\u1eefng s\\u1ea3n ph\\u1ea9m ph&ugrave; h\\u1ee3p, c\\u0169ng nh\\u01b0 g\\u1eedi qu&agrave; t\\u1eb7ng \\u0111\\u1ebfn b\\u1ea1n trong ng&agrave;y sinh nh\\u1eadt.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EB\\u1ea1n c&oacute; th\\u1ec3 \\u0111\\u0103ng nh\\u1eadp b\\u1eb1ng t&agrave;i kho\\u1ea3n MiharuBeauty (email + m\\u1eadt kh\\u1ea9u) ho\\u1eb7c \\u0111\\u0103ng nh\\u1eadp b\\u1eb1ng Google, Facebook.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EB\\u1ea1n c&oacute; th\\u1ec3 c\\u1eadp nh\\u1eadt th&ocirc;ng tin c&aacute; nh&acirc;n ho\\u1eb7c h\\u1ee7y (x&oacute;a) t&agrave;i kho\\u1ea3n b\\u1ea5t k\\u1ef3 l&uacute;c n&agrave;o khi kh&ocirc;ng c&ograve;n nhu c\\u1ea7u s\\u1eed d\\u1ee5ng\\u003C\\/li\\u003E\\n\\u003C\\/ul\\u003E\\n\\u003Ch4\\u003E\\u0110i\\u1ec1u 2: Vi\\u1ec7c b\\u1ea3o m\\u1eadt th&ocirc;ng tin\\u003C\\/h4\\u003E\\n\\u003Cul\\u003E\\n\\u003Cli\\u003EB\\u1ea1n c&oacute; tr&aacute;ch nhi\\u1ec7m t\\u1ef1 m&igrave;nh b\\u1ea3o qu\\u1ea3n m\\u1eadt kh\\u1ea9u, n\\u1ebfu m\\u1eadt kh\\u1ea9u b\\u1ecb l\\u1ed9 ra ngo&agrave;i d\\u01b0\\u1edbi b\\u1ea5t k\\u1ef3 h&igrave;nh th\\u1ee9c n&agrave;o, MiharuBeauty s\\u1ebd kh&ocirc;ng ch\\u1ecbu tr&aacute;ch nhi\\u1ec7m v\\u1ec1 m\\u1ecdi t\\u1ed5n th\\u1ea5t ph&aacute;t sinh.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EM\\u1ecdi th&ocirc;ng tin c&aacute; nh&acirc;n c\\u1ee7a b\\u1ea1n s\\u1ebd \\u0111\\u01b0\\u1ee3c ch&uacute;ng t&ocirc;i b\\u1ea3o m\\u1eadt, kh&ocirc;ng ti\\u1ebft l\\u1ed9 ra ngo&agrave;i. Ch&uacute;ng t&ocirc;i kh&ocirc;ng b&aacute;n hay trao \\u0111\\u1ed5i nh\\u1eefng th&ocirc;ng tin n&agrave;y v\\u1edbi b\\u1ea5t k\\u1ef3 m\\u1ed9t b&ecirc;n th\\u1ee9 ba n&agrave;o kh&aacute;c. Tuy nhi&ecirc;n, trong tr\\u01b0\\u1eddng h\\u1ee3p c\\u01a1 quan ch\\u1ee9c n\\u0103ng y&ecirc;u c\\u1ea7u, MiharuBeauty bu\\u1ed9c ph\\u1ea3i cung c\\u1ea5p nh\\u1eefng th&ocirc;ng tin n&agrave;y theo quy \\u0111\\u1ecbnh ph&aacute;p lu\\u1eadt.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EB\\u1ea1n c&oacute; quy\\u1ec1n s\\u1edf h\\u1eefu tr\\u1ecdn \\u0111\\u1eddi c&aacute;c s\\u1ea3n ph\\u1ea9m \\u0111&atilde; \\u0111\\u0103ng k&yacute;: kh&ocirc;ng gi\\u1edbi h\\u1ea1n s\\u1ed1 l\\u1ea7n tham gia h\\u1ecdc v&agrave; th\\u1eddi gian h\\u1ecdc.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EB\\u1ea1n kh&ocirc;ng \\u0111\\u01b0\\u1ee3c download video, kh&ocirc;ng \\u0111\\u01b0\\u1ee3c chia s\\u1ebb video l&ecirc;n Internet v\\u1edbi b\\u1ea5t k\\u1ef3 h&igrave;nh th\\u1ee9c n&agrave;o. N\\u1ebfu vi ph\\u1ea1m, t&agrave;i kho\\u1ea3n c\\u1ee7a b\\u1ea1n s\\u1ebd b\\u1ecb kho&aacute; v&agrave; b\\u1ea1n ph\\u1ea3i ch\\u1ecbu tr&aacute;ch nhi\\u1ec7m tr\\u01b0\\u1edbc ph&aacute;p lu\\u1eadt v\\u1ec1 h&agrave;nh vi x&acirc;m ph\\u1ea1m s\\u1edf h\\u1eefu tr&iacute; tu\\u1ec7.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EMiharuBeauty c&oacute; th\\u1ec3 g\\u1eedi th&ocirc;ng b&aacute;o t&igrave;nh h&igrave;nh h\\u1ecdc t\\u1eadp, ch\\u01b0\\u01a1ng tr&igrave;nh khuy\\u1ebfn m&atilde;i (n\\u1ebfu c&oacute;), th&ocirc;ng b&aacute;o s\\u1ea3n ph\\u1ea9m m\\u1edbi s\\u1eafp ra m\\u1eaft \\u0111\\u1ec3 kh&aacute;ch h&agrave;ng quan t&acirc;m c&oacute; th\\u1ec3 \\u0111\\u0103ng k&yacute; ngay \\u0111\\u1ec3 \\u0111\\u01b0\\u1ee3c \\u01b0u \\u0111&atilde;i. N\\u1ebfu b\\u1ea1n kh&ocirc;ng mu\\u1ed1n nh\\u1eadn email c&oacute; th\\u1ec3 b\\u1ea5m v&agrave;o link \\\"Ng\\u1eebng nh\\u1eadn email\\\" \\u1edf cu\\u1ed1i email.\\u003C\\/li\\u003E\\n\\u003C\\/ul\\u003E\\n\\u003Ch4\\u003E\\u0110i\\u1ec1u 3: \\u0110&aacute;nh gi&aacute; s\\u1ea3n ph\\u1ea9m v&agrave; th\\u1ea3o lu\\u1eadn\\u003C\\/h4\\u003E\\n\\u003Cul\\u003E\\n\\u003Cli\\u003EH\\u1ecdc vi&ecirc;n khi tham gia s\\u1ea3n ph\\u1ea9m tr&ecirc;n MiharuBeauty c&oacute; quy\\u1ec1n \\u0111&aacute;nh gi&aacute; v\\u1ec1 ch\\u1ea5t l\\u01b0\\u1ee3ng s\\u1ea3n ph\\u1ea9m.\\u003C\\/li\\u003E\\n\\u003Cli\\u003ETrong qu&aacute; tr&igrave;nh h\\u1ecdc, kh&aacute;ch h&agrave;ng c&oacute; b\\u1ea5t k\\u1ef3 th\\u1eafc m\\u1eafc hay g&oacute;p &yacute; n&agrave;o c&oacute; th\\u1ec3 \\u0111\\u0103ng b&igrave;nh lu\\u1eadn c\\u1ee7a m&igrave;nh l&ecirc;n ph\\u1ea7n Th\\u1ea3o lu\\u1eadn - ngay trong giao di\\u1ec7n b&agrave;i h\\u1ecdc \\u0111\\u1ec3 \\u0111\\u01b0\\u1ee3c chuy&ecirc;n vi&ecirc;n MiharuBeauty v&agrave; Gi\\u1ea3ng vi&ecirc;n h\\u1ed7 tr\\u1ee3 gi\\u1ea3i \\u0111&aacute;p.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EB&ecirc;n c\\u1ea1nh \\u0111&oacute;, c&aacute;c s\\u1ea3n ph\\u1ea9m l\\u1edbn tr&ecirc;n MiharuBeauty \\u0111\\u1ec1u c&oacute; Group Th\\u1ea3o lu\\u1eadn ri&ecirc;ng cho c&aacute;c kh&aacute;ch h&agrave;ng v&agrave; gi\\u1ea3ng vi&ecirc;n \\u0111\\u1ec3 trao \\u0111\\u1ed5i c&aacute;c v\\u1ea5n \\u0111\\u1ec1 chuy&ecirc;n m&ocirc;n.\\u003C\\/li\\u003E\\n\\u003C\\/ul\\u003E\\n\\u003Ch4\\u003E\\u0110i\\u1ec1u 4: Nghi&ecirc;m c\\u1ea5m s\\u1eed d\\u1ee5ng d\\u1ecbch v\\u1ee5 v\\u1edbi c&aacute;c h&agrave;nh vi d\\u01b0\\u1edbi \\u0111&acirc;y\\u003C\\/h4\\u003E\\n\\u003Cul\\u003E\\n\\u003Cli\\u003ES\\u1eed d\\u1ee5ng b\\u1ea5t k\\u1ef3 c&ocirc;ng c\\u1ee5 hay h&igrave;nh th\\u1ee9c n&agrave;o \\u0111\\u1ec3 can thi\\u1ec7p v&agrave;o c&aacute;c d\\u1ecbch v\\u1ee5, s\\u1ea3n ph\\u1ea9m trong h\\u1ec7 th\\u1ed1ng MiharuBeauty.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EPh&aacute;t t&aacute;n ho\\u1eb7c tuy&ecirc;n truy\\u1ec1n c\\u1ed5 v\\u0169 c&aacute;c ho\\u1ea1t \\u0111\\u1ed9ng ph&aacute;t t&aacute;n, can thi\\u1ec7p v&agrave; ph&aacute; ho\\u1ea1i n\\u1ed9i dung c&aacute;c b&agrave;i h\\u1ecdc tr&ecirc;n h\\u1ec7 th\\u1ed1ng c\\u1ee7a MiharuBeauty ra b&ecirc;n ngo&agrave;i. M\\u1ecdi vi ph\\u1ea1m khi b\\u1ecb ph&aacute;t hi\\u1ec7n s\\u1ebd b\\u1ecb x&oacute;a t&agrave;i kho\\u1ea3n v&agrave; c&oacute; th\\u1ec3 x\\u1eed l&yacute; theo quy \\u0111\\u1ecbnh c\\u1ee7a ph&aacute;p lu\\u1eadt v\\u1ec1 vi\\u1ec7c vi ph\\u1ea1m b\\u1ea3n quy\\u1ec1n.\\u003C\\/li\\u003E\\n\\u003Cli\\u003ES\\u1eed d\\u1ee5ng chung t&agrave;i kho\\u1ea3n: v\\u1edbi vi\\u1ec7c tr&ecirc;n 2 ng\\u01b0\\u1eddi c&ugrave;ng s\\u1eed d\\u1ee5ng chung m\\u1ed9t t&agrave;i kho\\u1ea3n khi b\\u1ecb ph&aacute;t hi\\u1ec7n s\\u1ebd b\\u1ecb x&oacute;a t&agrave;i kho\\u1ea3n ngay l\\u1eadp t\\u1ee9c.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EX&uacute;c ph\\u1ea1m, nh\\u1ea1o b&aacute;ng ng\\u01b0\\u1eddi kh&aacute;c d\\u01b0\\u1edbi b\\u1ea5t k\\u1ef3 h&igrave;nh th\\u1ee9c n&agrave;o: ch&ecirc; bai, k\\u1ef3 th\\u1ecb t&ocirc;n gi&aacute;o, gi\\u1edbi t&iacute;nh, s\\u1eafc t\\u1ed9c..\\u003C\\/li\\u003E\\n\\u003Cli\\u003EH&agrave;nh vi m\\u1ea1o nh\\u1eadn hay c\\u1ed1 &yacute; l&agrave;m ng\\u01b0\\u1eddi kh&aacute;c t\\u01b0\\u1edfng l\\u1ea7m m&igrave;nh l&agrave; m\\u1ed9t ng\\u01b0\\u1eddi s\\u1eed d\\u1ee5ng kh&aacute;c trong h\\u1ec7 th\\u1ed1ng d\\u1ecbch v\\u1ee5 c\\u1ee7a MiharuBeauty.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EB&agrave;n lu\\u1eadn v\\u1ec1 c&aacute;c v\\u1ea5n \\u0111\\u1ec1 ch&iacute;nh tr\\u1ecb, k\\u1ef3 th\\u1ecb t&ocirc;n gi&aacute;o, k\\u1ef3 th\\u1ecb s\\u1eafc t\\u1ed9c.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EH&agrave;nh vi, th&aacute;i \\u0111\\u1ed9 l&agrave;m t\\u1ed5n h\\u1ea1i \\u0111\\u1ebfn uy t&iacute;n c\\u1ee7a c&aacute;c s\\u1ea3n ph\\u1ea9m, d\\u1ecbch v\\u1ee5, s\\u1ea3n ph\\u1ea9m trong h\\u1ec7 th\\u1ed1ng MiharuBeauty d\\u01b0\\u1edbi b\\u1ea5t k\\u1ef3 h&igrave;nh th\\u1ee9c n&agrave;o, ph\\u01b0\\u01a1ng th\\u1ee9c n&agrave;o.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EMua b&aacute;n chuy\\u1ec3n nh\\u01b0\\u1ee3ng t&agrave;i kho\\u1ea3n, s\\u1ea3n ph\\u1ea9m c\\u1ee7a MiharuBeauty.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EM\\u1ea1o danh MiharuBeauty \\u1ea3nh h\\u01b0\\u1edfng \\u0111\\u1ebfn uy t&iacute;n c\\u1ee7a MiharuBeauty, g&acirc;y s\\u1ef1 nh\\u1ea7m l\\u1eabn cho c&aacute;c kh&aacute;ch h&agrave;ng v&agrave; \\u0111\\u1ed1i t&aacute;c theo b\\u1ea5t k\\u1ef3 ph\\u01b0\\u01a1ng th\\u1ee9c n&agrave;o (d&ugrave;ng \\u0111\\u1ecba ch\\u1ec9 email, t&ecirc;n mi\\u1ec1n website, fanpage c&oacute; ch\\u1eef MiharuBeauty, C\\u1eeda S\\u1ed5 V&agrave;ng, Nguy\\u1ec5n Duy C\\u01b0\\u01a1ng...)\\u003C\\/li\\u003E\\n\\u003Cli\\u003EKhi ph&aacute;t hi\\u1ec7n nh\\u1eefng h&agrave;nh vi tr&ecirc;n t\\u1eeb t&agrave;i kho\\u1ea3n c\\u1ee7a b\\u1ea1n, MiharuBeauty c&oacute; quy\\u1ec1n t\\u01b0\\u1edbc b\\u1ecf m\\u1ecdi quy\\u1ec1n l\\u1ee3i li&ecirc;n quan \\u0111\\u1ed1i v\\u1edbi t&agrave;i kho\\u1ea3n (bao g\\u1ed3m vi\\u1ec7c kh&oacute;a t&agrave;i kho\\u1ea3n) ho\\u1eb7c s\\u1eed d\\u1ee5ng nh\\u1eefng th&ocirc;ng tin m&agrave; b\\u1ea1n cung c\\u1ea5p khi \\u0111\\u0103ng k&yacute; t&agrave;i kho\\u1ea3n \\u0111\\u1ec3 chuy\\u1ec3n cho c\\u01a1 quan ch\\u1ee9c n\\u0103ng gi\\u1ea3i quy\\u1ebft theo quy \\u0111\\u1ecbnh c\\u1ee7a ph&aacute;p lu\\u1eadt.\\u003C\\/li\\u003E\\n\\u003C\\/ul\\u003E\\n\\u003Ch4\\u003E\\u0110i\\u1ec1u 5: Ch&iacute;nh s&aacute;ch ho&agrave;n tr\\u1ea3 h\\u1ecdc ph&iacute;\\u003C\\/h4\\u003E\\n\\u003Cul\\u003E\\n\\u003Cli\\u003EKhi th\\u1ef1c hi\\u1ec7n thanh to&aacute;n online th&agrave;nh c&ocirc;ng tr&ecirc;n website sweetgirlbeauty.com th&igrave; s\\u1ea3n ph\\u1ea9m c\\u1ea5p t\\u1ef1 \\u0111\\u1ed9ng t\\u1ea1i app MiharuBeauty v\\u1edbi c&ugrave;ng t&agrave;i kho\\u1ea3n tr&ecirc;n website&nbsp; sweetgirlbeauty.com. Trong tr\\u01b0\\u1eddng h\\u1ee3p n&agrave;y qu&yacute; kh&aacute;ch \\u0111&atilde; nh\\u1eadn \\u0111\\u01b0\\u1ee3c s\\u1ea3n ph\\u1ea9m t\\u1eeb MiharuBeauty, th&igrave; ch&iacute;nh s&aacute;ch ho&agrave;n ph&iacute; kh&ocirc;ng \\u0111\\u01b0\\u1ee3c &aacute;p d\\u1ee5ng.\\u003C\\/li\\u003E\\n\\u003C\\/ul\\u003E\\n\\u003Ch3\\u003EB\\u1ea2O M\\u1eacT THANH TO&Aacute;N\\u003C\\/h3\\u003E\\n\\u003Ch4\\u003E\\u0110i\\u1ec1u 6: Quy Ch\\u1ebf Thanh To&aacute;n\\u003C\\/h4\\u003E\\n\\u003Cul\\u003E\\n\\u003Cli\\u003E\\u0110\\u1ec3 \\u0111\\u1ea3m b\\u1ea3o an to&agrave;n b\\u1ea3o m\\u1eadt thanh to&aacute;n cho kh&aacute;ch h&agrave;ng, \\u1edf&nbsp; sweetgirlbeauty.com b\\u1ea1n s\\u1ebd ch\\u1ec9 \\u0111\\u1ec3 l\\u1ea1i th&ocirc;ng tin \\u0111\\u1eb7t h&agrave;ng nh\\u01b0 t&ecirc;n, s\\u1ed1 \\u0111i\\u1ec7n tho\\u1ea1i, \\u0111\\u1ecba ch\\u1ec9 nh\\u1eadn h&agrave;ng, email v&agrave; thanh to&aacute;n b\\u1eb1ng h&igrave;nh th\\u1ee9c chuy\\u1ec3n kho\\u1ea3n.\\u003C\\/li\\u003E\\n\\u003Cli\\u003E\\u0110\\u1ed1i v\\u1edbi c&aacute;c h&igrave;nh th\\u1ee9c thanh to&aacute;n tr\\u1ef1c tuy\\u1ebfn, sau khi \\u0111\\u01a1n h&agrave;ng \\u0111\\u01b0\\u1ee3c kh\\u1edfi t\\u1ea1o v&agrave; \\u0111\\u1ec3 l\\u1ea1i th&ocirc;ng tin \\u0111\\u1eb7t h&agrave;ng nh\\u01b0 t&ecirc;n, s\\u1ed1 \\u0111i\\u1ec7n tho\\u1ea1i, \\u0111\\u1ecba ch\\u1ec9 nh\\u1eadn h&agrave;ng, email, v&agrave; ch\\u1ecdn h&igrave;nh th\\u1ee9c thanh to&aacute;n tr\\u1ef1c tuy\\u1ebfn b\\u1ea1n s\\u1ebd \\u0111\\u01b0\\u1ee3c chuy\\u1ec3n v\\u1ec1 trang onepay.vn \\u0111\\u1ec3 th\\u1ef1c hi\\u1ec7n c&aacute;c giao d\\u1ecbch b\\u1eb1ng th\\u1ebb qu\\u1ed1c t\\u1ebf, n\\u1ed9i \\u0111\\u1ecba, internet banking, v&iacute; \\u0111i\\u1ec7n t\\u1eed,&hellip; sau khi giao d\\u1ecbch th&agrave;nh c&ocirc;ng t\\u1ea1i onepay.vn th&igrave; cha m\\u1eb9 s\\u1ebd \\u0111\\u01b0\\u1ee3c tr\\u1ea3 th&ocirc;ng tin v\\u1ec1&nbsp; sweetgirlbeauty.com.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EL\\u01b0u &yacute;: khi thanh \\u0111\\u1eb7t h&agrave;ng t\\u1ea1i sweetgirlbeauty.com, b\\u1ea1n ch\\u1ec9 thanh to&aacute;n tr\\u1ef1c tuy\\u1ebfn (b\\u1eb1ng th\\u1ebb qu\\u1ed1c t\\u1ebf, n\\u1ed9i \\u0111\\u1ecba, v&iacute; \\u0111i\\u1ec7n t\\u1eed,&hellip;) khi v&agrave; ch\\u1ec9 khi \\u0111&atilde; \\u0111\\u01b0\\u1ee3c chuy\\u1ec3n qua trang web onepay.vn v&agrave; tuy\\u1ec7t \\u0111\\u1ed1i KH&Ocirc;NG cung c\\u1ea5p th&ocirc;ng tin cho b\\u1ea5t c\\u1ee9 c&aacute; nh&acirc;n, \\u0111\\u01a1n v\\u1ecb n&agrave;o kh&aacute;c.\\u003C\\/li\\u003E\\n\\u003C\\/ul\\u003E\\n\\u003Ch4\\u003E\\u0110i\\u1ec1u 7: Cam K\\u1ebft B\\u1ea3o M\\u1eadt T\\u1ea1i ONEPAY.VN\\u003C\\/h4\\u003E\\n\\u003Cp\\u003EH\\u1ec7 th\\u1ed1ng thanh to&aacute;n th\\u1ebb \\u0111\\u01b0\\u1ee3c cung c\\u1ea5p b\\u1edfi c&aacute;c \\u0111\\u1ed1i t&aacute;c c\\u1ed5ng thanh to&aacute;n ONEPAY (&ldquo;\\u0110\\u1ed1i T&aacute;c C\\u1ed5ng Thanh To&aacute;n&rdquo;) \\u0111&atilde; \\u0111\\u01b0\\u1ee3c c\\u1ea5p ph&eacute;p ho\\u1ea1t \\u0111\\u1ed9ng h\\u1ee3p ph&aacute;p t\\u1ea1i Vi\\u1ec7t Nam. Theo \\u0111&oacute;, c&aacute;c ti&ecirc;u chu\\u1ea9n b\\u1ea3o m\\u1eadt thanh to&aacute;n th\\u1ebb t\\u1ea1i onepay.vn \\u0111\\u1ea3m b\\u1ea3o tu&acirc;n th\\u1ee7 theo c&aacute;c ti&ecirc;u chu\\u1ea9n b\\u1ea3o m\\u1eadt ng&agrave;nh.\\u003C\\/p\\u003E\\n\\u003Cp\\u003EGi\\u1ea5y Ch\\u1ee9ng Nh\\u1eadn Tu&acirc;n Th\\u1ee7 PCI DSS\\u003C\\/p\\u003E\\n\\u003Cul\\u003E\\n\\u003Cli\\u003EPCI DSS l&agrave; ti&ecirc;u chu\\u1ea9n b\\u1ea3o m\\u1eadt qu\\u1ed1c t\\u1ebf c&oacute; gi&aacute; tr\\u1ecb to&agrave;n c\\u1ea7u do H\\u1ed9i \\u0111\\u1ed3ng ti&ecirc;u chu\\u1ea9n b\\u1ea3o m\\u1eadt SSC thi\\u1ebft l\\u1eadp (Security Standards Council).\\u003C\\/li\\u003E\\n\\u003Cli\\u003EV\\u1edbi vi\\u1ec7c tu&acirc;n th\\u1ee7 PCI DSS, OnePay tham gia c&aacute;c ch\\u01b0\\u01a1ng tr&igrave;nh b\\u1ea3o v\\u1ec7 nh\\u01b0 Verified by Visa c\\u1ee7a VISA, MasterCard SecureCode c\\u1ee7a MasterCard, J\\/Secure c\\u1ee7a JCB, Safe Key c\\u1ee7a American Express.\\u003C\\/li\\u003E\\n\\u003C\\/ul\\u003E\\n\\u003Cp\\u003ECh\\u1ee9ng Ch\\u1ec9 TLS\\u003C\\/p\\u003E\\n\\u003Cul\\u003E\\n\\u003Cli\\u003ETh&ocirc;ng tin th\\u1ebb c\\u1ee7a b\\u1ea1n s\\u1ebd \\u0111\\u01b0\\u1ee3c b\\u1ea3o v\\u1ec7 trong su\\u1ed1t qu&aacute; tr&igrave;nh giao d\\u1ecbch b\\u1eb1ng giao th\\u1ee9c TLS. Giao th\\u1ee9c TLS s\\u1ebd m&atilde; h&oacute;a th&ocirc;ng tin b\\u1ea1n cung c\\u1ea5p trong su\\u1ed1t qu&aacute; tr&igrave;nh giao d\\u1ecbch.\\u003C\\/li\\u003E\\n\\u003Cli\\u003ENgo&agrave;i ra, OnePay c&ograve;n s\\u1eed d\\u1ee5ng nhi\\u1ec1u ph\\u1ea7n m\\u1ec1m v&agrave; thu\\u1eadt to&aacute;n kh&aacute;c \\u0111\\u1ec3 \\u0111\\u1ea3m b\\u1ea3o an ninh d\\u1eef li\\u1ec7u, an to&agrave;n tr&ecirc;n m&ocirc;i tr\\u01b0\\u1eddng Internet.\\u003C\\/li\\u003E\\n\\u003C\\/ul\\u003E\\n\\u003Cp\\u003EC\\u01a1 S\\u1edf H\\u1ea1 T\\u1ea7ng Ti&ecirc;n Ti\\u1ebfn\\u003C\\/p\\u003E\\n\\u003Cul\\u003E\\n\\u003Cli\\u003ETrung t&acirc;m d\\u1eef li\\u1ec7u ti&ecirc;u chu\\u1ea9n qu\\u1ed1c t\\u1ebf. Ti&ecirc;u chu\\u1ea9n b\\u1ea3o m\\u1eadt ISO\\/IEC 27001:2005. B\\u1ea3o \\u0111\\u1ea3m v\\u1eadn h&agrave;nh trung t&acirc;m kh&ocirc;i ph\\u1ee5c d\\u1eef li\\u1ec7u ch&iacute;nh.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EONEPAY cam k\\u1ebft \\u0111\\u1ea3m b\\u1ea3o th\\u1ef1c hi\\u1ec7n nghi&ecirc;m t&uacute;c c&aacute;c bi\\u1ec7n ph&aacute;p b\\u1ea3o m\\u1eadt c\\u1ea7n thi\\u1ebft cho m\\u1ecdi ho\\u1ea1t \\u0111\\u1ed9ng thanh to&aacute;n th\\u1ef1c hi\\u1ec7n tr&ecirc;n onepay.vn.\\u003C\\/li\\u003E\\n\\u003C\\/ul\\u003E\\n\\u003Ch3\\u003E\\u0110I\\u1ec0U KHO\\u1ea2N \\u0110\\u1ed0I V\\u1edaI NG\\u01af\\u1edcI CHIA S\\u1eba&nbsp;\\u003C\\/h3\\u003E\\n\\u003Ch4\\u003E\\u0110i\\u1ec1u 8. Quy \\u0111\\u1ecbnh v\\u1ec1 vi\\u1ec7c ph&acirc;n ph\\u1ed1i s\\u1ea3n ph\\u1ea9m v\\u1edbi M&atilde; chia s\\u1ebb&nbsp;\\u003C\\/h4\\u003E\\n\\u003Cul\\u003E\\n\\u003Cli\\u003EB\\u1ea1n c&oacute; th\\u1ec3 truy c\\u1eadp v&agrave;o ph\\u1ea7n T&agrave;i kho\\u1ea3n c\\u1ee7a t&ocirc;i &gt; \\u0110i\\u1ec3m chia s\\u1ebb t\\u1ea1i website sweetgirlbeauty.com \\u0111\\u1ec3 l\\u1ea5y m&atilde; chia s\\u1ebb (ch&iacute;nh l&agrave; s\\u1ed1 \\u0111i\\u1ec7n tho\\u1ea1i b\\u1ea1n d&ugrave;ng \\u0111\\u1ec3 \\u0111\\u0103ng k&yacute; t&agrave;i kho\\u1ea3n t\\u1ea1i ollearning.com v&agrave; app MiharuBeauty).\\u003C\\/li\\u003E\\n\\u003Cli\\u003E\\u0110\\u1ed1i t&aacute;c kh&ocirc;ng \\u0111\\u01b0\\u1ee3c m\\u1ea1o danh MiharuBeauty \\u0111\\u1ec3 truy\\u1ec1n th&ocirc;ng s\\u1ea3n ph\\u1ea9m g&acirc;y hi\\u1ec3u nh\\u1ea7m cho kh&aacute;ch h&agrave;ng (kh&ocirc;ng d&ugrave;ng MiharuBeauty, C\\u1eeda S\\u1ed5 V&agrave;ng, Nguy\\u1ec5n Duy C\\u01b0\\u01a1ng trong t&ecirc;n ng\\u01b0\\u1eddi g\\u1eedi email, trong fanpage, tr&ecirc;n k&ecirc;nh Youtube&hellip;)\\u003C\\/li\\u003E\\n\\u003Cli\\u003E\\u0110\\u1ed1i t&aacute;c kh&ocirc;ng l\\u1ea5y danh ngh\\u0129a MiharuBeauty, C\\u1eeda S\\u1ed5 V&agrave;ng, Nguy\\u1ec5n Duy C\\u01b0\\u01a1ng \\u0111\\u1ec3 l&agrave;m vi\\u1ec7c v\\u1edbi kh&aacute;ch h&agrave;ng.\\u003C\\/li\\u003E\\n\\u003Cli\\u003E\\u0110\\u1ed1i t&aacute;c kh&ocirc;ng \\u0111\\u01b0\\u1ee3c ph&eacute;p mua c&aacute;c l\\u01b0\\u1ee3t t&igrave;m ki\\u1ebfm v\\u1ec1 t\\u1eeb kh&oacute;a (nh\\u01b0 \\u003Ca href=\\\"https:\\/\\/unica.vn\\/tag\\/google-ads\\\"\\u003EGoogle Adwords\\u003C\\/a\\u003E), hay mua c&aacute;c t&ecirc;n mi\\u1ec1n li&ecirc;n quan \\u0111\\u1ebfn MiharuBeauty, MiharuBeauty.com, C\\u1eeda S\\u1ed5 V&agrave;ng hay Nguy\\u1ec5n Duy C\\u01b0\\u01a1ng. Trong tr\\u01b0\\u1eddng h\\u1ee3p b\\u1ecb ph&aacute;t hi\\u1ec7n, \\u0111\\u1ed1i t&aacute;c s\\u1ebd b\\u1ecb ph\\u1ea1t doanh thu, m\\u1ee9c ph\\u1ea1t c&oacute; th\\u1ec3 t\\u1eeb 15 ng&agrave;y, 30 ng&agrave;y ho\\u1eb7c 3 th&aacute;ng tu\\u1ef3 m\\u1ee9c \\u0111\\u1ed9 vi ph\\u1ea1m. Trong tr\\u01b0\\u1eddng h\\u1ee3p vi ph\\u1ea1m nhi\\u1ec1u h\\u01a1n 1 l\\u1ea7n, \\u0111\\u1ed1i t&aacute;c s\\u1ebd b\\u1ecb d\\u1eebng t&agrave;i kho\\u1ea3n (chung t&agrave;i kho\\u1ea3n h\\u1ecdc) c&oacute; th\\u1eddi h\\u1ea1n ho\\u1eb7c v&ocirc; th\\u1eddi h\\u1ea1n.\\u003C\\/li\\u003E\\n\\u003Cli\\u003E\\u0110\\u1ed1i t&aacute;c kh&ocirc;ng \\u0111\\u01b0\\u1ee3c ch\\u1ea1y qu\\u1ea3ng c&aacute;o n&oacute;i x\\u1ea5u c&aacute;c \\u0111\\u01a1n v\\u1ecb kinh doanh kh&aacute;c nh\\u1eb1m l&ocirc;i k&eacute;o ng\\u01b0\\u1eddi d&ugrave;ng\\u003C\\/li\\u003E\\n\\u003Cli\\u003E\\u0110\\u1ed1i t&aacute;c khi s\\u1eed d\\u1ee5ng Email Marketing, th&ocirc;ng tin truy\\u1ec1n th&ocirc;ng \\u0111i ph\\u1ea3i \\u0111\\u1ea1t c&aacute;c chu\\u1ea9n chung v\\u1ec1 k&ecirc;nh marketing n&agrave;y (nh\\u01b0 lu&ocirc;n \\u0111\\u1ec3 ch\\u1eef t\\u1eeb ch\\u1ed1i nh\\u1eadn email, kh&ocirc;ng ch\\u1ee9a ph\\u1ea7n m\\u1ec1m gi&aacute;n \\u0111i\\u1ec7p v&agrave; kh&ocirc;ng \\u0111\\u01b0\\u1ee3c Spam kh&aacute;ch h&agrave;ng).\\u003C\\/li\\u003E\\n\\u003Cli\\u003EKh&ocirc;ng \\u0111\\u01b0\\u1ee3c l&ocirc;i k&eacute;o kh&aacute;ch h&agrave;ng mua s\\u1ea3n ph\\u1ea9m b\\u1eb1ng c&aacute;ch t\\u1eb7ng ti\\u1ec1n ho\\u1eb7c c&aacute;c v\\u1eadt ph\\u1ea9m c&oacute; gi&aacute; tr\\u1ecb kh&aacute;c nh\\u01b0 th\\u1ebb c&agrave;o, s\\u1ea3n ph\\u1ea9m v\\u1eadt ch\\u1ea5t.\\u003C\\/li\\u003E\\n\\u003Cli\\u003EKh&ocirc;ng \\u0111\\u01b0\\u1ee3c l&ocirc;i k&eacute;o kh&aacute;ch h&agrave;ng t\\u1eeb c&aacute;c fanpage do MiharuBeauty v&agrave; C\\u1eeda S\\u1ed5 V&agrave;ng qu\\u1ea3n l&yacute;, t\\u1eeb website v&agrave; t\\u1eeb landing page (c&aacute;c h&agrave;nh vi nh\\u01b0 inbox cho kh&aacute;ch h&agrave;ng t\\u1eeb c&aacute;c qu\\u1ea3ng c&aacute;o c\\u1ee7a MiharuBeauty, tr\\u1ea3 l\\u1eddi comment g\\u1eafn m&atilde; chia s\\u1ebb tr&ecirc;n website v&agrave; landing page c\\u1ee7a MiharuBeauty, C\\u1eeda S\\u1ed5 V&agrave;ng \\u0111\\u1ec1u b\\u1ecb nghi&ecirc;m c\\u1ea5m).\\u003C\\/li\\u003E\\n\\u003Cli\\u003ECh\\u01b0\\u01a1ng tr&igrave;nh \\u0110i\\u1ec3m chia s\\u1ebb kh&ocirc;ng &aacute;p d\\u1ee5ng v\\u1edbi kh&aacute;ch h&agrave;ng \\u0110\\u1ea1i L&yacute;: Mua l\\u1ebb, Mua s\\u1ec9 b\\u1eb1ng ti\\u1ec1n k&yacute; qu\\u1ef9.\\u003C\\/li\\u003E\\n\\u003C\\/ul\\u003E\\n\\u003Ch4\\u003E\\u0110i\\u1ec1u 9. Thanh to&aacute;n \\u0111i\\u1ec3m chia s\\u1ebb (hoa h\\u1ed3ng)\\u003C\\/h4\\u003E\\n\\u003Cp\\u003E\\u0110\\u1ed1i T&aacute;c c\\u1ee7a MiharuBeauty s\\u1ebd \\u0111\\u01b0\\u1ee3c chia s\\u1ebb doanh thu theo th\\u1ecfa thu\\u1eadn c\\u1ee7a MiharuBeauty v\\u1edbi Ng\\u01b0\\u1eddi chia s\\u1ebb.\\u003C\\/p\\u003E\\n\\u003Cul\\u003E\\n\\u003Cli\\u003EDoanh thu t\\u1eeb ch\\u01b0\\u01a1ng tr&igrave;nh \\u0111\\u1ed1i t&aacute;c s\\u1ebd \\u0111\\u01b0\\u1ee3c h\\u1ea1ch to&aacute;n theo th&aacute;ng v&agrave; chi tr\\u1ea3 ch\\u1eadm nh\\u1ea5t v&agrave;o ng&agrave;y 10-20 c\\u1ee7a th&aacute;ng th\\u1ee9 2. V&iacute; d\\u1ee5: doanh thu th&aacute;ng 01 s\\u1ebd chi tr\\u1ea3 v&agrave;o t\\u1eeb ng&agrave;y 10-20\\/03 (c&oacute; tr\\u01b0\\u1eddng h\\u1ee3p tr\\u1ec5 do h\\u1ec7 th\\u1ed1ng ng&acirc;n h&agrave;ng ho\\u1eb7c tr&ugrave;ng v&agrave;o c&aacute;c ng&agrave;y ngh\\u1ec9).&nbsp;\\u003C\\/li\\u003E\\n\\u003Cli\\u003EM\\u1ee9c chi\\u1ebft kh\\u1ea5u c\\u1ee5 th\\u1ec3 cho ng\\u01b0\\u1eddi chia s\\u1ebb l&agrave;:\\u003C\\/li\\u003E\\n\\u003C\\/ul\\u003E\\n\\u003Cul\\u003E\\n\\u003Cli\\u003EMiharuBeauty s\\u1ebd kh\\u1ea5u tr\\u1eeb thu\\u1ebf thu nh\\u1eadp c&aacute; nh&acirc;n 10% \\u0111\\u1ed1i v\\u1edbi doanh thu \\u0111\\u1ed1i t&aacute;c \\u0111\\u1ea1t tr&ecirc;n 500,000\\u0111 (bao g\\u1ed3m c\\u1ea3 thu nh\\u1eadp t\\u1eeb th\\u01b0\\u1edfng n\\u1ebfu c&oacute;).\\u003C\\/li\\u003E\\n\\u003C\\/ul\\u003E\\n\\u003Ch3\\u003EH\\u1ee6Y TH\\u1eceA THU\\u1eacN V&Agrave; C\\u1eacP NH\\u1eacT \\u0110I\\u1ec0U KHO\\u1ea2N&nbsp;\\u003C\\/h3\\u003E\\n\\u003Ch4\\u003E\\u0110i\\u1ec1u 10. Hu\\u1ef7 tho\\u1ea3 thu\\u1eadn h\\u1ee3p t&aacute;c&nbsp;\\u003C\\/h4\\u003E\\n\\u003Cul\\u003E\\n\\u003Cli\\u003EB\\u1ea5t k\\u1ef3 ho\\u1ea1t \\u0111\\u1ed9ng gian l\\u1eadn hay c&aacute;c h&agrave;nh vi vi ph\\u1ea1m m\\u1ed9t trong c&aacute;c \\u0111i\\u1ec1u kho\\u1ea3n n&oacute;i tr&ecirc;n s\\u1ebd d\\u1eabn \\u0111\\u1ebfn vi\\u1ec7c d\\u1eebng th\\u1ecfa thu\\u1eadn h\\u1ee3p t&aacute;c, ho\\u1eb7c cao h\\u01a1n l&agrave; kh&oacute;a t&agrave;i kho\\u1ea3n v&agrave; h\\u1ee7y m\\u1ecdi k\\u1ebft qu\\u1ea3 \\u0111\\u1ea1t \\u0111\\u01b0\\u1ee3c t\\u1ea1i MiharuBeauty.com m&agrave; kh&ocirc;ng c\\u1ea7n th&ocirc;ng b&aacute;o tr\\u01b0\\u1edbc.\\u003C\\/li\\u003E\\n\\u003C\\/ul\\u003E\\n\\u003Ch4\\u003E\\u0110i\\u1ec1u 11. V\\u1ec1 vi\\u1ec7c c\\u1eadp nh\\u1eadt \\u0111i\\u1ec1u kho\\u1ea3n\\u003C\\/h4\\u003E\\n\\u003Cp\\u003EMiharuBeauty c&oacute; th\\u1ec3 thay \\u0111\\u1ed5i, b\\u1ed5 sung ho\\u1eb7c s\\u1eeda ch\\u1eefa th\\u1ecfa thu\\u1eadn n&agrave;y b\\u1ea5t c\\u1ee9 l&uacute;c n&agrave;o nh\\u1eb1m c\\u1eadp nh\\u1eadt nh\\u1eefng ch&iacute;nh s&aacute;ch m\\u1edbi nh\\u1ea5t. C&aacute;c c\\u1eadp nh\\u1eadt m\\u1edbi, quan tr\\u1ecdng s\\u1ebd \\u0111\\u01b0\\u1ee3c th&ocirc;ng b&aacute;o v&agrave; g\\u1eedi email t\\u1edbi c&aacute;c \\u0111\\u1ed1i t&aacute;c li&ecirc;n quan.&nbsp;\\u003C\\/p\\u003E\\n\\u003Cp\\u003E\\u003Cbr \\/\\u003E\\u003Cbr \\/\\u003E\\u003C\\/p\\u003E\"}', '1');

-- Deployed

-- 2023-07-17 Nhật
ALTER TABLE `user__coins` CHANGE `type` `type` ENUM('order','product','review','referral','share') NULL DEFAULT NULL;

-- 2023-07-19
UPDATE `pd__products` SET `short_description` = NULL WHERE 1;
ALTER TABLE `order__webhooks` ADD `type` VARCHAR(31) NULL DEFAULT NULL AFTER `id`;

-- 2023-07-19 Truong
ALTER TABLE `mkt__vouchers` ADD `product_id` INT(10) UNSIGNED NULL AFTER `order_id`;
ALTER TABLE `mkt__wheels` ADD `sort_order` INT (3) NOT NULL DEFAULT '1' AFTER `total`;
ALTER TABLE `mkt__wheels` ADD `product_id` INT(10) NULL AFTER `id`;

-- 2023-07-20
ALTER TABLE `pd__manufacturers` ADD `commission` SMALLINT(6) UNSIGNED NOT NULL DEFAULT '0' AFTER `name`;
UPDATE `pd__manufacturers` SET `commission` = '10' WHERE 1;

-- 2023-07-20 Nhật
ALTER TABLE `pg__menus` ADD `image` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `icon`;

-- Deployed
-- 2023-07-22 Nhật
ALTER TABLE `mkt__vouchers` ADD `quantity` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `total`;
ALTER TABLE `mkt__vouchers` CHANGE `used` `used` INT(10) UNSIGNED NOT NULL DEFAULT '0';

-- Deployed
-- 2023-07-24 Nhật
ALTER TABLE `users` ADD `coins_expired` DATE NULL AFTER `coins`;

-- Deployed
-- 2023-07-28 Truong
ALTER TABLE `pg__menus` ADD `is_redirect` BOOLEAN NOT NULL DEFAULT FALSE AFTER `is_header`;

-- 2023-08-03 Nhật
ALTER TABLE `aff__agent_points` ADD `status` BOOLEAN NOT NULL DEFAULT FALSE AFTER `amount`;

--2023-08-08 Nhật
ALTER TABLE `sys__feedbacks` ADD `phone_number` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `file`;

-- Deployed

-- 2023-08-10 Huy
ALTER TABLE `order__shipping` CHANGE `order_number` `order_number` CHAR(31) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
-- Deployed

ALTER TABLE `loc__wards` ADD `vt_id` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `district_id`;
ALTER TABLE `loc__wards` ADD UNIQUE KEY `loc__wards_vt_id_unique` (`vt_id`);
ALTER TABLE `loc__wards` ADD `vt_district_id` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `vt_id`;


-- 2023-08-11 Truong
ALTER TABLE `crt__sessions` CHANGE `user_id` `user_id` INT(10) UNSIGNED NULL;
-- php artisan cache:clear
-- Deployed

-- 2023-08-12 Nhật
ALTER TABLE `orders` ADD `reason` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `channel`;

-- 2023-08-16 Nhật
ALTER TABLE `mkt__vouchers` ADD `type` CHAR(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'F' AFTER `code`;

-- 2023-08-17 Nhật
ALTER TABLE `order__products` ADD `message` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `coins`;

-- Deployed
ALTER TABLE `orders` ADD `idx` VARCHAR(31) NULL AFTER `id`;
ALTER TABLE `orders` ADD UNIQUE KEY `orders_idx_unique` (`idx`);
-- Deployed

-- 2023-09-03 Truong
ALTER TABLE `pd__product_desc` CHANGE `longname` `long_name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `pd__products` ADD `long_name` VARCHAR(255) NULL AFTER `name`;
-- Deployed


-- 2023-09-16 Truong
ALTER TABLE `user__notifies` CHANGE `user_id` `user_id` INT(10) UNSIGNED NULL;

-- 2023-10-21 Nhat
ALTER TABLE `orders` ADD `shipping_discount` DECIMAL(15,0) NOT NULL DEFAULT '0' AFTER `shipping_fee`;
ALTER TABLE `crt__sessions` ADD `shipping_discount` DECIMAL(15,0) NULL DEFAULT '0' AFTER `shipping_fee`;
-- Deployed

-- 2023-10-25 Nhật
ALTER TABLE `mkt__coupons` ADD `product_ids` TEXT NULL DEFAULT NULL AFTER `code`;

-- 18-11-2023 Nhật
ALTER TABLE `pd__product_specials` ADD `quantity` SMALLINT(6) UNSIGNED NULL DEFAULT NULL AFTER `price`;
ALTER TABLE `pd__product_specials` ADD `used` SMALLINT(6) UNSIGNED NOT NULL DEFAULT '0' AFTER `quantity`;

-- Deployed

ALTER TABLE `aff__agent_withdrawals` ADD `start_range` DATE NULL AFTER `amount`, ADD `end_range` DATE NULL AFTER `start_range`;
-- Deployed
ALTER TABLE `mkt__wheels` ADD `lost` BOOLEAN NOT NULL DEFAULT FALSE AFTER `sort_order`;
-- Deployed

-- 2023-12-01 Truong
ALTER TABLE `mkt__vouchers` ADD `obj_id` INT(11) NULL AFTER `product_id`, ADD `source` VARCHAR(20) NULL AFTER `obj_id`;
-- Deployed

-- 2023-12-11 Nhat
ALTER TABLE `mkt__coupons` ADD `is_public` TINYINT(1) NOT NULL DEFAULT '0' AFTER `status`;

-- 2023-12-12 Nhật
ALTER TABLE `pd__products` ADD `no_cod` TINYINT(1) NOT NULL DEFAULT '0' AFTER `is_included`;

-- 2023-12-13 Nhật
ALTER TABLE `mkt__coupons` ADD `rank_id` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `product_ids`;
ALTER TABLE `mkt__coupons` ADD `max_discount` DECIMAL(15,0) NULL DEFAULT NULL AFTER `discount`;

-- 2023-12-17 Nhật
ALTER TABLE `mkt__vouchers` ADD `start_date` TIMESTAMP NULL DEFAULT NULL AFTER `used`;
ALTER TABLE `mkt__vouchers` CHANGE `end_date` `end_date` TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE `mkt__coupons` CHANGE `start_date` `start_date` TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE `mkt__coupons` CHANGE `end_date` `end_date` TIMESTAMP NULL DEFAULT NULL;
-- Deployed

-- 2023-01-06 Nhật
ALTER TABLE `mkt__coupons` ADD `new_member` TINYINT(1) NOT NULL DEFAULT '0' AFTER `is_public`;

-- 2023-01-06 Trường
ALTER TABLE `pd__product_specials` ADD `is_flashsale` BOOLEAN NOT NULL DEFAULT FALSE AFTER `price`;
-- Deployed Miharu

-- 2023-01-16 Nhật
ALTER TABLE `mkt__coupons` ADD `category_ids` TEXT NULL DEFAULT NULL AFTER `product_ids`;
ALTER TABLE `mkt__coupons` ADD `rule` ENUM('include','exclude') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'include' AFTER `rank_id`;

-- 2023-01-18 Nhật
ALTER TABLE `mkt__vouchers` CHANGE `product_id` `product_ids` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `mkt__vouchers` ADD `category_ids` TEXT NULL DEFAULT NULL AFTER `product_ids`;
ALTER TABLE `mkt__vouchers` ADD `rule` ENUM('include','exclude') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'include' AFTER `source`;

-- 2023-01-22 Nhật
ALTER TABLE `mkt__vouchers` ADD `max_discount` DECIMAL(15,0) NULL DEFAULT NULL AFTER `amount`;

-- 2024-01-29 Trường
ALTER TABLE `mkt__emails` CHANGE `content` `content` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
-- Deployed

-- 2024-01-29 Trường
ALTER TABLE `orders` ADD `tags` VARCHAR(191) NULL AFTER `comment`;
ALTER TABLE `mkt__emails` ADD `is_html` BOOLEAN NOT NULL DEFAULT FALSE AFTER `emails`;
-- Deployed

-- 2024-02-26 Trường
ALTER TABLE `mkt__coupons` ADD `limited` INT(10) NULL AFTER `end_date`;
ALTER TABLE `mkt__coupons` ADD `limit_per_customer` INT(10) NULL AFTER `uses_total`;
ALTER TABLE `mkt__coupon_histories` CHANGE `user_id` `user_id` INT(10) UNSIGNED NULL;
ALTER TABLE `mkt__coupon_histories` ADD `session_id` VARCHAR(32) NULL AFTER `user_id`;
-- Deployed onlstreet

-- 2024-02-29 Trường
ALTER TABLE `pd__flashsales` ADD `special_ids` TEXT NULL AFTER `end_date`;
-- Deployed onlstreet
ALTER TABLE `aff__agent_points` CHANGE `commission` `commission` DECIMAL(6,3) UNSIGNED NOT NULL DEFAULT '0';

-- Deployed

-- 2024-07-14 Truong
ALTER TABLE `mkt__wheels` ADD `type` CHAR(1) NOT NULL DEFAULT 'P' AFTER `name`, ADD `amount` DECIMAL(15,0) NOT NULL DEFAULT '0' AFTER `type`;
ALTER TABLE `mkt__wheels` ADD `end_date` TIMESTAMP NULL AFTER `amount`;
ALTER TABLE `user__wheels` ADD `email` VARCHAR(100) NULL AFTER `order_id`, ADD `phone_number` VARCHAR(30) NULL AFTER `email`;
-- Deployed sweetgirl

-- 2024-07-24 Truong
ALTER TABLE `aff__agent_withdrawals` ADD `approver_id` INT(11) UNSIGNED NULL AFTER `rejected_at`;
ALTER TABLE `user__wheels` CHANGE `user_id` `user_id` INT(10) UNSIGNED NULL;
ALTER TABLE `mkt__vouchers` CHANGE `user_id` `user_id` INT(10) UNSIGNED NULL;
-- Deployed

-- 2024-07-29
ALTER TABLE `mkt__emails` ADD `attach` VARCHAR(255) NULL AFTER `emails`;
-- Deployed

-- 2024-08-30
ALTER TABLE `mkt__wheels` ADD `category_id` INT(10) NULL AFTER `id`;
-- Deployed

-- 2024-09-06
ALTER TABLE `mkt__coupons` ADD `group` ENUM('order','shipping') NOT NULL DEFAULT 'order' AFTER `rank_id`;
-- Deployed

-- 2024-09-22
ALTER TABLE `users` ADD `spend` BIGINT(14) UNSIGNED NOT NULL DEFAULT '0' AFTER `password_failed`;
-- Deployed
