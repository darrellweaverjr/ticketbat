CREATE TABLE `venue_ads` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `venue_id` INT NOT NULL,
  `image` VARCHAR(200) NOT NULL,
  `url` VARCHAR(200) NOT NULL,
  `type` ENUM('Regular', 'Horizontal', 'Vertical') NOT NULL,
  `order` TINYINT(2) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `clicks` TINYINT(4) NOT NULL DEFAULT 0,
  `start_date` DATETIME NOT NULL,
  `end_date` DATETIME NOT NULL,
  `updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;