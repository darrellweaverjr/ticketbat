#create field seller into purchases to determinate origin of transaction
ALTER TABLE `purchases`
ADD COLUMN `seller` ENUM('Web', 'App', 'POS') NOT NULL DEFAULT 'Web' AFTER `note`;

#change field seller to app or pos system according to purchases
update purchases p join users u on p.user_id=u.id set p.seller = 'POS' where u.user_type_id = 7 OR u.user_type_id = 1;
update purchases p set p.seller = 'App' where p.session_id LIKE 'app_%';
