var PurchaseFunctions = function () {

    var initFunctions = function () {

        //remove item
        $('#tb_items tr > td:last-child button').on('click', function(ev) {
            var row = $(this).closest('tr');
            var id = row.attr('id');
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/shoppingcart/remove',
                data: { id: id },
                success: function(data) {
                    if(data.success)
                    {
                        swal({
                            title: "<span style='color:green;'>Updated!</span>",
                            html: true,
                            timer: 1500,
                            type: "success",
                            showConfirmButton: false
                        });
                        row.remove();
                        UpdateShoppingcartFunctions.init( data.cart );
                    }
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to remove the item. Please, try later",
                        html: true,
                        type: "error",
                        showConfirmButton: true
                    });
                }
            });
        });
        //update qty items
        $('#tb_items tr > td:nth-child(2) input').on('change', function(ev) {
            var input = $(this);
            var id = input.closest('tr').attr('id');
            var qty = parseInt(input.val());
            var qty_ = parseInt(input.closest('tr').data('qty'));
            var min = parseInt(input.attr('min'));
            var max = parseInt(input.attr('max'));
            if(qty<min || qty>max)
                input.val(qty_);
            else if(qty != qty_)
            {
                input.prop('disabled',true);
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/shoppingcart/update',
                    data: { id: id, qty: qty },
                    success: function(data) {
                        if(data.success)
                        {
                            swal({
                                title: "<span style='color:green;'>Updated!</span>",
                                html: true,
                                timer: 1500,
                                type: "success",
                                showConfirmButton: false
                            });
                            UpdateShoppingcartFunctions.init( data.cart );
                            input.prop('disabled',false);
                        }
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to change the quantity of tickets. Please, try later",
                            html: true,
                            type: "error",
                            showConfirmButton: true
                        },function(){
                            input.prop('disabled',false);
                        });
                    }
                });
            }
        });
        //add coupon
        $('#add_coupon_code').on('click', function(ev) {
            if( $('#form_coupon').valid() )
            {
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/shoppingcart/coupon',
                    data: $('#form_coupon').serializeArray(),
                    success: function(data) {
                        if(data.success)
                        {
                            swal({
                                title: "<span style='color:green;'>Updated!</span>",
                                html: true,
                                timer: 1500,
                                type: "success",
                                showConfirmButton: false
                            });
                            $('.alert-danger', $('#form_coupon') ).hide();
                            $('.alert-success', $('#form_coupon') ).html('Coupon accepted: '+data.msg).show();
                            UpdateShoppingcartFunctions.init( data.cart );
                        }
                        else
                        {
                            var error = 'Incorrect/Invalid Coupon: That coupon is not valid for you items.';
                            $('.alert-danger', $('#form_coupon') ).html(data.msg).show();
                            $('.alert-success', $('#form_coupon') ).hide();
                        }
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to load the coupon. Please, try later",
                            html: true,
                            type: "error",
                            showConfirmButton: true
                        });
                    }
                });
            }
        });
        //on change country select
        $('select[name="country"]').on('change', function(ev) {
            var country_code = $(this).val();
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/general/region',
                data: { country: country_code },
                success: function(data) {
                    if(data.success)
                    {
                        $('select[name="state"]').find('option').not(':first').remove();
                        $.each(data.regions,function(k, v) {
                            $('select[name="state"]').append('<option value="'+v.code+'">'+v.name+'</option>');
                        });
                        $('select[name="state"]').val('');
                    }
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to get the regions for that country. Please, select the first one",
                        html: true,
                        type: "error",
                        showConfirmButton: true
                    });
                }
            });
        });
        //on change ticket printed options select
        $('select[name="printed_tickets"]').on('change', function(ev) {
            var printed_option = $(this).val();
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/shoppingcart/printed',
                data: { option: printed_option },
                success: function(data) {
                    if(data.success)
                    {
                        swal({
                            title: "<span style='color:green;'>Updated!</span>",
                            html: true,
                            timer: 1500,
                            type: "success",
                            showConfirmButton: false
                        });
                        UpdateShoppingcartFunctions.init( data.cart );
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
                        text: "There was an error trying to set the ticket options. Please, select the first one.",
                        html: true,
                        type: "error",
                        showConfirmButton: true
                    });
                }
            });
        });

        //functio to update shoppingcart
        function update_shoppingcart()
        {
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/shoppingcart/items',
                success: function(data) {
                    if(data)
                        UpdateShoppingcartFunctions.init( data );
                    else
                        location.reload();
                },
                error: function(){
                    location.reload();
                }
            });
        }
        //update shoppingcart each minute
        setTimeout(update_shoppingcart(), 60000);

    }
    return {
        //main function to initiate the module
        init: function () {
            initFunctions();
        }
    };
}();
//*****************************************************************************************
var SubmitFunctions = function () {

    var initFunctions = function () {
        //on accept newsletter
        $('#accept_newsletter').bind('click','change', function(e){
            if( $(this).is(':checked') )
                $('#tabs_payment').find('input[name="newsletter"]').val(1);
            else
                $('#tabs_payment').find('input[name="newsletter"]').val(0);
        });
        //function disabled submit
        function disabled_submit()
        {
            $('#form_cash div.desglose input[name^="x"]').attr('readonly',false);
            $('#form_cash div.desglose input[name="change"]').attr('readonly',false);
            //submit
            $('#btn_process').prop('disabled',true);
        }
        //on change bill
        $('a[href^="#tab_"]').on('click', function(ev) {
            $('#accept_terms').prop('checked', false);

        });
        //on input or select change, disable submit to re-check values
        $('#tabs_payment input, #tabs_payment select').on('change', function(e){
            var form_id = $('#tabs_payment').find('.tab-pane.active:not(.hidden)').find('form').attr('id');
            if(!$('#'+form_id).validate().element( $(this) ))
            {
                $('#accept_terms').prop('checked', false);
                disabled_submit();
            }
        });
        //on accept terms
        $('#accept_terms').bind('click','change', function(e){
            var proceed = false;
            var form_id = $('#tabs_payment').find('.tab-pane.active:not(.hidden)').find('form').attr('id');
            $('.alert-warning', $('#'+form_id) ).hide();
            if( $(this).is(':checked') )
            {
                if( $('#'+form_id).valid() )
                {
                    switch(form_id)
                    {
                        case 'form_skip':
                            proceed = true;
                            break;
                        case 'form_card':
                            var amex_only = $('#form_card input[name="card"]').data('amex');
                            var year = parseInt($('#form_card select[name="year"]').val());
                            var month = parseInt($('#form_card select[name="month"]').val())-1;
                            if( amex_only>0 )
                                var exp_card =/^3[47][0-9]{13}$/;
                            else
                                var exp_card =/^(?:(4[0-9]{12}(?:[0-9]{3})?)|(5[1-5][0-9]{14})|(6(?:011|5[0-9]{2})[0-9]{12})|(3[47][0-9]{13})|(3(?:0[0-5]|[68][0-9])[0-9]{11})|((?:2131|1800|35[0-9]{3})[0-9]{11}))$/;
                            if(!exp_card.test( $('#form_card input[name="card"]').val() ))
                            {
                                if(amex_only>0)
                                    $('.alert-warning', $('#form_card') ).html('You must enter a valid Amerian Express credit card').show();
                                else
                                    $('.alert-warning', $('#form_card') ).html('You must enter a valid credit card').show();
                            }
                            else if(new Date() > new Date(year,month,1))
                            {
                                $('.alert-warning', $('#form_card') ).html('The credit card has expire. Please, check the month and year or try a different one.').show();
                            }
                            else
                                proceed = true;
                            break;
                        case 'form_swipe':
                            proceed = true;
                            break;
                        case 'form_cash':
                            $('#form_cash div.desglose input[name^="x"]').attr('readonly',true);
                            $('#form_cash div.desglose input[name="change"]').attr('readonly',true);
                            proceed = true;
                            break;
                    }
                }
                //uncheck btn
                if(proceed)
                {
                    $('#btn_process').prop('disabled',false);
                }
                else
                {
                    $('#btn_process').prop('disabled',true);
                    e.preventDefault();
                }
            }
            else
                disabled_submit();
        });
        //on submit
        $('#btn_process').click( function(){
            var form_id = $('#tabs_payment').find('.tab-pane.active:not(.hidden)').find('form').attr('id');
            if( $('#'+form_id).valid() )
            {
                $('#btn_process').addClass('hidden');
                $('#btn_loading').removeClass('hidden');
                swal({
                    title: "Processing your item(s)",
                    text: "Please, wait.",
                    type: "info",
                    showConfirmButton: false
                });
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/purchase/process',
                    data: $('#'+form_id).serializeArray(),
                    success: function(data) {
                        if(data.success)
                        {
                            swal({
                                title: "<span style='color:green;'>"+data.msg+"</span>",
                                html: true,
                                timer: 1500,
                                type: "success",
                                showConfirmButton: false
                            },function(){
                                $('#form_complete input[name="purchases"]').val(data.purchases);
                                $('#form_complete input[name="send_welcome_email"]').val(data.send_welcome_email);
                                $('#form_complete')[0].submit();
                            });
                        }
                        else
                        {
                            swal({
                                title: "<span style='color:red;'>Error!</span>",
                                text: data.msg,
                                html: true,
                                type: "error"
                            },function(){
                                $('#btn_loading').addClass('hidden');
                                $('#btn_process').removeClass('hidden');
                                $('#accept_terms').prop('checked', false);
                                disabled_submit();
                            });
                        }
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to process the item(s). Please, contact us.",
                            html: true,
                            type: "error",
                            showConfirmButton: true
                        },function(){
                            $('#btn_loading').addClass('hidden');
                            $('#btn_process').removeClass('hidden');
                            $('#accept_terms').prop('checked', false);
                            disabled_submit();
                        });
                    }
                });
            }
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
var GalleryImages = function () {

    var initGallery = function () {
        //banners carousel
        $('#myBanners').cubeportfolio({
            layoutMode: 'slider',
            defaultFilter: '*',
            animationType: 'fadeOut', // quicksand
            gapHorizontal: 30,
            gapVertical: 30,
            mediaQueries: [{ width: 320, cols: 1 }],
            gridAdjustment: 'responsive',
            caption: 'opacity',
            displayType: 'default',
            displayTypeSpeed: 1,
            auto:true,
            autoTimeout: 1500,
            drag:true,
            showNavigation: false,
            showPagination: false,
            rewindNav: true
        });
    }
    return {
        //main function to initiate map samples
        init: function () {
            initGallery();
        }
    };

}();
//*****************************************************************************************
jQuery(document).ready(function() {
    PurchaseFunctions.init();
    SubmitFunctions.init();
    GalleryImages.init();
});
