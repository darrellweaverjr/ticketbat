#erase unneeded field on venues
ALTER TABLE `venues` DROP COLUMN `after_purchase_link`;

#add field to add optional fees when purchasing using POS
ALTER TABLE `shows`
ADD COLUMN `pos_fee` DECIMAL(4,2) NULL DEFAULT NULL AFTER `regular_price`;
ALTER TABLE `venues` 
ADD COLUMN `pos_fee` DECIMAL(4,2) NULL DEFAULT NULL AFTER `disable_cash_breakdown`;
