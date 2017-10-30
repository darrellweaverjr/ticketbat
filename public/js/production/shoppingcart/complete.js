var CompleteFunctions = function () {
    
    var initFunctions = function () {
        
        $(window).bind('beforeunload', function(e){
            return "Are you sure you want to leave this page? This page will allow you to print your tickets.";
        });
        
        //on resend email welcome
        $('#resend_welcome').on('click', function(ev) {
            var user_id = $(this).data('id');
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/production/shoppingcart/welcome', 
                data: { user_id: user_id}, 
                success: function(data) {
                    if(data.success) 
                    {
                        swal({
                            title: "<span style='color:green;'>Sent!</span>",
                            html: true,
                            timer: 1500,
                            type: "success",
                            showConfirmButton: false
                        });
                    }
                    else
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error"
                        });
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to re-send the welcome email. Please, contact us.",
                        html: true,
                        type: "error",
                        showConfirmButton: true
                    });
                }
            }); 
        });
        
        //on resend receipts
        $('#resend_receipts').on('click', function(ev) {
            var purchases = $(this).data('purchases');
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/production/shoppingcart/receipts', 
                data: { purchases: purchases }, 
                success: function(data) {
                    if(data.success && data.sent_receipts) 
                    {
                        swal({
                            title: "<span style='color:green;'>Sent!</span>",
                            html: true,
                            timer: 1500,
                            type: "success",
                            showConfirmButton: false
                        });
                    }
                    else
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: 'There was an error trying to re-send the receipts',
                            html: true,
                            type: "error"
                        });
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to re-send the welcome email. Please, contact us.",
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
            initFunctions();        
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    CompleteFunctions.init();
});