SET FOREIGN_KEY_CHECKS = 0;

-- Change current tables
ALTER TABLE `#__redcore_webservices`
 ADD KEY `idx_webservice_keys` (`client`, `name`, `version`);

ALTER TABLE `#__redcore_oauth_clients`
 DROP KEY `idx_client_id`;

ALTER TABLE `#__redcore_oauth_clients`
 ADD UNIQUE KEY `idx_client_id` (`client_id`);

-- Add new tables

-- -----------------------------------------------------
-- Table `#__redcore_country`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__redcore_country` (
  `id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `alpha2` CHAR(2) NOT NULL,
  `alpha3` CHAR(3) NOT NULL,
  `numeric` SMALLINT(3) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_name` (`name` ASC),
  UNIQUE KEY `idx_alpha2` (`alpha2` ASC),
  UNIQUE KEY `idx_alpha3` (`alpha3` ASC),
  UNIQUE KEY `idx_numeric` (`numeric` ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `#__redcore_currency`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__redcore_currency` (
  `id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `alpha3` CHAR(3) NOT NULL,
  `numeric` SMALLINT(3) UNSIGNED NOT NULL,
  `symbol` VARCHAR(255) NOT NULL,
  `symbol_position` TINYINT(1) NOT NULL DEFAULT '1' COMMENT 'display currency symbol before (0) or after (1) price',
  `decimals` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'number of decimals to show in prices',
  `state` TINYINT(1) NOT NULL DEFAULT '1' COMMENT 'disabled(0) / enabled(1)',
  `blank_space` TINYINT(1) NOT NULL DEFAULT '1' COMMENT 'display a blank space between the currency symbol and the price',
  `decimal_separator` VARCHAR(1) NOT NULL DEFAULT ',',
  `thousands_separator` VARCHAR(1) NOT NULL DEFAULT '.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_alpha3` (`alpha3` ASC),
  UNIQUE KEY `idx_numeric` (`numeric` ASC),
  KEY `idx_name` (`name` ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `#__redcore_payments`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__redcore_payments` (
  `id`                  INT(11)  UNSIGNED NOT NULL AUTO_INCREMENT,
  `extension_name`      VARCHAR(255)      NOT NULL DEFAULT '',
  `owner_name`          VARCHAR(255)      NOT NULL DEFAULT '',
  `payment_name`        VARCHAR(50)       NOT NULL DEFAULT '',
  `sandbox`             TINYINT(1)        NOT NULL DEFAULT '0',
  `order_name`          VARCHAR(255)      NOT NULL DEFAULT '',
  `order_id`            VARCHAR(255)      NOT NULL DEFAULT '',
  `url_cancel`          VARCHAR(2000)     NOT NULL DEFAULT '',
  `url_accept`          VARCHAR(2000)     NOT NULL DEFAULT '',
  `client_email`        VARCHAR(255)      NOT NULL DEFAULT '',
  `created_date`        DATETIME          NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_date`       DATETIME          NOT NULL DEFAULT '0000-00-00 00:00:00',
  `confirmed_date`      DATETIME          NOT NULL DEFAULT '0000-00-00 00:00:00',
  `transaction_id`      VARCHAR(255)      NOT NULL DEFAULT '',
  `amount_original`     DECIMAL(10,2)     NOT NULL,
  `amount_order_tax`    DECIMAL(10,2)     NOT NULL,
  `order_tax_details`   VARCHAR(2000)     NOT NULL DEFAULT '',
  `amount_shipping`     DECIMAL(10,2)     NOT NULL,
  `shipping_details`    VARCHAR(2000)     NOT NULL DEFAULT '',
  `amount_payment_fee`  DECIMAL(10,2)     NOT NULL,
  `amount_total`        DECIMAL(10,2)     NOT NULL,
  `amount_paid`         DECIMAL(10,2)     NOT NULL,
  `currency`            VARCHAR(32)       NOT NULL DEFAULT '',
  `coupon_code`         VARCHAR(255)      NOT NULL DEFAULT '',
  `customer_note`       VARCHAR(2000)     NOT NULL DEFAULT '',
  `status`              VARCHAR(32)       NOT NULL DEFAULT '',
  `params`              TEXT              NOT NULL,
  `retry_counter`       TINYINT(4)        NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_extension_order_id` (`extension_name`, `order_id`),
  KEY `idx_extension_payment` (`extension_name`, `owner_name`, `payment_name`),
  KEY `idx_payment_confirmed` (`confirmed_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `#__redcore_payment_log`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__redcore_payment_log` (
  `id`              INT(11)  UNSIGNED NOT NULL AUTO_INCREMENT,
  `payment_id`      INT(11)  UNSIGNED NOT NULL,
  `created_date`    DATETIME          NOT NULL DEFAULT '0000-00-00 00:00:00',
  `amount`          DECIMAL(10,2)     NOT NULL,
  `currency`        VARCHAR(32)       NOT NULL DEFAULT '',
  `coupon_code`     VARCHAR(255)      NOT NULL DEFAULT '',
  `ip_address`      VARCHAR(100)      NOT NULL DEFAULT '',
  `referrer`        VARCHAR(2000)     NOT NULL DEFAULT '',
  `message_uri`     VARCHAR(2000)     NOT NULL DEFAULT '',
  `message_post`    TEXT              NOT NULL,
  `message_text`    TEXT              NOT NULL,
  `status`          VARCHAR(32)       NOT NULL DEFAULT '',
  `transaction_id`  VARCHAR(255)      NOT NULL DEFAULT '',
  `customer_note`   VARCHAR(2000)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_payment_id` (`payment_id`),
  KEY `idx_transaction_id` (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `#__redcore_payment_configuration`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__redcore_payment_configuration` (
  `id`                INT(11)  UNSIGNED NOT NULL AUTO_INCREMENT,
  `extension_name`    VARCHAR(255)      NOT NULL DEFAULT '',
  `owner_name`        VARCHAR(255)      NOT NULL DEFAULT '',
  `payment_name`      VARCHAR(50)       NOT NULL DEFAULT '',
  `params`            TEXT              NOT NULL,
  `state`             TINYINT(1)        NOT NULL DEFAULT '1',
  `checked_out`       INT(11)           NULL      DEFAULT NULL,
  `checked_out_time`  DATETIME          NOT NULL  DEFAULT '0000-00-00 00:00:00',
  `created_by`        INT(11)           NULL      DEFAULT NULL,
  `created_date`      DATETIME          NOT NULL  DEFAULT '0000-00-00 00:00:00',
  `modified_by`       INT(11)           NULL      DEFAULT NULL,
  `modified_date`     DATETIME          NOT NULL  DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `idx_extension_config` (`extension_name`, `owner_name`, `payment_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Add table data
-- Currency and country data are added through php script on this update

SET FOREIGN_KEY_CHECKS = 1;
