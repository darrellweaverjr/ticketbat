#add purchases taxes for sales
ALTER TABLE `purchases` 
CHANGE COLUMN `retail_price` `retail_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00 ,
CHANGE COLUMN `processing_fee` `processing_fee` DECIMAL(10,2) NOT NULL DEFAULT 0.00 ,
CHANGE COLUMN `savings` `savings` DECIMAL(10,2) NOT NULL DEFAULT 0.00 ,
CHANGE COLUMN `commission_percent` `commission_percent` DECIMAL(10,2) NOT NULL DEFAULT 0.00 ,
CHANGE COLUMN `price_paid` `price_paid` DECIMAL(10,2) NOT NULL DEFAULT 0.00 ,
ADD COLUMN `cc_fees` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `price_paid`,
ADD COLUMN `sales_taxes` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `cc_fees`;

#add shopping cart taxes for sales
ALTER TABLE `shoppingcart` 
ADD COLUMN `sales_taxes` DECIMAL(10,2) NULL DEFAULT NULL AFTER `total_cost`;

#add default values for venues
ALTER TABLE `ticketba_lvtn`.`venues` 
ADD COLUMN `default_processing_fee_pos` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `default_fixed_commission`,
ADD COLUMN `default_percent_pfee_pos` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `default_processing_fee_pos`,
ADD COLUMN `default_percent_commission_pos` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `default_percent_pfee_pos`,
ADD COLUMN `default_fixed_commission_pos` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `default_percent_commission_pos`,
ADD COLUMN `default_sales_taxes_percent` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `default_fixed_commission_pos`;