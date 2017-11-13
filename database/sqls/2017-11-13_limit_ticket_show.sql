ALTER TABLE `shows` 
ADD COLUMN `ticket_limit` TINYINT(3) NULL DEFAULT NULL AFTER `ua_conversion_code`;