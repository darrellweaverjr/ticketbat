var Analytics = function () {    
    var initAnalytics = function () {
        //old script    
        setTimeout(function(){var a=document.createElement("script");
        var b=document.getElementsByTagName("script")[0];
        a.src=document.location.protocol+"//dnn506yrbagrg.cloudfront.net/pages/scripts/0025/3481.js?"+Math.floor(new Date().getTime()/3600000);
        a.async=true;a.type="text/javascript";b.parentNode.insertBefore(a,b)}, 1);

        //fbq analytics
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
        n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
        document,'script','https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '1412841365444572'); // Insert your pixel ID here.
        fbq('track', 'PageView');
        
        //load values from content header
        var ua_code = $('meta[name="ua-code"]').attr('content');
        var ua_conversion_code = $('meta[name="ua-conversion_code"]').attr('content');
        var analytics = $('meta[name="analytics"]').attr('content');
        var transaction = $('meta[name="transaction"]').attr('content');
        var totals = $('meta[name="totals"]').attr('content');
        
        //google analytics for ticketbat 
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        ga('create', 'UA-53779511-1', 'auto');
        ga('send', 'pageview');
        //add ecomerce
        if(transaction !='' && va.total != '')
        {
            ga('require', 'ecommerce');
            ga('ecommerce:addTransaction', {
              'id': transaction,                     
              'affiliation': 'TicketBat.com',  
              'revenue': totals,
              'currency': 'USD'               
            });
            //loop items
            if(analytics && analytics.length>0)
            $.each(analytics,function(k, v) {
                ga('ecommerce:addItem', {
                    'id': transaction,                    
                    'name': v.ticket_type,    
                    'sku': v.event+' '+v.ticket_id,               
                    'category': v.venue,        
                    'price': v.price_paid,                
                    'quantity': v.qty,
                    'currency': 'USD'
                });
            });
            ga('ecommerce:send');
            ga('ecommerce:clear');
        }
        
        //google analytics for external funnel varible/page
        if(ua_code && ua_code.length>0)
        {
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
            ga('create', 'UA-'+ua_code, 'auto');
            ga('send', 'pageview');
            ga('require', 'ecommerce');
            //add ecomerce
            if(transaction !='' && va.total != '')
            {
                ga('ecommerce:addTransaction', {
                  'id': transaction,                     
                  'affiliation': 'TicketBat.com',  
                  'revenue': totals,
                  'currency': 'USD'               
                });
                //loop items
                if(analytics && analytics.length>0)
                $.each(analytics,function(k, v) {
                    ga('ecommerce:addItem', {
                        'id': transaction,                    
                        'name': v.ticket_type,    
                        'sku': v.event+' '+v.ticket_id,               
                        'category': v.venue,        
                        'price': v.price_paid,                
                        'quantity': v.qty,
                        'currency': 'USD'
                    });
                });
            }
            ga('ecommerce:send');
            ga('ecommerce:clear');
        }
        
        //google analytics for ua conversion code for defined shows
        if(ua_conversion_code && ua_conversion_code.length>0)
        {
            $.each(ua_conversion_code,function(ka, va) {
            
                (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
                ga('create', 'UA-'+va.ua, 'auto');
                ga('send', 'pageview');
                ga('require', 'ecommerce');
                //add ecomerce
                if(transaction !='')
                {
                    ga('ecommerce:addTransaction', {
                      'id': transaction,                     
                      'affiliation': 'TicketBat.com',  
                      'revenue': va.total,
                      'currency': 'USD'               
                    });
                    //loop items
                    if(analytics && analytics.length>0)
                    $.each(analytics,function(k, v) {
                        if(v.show_id == ka)
                        {
                            ga('ecommerce:addItem', {
                                'id': transaction,                    
                                'name': v.ticket_type,    
                                'sku': v.event+' '+v.ticket_id,               
                                'category': v.venue,        
                                'price': v.price_paid,                
                                'quantity': v.qty,
                                'currency': 'USD'
                            });
                        }
                    });
                }
                ga('ecommerce:send');
                ga('ecommerce:clear');
                
            });
        }
    }
    return {
        //main function to initiate the module
        init: function () {
            initAnalytics();        
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    Analytics.init();
});