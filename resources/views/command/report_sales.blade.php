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
                width: 100%;
                font-size:10px!important;
            }
            .theader {
                background-color: #CCCCCC;
                font-weight:bold;
                border-top: 1px solid #DDD;
                line-height: 2.5;
            }
            .ttotal {
                background-color: #DDDDDD;
                font-weight:bold;
                border-top: 1px solid #DDD;
                line-height: 2.5;
            }
            .ttitle {
                font-weight:bold;
                font-size:13px!important;
            }
        </style>
    </head>
    <body>
        @if($format == 'future_liabilities')
            @foreach($data as $d)
            <div style="page-break-after:always;">
              <h3 style="text-align:center">
                <img alt="TicketBat.com" height="50" width="300" src="http://www.ticketbat.com/themes/img/logo-header-white.jpg"/>
                <br><br>FUTURE LIABILITIES REPORT
            </h3><hr>
            <p class="ttitle">@if($d['type'] == 'venue') Venue: @endif {{$d['title']}} ( starting on {{$d['date']}} )</p>
                <table>
                  <tr class="theader">
                      <td>DATE/TIME</td>
                      @if($d['type'] == 'venue')
                      <td>SHOW</td>
                      <td style='text-align:center'>TRANSACTIONS</td>
                      <td style='text-align:center'>QTY</td>
                      <td style='text-align:right'>REVENUE(R)</td>
                      <td style='text-align:right'>FEES(F)</td>
                      <td style='text-align:right'>TB COMMISSION<br>EXPENSE(C)</td>
                      <td style='text-align:right'>NET TO SHOW(R-F-C)</td>
                      @else
                      <td>VENUE</td>
                      <td>SHOW</td>
                      <td style='text-align:center'>TRANSACTIONS</td>
                      <td style='text-align:center'>QTY</td>
                      <td style='text-align:right'>REVENUE(R)</td>
                      <td style='text-align:right'>FEES(F)</td>
                      <td style='text-align:right'>COMMISSION(C)</td>
                      <td style='text-align:right'>NET(F+C)</td>
                      @endif
                  </tr>
                  @foreach($d['table_future'] as $e)  
                    <tr>
                        <td>{{$e->shows_time}}</td>
                        @if($d['type'] == 'venue')
                        <td>{{$e->event}}</td>
                        <td style='text-align:center'>{{number_format($e->transactions)}}</td>
                        <td style='text-align:center'>{{number_format($e->tickets)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->paid,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->commissions,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->amount,2)}}</td>
                        @else
                        <td>{{$e->venue}}</td>
                        <td>{{$e->event}}</td>
                        <td style='text-align:center'>{{number_format($e->transactions)}}</td>
                        <td style='text-align:center'>{{number_format($e->tickets)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->paid,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->commissions,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->amount,2)}}</td>
                        @endif
                    </tr>
                @endforeach
                    <tr class="ttotal">
                      <td @if($d['type'] == 'admin') colspan="3" @else colspan="2" @endif>TOTALS:</td>
                      <td style='text-align:center'>{{$d['total']['transactions']}}</td>
                      <td style='text-align:center'>{{$d['total']['tickets']}}</td>
                      <td style='text-align:right'>$ {{number_format($d['total']['paid'],2)}}</td>
                      <td style='text-align:right'>$ {{number_format($d['total']['fees'],2)}}</td>
                      <td style='text-align:right'>$ {{number_format($d['total']['commissions'],2)}}</td>
                      <td style='text-align:right'>$ {{number_format($d['total']['amount'],2)}}</td>
                    </tr>
              </table>
            </div>
            @endforeach
            
            
        @elseif($format == 'sales')
            @foreach($data as $d)
            <div style="page-break-after:always;">
              <h3 style="text-align:center">
                    <img alt="TicketBat.com" height="50" width="300" src="http://www.ticketbat.com/themes/img/logo-header-white.jpg"/>
                    <br><br>SALES REPORT
                </h3><hr>
            <p class="ttitle">@if($d['type'] == 'venue') Venue: @endif {{$d['title']}} ( {{$d['date']}} )</p>
             @if(isset($d['table_types']))
              <hr><table>
                  <tr class="theader">
                      <td>TYPE</td>
                      <td style='text-align:center'>PURCHASES</td>
                      <td style='text-align:center'>QTY</td>
                      <td style='text-align:right'>REVENUE</td>
                      <td style='text-align:right'>FEES</td>
                      <td style='text-align:right'>COMMISSIONS</td>
                      <td style='text-align:right'>NET</td>
                  </tr>
                  @foreach($d['table_types']['data'] as $e)  
                    <tr>
                        <td>{{$e->payment_type}}</td>
                        <td style='text-align:center'>{{number_format($e->transactions)}}</td>
                        <td style='text-align:center'>{{number_format($e->tickets)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->paid,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->commissions,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->amount,2)}}</td>
                    </tr>
                  @endforeach 
                  <tr class="ttotal">
                        <td>Subtotals</td>
                        <td style='text-align:center'>{{number_format($d['table_types']['subtotal']['transactions'])}}</td>
                        <td style='text-align:center'>{{number_format($d['table_types']['subtotal']['tickets']))}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['subtotal']['paid']),2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['subtotal']['fees']),2)}}</td>
                        <td style='text-align:right'>$ {{number_format$d['table_types']['subtotal']['commissions']),2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['subtotal']['amount']),2)}}</td>
                    </tr>
                  @foreach($d['table_types']['others'] as $e)  
                    <tr>
                        <td>{{$e->payment_type}}</td>
                        <td style='text-align:center'>{{number_format($e->transactions)}}</td>
                        <td style='text-align:center'>{{number_format($e->tickets)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->paid,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->commissions,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->amount,2)}}</td>
                    </tr>
                  @endforeach 
                  <tr class="ttotal">
                        <td>Totals</td>
                        <td style='text-align:center'>{{number_format($d['table_types']['total']['transactions'])}}</td>
                        <td style='text-align:center'>{{number_format($d['table_types']['total']['tickets']))}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['total']['paid']),2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['total']['fees']),2)}}</td>
                        <td style='text-align:right'>$ {{number_format$d['table_types']['total']['commissions']),2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['total']['amount']),2)}}</td>
                    </tr>
              </table>
              @endif
              @if(isset($d['table_data']))
              <hr><table>
                  <tr class="theader">
                      @if($d['type'] == 'admin')
                        <td colspan="3" align='left'>VENUE</td>
                      @else
                        <td align='left'>SHOW</td>
                        <td style='text-align:center'>DATE/TIME</td>
                        <td style='text-align:center'>TICKET TYPE</td>
                      @endif
                        <td style='text-align:center'>TRANSACTIONS</td>
                        <td style='text-align:center'>QTY</td>
                        <td style='text-align:right'>REVENUE(R)</td>
                        <td style='text-align:right'>FEES(F)</td>
                        <td style='text-align:right'>COMMISSIONS(C)</td>
                        <td style='text-align:right'>NET(R-F-C)</td>
                  </tr>
                  @foreach($d['table_data'] as $e)  
                      <tr>
                          @if($d['type'] == 'admin')
                            <td colspan="3" align='left'>{{$e->name}}</td>
                          @else
                            <td align='left'>{{$e->name}}</td>
                            <td style='text-align:center'>{{$e->show_time}}</td>
                            <td style='text-align:center'>{{$e->ticket_type}}</td>
                          @endif
                            <td style='text-align:center'>{{number_format($e->transactions)}}</td>
                            <td style='text-align:center'>{{number_format($e->tickets)}}</td>
                            <td style='text-align:right'>$ {{number_format($e->paid,2)}}</td>
                            <td style='text-align:right'>$ {{number_format($e->fees,2)}}</td>
                            <td style='text-align:right'>$ {{number_format($e->commissions,2)}}</td>
                            <td style='text-align:right'>$ {{number_format($e->amount,2)}}</td>
                      </tr>
                  @endforeach
                  <tr class="ttotal">
                        <td colspan="3">Totals</td>
                        <td style='text-align:center'>{{number_format($d['table_types']['total']['transactions'])}}</td>
                        <td style='text-align:center'>{{number_format($d['table_types']['total']['tickets']))}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['total']['paid']),2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['total']['fees']),2)}}</td>
                        <td style='text-align:right'>$ {{number_format$d['table_types']['total']['commissions']),2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['total']['amount']),2)}}</td>
                      </tr>
              </table>
              @endif
            </div>
            @endforeach
            
        
        @elseif($format == 'financial')
            <h3 style="text-align:center">
                <img alt="TicketBat.com" height="50" width="300" src="http://www.ticketbat.com/themes/img/logo-header-white.jpg"/>
                <br><br>FINANCIAL REPORT
            </h3><hr>
            @foreach($data as $t)
            <table>
                <tr class="ttitle">
                    <td width="75%">{{$t['title']}}</td>
                    <td style='text-align:right'>@if(!empty($t['percent'])) @if($t['percent']>0)+ @endif {{$t['percent']}}% NET (C+F) @endif</td>
                </tr>
            </table>
            <table>
                <tr class="theader">
                    <td width="25%">VENUES</td>
                    <td style='text-align:center'>TRANSACTIONS</td>
                    <td style='text-align:center'>TICKETS</td>
                    <td style='text-align:right'>PAID</td>
                    <td style='text-align:right'>COMMIS.(C)</td>
                    <td style='text-align:right'>FEES(F)</td>
                    <td style='text-align:right'>C+F</td>
                </tr>
                @foreach($t['data'] as $v)
                <tr>
                    <td width="25%" style='text-align:left'>{{$v->name}}</td>
                    <td style='text-align:center'>{{number_format($v->purchases)}}</td>
                    <td style='text-align:center'>{{number_format($v->tickets)}}</td>
                    <td style='text-align:right'>$ {{number_format($v->paid,2)}}</td>
                    <td style='text-align:right'>$ {{number_format($v->commissions,2)}}</td>
                    <td style='text-align:right'>$ {{number_format($v->fees,2)}}</td>
                    <td style='text-align:right'>$ {{number_format($v->amount,2)}}</td>
                </tr>
                @endforeach
                <tr class="ttotal">
                    <td width="25%" style='text-align:left'>Totals</td>
                    <td style='text-align:center'>{{number_format($t['total']['transactions'])}}</td>
                    <td style='text-align:center'>{{number_format($t['total']['tickets'])}}</td>
                    <td style='text-align:right'>$ {{number_format($t['total']['paid'],2)}}</td>
                    <td style='text-align:right'>$ {{number_format($t['total']['commissions'],2)}}</td>
                    <td style='text-align:right'>$ {{number_format($t['total']['fees'],2)}}</td>
                    <td style='text-align:right'>$ {{number_format($t['total']['amount'],2)}}</td>
                </tr>
            </table><hr>
            @endforeach
        
        @endif
    {{--</body>
</html>--}}
@endif