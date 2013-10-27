CREATE TABLE IF NOT EXISTS `rl_review` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`team_id` INT UNSIGNED NOT NULL,
	`description` varchar(1024) DEFAULT NULL,
	`timestamp` INT UNSIGNED DEFAULT NULL,
	`teamspermissions` INT UNSIGNED DEFAULT 0,
	`otherpermissions` INT UNSIGNED DEFAULT 0,
	PRIMARY KEY (`id`),
	FOREIGN KEY (`team_id`) REFERENCES rl_team(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;