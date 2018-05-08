ALTER TABLE `purchases` 
ADD COLUMN `inclusive_fee` TINYINT(1) NOT NULL DEFAULT '0' AFTER `channel`;