SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `#__redcore_oauth_jti` (
  `issuer` varchar(80) NOT NULL,
  `subject` varchar(80),
  `audiance` varchar(80),
  `expires` TIMESTAMP NOT NULL,
  `jti` varchar(2000) NOT NULL
) DEFAULT CHARSET = utf8;

SET FOREIGN_KEY_CHECKS = 1;
