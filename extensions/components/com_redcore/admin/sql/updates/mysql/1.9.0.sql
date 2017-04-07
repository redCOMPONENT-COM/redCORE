-- -----------------------------------------------------
-- Table `#__redcore_schemas`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__redcore_schemas` (
  `asset_id` varchar(255) NOT NULL,
  `fields` text NOT NULL,
  `cached_on` datetime NOT NULL,
  PRIMARY KEY (`asset_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
