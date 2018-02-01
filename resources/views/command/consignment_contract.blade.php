<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>TicketBat Admin - Consignment Contract</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
        <style>
            body {
                margin: auto;
                width: 85%;
                padding: 10px;
                font-size:12px;
            }
            table {
                border-collapse: collapse;
                border-spacing: 0;
            }
            td, th {
                padding: 0;
                text-align: left;
            }

            .table {
                margin-bottom: 20px;
                max-width: 100%;
                width: 100%;
            }
            .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
                border-top: 1px solid #ddd;
                line-height: 1.42857;
                padding: 8px;
                vertical-align: top;
            }
            .table > thead > tr > th {
                border-bottom: 2px solid #ddd;
                vertical-align: bottom;
            }
            .table > caption + thead > tr:first-child > th, .table > colgroup + thead > tr:first-child > th, .table > thead:first-child > tr:first-child > th, .table > caption + thead > tr:first-child > td, .table > colgroup + thead > tr:first-child > td, .table > thead:first-child > tr:first-child > td {
                border-top: 0 none;
            }
            .table > tbody + tbody {
                border-top: 2px solid #ddd;
            }
            .table .table {
                background-color: #fff;
            }
            .table-condensed > thead > tr > th, .table-condensed > tbody > tr > th, .table-condensed > tfoot > tr > th, .table-condensed > thead > tr > td, .table-condensed > tbody > tr > td, .table-condensed > tfoot > tr > td {
                padding: 5px;
            }
            .table-bordered {
                border: 1px solid #ddd;
            }
            .table-bordered > thead > tr > th, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > tbody > tr > td, .table-bordered > tfoot > tr > td {
                border: 1px solid #ddd;
            }
            .table-bordered > thead > tr > th, .table-bordered > thead > tr > td {
                border-bottom-width: 2px;
            }
            .table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
                background-color: #EEEEEE;
            }
            .table-hover > tbody > tr:hover > td, .table-hover > tbody > tr:hover > th {
                background-color: #f5f5f5;
            }
            .table > thead > tr > td.active, .table > tbody > tr > td.active, .table > tfoot > tr > td.active, .table > thead > tr > th.active, .table > tbody > tr > th.active, .table > tfoot > tr > th.active, .table > thead > tr.active > td, .table > tbody > tr.active > td, .table > tfoot > tr.active > td, .table > thead > tr.active > th, .table > tbody > tr.active > th, .table > tfoot > tr.active > th {
                background-color: #f5f5f5;
            }
            .table-hover > tbody > tr > td.active:hover, .table-hover > tbody > tr > th.active:hover, .table-hover > tbody > tr.active:hover > td, .table-hover > tbody > tr:hover > .active, .table-hover > tbody > tr.active:hover > th {
                background-color: #e8e8e8;
            }
            .table > thead > tr > td.success, .table > tbody > tr > td.success, .table > tfoot > tr > td.success, .table > thead > tr > th.success, .table > tbody > tr > th.success, .table > tfoot > tr > th.success, .table > thead > tr.success > td, .table > tbody > tr.success > td, .table > tfoot > tr.success > td, .table > thead > tr.success > th, .table > tbody > tr.success > th, .table > tfoot > tr.success > th {
                background-color: #dff0d8;
            }
            .table-hover > tbody > tr > td.success:hover, .table-hover > tbody > tr > th.success:hover, .table-hover > tbody > tr.success:hover > td, .table-hover > tbody > tr:hover > .success, .table-hover > tbody > tr.success:hover > th {
                background-color: #d0e9c6;
            }
            .table > thead > tr > td.info, .table > tbody > tr > td.info, .table > tfoot > tr > td.info, .table > thead > tr > th.info, .table > tbody > tr > th.info, .table > tfoot > tr > th.info, .table > thead > tr.info > td, .table > tbody > tr.info > td, .table > tfoot > tr.info > td, .table > thead > tr.info > th, .table > tbody > tr.info > th, .table > tfoot > tr.info > th {
                background-color: #d9edf7;
            }
            .table-hover > tbody > tr > td.info:hover, .table-hover > tbody > tr > th.info:hover, .table-hover > tbody > tr.info:hover > td, .table-hover > tbody > tr:hover > .info, .table-hover > tbody > tr.info:hover > th {
                background-color: #c4e3f3;
            }
            .table > thead > tr > td.warning, .table > tbody > tr > td.warning, .table > tfoot > tr > td.warning, .table > thead > tr > th.warning, .table > tbody > tr > th.warning, .table > tfoot > tr > th.warning, .table > thead > tr.warning > td, .table > tbody > tr.warning > td, .table > tfoot > tr.warning > td, .table > thead > tr.warning > th, .table > tbody > tr.warning > th, .table > tfoot > tr.warning > th {
                background-color: #fcf8e3;
            }
            .table-hover > tbody > tr > td.warning:hover, .table-hover > tbody > tr > th.warning:hover, .table-hover > tbody > tr.warning:hover > td, .table-hover > tbody > tr:hover > .warning, .table-hover > tbody > tr.warning:hover > th {
                background-color: #faf2cc;
            }
            .table > thead > tr > td.danger, .table > tbody > tr > td.danger, .table > tfoot > tr > td.danger, .table > thead > tr > th.danger, .table > tbody > tr > th.danger, .table > tfoot > tr > th.danger, .table > thead > tr.danger > td, .table > tbody > tr.danger > td, .table > tfoot > tr.danger > td, .table > thead > tr.danger > th, .table > tbody > tr.danger > th, .table > tfoot > tr.danger > th {
                background-color: #f2dede;
            }
            .table-hover > tbody > tr > td.danger:hover, .table-hover > tbody > tr > th.danger:hover, .table-hover > tbody > tr.danger:hover > td, .table-hover > tbody > tr:hover > .danger, .table-hover > tbody > tr.danger:hover > th {
                background-color: #ebcccc;
            }
        </style>
    </head>
    <body>
        @if($format=='pdf')
        <div style="text-align:center">
            <img src="{{config('app.theme')}}img/logo-header.jpg" alt="TicketBat">
            <h1>CONSIGNMENT TICKET AGREEMENT</h1>    
        </div><br><br>
        <table class="table table-condensed table-responsive">
            <body>
                <tr>
                    <td style="text-align:right;width:25%;font-weight:bold">EVENT NAME: </td>
                    <td style="text-align:left;"> {{$consignment->show_name}}</td>
                </tr>
                <tr>
                    <td style="text-align:right;width:25%;font-weight:bold">EVENT DATE: </td>
                    <td style="text-align:left;"> {{date('m/d/Y @ h:i a',strtotime($consignment->show_time))}}</td>
                </tr>
                <tr>
                    <td style="text-align:right;width:25%;font-weight:bold">CONSIGNMENT DATE: </td>
                    <td style="text-align:left;"> {{date('m/d/Y',strtotime($consignment->created))}}</td>
                </tr>
            </body>
        </table><br><br>
        <table class="table table-striped table-responsive table-bordered table-condensed">
            <thead>
                <tr>
                    <th style="text-align:center;">TYPE</th>
                    <th style="text-align:right;">FACE VALUE</th>
                    <th style="text-align:right;">AMT DUE</th>
                    <th style="text-align:center;">QUANTITY</th>
                    <th style="text-align:right;">SERVICE CHARGE</th>  
                    <th style="text-align:right;">TOTAL PRICE</th>  
                    <th style="text-align:right;">TOTAL DUE</th>  
                </tr>
            </thead>
            <tbody>
                @foreach($consignment->types as $t)
                <tr>
                    <td style="text-align:center;">{{$t->ticket_type}}</td>
                    <td style="text-align:right;">$ {{number_format($t->retail_price + $t->processing_fee,2)}}</td>
                    <td style="text-align:right;">$ {{number_format($t->collect_price,2)}}</td>
                    <td style="text-align:center;">{{number_format($t->qty)}}</td>
                    <td style="text-align:right;">$ 0.00</td>
                    <td style="text-align:right;">$ {{number_format(($t->retail_price + $t->processing_fee) * $t->qty,2)}}</td>
                    <td style="text-align:right;">$ {{number_format($t->collect_price * $t->qty,2)}}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="font-weight:bold">
                    <td colspan="3">GRAND TOTAL</td>
                    <td style="text-align:center;">{{number_format($consignment->qty)}}</td>
                    <td style="text-align:right;">$ 0.00</td>
                    <td style="text-align:right;">$ {{number_format($consignment->total,2)}}</td>
                    <td style="text-align:right;">$ {{number_format($consignment->due,2)}}</td>
                </tr>
            </tfoot>
        </table>
        <p style="text-align:right;">
            <b>TICKETS PROCESSED BY:</b><i>&nbsp;&nbsp;&nbsp;&nbsp;{{$consignment->creator}}</i>
        </p><br><br>
        <p>
            By signing below, I am taking full responsability for the tickets listed above at their retail value.
            I understand that any lost, stolen or misplaced tickets are non-refundable and cannot be reprinted.
            Any exchanges or returns must be completed by close.
        </p><br>
        <p>
            <b>SIGNED:________________________________________________________  DATE: ______________________<br><br>PRINTED NAME:</b><i>&nbsp;&nbsp;&nbsp;&nbsp;{{$consignment->seller_name}}</i>
        </p>  
        @else
        @php echo $consignment @endphp
        @endif     
    </body>
</html>