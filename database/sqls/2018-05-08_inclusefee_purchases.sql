ALTER TABLE `purchases` 
ADD COLUMN `inclusive_fee` TINYINT(1) NOT NULL DEFAULT '0' AFTER `channel`;

update purchases p inner join tickets t on p.ticket_id=t.id
set p.inclusive_fee = 1 where t.inclusive_fee>0 AND p.id>0;