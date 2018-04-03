#create field seller into purchases to determinate origin of transaction
ALTER TABLE `purchases`
ADD COLUMN `seller` VARCHAR(1) NOT NULL DEFAULT 'W' AFTER `note`;

#change field seller to app or pos system according to purchases
update purchases p join users u on p.user_id=u.id set p.seller = 'P' where u.user_type_id = 7;
update purchases p set p.seller = 'A' where p.session_id LIKE 'app_%';
