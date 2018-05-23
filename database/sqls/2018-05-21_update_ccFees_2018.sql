#all purchases made this year
update purchases p 
inner join transactions t on p.transaction_id = t.id
set p.cc_fees = round(0.03*t.amount,2)
where t.trans_result='Approved' and p.payment_type='Credit' and date(p.created)>='2018-01-01';


#with the shows in this year
update purchases p 
inner join transactions t on p.transaction_id = t.id 
inner join show_times st on st.id = p.show_time_id
set p.cc_fees = round(0.03*t.amount,2)
where t.trans_result='Approved' and p.payment_type='Credit' and date(st.show_time)>='2018-01-01';

update purchases p 
inner join transactions t on p.transaction_id = t.id 
inner join show_times st on st.id = p.show_time_id
set p.cc_fees = round(0.03*p.price_paid,2)
where t.trans_result='Approved' and p.payment_type='Credit' and date(st.show_time)>='2018-01-01';