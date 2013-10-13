SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `#__redcore_plugin` (
  `id`               INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `extension_id`     INT(11)          NOT NULL
  COMMENT 'fk to #__extensions',
  `context`          VARCHAR(255)     NOT NULL,
  `params`           TEXT             NOT NULL,
  `state`            TINYINT(4)       NOT NULL DEFAULT '0',
  `checked_out`      INT(11) DEFAULT NULL,
  `checked_out_time` DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by`       INT(11) DEFAULT NULL,
  `created_date`     DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by`      INT(11) DEFAULT NULL,
  `modified_date`    DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY (`extension_id`),
  KEY (`context`),
  UNIQUE KEY (`extension_id`, `context`),
  KEY (`state`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

SET FOREIGN_KEY_CHECKS = 1;
