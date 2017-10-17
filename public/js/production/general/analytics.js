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
        
        //google analytics for ticketbat 
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        ga('create', 'UA-53779511-1', 'auto');
        ga('send', 'pageview');
        
        //google analytics for external funnel varible/page
        var ua_code = $('meta[name="ua-code"]').attr('content');
        if(ua_code && ua_code.length>0)
        {
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
            ga('create', 'UA-'+ua_code, 'auto');
            ga('send', 'pageview');
            ga('require', 'ecommerce');
            ga('ecommerce:send');
            ga('ecommerce:clear');
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