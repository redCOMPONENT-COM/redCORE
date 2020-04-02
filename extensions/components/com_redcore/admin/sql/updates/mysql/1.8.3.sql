SET FOREIGN_KEY_CHECKS = 0;

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
