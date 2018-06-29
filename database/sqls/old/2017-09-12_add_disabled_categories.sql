ALTER TABLE `categories` 
ADD COLUMN `disabled` BIT(1) NOT NULL DEFAULT 0 AFTER `id_parent`;
