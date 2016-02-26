--
-- redCORE currency Unit Test DDL
--

CREATE TABLE  `jos_redcore_currency` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` TEXT NOT NULL DEFAULT '',
  `alpha3` TEXT NOT NULL DEFAULT '',
  `numeric` INTEGER NOT NULL DEFAULT '0',
  `symbol` VARCHAR(255) NOT NULL,
  `symbol_position` INTEGER NOT NULL DEFAULT '1',
  `decimals` INTEGER NOT NULL DEFAULT '0',
  `state` INTEGER NOT NULL DEFAULT '1',
  `blank_space` INTEGER NOT NULL DEFAULT '1',
  `decimal_separator` TEXT NOT NULL DEFAULT ',',
  `thousands_separator` TEXT NOT NULL DEFAULT '.',
  CONSTRAINT `idx_alpha3` UNIQUE (`alpha3`),
  CONSTRAINT `idx_numeric` UNIQUE (`numeric`)
);

INSERT INTO jos_redcore_currency(`alpha3`, `name`, `symbol`, `numeric`, `symbol_position`, `decimals`, `state`, `blank_space`, `decimal_separator`, `thousands_separator`) VALUES('EUR', 'Euro', '€', 978, 1, 2, 1, 1, ',', '.');
INSERT INTO jos_redcore_currency(`alpha3`, `name`, `symbol`, `numeric`, `symbol_position`, `decimals`, `state`, `blank_space`, `decimal_separator`, `thousands_separator`) VALUES('USD', 'US Dollar', '$', 840, 0, 2, 1, 1, ',', '.');
INSERT INTO jos_redcore_currency(`alpha3`, `name`, `symbol`, `numeric`, `symbol_position`, `decimals`, `state`, `blank_space`, `decimal_separator`, `thousands_separator`) VALUES('VEF', 'Bolivar', 'Bs', 937, 1, 2, 1, 1, ',', '.');
