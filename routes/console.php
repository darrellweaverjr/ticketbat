<?php

//reports
Artisan::command('ReportManifest', function () {
    $this->call('Report:manifest');
});
Artisan::command('ReportSales {days=1} {onlyadmin=1}', function ($days, $onlyadmin) {
    $this->call('Report:sales',['days'=>$days,'onlyadmin'=>$onlyadmin]);
});
Artisan::command('ReportSalesReceipt {days=1}', function ($days) {
    $this->call('Report:sales_receipt',['days'=>$days]);
});
Artisan::command('ReportFinancial {weeks=0}', function ($weeks) {
    $this->call('Report:financial',['weeks'=>$weeks]);
});
Artisan::command('ReportConsignment', function () {
    $this->call('Report:consignment');
});
//promotions
Artisan::command('ReportSales {days=7}', function ($days) {
    $this->call('Promo:announced',['days'=>$days]);
});
Artisan::command('ReportSales {days=1} {onlyadmin=1}', function ($days, $onlyadmin) {
    $this->call('Report:sales',['days'=>$days,'onlyadmin'=>$onlyadmin]);
});
//utilities
Artisan::command('ShoppingcartClean {days=10}', function ($days) {
    $this->call('Shoppingcart:clean',['days'=>$days]);
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
