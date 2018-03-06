/*
this purchase was made in the old usaepay account, it was manually refund by shine@blackstarlv.com, this is just to update the status of the purchase in the system
*/
update purchases p set p.status='Chargeback' where p.id = 18125;