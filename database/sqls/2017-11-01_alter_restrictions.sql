ALTER TABLE `shows` 
CHANGE COLUMN `restrictions` `restrictions` ENUM('None', 'Over 5', 'Over 16', 'Over 18', 'Over 21') NOT NULL ;

ALTER TABLE `venues` 
CHANGE COLUMN `restrictions` `restrictions` ENUM('None', 'Over 5', 'Over 16', 'Over 18', 'Over 21') NOT NULL ;
