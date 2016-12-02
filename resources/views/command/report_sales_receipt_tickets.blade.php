<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
<meta name="viewport" content="width=610, initial-scale=1">
<style>
.rotate {
/* Safari */
-webkit-transform: rotate(-90deg);
/* Firefox */
-moz-transform: rotate(-90deg);
/* IE */
-ms-transform: rotate(-90deg);
/* Opera */
-o-transform: rotate(-90deg);
/* Internet Explorer */
filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);
}
</style>
<body>
  @if ($type && $type=='S')
        @foreach($tickets as $ticket)
        <div style='page-break-after:always;text-align:center;'>
                <div style='float:center;position:absolute;top:-23;left:38;'>
                      <div style='font-size:12px;'>${{$ticket['price_each']}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$ticket['id']}}-{{$ticket['number']}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$ticket['ticket_type']}}</div>   
                </div>
                <div style='text-align:center;width:90%;position:absolute;top:0;left:-20;'>
                        <div style='padding-top:2px;'><span style='font-size:22px'>{{$ticket['show_name']}}</span></div>
                        <div style='padding-top:2px;'><span style='font-size:12px'>at<br/></span>{{$ticket['venue_name']}}</div>
                        <div style='padding-top:3px;'><span style='font-size:12px'>on </span>{{date('l, m/d/Y',strtotime($ticket['show_time']))}} <span style='font-size:12px'>@if($ticket['time_alternative']) - @else at @endif</span>@if($ticket['time_alternative']) - @else {{date('h:i a',strtotime($ticket['show_time']))}} @endif</div>                     
                </div>
                <div class="rotate" style='font-size:12px;position:absolute;top:85;left:317;'>
                        <div style='padding-top:3px;font-size:12px;'>${{$ticket['price_each']}}</div>
                </div>
                <div class="rotate" style='font-size:12px;position:absolute;top:10;left:302;'>
                        <img src='{{$ticket['QRcode']}}' alt='TB{{$ticket['id']}}{{$ticket['user_id']}}{{$ticket['number']}}' width=70px height=70px/>
                </div>
                <div class="rotate" style='font-size:12px;position:absolute;top:-5;left:327;'>
                        <div style='padding-top:3px;font-size:12px;text-align:center;width:40px'>{{$ticket['id']}}-{{$ticket['number']}}</div>
                </div>
                <div style='width:80%;position:absolute;top:85;left:-20;font-size:12px;'>
                        <hr><div style='text-align:center;'><span>@if (!($ticket['restrictions']=='None' || $ticket['restrictions']=='Inherit'))RESTRICTIONS: {{$ticket['restrictions']}} years old to attend the event.@endif</span></div>
                </div>
        </div>
        <!--
        <div style='page-break-inside:avoid;page-break-after:always;background:white;width:610px;height:261px;margin:-40px;padding:-40px;font-size:18px;text-align:center;'>
                <div style='background:white; float:left;width:49%;position:absolute;top:-25;left:32;'>
                      <div style='padding-top:3px;font-size:12px;'>${{$ticket['price_each']}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$ticket['id']}}-{{$ticket['number']}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$ticket['ticket_type']}}</div>   
                </div>
                <div style='background:white; float:left;width:70%;position:absolute;top:0;left:-20;'>
                        <div style='padding-top:2px;'><span style='font-size:22px'>{{$ticket['show_name']}}</span></div>
                        <div style='padding-top:2px;'><span style='font-size:12px'>at<br/></span>{{$ticket['venue_name']}}</div>
                        <div style='padding-top:3px;'><span style='font-size:12px'>on </span>{{date('l, m/d/Y',strtotime($ticket['show_time']))}} <span style='font-size:12px'>@if($ticket['time_alternative']) - @else at @endif</span>@if($ticket['time_alternative']) - @else {{date('h:i a',strtotime($ticket['show_time']))}} @endif</div>                     
                </div>
                <div class="rotate" style='background:white;font-size:12px;float:right;height:31%;width:70px;margin:40px;position:absolute;top:75;left:290;'>
                        <div style='padding-top:3px;font-size:12px;'>${{$ticket['price_each']}}</div>
                </div>
                <div class="rotate" style='background:white;font-size:12px;float:right;height:31%;width:70px;margin:40px;position:absolute;top:30;left:267;'>
                        <img src='{{$ticket['QRcode']}}' alt='TB{{$ticket['id']}}{{$ticket['user_id']}}{{$ticket['number']}}' width=70px height=70px/>
                </div>
                <div class="rotate" style='background:white;font-size:12px;float:right;height:31%;width:70px;margin:40px;position:absolute;top:-15;left:290;'>
                        <div style='padding-top:3px;font-size:12px;'>{{$ticket['id']}}-{{$ticket['number']}}</div>
                </div>
                <div style='background:white;width:69%;position:absolute;top:85;left:-20;font-size:12px;'>
                        <hr><div style='text-align:center;'><span>@if (!($ticket['restrictions']=='None' || $ticket['restrictions']=='Inherit'))RESTRICTIONS: {{$ticket['restrictions']}} years old to attend the event.@endif</span></div>
                </div>
        </div>
        -->
        @endforeach
  @elseif ($type && $type=='C')
        @php $pages=array_chunk($tickets,5) @endphp
        @foreach($pages as $page)
        <div style='page-break-inside:avoid;page-break-after:always;background:white;width:610px;height:261px;text-align:center;'>
        @php $top=35 @endphp
              @foreach($page as $ticket)
              @if($top!=35)
              <div style='background:white;width:90%;position:absolute;top:{{-22+$top}}};left:30;font-size:12px;'>
                      <hr style="border: 0 none;border-top:2px dashed #322f32">
              </div>
              @endif
              <div style='background:white; float:right;width:20%;position:absolute;top:{{-15+$top}}};left:305;'>
                      <div style='padding-top:0px;'><img src='{{$ticket['QRcode']}}' alt='TB{{$ticket['id']}}{{$ticket['user_id']}}{{$ticket['number']}}' width=110px height=110px /></div>
              </div>
              <div style='background:white; float:left;width:49%;position:absolute;top:{{-10+$top}}};left:80;'>
                      <div style='padding-top:2px;'><span style='font-size:12px'></span>{{$ticket['show_name']}} ({{$ticket['ticket_type']}})</div>
                      <div style='padding-top:2px;'><span style='font-size:12px'>at<br/></span>{{$ticket['venue_name']}}</div>
                      <div style='padding-top:3px;'><span style='font-size:12px'>on </span> {{date('l, m/d/Y',strtotime($ticket['show_time']))}} </div>
                      <div style='padding-top:3px;'><span style='font-size:12px'>@if($ticket['time_alternative']) - @else at @endif</span>@if($ticket['time_alternative']) - @else {{date('h:i a',strtotime($ticket['show_time']))}} @endif</div>
                      <div style='padding-top:-5px;'><hr><span>{{$ticket['customer_name']}}</span></div>
              </div>
              <div style='background:white; float:right;width:18%;position:absolute;top:{{60+$top}}};left:315;'>
                      <div style='padding-top:2px;text-align:left;'><span style='font-size:12px;'>Paid: </span>$ {{$ticket['price_each']}} </div>
                      <div style='padding-top:2px;text-align:left;'><span style='font-size:12px;'>Ticket: </span> {{$ticket['id']}}-{{$ticket['number']}} </div>
              </div>
              <div style='background:white;width:69%;position:absolute;top:{{90+$top}}};left:80;font-size:12px;'>
                      <hr><div style='text-align:left;'><span>@if (!($ticket['restrictions']=='None' || $ticket['restrictions']=='Inherit'))RESTRICTIONS: {{$ticket['restrictions']}} years old to attend the event.@endif</span></div>
              </div>
              @php $top=$top+135 @endphp
              @endforeach
      </div>
      @endforeach
      
  @else
  @php echo $tickets @endphp
  @endif