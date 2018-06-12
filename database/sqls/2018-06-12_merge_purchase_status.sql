#update previous Fake to Void
update purchases p 
set p.status = 'Void'
where p.status = 'Fake';

#remove Fake status
ALTER TABLE `ticketba_lvtn`.`purchases` 
CHANGE COLUMN `status` `status` ENUM('Active', 'Pending Refund', 'Pending Refund: Show Cancelled', 'Pending Refund: User Cancelled', 'Refunded', 'Chargeback', 'Void') NOT NULL DEFAULT 'Active' ;
