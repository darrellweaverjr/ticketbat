CREATE TABLE `stage_image_ticket_type` (
  `stage_id` INT(10) UNSIGNED NOT NULL,
  `image_id` INT(10) UNSIGNED NOT NULL,
  `ticket_type` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`stage_id`, `image_id`, `ticket_type`))
ENGINE = InnoDB;
