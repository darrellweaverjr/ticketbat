ALTER TABLE `ticketba_lvtn`.`venues` 
ADD COLUMN `let` TINYINT(1) NOT NULL DEFAULT 0 AFTER `cutoff_hours_end`;
