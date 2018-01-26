ALTER TABLE `tickets` 
ADD COLUMN `avail_hours` INT(11) NULL AFTER `updated`;

ALTER TABLE `ticket_number` 
ADD COLUMN `updated` DATETIME NULL DEFAULT NULL AFTER `comment`;

