#ALTER TABLE `shows`
ADD COLUMN `after_purchase_note` TINYTEXT NULL DEFAULT NULL AFTER `ticket_limit`;
