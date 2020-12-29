ALTER TABLE `#__redcore_oauth_clients`
    ADD `client_type` ENUM ('confidential','public') NOT NULL DEFAULT 'confidential' AFTER `user_id`;