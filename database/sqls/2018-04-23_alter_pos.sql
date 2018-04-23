ALTER TABLE `shows` 
DROP COLUMN `pos_fee`;

ALTER TABLE `venues` 
DROP COLUMN `pos_fee`;

ALTER TABLE `ticketba_lvtn`.`venues` 
DROP COLUMN `disable_cash_breakdown`;

ALTER TABLE `venues` 
ADD COLUMN `cutoff_hours_start` TINYINT(2) NULL DEFAULT 5 AFTER `header_url`,
ADD COLUMN `cutoff_hours_end` TINYINT(2) NULL DEFAULT 24 AFTER `cutoff_hours_start`;