var PurchaseFunctions = function () {

    var initFunctions = function () {

        //remove item
        $('#tb_items tr > td.rm-tix').on('click', function(ev) {
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
                        UpdateShoppingcartFunctions.init( data.cart );
                        row.remove();
                        swal({
                            title: "<span style='color:green;'>Updated!</span>",
                            html: true,
                            timer: 1500,
                            type: "success",
                            showConfirmButton: false
                        });
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

        //function to update shoppingcart
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
                                $('#btn_process').prop('disabled',true);
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
                            $('#btn_process').prop('disabled',true);
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

        //check for broken images to change
        function check_images(){
            $('#myBanners .cbp-item.filtered:not(.hidden) img').each(function(){
                if((typeof this.naturalWidth != "undefined" && this.naturalWidth < 1 ) || this.readyState == 'uninitialized' || this.naturalWidth == "undefined" )
                    $(this).attr('src', $('meta[name="broken-image"]').attr('content') );
            });
        }
        //check images on load and check the location
        $(window).load(function(){
            check_images();
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
