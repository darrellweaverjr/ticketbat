ALTER TABLE `ticketba_lvtn`.`user_seller` 
CHANGE COLUMN `open_drawer` `open_drawer` INT(10) NOT NULL DEFAULT '0' ;

ALTER TABLE `ticketba_lvtn`.`seller_tally` 
DROP PRIMARY KEY,
ADD PRIMARY KEY (`id`);
