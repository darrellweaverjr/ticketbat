#erase unneeded field on venues
ALTER TABLE `venues` DROP COLUMN `after_purchase_link`;

#add field to add optional fees when purchasing using POS
ALTER TABLE `shows`
ADD COLUMN `pos_optional_fees` VARCHAR(25) NULL DEFAULT NULL AFTER `regular_price`;
