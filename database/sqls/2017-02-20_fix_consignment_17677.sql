#adjust purchase made by Pulsar(consignment), to match the consignment report
update purchases set retail_price=195.00, processing_fee=0.00, price_paid=195.00 where id = 17677;
