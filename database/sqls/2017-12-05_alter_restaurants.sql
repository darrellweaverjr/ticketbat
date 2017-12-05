CREATE TABLE `restaurant_reservations` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `schedule` DATETIME NOT NULL,
  `people` INT(2) NOT NULL DEFAULT 1,
  `first_name` VARCHAR(15) NOT NULL,
  `last_name` VARCHAR(25) NOT NULL,
  `phone` VARCHAR(10) NULL DEFAULT NULL,
  `email` VARCHAR(50) NULL DEFAULT NULL,
  `occasion` ENUM('Regular', 'Birthday', 'Anniversary', 'Date', 'Business', 'Celebration') NOT NULL DEFAULT 'Regular',
  `special_request` TINYTEXT NULL DEFAULT NULL,
  `newsletter` TINYINT(1) NOT NULL DEFAULT 0,
  `status` ENUM('Requested', 'Checked', 'Cancelled', 'Denied') NULL DEFAULT 'Requested',
  `restaurants_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB;

ALTER TABLE `restaurant_reservations` 
ADD FOREIGN KEY (`restaurants_id`)
REFERENCES `restaurants` (`id`)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE `restaurant_reservations` 
ADD COLUMN `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `restaurants_id`;

ALTER TABLE `restaurant_reviews` 
ADD COLUMN `notes` VARCHAR(45) NULL DEFAULT NULL AFTER `link`;

CREATE TABLE `restaurant_media` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `image_id` VARCHAR(45) NULL,
  `name` VARCHAR(45) NULL,
  PRIMARY KEY (`id`));

ALTER TABLE .`restaurant_reviews` 
DROP FOREIGN KEY `restaurant_reviews_ibfk_2`;
ALTER TABLE `restaurant_reviews` 
CHANGE COLUMN `image_id` `restaurant_media_id` INT(11) UNSIGNED NOT NULL ,
DROP INDEX `image_id` ;

ALTER TABLE `restaurant_reviews` 
ADD INDEX `restaurant_media_id` (`restaurant_media_id` ASC);

ALTER TABLE `restaurant_reviews` 
ADD FOREIGN KEY (`restaurant_media_id`)
REFERENCES `restaurant_media` (`id`)
ON DELETE NO ACTION
ON UPDATE NO ACTION;




ALTER TABLE `restaurant_awards` 
CHANGE COLUMN `awarded` `awarded` INT(11) NOT NULL ;