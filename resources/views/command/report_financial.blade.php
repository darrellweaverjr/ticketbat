<html lang="en-US">
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
    <meta charset="utf-8">
    <body>
        <h3 style="text-align:center">
            <img alt="TicketBat.com" height="50" width="300" src="{{asset('/themes/img/logo-header-white.jpg')}}"/>
            <br><br>FINANCIAL REPORT
        </h3><hr>
        @foreach($tables as $t)
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
                <td width="25%" style='text-align:left'>{!!$t['total']['name']!!}</td>
                <td style='text-align:center'>{{number_format($t['total']['purchases'])}}</td>
                <td style='text-align:center'>{{number_format($t['total']['tickets'])}}</td>
                <td style='text-align:right'>$ {{number_format($t['total']['paid'],2)}}</td>
                <td style='text-align:right'>$ {{number_format($t['total']['commissions'],2)}}</td>
                <td style='text-align:right'>$ {{number_format($t['total']['fees'],2)}}</td>
                <td style='text-align:right'>$ {{number_format($t['total']['amount'],2)}}</td>
            </tr>
        </table><hr>
        @endforeach
    </body>
</html>