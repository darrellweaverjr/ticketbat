CREATE TABLE `ticketba_lvtn`.`user_seller` (
  `user_id` INT(10) NOT NULL,
  `open_drawer` TINYINT(1) NOT NULL DEFAULT 0,
  `cash_in` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `time_in` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `session_id` VARCHAR(32) NOT NULL,
  UNIQUE INDEX `user_id_UNIQUE` (`user_id` ASC),
  PRIMARY KEY (`user_id`));


CREATE TABLE `ticketba_lvtn`.`seller_tally` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(10) NOT NULL,
  `time_in` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `time_out` DATETIME NULL DEFAULT NULL,
  `cash_in` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
  `cash_out` DECIMAL(10,2) NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC));
