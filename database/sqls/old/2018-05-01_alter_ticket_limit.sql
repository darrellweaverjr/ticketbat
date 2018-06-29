#default null is unlimited
ALTER TABLE `tickets` 
CHANGE COLUMN `max_tickets` `max_tickets` INT(10) UNSIGNED NULL DEFAULT NULL ;

#set to null to all unlimited tickets
update tickets t set t.max_tickets = null where t.max_tickets < 1 and t.id > 0;
