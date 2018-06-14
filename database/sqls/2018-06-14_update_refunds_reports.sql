#copy reason to be refunded to table refunded
ALTER TABLE `ticketba_lvtn`.`transaction_refunds` 
ADD COLUMN `refunded_reason` TEXT NULL DEFAULT NULL AFTER `sales_taxes`;

#check refunded dates from purchases table to refunded one, and reason to refunded too.
update transaction_refunds r 
inner join purchases p on p.id=r.purchase_id and r.result='Approved'
set r.created=p.refunded, r.refunded_reason=p.refunded_reason
where p.status = 'Refunded' or p.status = 'Chargeback';

#remove updated field
ALTER TABLE `ticketba_lvtn`.`purchases` 
DROP COLUMN `refunded`;

