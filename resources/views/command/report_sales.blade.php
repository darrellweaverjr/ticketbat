@if($format == 'csv')
"#","VENUE","EVENT","TICKET TYPE","SHOW TIME","PAYMENT METHOD","TRANSACTIONS","TICKETS","RETAIL PRICE","SAVINGS","TOTAL PAID","FEES","COMMISSIONS","NET"
@foreach ($data as $n => $p)
"{{$n+1}}","{{$p->venue}}","{{$p->name}}","{{$p->ticket_type}}","{{$p->show_time}}","{{$p->payment_type}}","{{$p->transactions}}","{{$p->tickets}}","$ {{$p->retail_price}}","$ {{$p->savings}}","$ {{$p->paid}}","$ {{$p->fees}}","$ {{$p->commissions}}","$ {{$p->amount}}"
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
                line-height: 1.0;
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
        @php $logo = "https://marketing-image-production.s3.amazonaws.com/uploads/0524976af1dff0748cb6a51c8ff0043e15fda93ccf1129a5124b18035eea48bd5b937bec629a19356d1ee0763159f3e1095a67bb6fe4780d92e2757b0493dbd1.jpg" @endphp
        
        @if($format == 'future_liabilities')
            @foreach($data as $d)
            <div style="page-break-after:always;">
              <h3 style="text-align:center">
                <img alt="TicketBat.com" height="50" width="300" src="{{$logo}}"/>
                <br><br>FUTURE LIABILITIES REPORT<br><span style="font-size:8px">Created on {{date('n/d/Y g:i A')}}</span>
            </h3><hr>
            <p class="ttitle">@if($d['type'] == 'venue') Venue: @endif {{html_entity_decode($d['title'])}} ( starting on {{$d['date']}} )</p>
                <table>
                  <tr class="theader">
                      <td>DATE/TIME</td>
                      @if($d['type'] == 'venue')
                      @else
                      <td>VENUES</td>
                      @endif
                      <td>SHOWS</td>
                      <td style='text-align:center'>TRANS.</td>
                      <td style='text-align:center'>TICKS.</td>
                      <td style='text-align:right'>TOTAL<br>PAID<br>(P)</td>
                      <td style='text-align:right'>TAXES<br>(T)</td>
                      <td style='text-align:right'>C.C.<br>FEE<br>(M)</td>
                      <td style='text-align:right'>PRNT<br>FEE<br>(F)</td>
                      <td style='text-align:right'>FEES<br>INC.<br>(F)</td>
                      <td style='text-align:right'>FEES<br>OVR.<br>(F)</td>
                      <td style='text-align:right'>COMM.<br>(C)</td>
                      <td style='text-align:right'>NET<br>@if($d['type']=='admin') (F+C) @else (P-T-M-F-C) @endif</td>
                  </tr>
                  @foreach($d['table_future'] as $e)
                    <tr>
                        <td>{{$e->show_time}}</td>
                        @if($d['type'] == 'venue')
                        <td>{{$e->event}}</td>
                        <td style='text-align:center'>{{number_format($e->transactions)}}</td>
                        <td style='text-align:center'>{{number_format($e->tickets)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->paid,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->taxes,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->cc_fee,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->printed_fee,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees_incl,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees_over,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->commissions,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->amount,2)}}</td>
                        @else
                        <td>{{$e->venue}}</td>
                        <td>{{$e->event}}</td>
                        <td style='text-align:center'>{{number_format($e->transactions)}}</td>
                        <td style='text-align:center'>{{number_format($e->tickets)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->paid,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->taxes,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->cc_fee,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->printed_fee,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees_incl,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees_over,2)}}</td>
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
                      <td style='text-align:right'>$ {{number_format($d['total']['taxes'],2)}}</td>
                      <td style='text-align:right'>$ {{number_format($d['total']['cc_fee'],2)}}</td>
                      <td style='text-align:right'>$ {{number_format($d['total']['printed_fee'],2)}}</td>
                      <td style='text-align:right'>$ {{number_format($d['total']['fees_incl'],2)}}</td>
                      <td style='text-align:right'>$ {{number_format($d['total']['fees_over'],2)}}</td>
                      <td style='text-align:right'>$ {{number_format($d['total']['commissions'],2)}}</td>
                      <td style='text-align:right'>$ {{number_format($d['total']['amount'],2)}}</td>
                    </tr>
              </table>
            </div>
            @endforeach

        @elseif($format == 'sales')
            @foreach($data as $d)
                @php $net = ($d['type']=='admin')? '(F+C)' : '(P-T-M-F-C)' @endphp
            <div style="page-break-after:always;">
              <h3 style="text-align:center">
                    <img alt="TicketBat.com" height="50" width="300" src="{{$logo}}"/>
                    <br><br>SALES REPORT<br><span style="font-size:8px">Created on {{date('n/d/Y g:i A')}}</span>
                </h3><hr>
            <p class="ttitle">@if($d['type'] == 'venue') Venue: @endif {{$d['title']}} ( {{$d['date']}} )</p>

            @if(isset($d['table_financial']))
            <hr>@foreach($d['table_financial'] as $t)
            <hr><table>
                  <tr class="ttitle">
                      <td colspan="7">{{html_entity_decode($t['title'])}}</td>
                      <td colspan="4" style='text-align:right'>@if(!empty($t['percent'])) @if($t['percent']>0)+ @endif {{$t['percent']}}% NET {{$net}} @endif</td>
                  </tr>
                  <tr class="theader">
                      <td width="25%">VENUES</td>
                      <td style='text-align:center'>TRANS.</td>
                      <td style='text-align:center'>TICKS.</td>
                      <td style='text-align:right'>TOTAL<br>PAID<br>(P)</td>
                      <td style='text-align:right'>TAXES<br>(T)</td>
                      <td style='text-align:right'>C.C.<br>FEE<br>(M)</td>
                      <td style='text-align:right'>PRNT<br>FEE<br>(F)</td>
                      <td style='text-align:right'>FEES<br>INC.<br>(F)</td>
                      <td style='text-align:right'>FEES<br>OVR.<br>(F)</td>
                      <td style='text-align:right'>COMM.<br>(C)</td>
                      <td style='text-align:right'>NET<br>{{$net}}</td>
                  </tr>
                  @foreach($t['data'] as $v)
                  @if($d['type'] == 'admin' || $d['type'] != 'admin' && $v->name == $d['title'])
                  <tr>
                      <td width="25%" style='text-align:left'>{{$v->name}}</td>
                      <td style='text-align:center'>{{number_format($v->transactions)}}</td>
                      <td style='text-align:center'>{{number_format($v->tickets)}}</td>
                      <td style='text-align:right'>$ {{number_format($v->paid,2)}}</td>
                      <td style='text-align:right'>$ {{number_format($v->taxes,2)}}</td>
                      <td style='text-align:right'>$ {{number_format($v->cc_fee,2)}}</td>
                      <td style='text-align:right'>$ {{number_format($v->printed_fee,2)}}</td>
                      <td style='text-align:right'>$ {{number_format($v->fees_incl,2)}}</td>
                      <td style='text-align:right'>$ {{number_format($v->fees_over,2)}}</td>
                      <td style='text-align:right'>$ {{number_format($v->commissions,2)}}</td>
                      <td style='text-align:right'>$ {{number_format($v->amount,2)}}</td>
                  </tr>
                  @endif
                  @endforeach
                  @if($d['type'] == 'admin')
                  <tr class="ttotal">
                      <td width="25%" style='text-align:left'>Subtotals</td>
                      <td style='text-align:center'>{{number_format($t['total']['transactions'])}}</td>
                      <td style='text-align:center'>{{number_format($t['total']['tickets'])}}</td>
                      <td style='text-align:right'>$ {{number_format($t['total']['paid'],2)}}</td>
                      <td style='text-align:right'>$ {{number_format($t['total']['taxes'],2)}}</td>
                      <td style='text-align:right'>$ {{number_format($t['total']['cc_fee'],2)}}</td>
                      <td style='text-align:right'>$ {{number_format($t['total']['printed_fee'],2)}}</td>
                      <td style='text-align:right'>$ {{number_format($t['total']['fees_incl'],2)}}</td>
                      <td style='text-align:right'>$ {{number_format($t['total']['fees_over'],2)}}</td>
                      <td style='text-align:right'>$ {{number_format($t['total']['commissions'],2)}}</td>
                      <td style='text-align:right'>$ {{number_format($t['total']['amount'],2)}}</td>
                  </tr>
                  @endif
                  @foreach($t['debits'] as $e)
                    <tr>
                        <td>{{$e->status}}</td>
                        <td style='text-align:center'>{{number_format($e->transactions)}}</td>
                        <td style='text-align:center'>{{number_format($e->tickets)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->paid,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->taxes,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->cc_fee,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->printed_fee,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees_incl,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees_over,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->commissions,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->amount,2)}}</td>
                    </tr>
                    @endforeach
                    <tr class="ttotal">
                        <td>Totals</td>
                        <td style='text-align:center'>{{number_format($t['grand_total']['transactions'])}}</td>
                        <td style='text-align:center'>{{number_format($t['grand_total']['tickets'])}}</td>
                        <td style='text-align:right'>$ {{number_format($t['grand_total']['paid'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($t['grand_total']['taxes'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($t['grand_total']['cc_fee'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($t['grand_total']['printed_fee'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($t['grand_total']['fees_incl'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($t['grand_total']['fees_over'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($t['grand_total']['commissions'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($t['grand_total']['amount'],2)}}</td>
                    </tr>
              </table>
              @endforeach
            @endif
            
            <hr>@if(isset($d['table_types']))
            <hr><table>
                  <tr class="ttitle">
                      <td colspan="7">PAYMENT METHOD REVIEW:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$d['date']}}</td>
                </tr>
                  <tr class="theader">
                      <td>TYPE</td>
                      <td style='text-align:center'>TRANS.</td>
                      <td style='text-align:center'>TICKS.</td>
                      <td style='text-align:right'>TOTAL<br>PAID<br>(P)</td>
                      <td style='text-align:right'>TAXES<br>(T)</td>
                      <td style='text-align:right'>C.C.<br>FEE<br>(M)</td>
                      <td style='text-align:right'>PRNT<br>FEE<br>(F)</td>
                      <td style='text-align:right'>FEES<br>INC.<br>(F)</td>
                      <td style='text-align:right'>FEES<br>OVR.<br>(F)</td>
                      <td style='text-align:right'>COMM.<br>(C)</td>
                      <td style='text-align:right'>NET<br>{{$net}}</td>
                  </tr>
                  @foreach($d['table_types']['data'] as $e)
                    <tr>
                        <td>{{$e->payment_type}}</td>
                        <td style='text-align:center'>{{number_format($e->transactions)}}</td>
                        <td style='text-align:center'>{{number_format($e->tickets)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->paid,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->taxes,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->cc_fee,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->printed_fee,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees_incl,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees_over,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->commissions,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->amount,2)}}</td>
                    </tr>
                  @endforeach
                  @if(count($d['table_types']['others']))
                  <tr class="ttotal">
                        <td>Subtotals</td>
                        <td style='text-align:center'>{{number_format($d['table_types']['subtotal']['transactions'])}}</td>
                        <td style='text-align:center'>{{number_format($d['table_types']['subtotal']['tickets'])}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['subtotal']['paid'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['subtotal']['taxes'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['subtotal']['cc_fee'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['subtotal']['printed_fee'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['subtotal']['fees_incl'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['subtotal']['fees_over'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['subtotal']['commissions'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['subtotal']['amount'],2)}}</td>
                    </tr>
                  @foreach($d['table_types']['others'] as $e)
                    <tr>
                        <td>{{$e->payment_type}}</td>
                        <td style='text-align:center'>{{number_format($e->transactions)}}</td>
                        <td style='text-align:center'>{{number_format($e->tickets)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->paid,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->taxes,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->cc_fee,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->printed_fee,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees_incl,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees_over,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->commissions,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->amount,2)}}</td>
                    </tr>
                  @endforeach
                  @endif
                    <tr class="ttotal">
                        <td>Subtotals</td>
                        <td style='text-align:center'>{{number_format($d['table_types']['total']['transactions'])}}</td>
                        <td style='text-align:center'>{{number_format($d['table_types']['total']['tickets'])}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['total']['paid'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['total']['taxes'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['total']['cc_fee'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['total']['printed_fee'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['total']['fees_incl'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['total']['fees_over'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['total']['commissions'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['total']['amount'],2)}}</td>
                    </tr>
                    @foreach($d['table_debits']['data'] as $e)
                    <tr>
                        <td>{{$e->status}}</td>
                        <td style='text-align:center'>{{number_format($e->transactions)}}</td>
                        <td style='text-align:center'>{{number_format($e->tickets)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->paid,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->taxes,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->cc_fee,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->printed_fee,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees_incl,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees_over,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->commissions,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->amount,2)}}</td>
                    </tr>
                    @endforeach
                    <tr class="ttotal">
                        <td>Totals</td>
                        <td style='text-align:center'>{{number_format($d['table_types']['total']['transactions']+$d['table_debits']['total']['transactions'])}}</td>
                        <td style='text-align:center'>{{number_format($d['table_types']['total']['tickets']+$d['table_debits']['total']['tickets'])}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['total']['paid']+$d['table_debits']['total']['paid'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['total']['taxes']+$d['table_debits']['total']['taxes'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['total']['cc_fee']+$d['table_debits']['total']['cc_fee'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['total']['printed_fee']+$d['table_debits']['total']['printed_fee'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['total']['fees_incl']+$d['table_debits']['total']['fees_incl'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['total']['fees_over']+$d['table_debits']['total']['fees_over'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['total']['commissions']+$d['table_debits']['total']['commissions'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_types']['total']['amount']+$d['table_debits']['total']['amount'],2)}}</td>
                    </tr>
              </table>
              @endif

              @if(isset($d['table_channels']))
              <hr><table>
                  <tr class="ttitle">
                      <td colspan="7">CHANNEL SALES:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$d['date']}}</td>
                </tr>
                  <tr class="theader">
                      <td>CHANNEL</td>
                      <td style='text-align:center'>TRANS.</td>
                      <td style='text-align:center'>TICKS.</td>
                      <td style='text-align:right'>TOTAL<br>PAID<br>(P)</td>
                      <td style='text-align:right'>TAXES<br>(T)</td>
                      <td style='text-align:right'>C.C.<br>FEE<br>(M)</td>
                      <td style='text-align:right'>PRNT<br>FEE<br>(F)</td>
                      <td style='text-align:right'>FEES<br>INC.<br>(F)</td>
                      <td style='text-align:right'>FEES<br>OVR.<br>(F)</td>
                      <td style='text-align:right'>COMM.<br>(C)</td>
                      <td style='text-align:right'>NET<br>{{$net}}</td>
                  </tr>
                  @foreach($d['table_channels']['data'] as $e)
                    <tr>
                        <td>{{$e->channel}}</td>
                        <td style='text-align:center'>{{number_format($e->transactions)}}</td>
                        <td style='text-align:center'>{{number_format($e->tickets)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->paid,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->taxes,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->cc_fee,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->printed_fee,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees_incl,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees_over,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->commissions,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->amount,2)}}</td>
                    </tr>
                  @endforeach
                    <tr class="ttotal">
                        <td>Subtotals</td>
                        <td style='text-align:center'>{{number_format($d['table_channels']['total']['transactions'])}}</td>
                        <td style='text-align:center'>{{number_format($d['table_channels']['total']['tickets'])}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_channels']['total']['paid'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_channels']['total']['taxes'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_channels']['total']['cc_fee'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_channels']['total']['printed_fee'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_channels']['total']['fees_incl'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_channels']['total']['fees_over'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_channels']['total']['commissions'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_channels']['total']['amount'],2)}}</td>
                    </tr>
                    @foreach($d['table_debits']['data'] as $e)
                    <tr>
                        <td>{{$e->status}}</td>
                        <td style='text-align:center'>{{number_format($e->transactions)}}</td>
                        <td style='text-align:center'>{{number_format($e->tickets)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->paid,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->taxes,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->cc_fee,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->printed_fee,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees_incl,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees_over,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->commissions,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->amount,2)}}</td>
                    </tr>
                    @endforeach
                    <tr class="ttotal">
                        <td>Totals</td>
                        <td style='text-align:center'>{{number_format($d['table_channels']['total']['transactions']+$d['table_debits']['total']['transactions'])}}</td>
                        <td style='text-align:center'>{{number_format($d['table_channels']['total']['tickets']+$d['table_debits']['total']['tickets'])}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_channels']['total']['paid']+$d['table_debits']['total']['paid'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_channels']['total']['taxes']+$d['table_debits']['total']['taxes'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_channels']['total']['cc_fee']+$d['table_debits']['total']['cc_fee'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_channels']['total']['printed_fee']+$d['table_debits']['total']['printed_fee'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_channels']['total']['fees_incl']+$d['table_debits']['total']['fees_incl'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_channels']['total']['fees_over']+$d['table_debits']['total']['fees_over'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_channels']['total']['commissions']+$d['table_debits']['total']['commissions'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_channels']['total']['amount']+$d['table_debits']['total']['amount'],2)}}</td>
                    </tr>
              </table>
              @endif
              
              @if(isset($d['table_tickets']))
              <hr><table>
                  <tr class="ttitle">
                      <td colspan="7">TICKETS REVIEW:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$d['date']}}</td>
                </tr>
                  <tr class="theader">
                        <td>TYPE</td>
                        <td style='text-align:center'>TRANS.</td>
                        <td style='text-align:center'>TICKS.</td>
                        <td style='text-align:right'>TOTAL<br>PAID<br>(P)</td>
                        <td style='text-align:right'>TAXES<br>(T)</td>
                        <td style='text-align:right'>C.C.<br>FEE<br>(M)</td>
                        <td style='text-align:right'>PRNT<br>FEE<br>(F)</td>
                        <td style='text-align:right'>FEES<br>INC.<br>(F)</td>
                        <td style='text-align:right'>FEES<br>OVR.<br>(F)</td>
                        <td style='text-align:right'>COMM.<br>(C)</td>
                        <td style='text-align:right'>NET<br>{{$net}}</td>
                  </tr>
                  @foreach($d['table_tickets']['data'] as $e)
                    <tr>
                        <td>{{$e->ticket_type}} - {{$e->title}}</td>
                        <td style='text-align:center'>{{number_format($e->transactions)}}</td>
                        <td style='text-align:center'>{{number_format($e->tickets)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->paid,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->taxes,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->cc_fee,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->printed_fee,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees_incl,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees_over,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->commissions,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->amount,2)}}</td>
                    </tr>
                  @endforeach
                    <tr class="ttotal">
                        <td>Subtotals</td>
                        <td style='text-align:center'>{{number_format($d['table_tickets']['total']['transactions'])}}</td>
                        <td style='text-align:center'>{{number_format($d['table_tickets']['total']['tickets'])}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_tickets']['total']['paid'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_tickets']['total']['taxes'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_tickets']['total']['cc_fee'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_tickets']['total']['printed_fee'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_tickets']['total']['fees_incl'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_tickets']['total']['fees_over'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_tickets']['total']['commissions'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_tickets']['total']['amount'],2)}}</td>
                    </tr>
                    @foreach($d['table_debits']['data'] as $e)
                    <tr>
                        <td>{{$e->status}}</td>
                        <td style='text-align:center'>{{number_format($e->transactions)}}</td>
                        <td style='text-align:center'>{{number_format($e->tickets)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->paid,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->taxes,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->cc_fee,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->printed_fee,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees_incl,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees_over,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->commissions,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->amount,2)}}</td>
                    </tr>
                    @endforeach
                    <tr class="ttotal">
                        <td>Totals</td>
                        <td style='text-align:center'>{{number_format($d['table_tickets']['total']['transactions']+$d['table_debits']['total']['transactions'])}}</td>
                        <td style='text-align:center'>{{number_format($d['table_tickets']['total']['tickets']+$d['table_debits']['total']['tickets'])}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_tickets']['total']['paid']+$d['table_debits']['total']['paid'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_tickets']['total']['taxes']+$d['table_debits']['total']['taxes'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_tickets']['total']['cc_fee']+$d['table_debits']['total']['cc_fee'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_tickets']['total']['printed_fee']+$d['table_debits']['total']['printed_fee'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_tickets']['total']['fees_incl']+$d['table_debits']['total']['fees_incl'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_tickets']['total']['fees_over']+$d['table_debits']['total']['fees_over'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_tickets']['total']['commissions']+$d['table_debits']['total']['commissions'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_tickets']['total']['amount']+$d['table_debits']['total']['amount'],2)}}</td>
                    </tr>
              </table>
              @endif
              
              @if(isset($d['table_sellers']))
              <hr><table>
                  <tr class="ttitle">
                      <td colspan="7">POS SELLERS REVIEW:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$d['date']}}</td>
                </tr>
                  <tr class="theader">
                        <td>SELLER<br>EMAIL</td>
                        <td>EVENT<br>DATE</td>
                        <td>VENUES</td>
                        <td>EVENTS</td>
                        <td>PAYMENT</td>
                        <td>TICKET</td>
                        <td style='text-align:center'>TRANS.</td>
                        <td style='text-align:center'>TICKS.</td>
                        <td style='text-align:right'>TOTAL<br>PAID<br>(P)</td>
                        <td style='text-align:right'>TAXES<br>(T)</td>
                        <td style='text-align:right'>C.C.<br>FEE<br>(M)</td>
                        <td style='text-align:right'>PRNT<br>FEE<br>(F)</td>
                        <td style='text-align:right'>FEES<br>INC.<br>(F)</td>
                        <td style='text-align:right'>FEES<br>OVR.<br>(F)</td>
                        <td style='text-align:right'>COMM.<br>(C)</td>
                        <td style='text-align:right'>NET<br>{{$net}}</td>
                  </tr>
                  @foreach($d['table_sellers']['data'] as $e)
                    <tr>
                        <td>{{$e->email}}</td>
                        <td>{{$e->show_time}}</td>
                        <td>{{$e->venue}}</td>
                        <td>{{$e->event}}</td>
                        <td>{{$e->payment_type}}</td>
                        <td>{{$e->ticket_type}} - {{$e->title}}</td>
                        <td style='text-align:center'>{{number_format($e->transactions)}}</td>
                        <td style='text-align:center'>{{number_format($e->tickets)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->paid,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->taxes,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->cc_fee,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->printed_fee,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees_incl,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees_over,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->commissions,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->amount,2)}}</td>
                    </tr>
                  @endforeach
                    <tr class="ttotal">
                        <td colspan="6">Subtotals</td>
                        <td style='text-align:center'>{{number_format($d['table_sellers']['total']['transactions'])}}</td>
                        <td style='text-align:center'>{{number_format($d['table_sellers']['total']['tickets'])}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_sellers']['total']['paid'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_sellers']['total']['taxes'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_sellers']['total']['cc_fee'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_sellers']['total']['printed_fee'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_sellers']['total']['fees_incl'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_sellers']['total']['fees_over'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_sellers']['total']['commissions'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_sellers']['total']['amount'],2)}}</td>
                    </tr>
                    @foreach($d['table_debits']['data'] as $e)
                    <tr>
                        <td colspan="6">{{$e->status}}</td>
                        <td style='text-align:center'>{{number_format($e->transactions)}}</td>
                        <td style='text-align:center'>{{number_format($e->tickets)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->paid,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->taxes,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->cc_fee,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->printed_fee,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees_incl,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees_over,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->commissions,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->amount,2)}}</td>
                    </tr>
                    @endforeach
                    <tr class="ttotal">
                        <td colspan="6">Totals</td>
                        <td style='text-align:center'>{{number_format($d['table_sellers']['total']['transactions']+$d['table_debits']['total']['transactions'])}}</td>
                        <td style='text-align:center'>{{number_format($d['table_sellers']['total']['tickets']+$d['table_debits']['total']['tickets'])}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_sellers']['total']['paid']+$d['table_debits']['total']['paid'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_sellers']['total']['taxes']+$d['table_debits']['total']['taxes'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_sellers']['total']['cc_fee']+$d['table_debits']['total']['cc_fee'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_sellers']['total']['printed_fee']+$d['table_debits']['total']['printed_fee'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_sellers']['total']['fees_incl']+$d['table_debits']['total']['fees_incl'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_sellers']['total']['fees_over']+$d['table_debits']['total']['fees_over'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_sellers']['total']['commissions']+$d['table_debits']['total']['commissions'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_sellers']['total']['amount']+$d['table_debits']['total']['amount'],2)}}</td>
                    </tr>
              </table>
              @endif
              
              @if(isset($d['table_refunds']))
              <hr><table>
                  <tr class="ttitle">
                      <td colspan="7">DEBITS REVIEW:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$d['date']}}</td>
                </tr>
                  <tr class="theader">
                        <td>DEBIT<br>ON</td>
                        <td>STATUS</td>
                        <td>VENUES</td>
                        <td>EVENTS</td>
                        <td>PAYMENT</td>
                        <td>TICKET</td>
                        <td style='text-align:center'>TRANS.</td>
                        <td style='text-align:center'>TICKS.</td>
                        <td style='text-align:right'>TOTAL<br>PAID<br>(P)</td>
                        <td style='text-align:right'>TAXES<br>(T)</td>
                        <td style='text-align:right'>C.C.<br>FEE<br>(M)</td>
                        <td style='text-align:right'>PRNT<br>FEE<br>(F)</td>
                        <td style='text-align:right'>FEES<br>INC.<br>(F)</td>
                        <td style='text-align:right'>FEES<br>OVR.<br>(F)</td>
                        <td style='text-align:right'>COMM.<br>(C)</td>
                        <td style='text-align:right'>NET<br>{{$net}}</td>
                  </tr>
                  @foreach($d['table_refunds']['data'] as $e)
                    <tr>
                        <td>{{$e->created}}</td>
                        <td>{{$e->status}}</td>
                        <td>{{$e->venue}}</td>
                        <td>{{$e->event}}</td>
                        <td>{{$e->payment_type}}</td>
                        <td>{{$e->ticket_type}} - {{$e->title}}</td>
                        <td style='text-align:center'>{{number_format($e->transactions)}}</td>
                        <td style='text-align:center'>{{number_format($e->tickets)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->paid,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->taxes,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->cc_fee,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->printed_fee,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees_incl,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees_over,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->commissions,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->amount,2)}}</td>
                    </tr>
                  @endforeach
                    <tr class="ttotal">
                        <td colspan="6">Totals</td>
                        <td style='text-align:center'>{{number_format($d['table_refunds']['total']['transactions'])}}</td>
                        <td style='text-align:center'>{{number_format($d['table_refunds']['total']['tickets'])}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_refunds']['total']['paid'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_refunds']['total']['taxes'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_refunds']['total']['cc_fee'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_refunds']['total']['printed_fee'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_refunds']['total']['fees_incl'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_refunds']['total']['fees_over'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_refunds']['total']['commissions'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_refunds']['total']['amount'],2)}}</td>
                    </tr>
              </table>
              @endif
              
              @if(isset($d['table_shows']))
              <hr><hr><table>
                  <tr class="ttitle">
                      <td colspan="9">SHOWS BREAKDOWN ON PERIOD:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$d['date']}}</td>
                  </tr>
                  <tr class="theader">
                        <td width="25%" align='left'>SHOWS</td>
                        <td style='text-align:center'>DATE<br>TIME</td>
                        <td style='text-align:center'>TICKET</td>
                        <td style='text-align:center'>TRANS.</td>
                        <td style='text-align:center'>TICKS.</td>
                        <td style='text-align:right'>TOTAL<br>PAID<br>(P)</td>
                        <td style='text-align:right'>TAXES<br>(T)</td>
                        <td style='text-align:right'>C.C.<br>FEE<br>(M)</td>
                        <td style='text-align:right'>PRNT<br>FEE<br>(F)</td>
                        <td style='text-align:right'>FEES<br>INC.<br>(F)</td>
                        <td style='text-align:right'>FEES<br>OVR.<br>(F)</td>
                        <td style='text-align:right'>COMM.<br>(C)</td>
                        <td style='text-align:right'>NET<br>{{$net}}</td>
                  </tr>
                  @foreach($d['table_shows']['data'] as $e)
                    <tr>
                          <td width="25%" align='left'>{{$e->name}}</td>
                          <td style='text-align:center'>{{$e->show_time}}</td>
                          <td style='text-align:center'>{{$e->ticket_type}} - {{$e->title}}</td>
                          <td style='text-align:center'>{{number_format($e->transactions)}}</td>
                          <td style='text-align:center'>{{number_format($e->tickets)}}</td>
                          <td style='text-align:right'>$ {{number_format($e->paid,2)}}</td>
                          <td style='text-align:right'>$ {{number_format($e->taxes,2)}}</td>
                          <td style='text-align:right'>$ {{number_format($e->cc_fee,2)}}</td>
                          <td style='text-align:right'>$ {{number_format($e->printed_fee,2)}}</td>
                          <td style='text-align:right'>$ {{number_format($e->fees_incl,2)}}</td>
                          <td style='text-align:right'>$ {{number_format($e->fees_over,2)}}</td>
                          <td style='text-align:right'>$ {{number_format($e->commissions,2)}}</td>
                          <td style='text-align:right'>$ {{number_format($e->amount,2)}}</td>
                    </tr>
                  @endforeach
                  <tr class="ttotal">
                        <td colspan="3">Subtotals</td>
                        <td style='text-align:center'>{{number_format($d['table_shows']['total']['transactions'])}}</td>
                        <td style='text-align:center'>{{number_format($d['table_shows']['total']['tickets'])}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_shows']['total']['paid'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_shows']['total']['taxes'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_shows']['total']['cc_fee'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_shows']['total']['printed_fee'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_shows']['total']['fees_incl'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_shows']['total']['fees_over'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_shows']['total']['commissions'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_shows']['total']['amount'],2)}}</td>
                      </tr>
                    @foreach($d['table_debits']['data'] as $e)
                    <tr>
                        <td colspan="3">{{$e->status}}</td>
                        <td style='text-align:center'>{{number_format($e->transactions)}}</td>
                        <td style='text-align:center'>{{number_format($e->tickets)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->paid,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->taxes,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->cc_fee,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->printed_fee,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees_incl,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->fees_over,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->commissions,2)}}</td>
                        <td style='text-align:right'>$ {{number_format($e->amount,2)}}</td>
                    </tr>
                    @endforeach
                    <tr class="ttotal">
                        <td colspan="3">Totals</td>
                        <td style='text-align:center'>{{number_format($d['table_shows']['total']['transactions']+$d['table_debits']['total']['transactions'])}}</td>
                        <td style='text-align:center'>{{number_format($d['table_shows']['total']['tickets']+$d['table_debits']['total']['tickets'])}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_shows']['total']['paid']+$d['table_debits']['total']['paid'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_shows']['total']['taxes']+$d['table_debits']['total']['taxes'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_shows']['total']['cc_fee']+$d['table_debits']['total']['cc_fee'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_shows']['total']['printed_fee']+$d['table_debits']['total']['printed_fee'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_shows']['total']['fees_incl']+$d['table_debits']['total']['fees_incl'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_shows']['total']['fees_over']+$d['table_debits']['total']['fees_over'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_shows']['total']['commissions']+$d['table_debits']['total']['commissions'],2)}}</td>
                        <td style='text-align:right'>$ {{number_format($d['table_shows']['total']['amount']+$d['table_debits']['total']['amount'],2)}}</td>
                    </tr>
              </table>
              @endif
              
            </div>
            @endforeach
        @endif
@endif
