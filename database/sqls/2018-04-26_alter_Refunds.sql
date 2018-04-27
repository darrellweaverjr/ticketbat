#add new status into DB
ALTER TABLE `purchases` 
CHANGE COLUMN `status` `status` ENUM('Active', 'User Canceled', 'Show Canceled', 'Fake', 'Chargeback' , 'Refunded', 'Void', 'Pending Refund', 'Pending Refund: Show Cancelled', 'Pending Refund: User Cancelled') NOT NULL DEFAULT 'Active' ;

#update previous status to new one
update purchases p set p.status='Refunded' where p.status = 'Chargeback';

#remove all status
ALTER TABLE `purchases` 
CHANGE COLUMN `status` `status` ENUM('Active', 'User Canceled', 'Show Canceled', 'Fake', 'Refunded', 'Void', 'Pending Refund', 'Pending Refund: Show Cancelled', 'Pending Refund: User Cancelled') NOT NULL DEFAULT 'Active' ;
