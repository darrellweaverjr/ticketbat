update purchases p 
inner join transactions t on p.transaction_id = t.id
set p.cc_fees = round(0.03*t.amount,2)
where t.trans_result='Approved' and p.payment_type='Credit' and date(p.created)>='2018-01-01';