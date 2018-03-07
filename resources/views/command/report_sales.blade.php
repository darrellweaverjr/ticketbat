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
        @if($format == 'referrer')
            <h3 style="text-align:center">
                <img alt="TicketBat.com" height="50" width="300" src="/themes/img/logo-header-white.jpg"/>
                <br><br>REFERRER SALES REPORT
            </h3><hr>
            <p class="ttitle">Date: {{$date_report}}</p>
            <table>
                <tr class="theader">
                    <td>REFERRER</td>
                    <td>NAME</td>
                    <td style='text-align:center'>TICKET TYPE</td>
                    <td style='text-align:center'>QTY</td>
                    <td style='text-align:center'>PURCHASES</td>
                    <td style='text-align:center'>METHOD</td>
                </tr>
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
            </table>
        @elseif($format == 'future_liabilities')
            @foreach($data as $d)
            <div style="page-break-after:always;">
              <h3 style="text-align:center">
                <img alt="TicketBat.com" height="50" width="300" src="/themes/img/logo-header-white.jpg"/>
                <br><br>FUTURE LIABILITIES REPORT
            </h3><hr>
            <p class="ttitle">@if($d['name'] == 'Totals') All venues @else Venue: "{{$d['name']}}" @endif - Starting on {{date('F j, Y')}}</p>
                <table>
                  <tr class="theader">
                      <td>DATE/TIME</td>
                      @if($d['name'] != 'Totals')
                      <td>SHOW</td>
                      <td style='text-align:center'>PURCHASES</td>
                      <td style='text-align:center'>QTY</td>
                      <td style='text-align:right'>GROSS REVENUE</td>
                      <td style='text-align:right'>PROCESSING FEE</td>
                      <td style='text-align:right'>TB COMMISSION<br>EXPENSE</td>
                      <td style='text-align:right'>NET TO SHOW</td>
                      @else
                      <td>VENUE</td>
                      <td>SHOW</td>
                      <td style='text-align:center'>PURCHASES</td>
                      <td style='text-align:center'>QTY</td>
                      <td style='text-align:right'>GROSS REVENUE</td>
                      <td style='text-align:right'>PROCESSING FEE</td>
                      <td style='text-align:right'>COMMISSION</td>
                      <td style='text-align:right'>GROSS PROFIT</td>
                      @endif
                  </tr>
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
                    <tr class="ttotal">
                      <td @if($d['name'] == 'Totals') colspan="3" @else colspan="2" @endif>TOTALS:</td>
                      <td style='text-align:center'>{{$d['future_t']['t_purchases']}}</td>
                      <td style='text-align:center'>{{$d['future_t']['t_ticket']}}</td>
                      <td style='text-align:right'>$ {{number_format($d['future_t']['t_gross_revenue'],2)}}</td>
                      <td style='text-align:right'>$ {{number_format($d['future_t']['t_processing_fee'],2)}}</td>
                      <td style='text-align:right'>$ {{number_format($d['future_t']['t_commission'],2)}}</td>
                      <td style='text-align:right'>$ {{number_format($d['future_t']['t_net'],2)}}</td>
                    </tr>
              </table>
            </div>
            @endforeach
        @elseif($format == 'types')
            @if(isset($data[0]['types']))
              <h3 style="text-align:center">
                    <img alt="TicketBat.com" height="50" width="300" src="/themes/img/logo-header-white.jpg"/>
                    <br><br>TOTAL SALES REPORT
                </h3><hr>
                <p class="ttitle">Date: {{$data[0]['date']}}</p>
              <hr><table>
                  <tr class="theader">
                      <td>TYPE</td>
                      <td style='text-align:center'>PURCHASES</td>
                      <td style='text-align:center'>QTY</td>
                      <td style='text-align:right'>GROSS REVENUE</td>
                      <td style='text-align:right'>PROCESSING FEE</td>
                      <td style='text-align:right'>COMMISSION</td>
                      <td style='text-align:right'>NET</td>
                  </tr>
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
                  <tr class="ttotal">
                        <td>TOTALS:</td>
                        <td style='text-align:center'>{{$data[0]['total']['t_purchases']}}</td>
                        <td style='text-align:center'>{{$data[0]['total']['t_ticket']}}</td>
                        <td style='text-align:right'>$ {{number_format($data[0]['total']['t_gross_revenue'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($data[0]['total']['t_processing_fee'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($data[0]['total']['t_commission'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($data[0]['total']['t_net'],2)}}</td>
                      </tr>
              </table>
               @endif
        @else
            @foreach($data as $d)
            <div style="page-break-after:always;">
              <h3 style="text-align:center">
                    <img alt="TicketBat.com" height="50" width="300" src="/themes/img/logo-header-white.jpg"/>
                    <br><br>SALES REPORT
                </h3><hr>
            <p class="ttitle">@if($d['name'] == 'Totals') All venues: @else @if($d['type']=='venue')Venue: @else Show: @endif "{{$d['name']}}" on @endif {{$d['date']}}</p>
             @if(isset($d['types']))
              <hr><table>
                  <tr class="theader">
                      <td>TYPE</td>
                      <td style='text-align:center'>PURCHASES</td>
                      <td style='text-align:center'>QTY</td>
                      <td style='text-align:right'>GROSS REVENUE</td>
                      <td style='text-align:right'>PROCESSING FEE</td>
                      <td style='text-align:right'>COMMISSION</td>
                      <td style='text-align:right'>NET</td>
                  </tr>
                  @foreach($d['types'] as $k => $e)  
                      <tr @if($e->payment_type=='Subtotal') class="ttotal" @endif >
                          <td>{{$e->payment_type}}</td>
                          <td style='text-align:center'>{{number_format($e->purchase_count)}}</td>
                          <td style='text-align:center'>{{number_format($e->qty)}}</td>
                          <td style='text-align:right'>$ {{number_format($e->gross_revenue,2)}}</td>
                          <td style='text-align:right'>$ {{number_format($e->processing_fee,2)}}</td>
                          <td style='text-align:right'>$ {{number_format($e->commission,2)}}</td>
                          <td style='text-align:right'>$ {{number_format($e->net,2)}}</td>
                      </tr>
                  @endforeach 
              </table>
               @endif
              <hr><table>
                  <tr class="theader">
                      <td @if($d['name'] == 'Totals') colspan="3" @endif align='left'>@if($d['type']=='venue' && $d['name'] !='Totals')SHOW @else VENUE @endif</td>
                      @if($d['name'] != 'Totals')
                      <td style='text-align:center'>DATE/TIME</td>
                      <td style='text-align:center'>TICKET TYPE</td>
                      @endif
                      <td style='text-align:center'>QTY</td>
                      <td style='text-align:right'>GROSS REVENUE</td>
                      <td style='text-align:right'>PROCESSING FEE</td>
                      <td style='text-align:right'>COMMISSION</td>
                      <td style='text-align:right'>NET</td>
                  </tr>
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
                  <tr class="ttotal">
                        <td colspan="3">TOTALS:</td>
                        <td style='text-align:center'>{{$d['total']['t_ticket']}}</td>
                        <td style='text-align:right'>$ {{number_format($d['total']['t_gross_revenue'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['total']['t_processing_fee'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['total']['t_commission'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['total']['t_net'],2)}}</td>
                      </tr>
              </table>
            </div>
            @endforeach
        @endif
    {{--</body>
</html>--}}
@endif