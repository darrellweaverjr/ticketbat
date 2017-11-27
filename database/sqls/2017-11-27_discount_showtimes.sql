CREATE TABLE `discount_show_times` (
  `discount_id` INT(10) UNSIGNED NOT NULL,
  `show_time_id` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`discount_id`, `show_time_id`))
ENGINE = InnoDB;
