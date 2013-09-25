CREATE TABLE IF NOT EXISTS `#__oauth_clients` (
`client_id` INTEGER PRIMARY KEY AUTO_INCREMENT,
`key` VARCHAR(255) NOT NULL DEFAULT '',
`alias` VARCHAR(255) NOT NULL DEFAULT '',
`secret` VARCHAR(255) NOT NULL DEFAULT '',
`title` VARCHAR(255) NOT NULL DEFAULT '',
CONSTRAINT `idx_oauth_clients_key` UNIQUE (`key`)
);

DROP TABLE IF EXISTS `#__oauth_credentials`;
CREATE TABLE IF NOT EXISTS `#__oauth_credentials` (
  `credentials_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` varchar(255) NOT NULL DEFAULT '',
  `client_secret` varchar(255) NOT NULL DEFAULT '',
  `temporary_token` varchar(255) NOT NULL,
  `access_token` varchar(255) NOT NULL DEFAULT '',
  `refresh_token` varchar(255) NOT NULL,
  `resource_uri` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT '',
  `callback_url` varchar(255) NOT NULL DEFAULT '',
  `resource_owner_id` int(11) NOT NULL DEFAULT '0',
  `expiration_date` datetime DEFAULT NULL,
  `temporary_expiration_date` datetime DEFAULT NULL,
  PRIMARY KEY (`credentials_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ;
