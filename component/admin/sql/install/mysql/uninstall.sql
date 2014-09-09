SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `#__redcore_oauth_clients`;
DROP TABLE IF EXISTS `#__redcore_oauth_access_tokens`;
DROP TABLE IF EXISTS `#__redcore_oauth_authorization_codes`;
DROP TABLE IF EXISTS `#__redcore_oauth_refresh_tokens`;
DROP TABLE IF EXISTS `#__redcore_oauth_users`;
DROP TABLE IF EXISTS `#__redcore_oauth_scopes`;
DROP TABLE IF EXISTS `#__redcore_oauth_jwt`;
DROP TABLE IF EXISTS `#__redcore_oauth_public_keys`;

SET FOREIGN_KEY_CHECKS = 1;
