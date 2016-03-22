SET FOREIGN_KEY_CHECKS = 0;

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
