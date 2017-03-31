-- -----------------------------------------------------
-- Table `#__redcore_schemas`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__redcore_schemas` (
  `asset_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fields` text COLLATE utf8_unicode_ci NOT NULL,
  `cached_on` datetime NOT NULL,
  PRIMARY KEY (`asset_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
