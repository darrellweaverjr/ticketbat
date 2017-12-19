ALTER TABLE `devticke_lvtn`.`purchases` 
CHANGE COLUMN `status` `status` ENUM('Active', 'User Canceled', 'Show Canceled', 'Fake', 'Chargeback', 'Void', 'Pending Refund', 'Pending Refund: Show Cancelled', 'Pending Refund: User Cancelled') NOT NULL DEFAULT 'Active' ;
