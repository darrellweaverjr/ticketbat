var ScrollToTop = function () {    
    var initScroll = function () {
        // When the user scrolls down 20px from the top of the document, show the button
        window.onscroll = function() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                document.getElementsByClassName('scroll-to-top')[0].style.display = 'block';
            } else {
                document.getElementsByClassName('scroll-to-top')[0].style.display = 'none';
            }
        };
        $('.scroll-to-top').on('click',function(){
            document.body.scrollTop = 0; // For Chrome, Safari and Opera 
            document.documentElement.scrollTop = 0; // For IE and Firefox
        });
    }
    return {
        //main function to initiate the module
        init: function () {
            initScroll();        
        }
    };
}();
//*****************************************************************************************
var Logout = function () {    
    var initLogout = function () {
        //function logout on close modal
        $('#btn_logout').on('click', function(ev) {
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/production/user/logout', 
                success: function(data) {
                    if(data.success) 
                        location.reload();
                    else{
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error",
                            showConfirmButton: true
                        });
                    }
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to logout from the system.",
                        html: true,
                        type: "error",
                        showConfirmButton: true
                    });
                }
            }); 
        });
    }
    return {
        //main function to initiate the module
        init: function () {
            initLogout();        
        }
    };
}();
//*****************************************************************************************
var ShoppingcartQtyItems = function () {    
    var initQty = function () {
        //function to autoload qty of items into session cart
        var time = $('#timerClock').data('countdown');
        jQuery.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: 'POST',
            url: '/production/shoppingcart/count', 
            success: function(data) {
                if(data.success) 
                {
                    $('#shoppingcart_qty_items').html(data.qty_items);
                    if(data.qty_items>0)
                    {
                        Countdown.init();
                        $('#continue_buy_checkout_msg').css('display','block');
                    }
                    else
                    {
                        if(time!='')
                            Countdown.close();
                    }
                }
                else
                {
                    $('#shoppingcart_qty_items').html(0);
                    if(time!='')
                        Countdown.close();
                } 
            },
            error: function(){
                $('#shoppingcart_qty_items').html(0);
                if(time!='')
                    Countdown.close();
            }
        }); 
    }
    return {
        //main function to initiate the module
        init: function () {
            initQty();        
        }
    };
}();
//*****************************************************************************************
var Countdown = function () {    
    var initCount = function () {
        var time = $('#timerClock').data('countdown');
        if(time!='')
        {
            $('#timerClock').html(time);
            setTimeout(keepCount, 1000); 
            
            $('#timerClockPanel').css('display','block'); 
        } 
        else
        {
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/production/shoppingcart/countdown', 
                data: {status:1}, 
                success: function(data) {
                    if(data.success) 
                    {
                        $('#timerClock').html(data.init);
                        setTimeout(keepCount, 1000); 
                        
                        $('#timerClockPanel').css('display','block'); 
                    }    
                }
            }); 
        }
    }
    var resetCount = function () {
        jQuery.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: 'POST',
            url: '/production/shoppingcart/countdown', 
            data: {status:1}, 
            success: function(data) {
                if(data.success) 
                {
                    $('#timerClock').html(data.init);
                    $('#timerClock').data('countdown',data.init);
                    setTimeout(keepCount, 1000); 
                }    
            }
        }); 
    }
    var keepCount = function () {
        var time = $('#timerClock').html();
        if(time!='' && time!='00:00')
        {
            var value = new Date("01/01/2017 12:" + time);
            value.setSeconds(value.getSeconds() - 1);
            var minutes = (value.getMinutes()>9)? value.getMinutes() : '0'+value.getMinutes();
            var seconds = (value.getSeconds()>9)? value.getSeconds() : '0'+value.getSeconds();
            $('#timerClock').html(minutes+':'+seconds);
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/production/shoppingcart/countdown', 
                data: {status:0}
            }); 
            setTimeout(keepCount, 1000); 
        }
        else
        {
            swal({
                title: "You have run out of time for checkout.<br>Would you like to continue?",
                //text: "You have run out of time for checkout, would you like to continue?",
                html: true,
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Reset",
                cancelButtonText: "Dismiss",
                closeOnConfirm: false,
                closeOnCancel: true
              },
              function(isConfirm) {
                if (isConfirm) 
                {
                    swal({
                        title: "Information",
                        text: "The countdown has been reset.<br>You have "+$('#timerClock').data('countdown')+" minutes more to make the payment.",
                        html:true,
                        type: "info",
                        timer: 3000,
                        showConfirmButton: false
                    },function(){
                        window.location.href = '/production/shoppingcart';
                    });
                }
                else
                {
                    jQuery.ajax({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        type: 'POST',
                        url: '/production/shoppingcart/countdown', 
                        data: {status:-2}, 
                        success: function(data) {
                            swal({
                                title: "Information",
                                text: "The items in your shopping cart have been deleted.<br>You will now be redirected to the home page.",
                                html:true,
                                type: "info",
                                timer: 3000,
                                showConfirmButton: false
                            },function(){
                                window.location.href = '/production/home';
                            });
                        }
                    }); 
                }
            }); 
        }
    }
    var closeCount = function () {
        $('#timerClock').data('countdown','');
        jQuery.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: 'POST',
            url: '/production/shoppingcart/countdown', 
            data: {status:-1}
        }); 
        $('#timerClockPanel').css('display','none'); 
    }
    return {
        //main function to initiate the module
        init: function () {
            initCount();        
        },
        reset: function () {
            resetCount();        
        },
        keep: function () {
            keepCount();        
        },
        close: function () {
            closeCount();        
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    ScrollToTop.init();
    Logout.init();
    ShoppingcartQtyItems.init();
});