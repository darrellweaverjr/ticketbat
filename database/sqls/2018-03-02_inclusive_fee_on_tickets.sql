#ALTER TABLE `tickets`
ADD COLUMN `inclusive_fee` TINYINT(3) NOT NULL DEFAULT 0 AFTER `avail_hours`;
