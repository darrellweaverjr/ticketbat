@if($format == 'csv')
"#","NAME","TICKET TYPE","SHOW TIME","QTY","PURCHASE COUNT","RETAIL PRICE","PROCESSING FEE","SAVINGS","TOTAL PAID","COMMISSION PERCENT", "COMMISSION", "DUE TO SHOW","REFERRER"
@foreach ($purchases as $n => $p)
"{{$n+1}}","{{$p->s_name}}","{{$p->ticket_type}}","{{$p->shows_time}}","{{$p->qty}}","{{$p->purchase_count}}","$ {{$p->retail_price}}","$ {{$p->processing_fee}}","$ {{$p->savings}}","$ {{$p->total_paid}}","{{$p->commission_percent}} %","$ {{$p->commission}}","$ {{$p->due_to_show}}","{{$p->url}}"
@endforeach
@else
<!DOCTYPE html>
<html>
    <head>
        <style>
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
            table col[class*="col-"] {
                display: table-column;
                float: none;
                position: static;
            }
            table td[class*="col-"], table th[class*="col-"] {
                display: table-cell;
                float: none;
                position: static;
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
        @if($format == 'referrer')
            <h1>TicketBat.com</h1>
            <p>Sales For {{$date_report}}</p>
            <table class="table table-striped table-responsive">
                <thead>
                <tr>
                    <th>REFERRER</th>
                    <th>NAME</th>
                    <th>TICKET TYPE</th>
                    <th>QTY</th>
                    <th>PURCHASE COUNT</th>
                </tr>
                </thead>
                <tbody>
                @foreach($purchases as $p)
                    <tr>
                        <td>{{$p->referral_url}}</td>
                        <td>{{$p->s_name}}</td>
                        <td>{{$p->ticket_type}}</td>
                        <td>{{$p->qty}}</td>
                        <td>{{$p->purchase_count}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            @foreach($data as $d)
            <div style="page-break-after:always;">
              <h1>TicketBat.com</h1>
              @if($d['name'] == 'Totals')
              <p><h3>Total Sales</h3></p>
              @else
              <p><h3>Sales For @if($d['type']=='venue')Venue @else Show @endif : <i>{{$d['name']}}</i></h3></p>
              @endif
              <p><h3>Date: <i>{{$d['date']}}</i></h3></p>
              <table class="table table-striped table-responsive">
                  <thead>
                  <tr>
                      <th @if($d['name'] == 'Totals') colspan="2" @endif align='center'>@if($d['type']=='venue' && $d['name'] !='Totals')SHOW @else VENUE @endif</th>
                      @if($d['name'] != 'Totals')<th align='center'>TICKET TYPE</th>@endif
                      <th align='center'>QTY</th>
                      <!--<th align='center'>PURCHASE COUNT</th>-->
                      <th style='text-align:right'>GROSS REVENUE</th>
                      <th style='text-align:right'>PROCESSING FEE</th>
                      <th style='text-align:right'>COMMISSION [PERCENT]</th>
                      <th style='text-align:right'>NET</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($d['elements'] as $e)
                      <tr>
                          <td @if($d['name'] == 'Totals')colspan="2" @endif align='left'>@if($d['type']=='venue' && $d['name'] !='Totals') {{$e['s_name']}} @else {{$e['name']}} @endif</td>
                          @if($d['name'] != 'Totals')<td align='center'>{{$e['ticket_type']}}</td>@endif
                          <td align='center'>{{$e['qty']}}</td>
                          <!--<td align='center'>{{$e['purchase_count']}}</td>-->
                          <td style='text-align:right'>$ {{$e['gross_revenue']}}</td>
                          <td style='text-align:right'>$ {{$e['processing_fee']}}</td>
                          <td style='text-align:right'>$ {{$e['commission']}} [{{$e['commission_percent']}}%]</td>
                          <td style='text-align:right'>$ {{$e['net']}}</td>
                      </tr>
                  @endforeach
                  </tbody>
                  <tfoot>
                      <tr>
                        <th colspan="2">TOTALS:</th>
                        <th align='center'>{{$d['total']['t_ticket']}}</th>
                        <!--<th align='center'>{{$d['total']['t_purchases']}}</th>-->
                        <th style='text-align:right'>$ {{$d['total']['t_gross_revenue']}}</th>
                        <th style='text-align:right'>$ {{$d['total']['t_processing_fee']}}</th>
                        <th style='text-align:right'>$ {{$d['total']['t_commission']}} [{{$d['total']['t_commission_percent']}}%]</th>
                        <th style='text-align:right'>$ {{$d['total']['t_net']}}</th>
                      </tr>
                  </tfoot>
              </table>
            </div>
            @endforeach
        @endif
    {{--</body>
</html>--}}
@endif