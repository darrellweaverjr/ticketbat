@if($format == 'csv')
"#","STATUS","ORDER","EVENT NAME","SHOW DATE","DATE PURCHASED","CUSTOMER NAME","CUSTOMER ADDRESS","CUSTOMER PHONE","EMAIL","QTY","CODE","DESCRIPTION","SAVED","AMOUNT PAID","SHARED TO"
@if(isset($data['purchases']))
@foreach ($data['purchases'] as $n => $p)
"{{$n+1}}","{{$p['p_status']}}","{{$p['id']}}","{{$p['event_name']}}","{{date('m/d/Y g:ia',strtotime($p['show_time']))}}","{{date('m/d/Y g:ia',strtotime($p['created']))}}","{{$p['customer_name']}}","{{$p['address']}}","{{$p['phone']}}","{{$p['email']}}","{{$p['quantity']}}","{{$p['code']}}","{{$p['description']}}","@if(!isset($p['savings'])) - @else ${{money_format('%(#10n',$p['savings'])}} @endif","${{money_format('%(#10n',$p['amount'])}}","{{$p['gifts']}}"
@endforeach
@endif
@elseif($format == 'pdf')
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
            .ttitle {
                font-weight:bold;
                font-size:13px!important;
            }
        </style>
    </head>
    <body>
        <h3 style="text-align:center">
            <img alt="TicketBat.com" height="50" widtd="300" src="https://www.ticketbat.com/themes/img/logo-header-white.jpg"/>
            <br><br>MANIFEST REPORT
        </h3><hr>
        <p class="ttitle">The following tickets have been purchased for "{{$data['name']}}" on "{{date('m/d/Y g:ia',strtotime($data['show_time']))}}"</p>
        <table>
            <tr class="theader">
                <td></td>
                <td>STATUS</td>
                <td>ORDER</td>
                <td>DATE PURCHASED</td>
                <td>CUSTOMER NAME</td>
                <td>CUSTOMER ADDRESS</td>
                <td>CUSTOMER PHONE</td>
                <td>EMAIL</td>
                <td>QTY</td>
                <td>CODE</td>
                <td>DESCRIPTION</td>
                <td style='text-align:right'>SAVED</td>
                <td style='text-align:right'>AMOUNT</td>
                <td style='text-align:center'>SHARED TO</td>
            </tr>
        @if(isset($data['purchases']))
        @foreach ($data['purchases'] as $n => $p)
            <tr>
                <td>{{$n+1}}</td>
                <td>{{$p['p_status']}}</td>
                <td>{{$p['id']}}</td>
                <td>{{date('m/d/Y g:ia',strtotime($p['created']))}}</td>
                <td>{{$p['customer_name']}}</td>
                <td>{{$p['address']}}</td>
                <td>{{$p['phone']}}</td>
                <td>{{$p['email']}}</td>
                <td>{{$p['quantity']}}</td>
                <td>{{$p['code']}}</td>
                <td>{{$p['description']}}</td>
                <td style='text-align:right'>@if(!isset($p['savings'])) - @else $ {{money_format('%(#10n',$p['savings'])}} @endif</td>
                <td style='text-align:right'>$ {{money_format('%(#10n',$p['amount'])}}</td>
                <td style='text-align:center'>{{$p['gifts']}}</td>
            </tr>
            @endforeach
            @endif
        </table>
        <p class="ttitle">Generated on: {{date('m/d/Y g:ia')}}</p>
    </body>
</html>
@else
@php echo $data @endphp
@endif
