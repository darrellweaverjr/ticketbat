ALTER TABLE `purchases`
CHANGE COLUMN `payment_type` `payment_type` ENUM('None', 'Credit', 'Cash', 'Consignment', 'Free event') NOT NULL DEFAULT 'Credit' ;
