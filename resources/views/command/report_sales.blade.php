@if($format == 'csv')
"#","NAME","TICKET TYPE","SHOW TIME","QTY","PURCHASES","METHOD","RETAIL PRICE","PROCESSING FEE","SAVINGS","TOTAL PAID","COMMISSION","DUE TO SHOW","REFERRER"
@foreach ($purchases as $n => $p)
"{{$n+1}}","{{$p->s_name}}","{{$p->ticket_type}}","{{$p->shows_time}}","{{$p->qty}}","{{$p->purchase_count}}","{{$p->method}}","$ {{$p->retail_price}}","$ {{$p->processing_fee}}","$ {{$p->savings}}","$ {{$p->total_paid}}","$ {{$p->commission}}","$ {{$p->due_to_show}}","{{$p->url}}"
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
    <body style="font-size:10px">
        @if($format == 'referrer')
            <h1>TicketBat.com</h1>
            <h3>Referrer Sales<br><i>{{$date_report}}</i></h3>
            <table class="table table-striped table-responsive">
                <thead>
                <tr>
                    <th>REFERRER</th>
                    <th>NAME</th>
                    <th style='text-align:center'>TICKET TYPE</th>
                    <th style='text-align:center'>QTY</th>
                    <th style='text-align:center'>PURCHASES</th>
                    <th style='text-align:center'>METHOD</th>
                </tr>
                </thead>
                <tbody>
                @foreach($purchases as $p)
                    <tr>
                        <td>{{$p->referral_url}}</td>
                        <td>{{$p->s_name}}</td>
                        <td style='text-align:center'>{{$p->ticket_type}}</td>
                        <td style='text-align:center'>{{number_format($p->qty)}}</td>
                        <td style='text-align:center'>{{number_format($p->purchase_count)}}</td>
                        <td style='text-align:center'>{{$p->method}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @elseif($format == 'future_liabilities')
            @foreach($data as $d)
            <div style="page-break-after:always;">
              <h1>TicketBat.com</h1>
              <h3>Future Liabilities @if($d['name'] == 'Totals') Total @else For Venue: <i>{{$d['name']}}</i> @endif<br><i>Starting on {{date('F j, Y')}}</i></h3>
                <table class="table table-striped table-responsive">
                  <thead>
                  <tr>
                      <th>DATE/TIME</th>
                      @if($d['name'] != 'Totals')
                      <th>SHOW</th>
                      <th style='text-align:center'>PURCHASES</th>
                      <th style='text-align:center'>QTY</th>
                      <th style='text-align:right'>GROSS REVENUE</th>
                      <th style='text-align:right'>PROCESSING FEE</th>
                      <th style='text-align:right'>TB COMMISSION<br>EXPENSE</th>
                      <th style='text-align:right'>NET TO SHOW</th>
                      @else
                      <th>VENUE</th>
                      <th>SHOW</th>
                      <th style='text-align:center'>PURCHASES</th>
                      <th style='text-align:center'>QTY</th>
                      <th style='text-align:right'>GROSS REVENUE</th>
                      <th style='text-align:right'>PROCESSING FEE</th>
                      <th style='text-align:right'>COMMISSION</th>
                      <th style='text-align:right'>GROSS PROFIT</th>
                      @endif
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($d['future'] as $e)  
                      <tr>
                          <td>{{$e->shows_time}}</td>
                          @if($d['name'] != 'Totals')
                          <td>{{$e->s_name}}</td>
                          @else
                          <td>{{$e->v_name}}</td>
                          <td>{{$e->s_name}}</td>
                          @endif
                          <td style='text-align:center'>{{number_format($e->purchase_count)}}</td>
                          <td style='text-align:center'>{{number_format($e->qty)}}</td>
                          <td style='text-align:right'>$ {{number_format($e->gross_revenue,2)}}</td>
                          <td style='text-align:right'>$ {{number_format($e->processing_fee,2)}}</td>
                          <td style='text-align:right'>$ {{number_format($e->commission,2)}}</td>
                          @if($d['name'] != 'Totals')
                          <td style='text-align:right'>$ {{number_format($e->net,2)}}</td>
                          @else
                          <td style='text-align:right'>$ {{number_format($e->processing_fee+$e->commission,2)}}</td>
                          @endif
                      </tr>
                  @endforeach
                  </tbody>
                  <tfoot>
                      <tr>
                        <th @if($d['name'] == 'Totals') colspan="3" @else colspan="2" @endif>TOTALS:</th>
                        <th style='text-align:center'>{{$d['future_t']['t_purchases']}}</th>
                        <th style='text-align:center'>{{$d['future_t']['t_ticket']}}</th>
                        <th style='text-align:right'>$ {{number_format($d['future_t']['t_gross_revenue'],2)}}</th>
                        <th style='text-align:right'>$ {{number_format($d['future_t']['t_processing_fee'],2)}}</th>
                        <th style='text-align:right'>$ {{number_format($d['future_t']['t_commission'],2)}}</th>
                        <th style='text-align:right'>$ {{number_format($d['future_t']['t_net'],2)}}</th>
                      </tr>
                  </tfoot>
              </table>
            </div>
            @endforeach
        @elseif($format == 'types')
            @if(isset($data[0]['types']))
              <h1><center>TicketBat.com</center></h1>
              <h2><center>Total Sales Report for {{$data[0]['date']}}</center></h2>
              <hr><table class="table table-striped table-responsive">
                  <thead>
                  <tr>
                      <th>TYPE</th>
                      <th style='text-align:center'>PURCHASES</th>
                      <th style='text-align:center'>QTY</th>
                      <th style='text-align:right'>GROSS REVENUE</th>
                      <th style='text-align:right'>PROCESSING FEE</th>
                      <th style='text-align:right'>COMMISSION</th>
                      <th style='text-align:right'>NET</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($data[0]['types'] as $k => $e)  
                      <tr @if($e->payment_type=='Subtotal') style="font-weight:bold;" @endif >
                          <td>{{$e->payment_type}}</td>
                          <td style='text-align:center'>{{number_format($e->purchase_count)}}</td>
                          <td style='text-align:center'>{{number_format($e->qty)}}</td>
                          <td style='text-align:right'>$ {{number_format($e->gross_revenue,2)}}</td>
                          <td style='text-align:right'>$ {{number_format($e->processing_fee,2)}}</td>
                          <td style='text-align:right'>$ {{number_format($e->commission,2)}}</td>
                          <td style='text-align:right'>$ {{number_format($e->net,2)}}</td>
                      </tr>
                  @endforeach 
                  </tbody>
                  <tfoot>
                      <tr>
                        <th>TOTALS:</th>
                        <th style='text-align:center'>{{$data[0]['total']['t_purchases']}}</th>
                        <th style='text-align:center'>{{$data[0]['total']['t_ticket']}}</th>
                        <th style='text-align:right'>$ {{number_format($data[0]['total']['t_gross_revenue'],2)}}</th>
                        <th style='text-align:right'>$ {{number_format($data[0]['total']['t_processing_fee'],2)}}</th>
                        <th style='text-align:right'>$ {{number_format($data[0]['total']['t_commission'],2)}}</th>
                        <th style='text-align:right'>$ {{number_format($data[0]['total']['t_net'],2)}}</th>
                      </tr>
                  </tfoot>
              </table>
               @endif
        @else
            @foreach($data as $d)
            <div style="page-break-after:always;">
              <h1>TicketBat.com</h1>
              <h3>Sales @if($d['name'] == 'Totals') Total @else For @if($d['type']=='venue')Venue @else Show @endif: <i>{{$d['name']}}</i> @endif<br><i>{{$d['date']}}</i></h3>
             @if(isset($d['types']))
              <hr><table class="table table-striped table-responsive">
                  <thead>
                  <tr>
                      <th>TYPE</th>
                      <th style='text-align:center'>PURCHASES</th>
                      <th style='text-align:center'>QTY</th>
                      <th style='text-align:right'>GROSS REVENUE</th>
                      <th style='text-align:right'>PROCESSING FEE</th>
                      <th style='text-align:right'>COMMISSION</th>
                      <th style='text-align:right'>NET</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($d['types'] as $k => $e)  
                      <tr @if($e->payment_type=='Subtotal') style="font-weight:bold;" @endif >
                          <td>{{$e->payment_type}}</td>
                          <td style='text-align:center'>{{number_format($e->purchase_count)}}</td>
                          <td style='text-align:center'>{{number_format($e->qty)}}</td>
                          <td style='text-align:right'>$ {{number_format($e->gross_revenue,2)}}</td>
                          <td style='text-align:right'>$ {{number_format($e->processing_fee,2)}}</td>
                          <td style='text-align:right'>$ {{number_format($e->commission,2)}}</td>
                          <td style='text-align:right'>$ {{number_format($e->net,2)}}</td>
                      </tr>
                  @endforeach 
                  </tbody>
              </table>
               @endif
              <hr><table class="table table-striped table-responsive">
                  <thead>
                  <tr>
                      <th @if($d['name'] == 'Totals') colspan="3" @endif align='left'>@if($d['type']=='venue' && $d['name'] !='Totals')SHOW @else VENUE @endif</th>
                      @if($d['name'] != 'Totals')
                      <th style='text-align:center'>DATE/TIME</th>
                      <th style='text-align:center'>TICKET TYPE</th>
                      @endif
                      <th style='text-align:center'>QTY</th>
                      <th style='text-align:right'>GROSS REVENUE</th>
                      <th style='text-align:right'>PROCESSING FEE</th>
                      <th style='text-align:right'>COMMISSION</th>
                      <th style='text-align:right'>NET</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($d['elements'] as $e)  
                      <tr>
                          <td @if($d['name'] == 'Totals')colspan="3" @endif>@if($d['type']=='venue' && $d['name'] !='Totals') {{$e->s_name}} @else {{$e->name}} @endif</td>
                          @if($d['name'] != 'Totals')
                          <td style='text-align:center'>{{$e->shows_time}}</td>
                          <td style='text-align:center'>{{$e->ticket_type}}</td>
                          @endif
                          <td style='text-align:center'>{{number_format($e->qty)}}</td>
                          <td style='text-align:right'>$ {{number_format($e->gross_revenue,2)}}</td>
                          <td style='text-align:right'>$ {{number_format($e->processing_fee,2)}}</td>
                          <td style='text-align:right'>$ {{number_format($e->commission,2)}}</td>
                          <td style='text-align:right'>$ {{number_format($e->net,2)}}</td>
                      </tr>
                  @endforeach
                  </tbody>
                  <tfoot>
                      <tr>
                        <th colspan="3">TOTALS:</th>
                        <th style='text-align:center'>{{$d['total']['t_ticket']}}</th>
                        <th style='text-align:right'>$ {{number_format($d['total']['t_gross_revenue'],2)}}</th>
                        <th style='text-align:right'>$ {{number_format($d['total']['t_processing_fee'],2)}}</th>
                        <th style='text-align:right'>$ {{number_format($d['total']['t_commission'],2)}}</th>
                        <th style='text-align:right'>$ {{number_format($d['total']['t_net'],2)}}</th>
                      </tr>
                  </tfoot>
              </table>
            </div>
            @endforeach
        @endif
    {{--</body>
</html>--}}
@endif