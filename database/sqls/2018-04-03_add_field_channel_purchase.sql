#create field seller into purchases to determinate origin of transaction
ALTER TABLE `purchases`
ADD COLUMN `channel` ENUM('Web', 'App', 'POS','Consignment') NOT NULL DEFAULT 'Web' AFTER `note`;

#change field seller to app or pos system according to purchases
update purchases p set p.channel = 'Web' where p.id>0;
update purchases p join users u on p.user_id=u.id set p.channel = 'POS' where u.user_type_id = 7 OR u.user_type_id = 1;
update purchases p set p.channel = 'App' where p.session_id LIKE 'app_%';
update purchases p set p.channel = 'Consignment' where p.ticket_type = 'Consignment';
