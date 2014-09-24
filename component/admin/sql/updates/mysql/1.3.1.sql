SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `#__redcore_oauth_access_tokens`
ADD INDEX `idx_client_id` (`client_id`);

ALTER TABLE `#__redcore_oauth_authorization_codes`
ADD INDEX `idx_client_id` (`client_id`);

ALTER TABLE `#__redcore_oauth_refresh_tokens`
ADD INDEX `idx_client_id` (`client_id`);

ALTER TABLE `#__redcore_oauth_clients`
CHANGE `scope` `scope` TEXT;

ALTER TABLE `#__redcore_oauth_access_tokens`
CHANGE `scope` `scope` TEXT;

ALTER TABLE `#__redcore_oauth_authorization_codes`
CHANGE `scope` `scope` TEXT;

ALTER TABLE `#__redcore_oauth_refresh_tokens`
CHANGE `scope` `scope` TEXT;

INSERT INTO `#__redcore_oauth_scopes` (`scope`) VALUES
  ('create'),
  ('read'),
  ('update'),
  ('delete'),
  ('documentation'),
  ('task');

SET FOREIGN_KEY_CHECKS = 1;
