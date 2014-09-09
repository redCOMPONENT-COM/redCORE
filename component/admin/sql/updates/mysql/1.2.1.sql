SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `#__redcore_oauth_clients` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` varchar(80) NOT NULL DEFAULT '',
  `client_secret` varchar(80) NOT NULL DEFAULT '',
  `redirect_uri` varchar(2000) NOT NULL DEFAULT '',
  `grant_types` varchar(80),
  `scope` varchar(100),
  `user_id` varchar(80),
  PRIMARY KEY (`id`),
  KEY `idx_client_id` (`client_id`)
) DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `#__redcore_oauth_access_tokens` (
  `access_token` varchar(40) NOT NULL DEFAULT '',
  `client_id` varchar(80) NOT NULL DEFAULT '',
  `user_id` varchar(255),
  `expires` TIMESTAMP NOT NULL,
  `scope` varchar(2000),
  CONSTRAINT `redcore_access_token_pk` PRIMARY KEY (`access_token`)
) DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `#__redcore_oauth_authorization_codes` (
  `authorization_code` varchar(40) NOT NULL DEFAULT '',
  `client_id` varchar(80) NOT NULL DEFAULT '',
  `user_id` varchar(255),
  `redirect_uri` varchar(2000),
  `expires` TIMESTAMP NOT NULL,
  `scope` varchar(2000),
  CONSTRAINT `redcore_auth_code_pk` PRIMARY KEY (`authorization_code`)
) DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `#__redcore_oauth_refresh_tokens` (
  `refresh_token` varchar(40) NOT NULL DEFAULT '',
  `client_id` varchar(80) NOT NULL DEFAULT '',
  `user_id` varchar(255),
  `expires` TIMESTAMP NOT NULL,
  `scope` varchar(2000),
  CONSTRAINT `redcore_refresh_token_pk` PRIMARY KEY (`refresh_token`)
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

SET FOREIGN_KEY_CHECKS = 1;
