#add free event as a new payment type
ALTER TABLE `purchases`
CHANGE COLUMN `payment_type` `payment_type` ENUM('None', 'Credit', 'PayPal', 'Check', 'Cash', 'Consignment', 'Free event') NOT NULL DEFAULT 'Credit' ;

#update purchases in the system that has real free event, free tickets and customer didnt pay nothing
update purchases p
inner join tickets t on p.ticket_id=t.id
set p.payment_type='Free event'
where p.payment_type<>'Consignment' and t.retail_price<0.01 and p.price_paid<0.01;
