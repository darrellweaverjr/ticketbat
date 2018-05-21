ALTER TABLE `purchases` 
ADD COLUMN `printed_fee` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `commission_percent`;
