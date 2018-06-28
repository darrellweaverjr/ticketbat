var SellerFunctions = function () {    
    var initFunctions = function () {
        
        //function open drawer
        $('#btn_seller_open').on('click', function(ev) {
            $('#modal_seller_open').modal('hide');
            swal({
                title: "Opening drawer",
                text: "Please, wait.",
                type: "info",
                showConfirmButton: false
            });
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/user/seller/drawer_open', 
                data: $('#form_seller_open').serializeArray(), 
                success: function(data) {
                    if(data.success) 
                    {
                        swal({
                            title: "<span style='color:green;'>Opened!</span>",
                            text: data.msg,
                            html: true,
                            timer: 1500,
                            type: "success",
                            showConfirmButton: false
                        },function(){
                            location.reload();
                        });
                    }
                    else{
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_seller_open').modal('show');
                        });
                    }
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to open the drawer.",
                        html: true,
                        type: "error"
                    },function(){
                        $('#modal_seller_open').modal('show');
                    });
                }
            }); 
        });
        
        //function continue drawer
        $('#btn_seller_continue').on('click', function(ev) {
            $('#modal_seller_continue').modal('hide');
            swal({
                title: "Updating drawer",
                text: "Please, wait.",
                type: "info",
                showConfirmButton: false
            });
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/user/seller/drawer_continue', 
                data: $('#form_seller_continue').serializeArray(), 
                success: function(data) {
                    if(data.success) 
                    {
                        swal({
                            title: "<span style='color:green;'>Updated!</span>",
                            text: data.msg,
                            html: true,
                            timer: 1500,
                            type: "success",
                            showConfirmButton: false
                        },function(){
                            location.reload();
                        });
                    }
                    else{
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_seller_continue').modal('show');
                        });
                    }
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to close the drawer.",
                        html: true,
                        type: "error"
                    },function(){
                        $('#modal_seller_continue').modal('show');
                    });
                }
            }); 
        });
        //function continue drawer
        $('#btn_seller_closing').on('click', function(ev) {
            $('#modal_seller_continue').modal('hide');
            $('#modal_seller_close').modal('show');             
        });
        
        //function close drawer
        $('#btn_seller_close').on('click', function(ev) {
            $('#modal_seller_close').modal('hide');
            swal({
                title: "Closing drawer",
                text: "Please, wait.",
                type: "info",
                showConfirmButton: false
            });
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/user/seller/drawer_close', 
                data: $('#form_seller_close').serializeArray(), 
                success: function(data) {
                    if(data.success) 
                    {
                        swal({
                            title: "<span style='color:green;'>Closed!<br><b>Cash out: $"+data.cash_out+"</b></span>",
                            text: data.msg,
                            html: true,
                            type: "success",
                            showConfirmButton: true
                        },function(){
                            location.reload();
                        });
                    }
                    else{
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error"
                        },function(){
                            location.reload();
                        });
                    }
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to close the drawer.",
                        html: true,
                        type: "error"
                    },function(){
                        $('#modal_seller_close').modal('show');
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
var SellerValidation = function () {
    return {
        //main function to initiate the module
        init: function () {
            // advance validation
            /*var rules = {
                name: {
                    minlength: 3,
                    maxlength: 200,
                    required: false
                },
                email: {
                    minlength: 5,
                    maxlength: 200,
                    email: true,
                    required: true
                },
                phone: {
                    digits: true,
                    range: [1000000000,9999999999],
                    required: false
                },  
                event: {
                    minlength: 5,
                    maxlength: 200,
                    required: false
                },
                date: {
                    minlength: 16,
                    maxlength: 16,
                    date:true,
                    required: false
                },
                message: {
                    minlength: 5,
                    maxlength: 250,
                    required: true
                }
            };
            MainFormValidation.init('form_contact_us',rules,{});*/
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    SellerFunctions.init();
    SellerValidation.init();
});