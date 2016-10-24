<html lang="en-US">
    <head>
        <style>
            table {
                border-collapse: collapse;
            }

            table, th, td {
                border: 1px solid black;
                text-align: center;
                font-size: 15.7px;
            }
            tr:hover {
                background-color: #f5f5f5;
            }
            tr:nth-child(even) {
                background-color: #f2f2f2;
            }
        </style>  
    </head>
    <meta charset="utf-8">
    <body style="margin:-20px -20px">

        @if ($filter==0)	

        <table align="center" style="margin-top:80px;border: 2; height: 800px; page-break-inside:avoid;page-break-after:always;">       
            <tr>
                <td colspan="19" style="text-align:center;font-size: 20px;background-color: black; color:white">
                    <div class="purchase-reciept"><b>TICKETBAT FINANCIAL REPORT<br/>ADMIN</b></div>
                </td>
            </tr>
            <tr style="background-color: gray; color:white">

                <td style="width:400px;background-color: gray; color:white"></td>
                @foreach($days_this_week as $week_day=>$date)
                <td colspan="2" style="background-color: gray; color:white">{{date('m/d/Y',strtotime($date))}}<br/>{{$week_day}}
                </td>
                @endforeach
                <td colspan="2" style="background-color: gray; color:white">Week Total</td>
                <td colspan="2" style="background-color: gray; color:white">All Time Total</td>

            </tr>
            <tr style="background-color: gray; color:white">
                <td style="width:400px;background-color: gray; color:white"></td>
                @for ($x = 1; $x <= 9; $x++)
                <td style="background-color: gray; color:white">Amount</td><td style="background-color: gray; color:white">Qty</td>
                @endfor
            </tr>
            <tr>
                <td colspan="19" style="background-color: gray"></td>
            </tr>
            <!--      GROSS REVENUE      -->
            <tr>
                <td colspan="19" height="15px" style="background-color: gray; color:white">GROSS REVENUE</td>
            </tr>

            @foreach($gross_revenue as $type=>$gross)
                @if($type !='total_total')
            <tr>
                <td style="width:400px;text-align: left;font-weight: bold">                                    
                    @if($type =='week_this') Current Week 
                    @elseif($type =='week_last') Previous Week 
                    @elseif($type =='week_diff') Increase/Decrease
                    @else Increase/Decrease %
                    @endif
                </td>
                @foreach($days_this_week as $week_day=>$date)
                    @if((date('Y-m-d')>= $date || $type=='week_last') && isset($gross[$date]))                          
                        @php $revenue = number_format($gross[$date]['gross_revenue'],2); @endphp
                        @if($revenue < 0) @php $revenue = '<b style="color:red">'.$revenue.'</b>'; @endphp @endif
                        @php $qty = number_format($gross[$date]['qty']) @endphp
                        @if($qty < 0)  @php $qty = '<b style="color:red">'.$qty.'</b>' @endphp @endif
                        @php $t_revenue = number_format($gross['week_total']['gross_revenue'],2) @endphp
                        @if($t_revenue < 0)  @php $t_revenue = '<b style="color:red">'.$t_revenue.'</b>' @endphp @endif
                        @php $t_qty = number_format($gross['week_total']['qty']) @endphp
                        @if($t_qty < 0)  @php $t_qty = '<b style="color:red">'.$t_qty.'</b>' @endphp @endif  
                <td style="padding-right: 10px;text-align: right;">@if($type != 'week_perc') $ @endif {{$revenue}} @if($type == 'week_perc') %@endif</td>
                <td style="text-align: center;">{{$qty}} @if($type == 'week_perc') %@endif</td>
                    @else
                <td style="padding-right: 10px;text-align: center;">-</td>
                <td style="text-align: center;">-</td>
                    @endif
                @endforeach
                <td style="padding-right: 10px;text-align: right;font-weight: bold">@if($type != 'week_perc') $ @endif {{$t_revenue}} @if($type == 'week_perc') % @endif</td>
                <td style="text-align: center;font-weight: bold">{{$t_qty}} @if($type == 'week_perc') % @endif</td>
                @if($type=='week_this')
                <td style="padding-right: 10px;text-align: right;font-weight: bold; color:Blue  ;font-size: 18px;background-color: lightgray" rowspan="5">$ {{number_format($gross_revenue['total_total']['gross_revenue'],2)}}</td>
                <td style="text-align: center;font-weight: bold; color:Blue  ;font-size: 18px;background-color: lightgray" rowspan="5">{{number_format($gross_revenue['total_total']['qty'])}}</td>
                @endif
        </tr> 
            @endif
        @endforeach
        <tr>
            <td colspan="19" style="background-color: gray"></td>
        </tr>

        <!--      COUPONS      -->
        <tr>

            <td colspan="19" style="width:400px;text-align: left;font-weight: bold;background-color: lightgray">COUPONS</td>

        </tr> 
        @foreach($coupons as $code=>$coupon)
            @if($code != 'total')
        <tr>
            <td style="width:400px;text-align: left;">{{$code}}</td>
                @foreach($days_this_week as $week_day=>$date)
                    @if(date('Y-m-d')>= $date && isset($coupon[$date]))
            <td style="padding-right: 10px;text-align: right;">$ {{number_format($coupon[$date]['savings'],2)}}</td>
            <td style="text-align: center;">{{number_format($coupon[$date]['qty'],2)}}</td>
                    @else
            <td style="padding-right: 10px;text-align: center;">$ 0.00</td>
            <td style="text-align: center;">0</td>
                    @endif
                @endforeach
            <td style="padding-right: 10px;text-align: right;font-weight: bold">$ {{number_format($coupon['week_total']['savings'],2)}}</td>
            <td style="text-align: center;font-weight: bold;">{{number_format($coupon['week_total']['qty'])}}</td>
            <td style="padding-right: 10px;text-align: right;font-weight: bold;">$ {{number_format($coupon['total_total']['savings'],2)}}</td>
            <td style="text-align: center;font-weight: bold;">{{number_format($coupon['total_total']['qty'])}}</td>
        </tr> 
            @endif 
        @endforeach
        <tr style=";background-color: lightgray">
            <td style="width:400px;text-align: left;font-weight: bold">Coupons Total</td>
            @foreach($days_this_week as $week_day=>$date)
                @if(date('Y-m-d')>= $date && isset($coupons['total'][$date]))
            <td style="padding-right: 10px;text-align: right;font-weight: bold">$ {{number_format($coupons['total'][$date]['savings'],2)}}</td>
            <td style="text-align: center;font-weight: bold">{{number_format($coupons['total'][$date]['qty'])}}</td>
                @else
            <td style="padding-right: 10px;text-align: center;font-weight: bold">$ 0.00</td>
            <td style="text-align: center;font-weight: bold">0</td>
                @endif
            @endforeach
            <td style="padding-right: 10px;text-align: right;font-weight: bold">$ {{number_format($coupons['total']['week_total']['savings'],2)}}</td>
            <td style="text-align: center;font-weight: bold">{{number_format($coupons['total']['week_total']['qty'])}}</td>
            <td style="padding-right: 10px;text-align: right;font-weight: bold; color:Blue">$ {{number_format($coupons['total']['total_total']['savings'],2)}}</td>
            <td style="text-align: center;font-weight: bold; color:Blue">{{number_format($coupons['total']['total_total']['qty'])}}</td>
        </tr> 
        <tr>
            <td colspan="19" style="background-color: gray"></td>
        </tr>

        <!--      GROSS PROFITS      -->
        
        <tr>
            <td colspan="19" height="15px" style="background-color: gray; color:white">GROSS PROFITS</td>
        </tr>
        @foreach($gross_profit as $type=>$profit)
            @if($type !='total_total')
        <tr>
            <td colspan="19" style="width:400px;text-align: left;">
                @if($type =='week_this') Current Week 
                @elseif($type =='week_last') Previous Week 
                @elseif($type =='week_diff') Increase/Decrease
                @elseif($type =='week_perc') Increase/Decrease %
                @else
                @endif
            </td>
        </tr> 
        <tr>
            <td style="width:400px;text-align: left;">Commissions</td>
            @foreach($days_this_week as $week_day=>$date)
                @if(date('Y-m-d')>= $date && isset($profit[$date])) 
                    @php $commissions = number_format($profit[$date]['commissions'],2) @endphp
                    @if($commissions < 0)  @php $commissions = '<b style="color:red">'.$commissions.'</b>' @endphp @endif
                    @php $t_commissions = number_format($profit['week_total']['commissions'],2) @endphp
                    @if($t_commissions < 0)  @php $t_commissions = '<b style="color:red">'.$t_commissions.'</b>' @endphp @endif
        <td style="padding-right: 10px;text-align: right;">@if($type != 'week_perc') $ @endif {{$commissions}} @if($type == 'week_perc') % @endif</td>
        <td>-</td>
                @else
        <td style="padding-right: 10px;text-align: center;">-</td>
        <td>-</td>
                @endif
            @endforeach
        <td style="padding-right: 10px;text-align: right;font-weight: bold">@if($type != 'week_perc') $ @endif {{$t_commissions}} @if($type == 'week_perc') % @endif</td>
        <td>-</td>
        <td @if($type != 'week_this')style="padding-right:10px;text-align:right;font-weight:bold;color:Blue;background-color:gray;" 
             @else style="padding-right:10px;text-align:right;font-weight:bold;color:Blue;" @endif 
             colspan="2">@if($type == 'week_this')$ {{number_format($gross_profit['total_total']['commissions'],2)}} @endif</td>
        </tr>
        <tr>
            <td style="width:400px;text-align: left;">Proccessing Fees</td>
            @foreach($days_this_week as $week_day=>$date)
                @if(date('Y-m-d')>= $date && isset($profit[$date])) 
                    @php $processing_fees = number_format($profit[$date]['processing_fees'],2) @endphp
                    @if($processing_fees < 0)  @php $processing_fees = '<b style="color:red">'.$processing_fees.'</b>' @endphp @endif
                    @php $t_processing_fees = number_format($profit['week_total']['processing_fees'],2) @endphp
                    @if($t_processing_fees < 0)  @php $t_processing_fees = '<b style="color:red">'.$t_processing_fees.'</b>' @endphp @endif
            <td style="padding-right: 10px;text-align: right;">@if($type != 'week_perc') $ @endif {{$processing_fees}} @if($type == 'week_perc') % @endif</td>
        <td>-</td>
                @else
        <td style="padding-right: 10px;text-align: center;">-</td>
        <td>-</td>
                @endif 
            @endforeach
        <td style="padding-right: 10px;text-align: right;font-weight: bold">@if($type != 'week_perc') $ @endif {{$t_processing_fees}} @if($type == 'week_perc') % @endif</td>
            <td>-</td>
            <td @if($type != 'week_this')style="padding-right:10px;text-align:right;font-weight:bold;color:Blue;background-color:gray;" 
                 @else style="padding-right:10px;text-align:right;font-weight:bold;color:Blue;" @endif 
                 colspan="2">@if($type == 'week_this')$ {{number_format($gross_profit['total_total']['processing_fees'],2)}} @endif</td>
        </tr>
        <tr>
            <td style="width:400px;text-align: left;font-weight: bold">
                @if($type =='week_this') Current Week 
                @elseif($type =='week_last') Previous Week 
                @elseif($type =='week_diff') Increase/Decrease
                @elseif($type =='week_perc') Increase/Decrease %
                @else   
                @endif
                Total
            </td>
                @foreach($days_this_week as $week_day=>$date)
                    @if(date('Y-m-d')>= $date && isset($profit[$date])) 
                        @php $profitt = number_format($profit[$date]['gross_profit'],2) @endphp
                        @if($profitt < 0)  @php $profitt = '<b style="color:red">'.$profitt.'</b>' @endphp @endif
                        @php $t_profit = number_format($profit['week_total']['gross_profit'],2) @endphp
                        @if($t_profit < 0)  @php $t_profit = '<b style="color:red">'.$t_profit.'</b>' @endphp @endif
            <td style="padding-right: 10px;text-align: right;font-weight: bold">@if($type != 'week_perc') $ @endif {{$profitt}} @if($type == 'week_perc') % @endif</td>
            <td>-</td>
                    @else
            <td style="padding-right: 10px;text-align: center;">-</td>
            <td>-</td>
                    @endif
                @endforeach
        <td style="padding-right: 10px;text-align: right;font-weight: bold">@if($type != 'week_perc') $ @endif {{$t_profit}} @if($type == 'week_perc') % @endif</td>
            <td>-</td>
            <td @if($type != 'week_this')style="padding-right:10px;text-align:right;font-weight:bold;color:Blue;background-color:gray;" 
                 @else style="padding-right:10px;text-align:right;font-weight:bold;color:Blue;" @endif
                 colspan="2">@if($type == 'week_this')$ {{number_format($gross_profit['total_total']['gross_profit'],2)}} @endif</td>
        </tr>
        <tr>
            <td colspan="19" style="background-color: gray"></td>
        </tr> 
            @endif
        @endforeach

            <!--      CHARGEBACK      -->
            <tr>
                <td colspan="19" height="15px" style="background-color: gray; color:white">CHARGEBACK</td>
            </tr>
            <tr>                              
                <td style="width:400px;text-align: left;font-weight: bold">Charge Back</td>
                @foreach($charge_back as $date=>$charges)
                    @if((date('Y-m-d')>= $date || $date=='week_total') && isset($charges['price_paid']) && isset($charges['qty'])) 
                <td @if($date=='week_total')style="padding-right:10px;text-align:right;font-weight:bold;" @else style="padding-right:10px;text-align:right;" @endif>$ -{{number_format($charges['price_paid'],2)}}</td>
            <td @if($date=='week_total')style="padding-right:10px;text-align:right;font-weight:bold;" @else style="padding-right:10px;text-align:right;" @endif>{{number_format($charges['qty'])}}</td> 
                    @elseif($date!='total_total')
            <td style="padding-right: 10px;text-align: center;">$ -0.00</td>
            <td style="padding-right: 10px;text-align: center;">0</td>   
                    @endif
                @endforeach
            <td style="padding-right: 10px;text-align: right;font-weight: bold; color:Blue">$ -{{number_format($charge_back['total_total']['price_paid'],2)}}</td>
                <td style="text-align: center;font-weight: bold; color:Blue">{{number_format($charge_back['total_total']['qty'])}}</td> 
            </tr>
            <tr>
                <td colspan="19" style="background-color: gray"></td>
            </tr>  
            <tr>
                <td colspan="19" style="background-color: white;border:1px solid white;text-align:left"><i>NOTE: Only showed venues with tickets sold this week.</i></td>
            </tr>                                                
        </table> 
        @endif     
        
        <!--      VENUES      -->

        @foreach($venues as $venue_id=>$venue)
            @if(($filter == 0 || ($filter != 0 && $venue_id == $filter)) && (($venue['total_total']['qty'] != 0 && $filter==0) || $filter!=0))
        <table align="center" style="margin-top:80px; border: 2px; page-break-inside:avoid;page-break-after:always;">       
        <tr>
            <td colspan="19" style="text-align:center;font-size: 20px;background-color: black; color:white">
                    <div class="purchase-reciept"><b>TICKETBAT FINANCIAL REPORT FOR VENUES <br/>- {{strtoupper($venue['name'])}} -</b></div>
                </td>
            </tr>
            <tr style="background-color: gray; color:white">
                <td style="width:500px;background-color: gray; color:white"></td>                
                @foreach($days_this_week as $week_day=>$date)
                <td colspan="2" style="background-color: gray; color:white">{{date('m/d/Y',strtotime($date))}}<br/>{{$week_day}}
                </td>
                @endforeach
                <td colspan="2" style="background-color: gray; color:white">Week Total</td>
                <td colspan="2" style="background-color: gray; color:white">All Time Total</td>
            </tr>
            <tr style="background-color: gray; color:white">
                <td style="width:500px;background-color: gray; color:white"></td>
                @for ($x = 1; $x <= 9; $x++)
                <td style="background-color: gray; color:white">Amount</td><td style="background-color: gray; color:white">Qty</td>
                @endfor
            </tr>
            <tr>
                <td colspan="19" style="background-color: gray"></td>
            </tr>

            <tr>

                <td colspan="19" style="width:500px;text-align: left;font-weight: bold;background-color: lightgray">{{strtoupper($venue['name'])}}</td>

            </tr>
            @foreach($venue['shows'] as $show_id=>$show)
            <tr>
                <td style="width:500px;text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp;{{mb_strimwidth($show['name'],0,55,'...')}}</td>
                @foreach($days_this_week as $week_day=>$date)
                    @if(date('Y-m-d')>= $date) 
                <td style="padding-right: 10px;text-align: right;">$ {{number_format($show['this_week'][$date]['price_paid'],2)}}</td>
                <td style="text-align: center;">{{number_format($show['this_week'][$date]['qty'])}}</td>
                        @else
                <td style="padding-right: 10px;text-align: center;">-</td>
                <td style="text-align: center;">-</td>
                        @endif
                    @endforeach
                <td style="padding-right: 10px;text-align: right;font-weight: bold">$ {{number_format($show['this_week']['week_total']['price_paid'],2)}}</td>
                <td style="text-align: center;font-weight: bold">{{number_format($show['this_week']['week_total']['qty'])}}</td>
                <td style="padding-right: 10px;text-align: right;font-weight: bold; color:RoyalBlue ">$ {{number_format($show['total_total']['price_paid'],2)}}</td>
                <td style="text-align: center;font-weight: bold; color:RoyalBlue ">{{number_format($show['total_total']['qty'])}}</td>
            </tr>
                @if(isset($show['this_week']['show_time']))
                    @foreach($show['this_week']['show_time'] as $st_time=>$st)
            <tr>
                <td style="width:500px;text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$st_time}}</td>
                        @foreach($days_this_week as $week_day=>$date)
                            @if(date('Y-m-d')>= $date) 
                <td style="padding-right: 10px;text-align: right;">$ {{number_format($st[$date]['price_paid'],2)}}</td>
                <td style="text-align: center;">{{number_format($st[$date]['qty'],2)}}</td>
                            @else
                <td style="padding-right: 10px;text-align: center;">-</td>
                <td style="text-align: center;">-</td>
                            @endif
                        @endforeach
            <td style="padding-right: 10px;text-align: right;font-weight: bold">$ {{number_format($st['week_total']['price_paid'],2)}}</td>
                <td style="text-align: center;font-weight: bold">{{number_format($st['week_total']['qty'])}}</td>
                <td style="padding-right: 10px;text-align: right;font-weight: bold; color:DodgerBlue">$ {{number_format($st['total_total']['price_paid'],2)}}</td>
                <td style="text-align: center;font-weight: bold; color:DodgerBlue">{{number_format($st['total_total']['qty'])}}</td>
            </tr>
                    @endforeach     
                @endif
            @endforeach
            <tr style="font-weight: bold;background-color: lightgray">
                <td style="width:500px;text-align: left;">{{$venue['name']}} Charge Back</td>
            
            @foreach($venue['this_week'] as $date=>$subtotals)
                @if(date('Y-m-d')>= $date || $date=='week_total') 
                <td style="padding-right: 10px;text-align: right;">$ -{{number_format($subtotals['chargeback_amount'],2)}}</td>
                <td style="text-align: center;">{{number_format($subtotals['chargeback_qty'])}}</td>
                    @else
                <td style="padding-right: 10px;text-align: center;">-</td>
                <td style="text-align: center;">-</td>
                @endif
            @endforeach
            <td style="padding-right: 10px;text-align: right;; color:Blue;background-color: lightgray">$ -{{number_format($venue['total_total']['chargeback_amount'],2)}}</td>
                <td style="text-align: center; color:Blue;background-color: lightgray">{{number_format($venue['total_total']['chargeback_qty'])}}</td>                                
            </tr>
            <tr style="font-weight: bold;background-color: lightgray">
                <td style="width:500px;text-align: left;">{{$venue['name']}} Gross Revenue<br/>{{$venue['name']}} Commissions<br/>{{$venue['name']}} Processing Fees<br/>{{$venue['name']}} Gross Profit</td>
            @foreach($venue['this_week'] as $date=>$subtotals)
                @if(date('Y-m-d')>= $date || $date=='week_total') 
                <td style="padding-right: 10px;text-align: right;">
                    $ {{number_format($subtotals['gross_revenue'],2)}}<br/>
                    $ {{number_format($subtotals['commissions'],2)}}<br/>
                    $ {{number_format($subtotals['processing_fees'],2)}}<br/>
                    $ {{number_format($subtotals['gross_profit'],2)}}
                </td>
                <td style="text-align: center;">{{number_format($subtotals['qty'])}}</td>
                    @else
                <td style="padding-right: 10px;text-align: center;">-</td>
                <td style="text-align: center;">-</td>
                @endif
            @endforeach
                <td style="padding-right: 10px;text-align: right;; color:Blue;background-color: lightgray">
                    $ {{number_format($venue['total_total']['price_paid'],2)}}<br/>
                    $ {{number_format($venue['total_total']['commission'],2)}}<br/>
                    $ {{number_format($venue['total_total']['processing_fee'],2)}}<br/>
                    $ {{number_format($venue['total_total']['gross_profit'],2)}}
                </td>
                <td style="text-align: center; color:Blue;background-color: lightgray">{{number_format($venue['total_total']['qty'])}}</td>                                
            </tr>
            <tr>
                <td colspan="19" style="background-color: gray"></td>                                
            </tr>
        </table>    
            @endif
        @endforeach 
    </body> 
</html>    