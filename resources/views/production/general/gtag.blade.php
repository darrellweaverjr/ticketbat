<!-- OLD SCRIPT -->
<!--  THIS APPEARS TO BE AN OLD CRAZY EGG TRACKING SCRIPT -->
<script type="text/javascript">
    setTimeout(function(){var a=document.createElement("script");
    var b=document.getElementsByTagName("script")[0];
    a.src=document.location.protocol+"//dnn506yrbagrg.cloudfront.net/pages/scripts/0025/3481.js?"+Math.floor(new Date().getTime()/3600000);
    a.async=true;a.type="text/javascript";b.parentNode.insertBefore(a,b)}, 1);
</script>

<!-- FBQ ANALYTICS -->
<script type="text/javascript">
    !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
    n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
    document,'script','https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '1412841365444572'); // Insert your pixel ID here.
    fbq('track', 'PageView');
</script>

<!-- LOAD GTAG SCRIPT -->
<script type="text/javascript">@php echo file_get_contents('https://www.googletagmanager.com/gtag/js?id='.env('GA_TRACKING_ID','UA-53779511-1')) @endphp</script>

<!-- GTAG SCRIPT TICKETBAT -->
<script type="text/javascript">
    //google analytics for ticketbat
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', {{env('GA_TRACKING_ID','UA-53779511-1')}});
    gtag('event', 'page_view', { 'send_to': {{env('GA_TRACKING_ID','UA-53779511-1')}} });

    //google analytics for ticketbat - purchase
    @if(!empty($analytics) && isset($transaction) && isset($totals))
        var items = [];
        @foreach($analytics as $k=>$a)
            items.push({
                "id": "{{$a['id']}}",
                "name": "{{$a['ticket_type']}}",
                "list_name": "{{$a['event']}} {{$a['ticket_id']}}",
                "brand": "Tickets",
                "category": "{{$a['venue']}}",
                //"variant": "Black",
                "list_position": {{$k+1}},
                "quantity": {{$a['qty']}},
                "price": '{{$a['price']}}'
            });
        @endforeach
        gtag('event', 'purchase', {
            "transaction_id": "{{$transaction}}",
            "affiliation": "TicketBat.com",
            "value": {{$totals}},
            "currency": "USD",
            "tax": 0,
            "shipping": 0,
            "items": items
        });

        //google analytics for external funnel varible/page
        @if(!empty($ua_code))
            gtag('config', 'UA-{{$ua_code}}');
            gtag('event', 'page_view', { 'send_to': 'UA-{{$ua_code}}'});
            gtag('event', 'purchase', {
                "transaction_id": "{{$transaction}}",
                "affiliation": "TicketBat.com",
                "value": {{$totals}},
                "currency": "USD",
                "tax": 0,
                "shipping": 0,
                "items": items
              });
        @endif

        //google analytics for ua conversion code for defined shows
        @if(!empty($ua_conversion_code))
            @foreach($ua_conversion_code as $sc=>$cc)
                var items = [];
                @foreach($analytics as $k=>$a)
                    @if($a['show_id']==$sc)
                        items.push({
                            "id": "{{$a['id']}}",
                            "name": "{{$a['ticket_type']}}",
                            "list_name": "{{$a['event']}} {{$a['ticket_id']}}",
                            "brand": "Tickets",
                            "category": "{{$a['venue']}}",
                            //"variant": "Black",
                            "list_position": {{$k+1}},
                            "quantity": {{$a['qty']}},
                            "price": '{{$a['price']}}'
                        });
                    @endif
                @endforeach
                gtag('config', 'UA-{{$cc['ua']}}');
                gtag('event', 'page_view', { 'send_to': 'UA-{{$cc['ua']}}'});
                gtag('event', 'purchase', {
                    "transaction_id": "{{$transaction}}",
                    "affiliation": "TicketBat.com",
                    "value": {{$cc['total']}},
                    "currency": "USD",
                    "tax": 0,
                    "shipping": 0,
                    "items": items
                  });
            @endforeach
        @endif

    @endif
</script>

<!-- PERSONALIZED CONVERSIONS CODE -->
@if(!empty($conversion_code))
    @foreach($conversion_code as $cc)
        @php echo $cc @endphp
    @endforeach
@endif