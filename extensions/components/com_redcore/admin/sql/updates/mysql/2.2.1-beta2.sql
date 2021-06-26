ALTER TABLE `#__redcore_oauth_authorization_codes`
	ADD `id_token` VARCHAR(1000) DEFAULT NULL AFTER `scope`;