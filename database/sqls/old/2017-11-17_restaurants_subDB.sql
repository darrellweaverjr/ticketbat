-- MySQL Script generated by MySQL Workbench

-- -----------------------------------------------------
-- Table `mydb`.`restaurants`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `restaurants` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `venue_id` INT(10) UNSIGNED NOT NULL,
  `name` VARCHAR(45) NOT NULL,
  `phone` VARCHAR(45) NULL DEFAULT NULL,
  `description` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`restaurant_specials`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `restaurant_specials` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(45) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `order` TINYINT NOT NULL DEFAULT 1,
  `image_id` VARCHAR(1000) NOT NULL,
  `enabled` TINYINT(1) NOT NULL DEFAULT 1,
  `restaurants_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB;


ALTER TABLE `restaurant_specials` 
ADD FOREIGN KEY (`restaurants_id`)
REFERENCES `restaurants` (`id`)
ON DELETE NO ACTION
ON UPDATE NO ACTION;


-- -----------------------------------------------------
-- Table `mydb`.`restaurant_awards`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `restaurant_awards` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `awarded` VARCHAR(45) NOT NULL,
  `description` TINYTEXT NULL,
  `posted` DATETIME NOT NULL,
  `image_id` VARCHAR(1000) NOT NULL,
  `restaurants_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB;


ALTER TABLE `restaurant_awards` 
ADD FOREIGN KEY (`restaurants_id`)
REFERENCES `restaurants` (`id`)
ON DELETE NO ACTION
ON UPDATE NO ACTION;


-- -----------------------------------------------------
-- Table `mydb`.`restaurant_menu`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `restaurant_menu` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `notes` TINYTEXT NULL DEFAULT NULL,
  `parent_id` INT(11) NOT NULL DEFAULT 0,
  `enabled` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`restaurant_items`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `restaurant_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `notes` VARCHAR(45) NULL DEFAULT NULL,
  `description` VARCHAR(45) NULL DEFAULT NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `image_id` VARCHAR(1000) NULL DEFAULT NULL,
  `order` TINYINT(1) NULL DEFAULT 1,
  `enabled` TINYINT(1) NULL,
  `restaurants_id` INT(11) NOT NULL,
  `restaurant_menu_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB;


ALTER TABLE `restaurant_items` 
ADD FOREIGN KEY (`restaurants_id`)
REFERENCES `restaurants` (`id`)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE `restaurant_items` 
ADD FOREIGN KEY (`restaurant_menu_id`)
REFERENCES `restaurant_menu` (`id`)
ON DELETE NO ACTION
ON UPDATE NO ACTION;


-- -----------------------------------------------------
-- Table `mydb`.`restaurant_albums`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `restaurant_albums` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(45) NOT NULL,
  `posted` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `enabled` TINYINT(1) NOT NULL DEFAULT 1,
  `restaurants_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB;


ALTER TABLE `restaurant_albums` 
ADD FOREIGN KEY (`restaurants_id`)
REFERENCES `restaurants` (`id`)
ON DELETE NO ACTION
ON UPDATE NO ACTION;


-- -----------------------------------------------------
-- Table `mydb`.`restaurant_album_images`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `restaurant_album_images` (
  `image_id` INT(10) UNSIGNED NOT NULL,
  `restaurant_albums_id` INT NOT NULL)
ENGINE = InnoDB;


ALTER TABLE `restaurant_album_images` 
ADD FOREIGN KEY (`restaurant_albums_id`)
REFERENCES `restaurant_albums` (`id`)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE `restaurant_album_images` 
ADD FOREIGN KEY (`image_id`)
REFERENCES `images` (`id`)
ON DELETE NO ACTION
ON UPDATE NO ACTION;


-- -----------------------------------------------------
-- Table `mydb`.`restaurant_reviews`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `restaurant_reviews` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` TINYTEXT NOT NULL,
  `link` TINYTEXT NOT NULL,
  `posted` DATETIME NOT NULL,
  `image_id` INT(10) UNSIGNED NOT NULL,
  `restaurants_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB;



ALTER TABLE `restaurant_reviews` 
ADD FOREIGN KEY (`restaurants_id`)
REFERENCES `restaurants` (`id`)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE `restaurant_reviews` 
ADD FOREIGN KEY (`image_id`)
REFERENCES `images` (`id`)
ON DELETE NO ACTION
ON UPDATE NO ACTION;


-- -----------------------------------------------------
-- Table `mydb`.`restaurant_comments`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `restaurant_comments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL DEFAULT 'Anonymous',
  `rating` TINYINT(1) NOT NULL DEFAULT 5,
  `posted` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment` TINYTEXT NOT NULL,
  `enabled` TINYINT(1) NULL DEFAULT 0,
  `restaurants_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB;


ALTER TABLE `restaurant_comments` 
ADD FOREIGN KEY (`restaurants_id`)
REFERENCES `restaurants` (`id`)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE `restaurants` 
CHANGE COLUMN `description` `description` TINYTEXT NULL DEFAULT NULL ;

ALTER TABLE `restaurant_menu` 
CHANGE COLUMN `enabled` `disabled` TINYINT(1) NOT NULL DEFAULT '0' ;

ALTER TABLE `restaurant_items` 
CHANGE COLUMN `description` `description` TINYTEXT NULL DEFAULT NULL ;


