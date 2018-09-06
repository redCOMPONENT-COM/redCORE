
-- -----------------------------------------------------
-- Table `#__redcore_webservice_history_log`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__redcore_webservice_history_log` (
  `id`                   INT(11)  UNSIGNED NOT NULL AUTO_INCREMENT,
  `webservice_name`      VARCHAR(255)      NOT NULL DEFAULT '',
  `webservice_version`   VARCHAR(5)        NOT NULL DEFAULT '1.0.0',
  `webservice_client`    VARCHAR(15)       NOT NULL DEFAULT 'site',
  `url`                  VARCHAR(2000)     NOT NULL DEFAULT '',
  `authentication`       VARCHAR(50)       NOT NULL DEFAULT '',
  `authentication_user`  VARCHAR(100)      NOT NULL DEFAULT '',
  `operation`            VARCHAR(50)       NOT NULL DEFAULT '',
  `method`               VARCHAR(50)       NOT NULL DEFAULT '',
  `using_soap`           TINYINT(1)        NOT NULL DEFAULT '0',
  `execution_time`       INT(5)            NOT NULL DEFAULT '0',
  `execution_memory`     INT(8)            NOT NULL DEFAULT '0',
  `file_name`            VARCHAR(255)      NOT NULL DEFAULT '',
  `messages`             TEXT              NULL,
  `status`               VARCHAR(255)      NOT NULL DEFAULT '',
  `created_by`           INT(11)           NULL     DEFAULT NULL,
  `created_date`         DATETIME          NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `idx_webservice_title` (`webservice_name`, `webservice_version`, `webservice_client`),
  KEY `idx_operation` (`operation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;