#add new fields into refunds to fill out table reports
ALTER TABLE `ticketba_lvtn`.`transaction_refunds` 
ADD COLUMN `quantity` INT(10) NOT NULL DEFAULT null AFTER `created`,
ADD COLUMN `retail_price` DECIMAL(10,2) NOT NULL DEFAULT null AFTER `quantity`,
ADD COLUMN `processing_fee` DECIMAL(10,2) NOT NULL DEFAULT null AFTER `retail_price`,
ADD COLUMN `savings` DECIMAL(10,2) NOT NULL DEFAULT null AFTER `processing_fee`,
ADD COLUMN `commission_percent` DECIMAL(10,2) NOT NULL DEFAULT null AFTER `savings`,
ADD COLUMN `printed_fee` DECIMAL(10,2) NOT NULL DEFAULT null AFTER `commission_percent`,
ADD COLUMN `sales_taxes` DECIMAL(10,2) NOT NULL DEFAULT null AFTER `printed_fee`;

#update new fields with same values of the reports, old ones
update purchases p 
inner join transaction_refunds r on r.purchase_id = p.id and r.result='Approved'
set r.quantity = p.quantity,
r.retail_price = p.retail_price,
r.processing_fee = p.processing_fee,
r.savings = p.savings,
r.commission_percent = p.commission_percent,
r.printed_fee = p.printed_fee,
r.sales_taxes = p.sales_taxes
where (p.status='Refunded' or p.status='Chargeback')
and p.price_paid=r.amount;
