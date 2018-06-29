ALTER TABLE `purchases` 
CHANGE COLUMN `status` `status` ENUM('Active', 'User Canceled', 'Show Canceled', 'Fake', 'Refunded', 'Chargeback', 'Void', 'Pending Refund', 'Pending Refund: Show Cancelled', 'Pending Refund: User Cancelled') NOT NULL DEFAULT 'Active' ;
