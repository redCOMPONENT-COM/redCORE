SET FOREIGN_KEY_CHECKS = 0;

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
-- Table `#__redcore_oauth_clients`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__redcore_oauth_clients` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` varchar(80) NOT NULL DEFAULT '',
  `client_secret` varchar(80) NOT NULL DEFAULT '',
  `redirect_uri` varchar(2000) NOT NULL DEFAULT '',
  `grant_types` varchar(80),
  `scope` TEXT,
  `user_id` varchar(80),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `#__redcore_oauth_access_tokens`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__redcore_oauth_access_tokens` (
  `access_token` varchar(40) NOT NULL DEFAULT '',
  `client_id` varchar(80) NOT NULL DEFAULT '',
  `user_id` varchar(255),
  `expires` TIMESTAMP NOT NULL,
  `scope` TEXT,
  PRIMARY KEY (`access_token`),
  KEY `idx_client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `#__redcore_oauth_authorization_codes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__redcore_oauth_authorization_codes` (
  `authorization_code` varchar(40) NOT NULL DEFAULT '',
  `client_id` varchar(80) NOT NULL DEFAULT '',
  `user_id` varchar(255),
  `redirect_uri` varchar(2000),
  `expires` TIMESTAMP NOT NULL,
  `scope` TEXT,
  PRIMARY KEY (`authorization_code`),
  KEY `idx_client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `#__redcore_oauth_refresh_tokens`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__redcore_oauth_refresh_tokens` (
  `refresh_token` varchar(40) NOT NULL DEFAULT '',
  `client_id` varchar(80) NOT NULL DEFAULT '',
  `user_id` varchar(255),
  `expires` TIMESTAMP NOT NULL,
  `scope` TEXT,
  PRIMARY KEY (`refresh_token`),
  KEY `idx_client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `#__redcore_oauth_users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__redcore_oauth_users` (
  `username` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(2000),
  `first_name` varchar(255),
  `last_name` varchar(255),
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `#__redcore_oauth_scopes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__redcore_oauth_scopes` (
  `scope` TEXT,
  `is_default` BOOLEAN
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `#__redcore_oauth_jwt`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__redcore_oauth_jwt` (
  `client_id` varchar(80) NOT NULL DEFAULT '',
  `subject` varchar(80),
  `public_key` varchar(2000),
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `#__redcore_oauth_jti`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__redcore_oauth_jti` (
  `issuer` varchar(80) NOT NULL,
  `subject` varchar(80),
  `audiance` varchar(80),
  `expires` TIMESTAMP NOT NULL,
  `jti` varchar(2000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `#__redcore_oauth_public_keys`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__redcore_oauth_public_keys` (
  `client_id`            varchar(80),
  `public_key`           varchar(2000),
  `private_key`          varchar(2000),
  `encryption_algorithm` varchar(100) DEFAULT 'RS256',
  PRIMARY KEY (`client_id`)
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

-- -----------------------------------------------------
-- Table `#__redcore_webservices`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__redcore_webservices` (
  `id`                INT(10)     UNSIGNED  NOT NULL AUTO_INCREMENT,
  `name`              VARCHAR(255)          NOT NULL DEFAULT '',
  `version`           VARCHAR(5)            NOT NULL DEFAULT '1.0.0',
  `title`             VARCHAR(255)          NOT NULL DEFAULT '',
  `path`              VARCHAR(255)          NOT NULL DEFAULT '',
  `xmlFile`           VARCHAR(255)          NOT NULL DEFAULT '',
  `xmlHashed`         VARCHAR(32)           NOT NULL DEFAULT '',
  `operations`        TEXT                  NULL,
  `scopes`            TEXT                  NULL,
  `client`            VARCHAR(15)           NOT NULL DEFAULT 'site',
  `state`             TINYINT(1)            NOT NULL DEFAULT '1',
  `checked_out`       INT(11)               NULL     DEFAULT NULL,
  `checked_out_time`  DATETIME              NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by`        INT(11)               NULL     DEFAULT NULL,
  `created_date`      DATETIME              NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by`       INT(11)               NULL     DEFAULT NULL,
  `modified_date`     DATETIME              NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `idx_webservice_keys` (`client`, `name`, `version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `#__redcore_translation_tables`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__redcore_translation_tables` (
  `id`                INT(11)     UNSIGNED  NOT NULL AUTO_INCREMENT,
  `name`              VARCHAR(255)          NOT NULL DEFAULT '',
  `extension_name`    VARCHAR(255)          NOT NULL DEFAULT '',
  `title`             VARCHAR(255)          NOT NULL DEFAULT '',
  `version`           VARCHAR(10)           NOT NULL DEFAULT '1.0.0',
  `primary_columns`   VARCHAR(100)          NOT NULL DEFAULT 'id',
  `translate_columns` VARCHAR(500)          NOT NULL DEFAULT '',
  `fallback_columns`  VARCHAR(500)          NOT NULL DEFAULT '',
  `form_links`        TEXT                  NULL,
  `xml_path`          VARCHAR(500)          NOT NULL DEFAULT '',
  `xml_hashed`        VARCHAR(32)           NOT NULL DEFAULT '',
  `filter_query`      VARCHAR(255)          NOT NULL DEFAULT '',
	`params`            TEXT                  NOT NULL,
  `state`             TINYINT(1)            NOT NULL DEFAULT '1',
  `checked_out`       INT(11)               NULL     DEFAULT NULL,
  `checked_out_time`  DATETIME              NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by`        INT(11)               NULL     DEFAULT NULL,
  `created_date`      DATETIME              NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by`       INT(11)               NULL     DEFAULT NULL,
  `modified_date`     DATETIME              NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `idx_translations_keys` (`name`, `extension_name`, `state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `#__redcore_translation_columns`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__redcore_translation_columns` (
	`id`                    INT(11)      UNSIGNED  NOT NULL AUTO_INCREMENT,
	`translation_table_id`  INT(11)      UNSIGNED  NOT NULL,
	`name`                  VARCHAR(100)           NOT NULL,
	`title`                 VARCHAR(100)           NOT NULL DEFAULT '',
	`column_type`           VARCHAR(45)            NOT NULL DEFAULT 'translate',
	`value_type`            VARCHAR(45)            NOT NULL DEFAULT 'text',
	`fallback`              TINYINT(1)             NOT NULL DEFAULT '0',
	`filter`                VARCHAR(50)            NOT NULL DEFAULT 'RAW',
	`description`           TEXT                   NOT NULL,
	`params`                TEXT                   NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `unq_translation_columns_keys` (`translation_table_id`, `name`),
	KEY `idx_translation_columns_keys` (`translation_table_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------------
-- Table `#__redcore_schemas`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__redcore_schemas` (
  `asset_id` varchar(255) NOT NULL,
  `fields` text NOT NULL,
  `cached_on` datetime NOT NULL,
  PRIMARY KEY (`asset_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
