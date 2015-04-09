CREATE TABLE `__cache` (
	`cache_id` VARCHAR(255) NOT NULL,
	`cache_value` TEXT NOT NULL,
	`cache_ttl` DATETIME NOT NULL,
	PRIMARY KEY (`cache_id`),
	INDEX `cache_ttl` (`cache_ttl`)
)
ENGINE=InnoDB;
