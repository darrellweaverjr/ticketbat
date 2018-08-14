ALTER TABLE `ticketba_lvtn`.`contacts` 
ADD COLUMN `status` ENUM('Pending', 'Attended', 'No solution') NOT NULL DEFAULT 'Pending' AFTER `created`;

update contacts set contacts.status = 'Attended';