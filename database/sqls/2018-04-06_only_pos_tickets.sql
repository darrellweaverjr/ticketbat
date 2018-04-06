#add field only POS sytem tickets and change size of other boolean fields
ALTER TABLE `tickets`
CHANGE COLUMN `is_default` `is_default` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
CHANGE COLUMN `is_active` `is_active` TINYINT(1) NOT NULL DEFAULT '1' ,
CHANGE COLUMN `inclusive_fee` `inclusive_fee` TINYINT(1) NOT NULL DEFAULT '0' ,
ADD COLUMN `only_pos` TINYINT(1) NOT NULL DEFAULT 0 AFTER `inclusive_fee`;
