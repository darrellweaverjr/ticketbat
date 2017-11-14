ALTER TABLE `consignments` 
ADD COLUMN `signed` DATETIME NULL DEFAULT NULL AFTER `agreement`;
