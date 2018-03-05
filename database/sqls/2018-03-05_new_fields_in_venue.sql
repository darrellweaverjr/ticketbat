ALTER TABLE `venues` 
ADD COLUMN `disable_cash_breakdown` TINYINT(1) NULL DEFAULT 0 AFTER `default_fixed_commission`,
ADD COLUMN `after_purchase_link` TINYINT(1) NULL DEFAULT 0 AFTER `disable_cash_breakdown`;
