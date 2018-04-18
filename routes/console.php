<?php

//REPORTS
Artisan::command('ReportManifest {date=0}', function ($date) {
    $this->call('Report:manifest',['date'=>$date]);
});
Artisan::command('ReportSales {days=1} {onlyadmin=1}', function ($days, $onlyadmin) {
    $this->call('Report:sales',['days'=>$days,'onlyadmin'=>$onlyadmin]);
});
Artisan::command('ReportSalesReceipt {days=1}', function ($days) {
    $this->call('Report:sales_receipt',['days'=>$days]);
});
Artisan::command('ReportConsignment', function () {
    $this->call('Report:consignment');
});
//PROMOTIONS
Artisan::command('PromoAnnounced {days=7}', function ($days) {
    $this->call('Promo:announced',['days'=>$days]);
});
//UTILITIES
Artisan::command('ShoppingcartClean {days=10}', function ($days) {
    $this->call('Shoppingcart:clean',['days'=>$days]);
});
Artisan::command('BrokenImageClean', function () {
    $this->call('BrokenImage:clean');
});
Artisan::command('ShoppingcartRecover {hours=4}', function ($hours) {
    $this->call('Shoppingcart:recover',['hours'=>$hours]);
});
Artisan::command('ContractUpdateTickets', function () {
    $this->call('Contract:update_tickets');
});
Artisan::command('CheckConsignments', function () {
    $this->call('Consignments:check');
});
