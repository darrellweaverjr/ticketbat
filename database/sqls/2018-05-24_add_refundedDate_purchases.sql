#disable function to changed updated field 
ALTER TABLE `ticketba_lvtn`.`purchases` 
CHANGE COLUMN `updated` `updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ;

#disable function to changed updated field 
ALTER TABLE `ticketba_lvtn`.`transaction_refunds` 
CHANGE COLUMN `created` `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ;

#create refunded date on table purchases
ALTER TABLE `purchases` 
ADD COLUMN `refunded` DATETIME NULL DEFAULT NULL AFTER `inclusive_fee`;

#add field payment type for refunds
ALTER TABLE `transaction_refunds` 
ADD COLUMN `payment_type` VARCHAR(20) NOT NULL DEFAULT 'Credit' AFTER `error`;

#update refunded field on table purchases
update purchases p 
left join transaction_refunds r on r.purchase_id = p.id and r.result='Approved'
set p.refunded = coalesce(r.created,p.updated)
where p.status='Refunded' or p.status='Chargeback';

#create new entry for transaction refunded for each refund
INSERT INTO transaction_refunds(`purchase_id`,`user_id`,`amount`,`description`,`result`,`error`,`payment_type`,`created`)
SELECT p.id,15757,p.price_paid,concat('Import entry - previous ',p.status),'Approved','Approved',p.payment_type,p.refunded
        FROM purchases p
        WHERE NOT EXISTS 
           (SELECT * FROM transaction_refunds r WHERE r.purchase_id=p.id AND r.result='Approved')
           AND (p.status='Refunded' or p.status='Chargeback');

#enable function to changed updated field 
ALTER TABLE `ticketba_lvtn`.`purchases` 
CHANGE COLUMN `updated` `updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ;