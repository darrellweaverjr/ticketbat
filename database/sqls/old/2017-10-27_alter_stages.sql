ALTER TABLE `stages` 
ADD COLUMN `ticket_order` VARCHAR(200) NULL DEFAULT NULL AFTER `image_url`;
