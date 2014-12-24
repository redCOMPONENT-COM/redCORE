SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `#__redcore_oauth_clients` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` varchar(80) NOT NULL DEFAULT '',
  `client_secret` varchar(80) NOT NULL DEFAULT '',
  `redirect_uri` varchar(2000) NOT NULL DEFAULT '',
  `grant_types` varchar(80),
  `scope` TEXT,
  `user_id` varchar(80),
  PRIMARY KEY (`id`),
  KEY `idx_client_id` (`client_id`)
) DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `#__redcore_oauth_access_tokens` (
  `access_token` varchar(40) NOT NULL DEFAULT '',
  `client_id` varchar(80) NOT NULL DEFAULT '',
  `user_id` varchar(255),
  `expires` TIMESTAMP NOT NULL,
  `scope` TEXT,
  CONSTRAINT `redcore_access_token_pk` PRIMARY KEY (`access_token`),
  KEY `idx_client_id` (`client_id`)
) DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `#__redcore_oauth_authorization_codes` (
  `authorization_code` varchar(40) NOT NULL DEFAULT '',
  `client_id` varchar(80) NOT NULL DEFAULT '',
  `user_id` varchar(255),
  `redirect_uri` varchar(2000),
  `expires` TIMESTAMP NOT NULL,
  `scope` TEXT,
  CONSTRAINT `redcore_auth_code_pk` PRIMARY KEY (`authorization_code`),
  KEY `idx_client_id` (`client_id`)
) DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `#__redcore_oauth_refresh_tokens` (
  `refresh_token` varchar(40) NOT NULL DEFAULT '',
  `client_id` varchar(80) NOT NULL DEFAULT '',
  `user_id` varchar(255),
  `expires` TIMESTAMP NOT NULL,
  `scope` TEXT,
  CONSTRAINT `redcore_refresh_token_pk` PRIMARY KEY (`refresh_token`),
  KEY `idx_client_id` (`client_id`)
) DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `#__redcore_oauth_users` (
  `username` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(2000),
  `first_name` varchar(255),
  `last_name` varchar(255),
  CONSTRAINT `redcore_username_pk` PRIMARY KEY (`username`)
) DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `#__redcore_oauth_scopes` (
  `scope` TEXT,
  `is_default` BOOLEAN
) DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `#__redcore_oauth_jwt` (
  `client_id` varchar(80) NOT NULL DEFAULT '',
  `subject` varchar(80),
  `public_key` varchar(2000),
  CONSTRAINT `redcore_jwt_client_id_pk` PRIMARY KEY (`client_id`)
) DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `#__redcore_oauth_public_keys` (
  `client_id`            varchar(80),
  `public_key`           varchar(2000),
  `private_key`          varchar(2000),
  `encryption_algorithm` varchar(100) DEFAULT 'RS256',
  CONSTRAINT `redcore_oauth_public_keys_client_id_pk` PRIMARY KEY (`client_id`)
) DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `#__redcore_webservices` (
  `id`          INT(10)     UNSIGNED  NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(255)          NOT NULL DEFAULT '',
  `version`     VARCHAR(5)            NOT NULL DEFAULT '1.0.0',
  `title`       VARCHAR(255)          NOT NULL DEFAULT '',
  `path`        VARCHAR(255)          NOT NULL DEFAULT '',
  `xmlFile`     VARCHAR(255)          NOT NULL DEFAULT '',
  `xmlHashed`   VARCHAR(32)           NOT NULL DEFAULT '',
  `operations`  TEXT                  NULL,
  `scopes`      TEXT                  NULL,
  `client`      VARCHAR(15)           NOT NULL DEFAULT 'site',
  `state`       TINYINT(1)            NOT NULL DEFAULT '1',
  `checked_out`       INT(11)              NULL     DEFAULT NULL,
  `checked_out_time`  DATETIME             NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by`        INT(11)              NULL     DEFAULT NULL,
  `created_date`      DATETIME             NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by`       INT(11)              NULL     DEFAULT NULL,
  `modified_date`     DATETIME             NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET = utf8;

SET FOREIGN_KEY_CHECKS = 1;
