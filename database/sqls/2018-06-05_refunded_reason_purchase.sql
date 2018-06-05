ALTER TABLE `purchases` 
ADD COLUMN `refunded_reason` TEXT NULL DEFAULT NULL AFTER `refunded`;