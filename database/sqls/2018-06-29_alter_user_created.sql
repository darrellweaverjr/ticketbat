ALTER TABLE `ticketba_lvtn`.`users` 
ADD COLUMN `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `fixed_processing_fee`;


update users u
left join customers c on c.email=u.email
set u.created = coalesce(c.created,u.updated)
where u.id>0;