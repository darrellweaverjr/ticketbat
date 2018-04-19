@if($format == 'csv')
"#","ORDER ID","SHOW NAME","FIRST NAME","LAST NAME","TICKET TYPE","DISCOUNT CODE","QUANTITY","PRICE","PROCESSING FEE","DISCOUNT","TOTAL PAID","SHOW DATE/TIME"
@foreach ($data as $n => $p)
"{{$n+1}}","{{$p['id']}}","{{$p['show_name']}}","{{$p['first_name']}}","{{$p['last_name']}}","{{$p['ticket_type']}}","{{$p['code']}}","{{$p['qty']}}","$ {{$p['retail_price']}}","$ {{$p['processing_fee']}}","$ {{$p['savings']}}","$ {{$p['price_paid']}}","{{date('m/d/Y g:ia',strtotime($p['show_time']))}}"
@endforeach
@elseif($format == 'pdf')
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
        <!--<link href='https://fonts.googleapis.com/css?family=Oswald|Roboto|Raleway' rel='stylesheet' type='text/css'>-->
            <style>
                <!--@import url(https://fonts.googleapis.com/css?family=Roboto|Oswald:400,700,300|Raleway);-->
                body{
                    color:#000;
                    padding:0;
                    margin:0;
                    font-family: "Oswald", Arial, Serif;
                }
                .container{
                    width:600px;
                    overflow: hidden;
                    margin:auto;
                }
                .tb-body{
                    background-color:#ffffff;
                    margin-top:20px;
                }
                .tb-title{
                    font-family: "Oswald", Arial, Serif;
                    font-size:14px;
                    text-transform:uppercase;
                    padding: 10px;
                    text-align: center;
                }
                .tb-order-title{
                    font-family: "Oswald", Arial, Serif;
                    font-size:14px;
                    text-transform:uppercase;
                    padding: 10px 0;
                }
                .tb-order-subtitle{
                    font-family: "Oswald", Arial, Serif;
                    font-size:14px;
                    text-transform:uppercase;
                }
                .tb-order-txt{
                    font-family: "Raleway", Arial, serif;
                    font-size:14px;
                    text-transform:uppercase;
                    color:#357ebd;
                }
                .tb-customer-info-txt{
                    font-family: "Raleway", Arial, serif;
                    font-size: 14px;
                    color: #424242;
                }
                table.tb-show-details-table {
                    border: 1px solid #E3E3E3;
                    padding: 0 0 10px 0;
                    width: 590px;
                }
                .tb-customer-ticket-txt{
                    font-family: "Raleway", Arial, serif;
                    font-size: 12px;
                    color: #424242;
                }
                .tb-customer-ticket-name-txt{
                    text-transform:capitalize;
                    font-family: "Raleway", Arial, serif;
                    font-size: 12px;
                    color: #357ebd;
                }
                .tb-customer-txt{
                    font-family: "Oswald", Arial, serif;
                    font-size:14px;
                    text-transform:uppercase;
                    color:#357ebd;
                }
                .tb-customer-intro{
                    text-align:right;
                    float:left;
                    width:78%;
                }
                .tb-customer-intro-cust{
                    text-align:left;
                    float:left;
                }
                .tb-ticket-top-sect{
                    border-bottom:1px solid #E3E3E3;
                    border-top:1px solid #E3E3E3;
                    padding:10px;
                    overflow: auto;
                    margin:10px 0;
                }
                .tb-highlight-color{
                    color: #4A9B00;
                }
                .tb-highlight-color-blue{
                    color: #357ebd;
                }
                .tb-show-details-title{
                    background-color:#E3E3E3;
                    color:#000000;
                    padding:10px 0;
                    font-family: "Oswald", Arial, Serif;
                    font-size:14px;
                    text-transform: uppercase;
                    overflow: auto;
                }
                .tb-ticket-purchase-info {
                    font-family: "Roboto", arial, serif;
                    overflow: auto;
                }
                .tb-total-price {
                    font-family: "Oswald", arial, serif;
                    font-size: 12px;
                    line-height: 14px;
                }
                .bar-code-sect{
                    height:110px;
                }
                .tb-purchase-label{
                    color:#4A9B00;
                    font-family:"Oswald", Arial, Serif;
                }
                .tb-purchase-label-total{
                    color:#357ebd;
                    font-family:"Oswald", Arial, Serif;
                }
                .header{
                    width:100%;
                }
                .header img{
                    width:100%;
                }
                .show-date{
                    width:80px;
                    padding:5px;
                    word-wrap: break-word;
                }
                .show-details{
                    width: 270px;
                    padding:5px;
                }
                .show-price {
                    width: 115px;
                    padding: 5px;
                    word-wrap: break-word;
                }
            </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <img src="http://www.ticketbat.com/themes/img/logo-header-white.jpg" alt="Ticket Bat">
            </div>
            <div class="tb-title">
                Your Ticketbat purchase for <span class="tb-highlight-color">{{$purchase['show_name']}}</span> on <span class="tb-highlight-color">{{date('m/d/Y g:ia',strtotime($purchase['show_time']))}}</span> is confirmed. Have a great time!
            </div>
            @if($purchase['restrictions'] != 'None')
            <div class="tb-ticket-top-sect">
                <div class="tb-customer-intro-cust">
                    <span class="tb-order-subtitle">Restrictions:</span> <span class="tb-customer-txt">{{$purchase['restrictions']}}</span>
                </div>
            </div><br><br>
            @endif
            <div class="tb-ticket-top-sect">
                <div class="tb-customer-intro-cust">
                    <span class="tb-order-subtitle">Order ID:</span> <span class="tb-customer-txt">{{$purchase['id']}}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="tb-order-subtitle">Customer Name:</span> <span class="tb-customer-txt">{{$purchase['first_name']}}  {{$purchase['last_name']}}</span>
                </div>
            </div><br><br><br>
            <table class="tb-show-details-table" border="0" align="center" cellpadding="10" cellspacing="0">
                <tbody>
                    <tr class="tb-show-details-title">
                        <td><div class="show-date">Show Date</div></td>
                        <td><div class="show-details">SHOW DETAILS</div></td>
                        <td><div class="show-price">Price Per Item</div></td>
                    </tr>
                    <tr class="tb-ticket-purchase-info">
                        <td valign="top">
                            <div  class="show-date">
                                @if ($purchase['time_alternative']) {{$purchase['time_alternative']}} @else {{date('m/d/Y g:ia',strtotime($purchase['show_time']))}} @endif
                            </div>
                        </td>
                        <td valign="top">
                            <div class="show-details">
                                <span class="tb-highlight-color">Package: {{$purchase['title']}}</span><br>
                                <span class="tb-highlight-color-blue">{{$purchase['show_name']}}</span><br>
                                {{$purchase['qty']}} - {{$purchase['ticket_type_type']}}
                            </div>
                        </td>
                        <td valign="top">
                            <div  class="show-price">$ {{money_format('%(#10n',$purchase['price_each'])}}</div>
                        </td>
                        </tr>
                </tbody>
            </table>
            <div class="bar-code-sect">
                <table width="90%" border="0" align="center" cellpadding="2" cellspacing="2">
                    <tbody>
                        <tr>
                            <td width="50%">
                            </td>
                            <td width="50%">
                                <table class="tb-total-price" width="100%" border="0" align="right" cellpadding="3" cellspacing="2">
                                    <tbody>
                                        <tr>
                                            <td class="tb-purchase-label-total" valign="top" align="right" style="width:75%">Payment Method:</td>
                                            <td align="left" valign="top">{{$purchase['payment_type']}}</td>
                                        </tr>
                                        <tr>
                                            <td class="tb-purchase-label-total" valign="top" align="right" >Quantity:</td>
                                            <td align="left" valign="top">{{$purchase['qty']}}</td>
                                        </tr>
                                        <tr>
                                            <td class="tb-purchase-label"  align="right" valign="top" >Total:</td>
                                            <td align="left" valign="top">$ {{money_format('%(#10n',$purchase['price_paid'])}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <table class="tb-show-details-table" width="98%" border="0" align="center" cellpadding="10" cellspacing="0">
                <tbody>
                    <tr>
                        <td>
                            <span class="tb-customer-info-txt">Dear </span>
                            <span class="tb-customer-ticket-name-txt">{{$purchase['first_name']}},</span><blockquote>
                                <p><span class="tb-customer-ticket-txt">{{$purchase['ticket_info']}}</span></p>
                            </blockquote>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </body>
</html>
@elseif($format == 'printer')
        <div style="text-align:center">
            <img src="http://www.ticketbat.com/themes/img/logo-header-white.jpg" width="280" alt="TicketBat Receipt">
        </div><hr>
        @if(!empty($printer['restrictions']))
        <b>Restrictions:</b> {{implode(', ',$printer['restrictions'])}}<br>
        @endif
        <b>Order ID:</b> {{$printer['order_id']}}<hr>
        <table border="0" align="center"  cellspacing="0">
            <thead>
                <tr>
                    <th width="70%">ITEMS</th>
                    <th width="30%">PRICE</th>
                </tr>
            </thead>
            <tbody>
                @foreach($printer['items'] as $p)
                <tr>
                    <td valign="top">
                        {{$p->quantity}} {{$p->ticket_type_type}} ticket(s)<br>
                        {{$p->show_name}} <br> 
                        @if($p->time_alternative) {{$p->time_alternative}} @else {{date('m/d/Y g:ia',strtotime($p->show_time))}} @endif     
                        @if(!empty($p->title)) <br>Package: {{$p->title}} @endif
                    </td>
                    <td valign="bottom" style="text-align:right">
                        ${{money_format('%(#10n',$p->price_paid)}}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="font-weight:bold;">
                <tr>
                    <td valign="bottom">QTY</td>
                    <td valign="bottom" style="text-align:right">{{$printer['qty']}}</td>
                </tr>
                <tr>
                    <td valign="bottom">TOTALS</td>
                    <td valign="bottom" style="text-align:right">${{money_format('%(#10n',$printer['total'])}}</td>
                </tr>
                <tr>
                    <td valign="bottom">PAID</td>
                    <td valign="bottom" style="text-align:right">${{money_format('%(#10n',$printer['total'])}}</td>
                </tr>
                <tr>
                    <td valign="bottom">PENDING</td>
                    <td valign="bottom" style="text-align:right">$0.00</td>
                </tr>
            </tfoot>
        </table><hr>
        <p>{{implode('<br>',$printer['info'])}}</p>    
@endif
