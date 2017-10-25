var ShareFunctions = function () {
    
    var initFunctions = function () {
        
        //load share tickets
        $('#tb_items tr > td:nth-child(6) button').on('click', function(ev) {
            var qty = parseInt($(this).data('qty'));   
            var shoppingcart_id = $(this).data('id');
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/production/shoppingcart/share', 
                data: { id: shoppingcart_id }, 
                success: function(data) {
                    if(data.success) 
                    {
                        $('#form_share_tickets input[name="purchases_id"]').val(shoppingcart_id);
                        ShareTicketsFunctions.load(data,qty); 
                        $('#modal_share_tickets').modal('show');
                    }
                    else{
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error"
                        });
                    }
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to load the shared tickets.",
                        html: true,
                        type: "error"
                    });
                }
            }); 
        });
        
        //function save
        $('#btn_share_tickets').on('click', function(ev) {
            //submit values of tickets
            if( ShareTicketsFunctions.check() )
            {  
                $('#modal_share_tickets').modal('hide');
                swal({
                    title: "Sharing your tickets...",
                    text: "Please, wait.",
                    type: "info",
                    showConfirmButton: false
                });
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/production/shoppingcart/share', 
                    data: $('#form_share_tickets').serializeArray(), 
                    success: function(data) {
                        if(data.success) 
                        {
                            swal({
                                title: "<span style='color:green;'>Saved!</span>",
                                text: data.msg,
                                html: true,
                                timer: 1500,
                                type: "success",
                                showConfirmButton: false
                            });
                        }
                        else{
                            swal({
                                title: "<span style='color:red;'>Error!</span>",
                                text: data.msg,
                                html: true,
                                type: "error"
                            },function(){
                                $('#modal_share_tickets').modal('show');
                            });
                        }
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to create the user.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_share_tickets').modal('show');
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
var PurchaseFunctions = function () {
    
    var initFunctions = function () {
        //remove item
        $('#tb_items tr > td:last-child button').on('click', function(ev) {
            var id = $(this).data('id');
            var row = $(this).closest('tr');
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/production/shoppingcart/remove', 
                data: { id: id }, 
                success: function(data) {
                    if(data.success) 
                    {
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
            var id = $(this).data('id');
            var qty = parseInt($(this).val());
            var qty_ = parseInt($(this).data('qty'));
            var min = parseInt($(this).attr('min'));
            var max = parseInt($(this).attr('max'));
            if(qty<min || qty>max)
                $(this).val(qty_);
            else if(qty != qty_)
            {
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/production/shoppingcart/update', 
                    data: { id: id, qty: qty }, 
                    success: function(data) {
                        if(data.success) 
                        {
                            UpdateShoppingcartFunctions.init( data.cart );
                        }
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to change the quantity of tickets. Please, try later",
                            html: true,
                            type: "error",
                            showConfirmButton: true
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
                    url: '/production/shoppingcart/coupon', 
                    data: $('#form_coupon').serializeArray(), 
                    success: function(data) {
                        if(data.success) 
                        {
                            $('#coupon_msg').html(data.msg);
                            $('.alert-success', $('#form_coupon') ).show();
                            UpdateShoppingcartFunctions.init( data.cart );
                        }
                        else
                        {
                            $('#coupon_msg').html(data.msg);
                            var validator = $( "#form_coupon" ).validate();
                            validator.showErrors({
                              "coupon": "Incorrect/Invalid Coupon: That coupon is not valid for you items."
                            });
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
                url: '/production/general/region', 
                data: { country: country_code }, 
                success: function(data) {
                    if(data.success) 
                    {
                        $('select[name="state"]').empty();
                        $.each(data.regions,function(k, v) {
                            $('select[name="state"]').append('<option value="'+v.code+'">'+v.name+'</option>');
                        });
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
            $('#form_cash div.desglose input[name^="x"]').attr('disabled',false);
            $('#form_cash div.desglose input[name="change"]').attr('disabled',false);
            $('#form_cash div.desglose input[name="pending"]').attr('disabled',false);
            $('#form_cash div.desglose input[name="subtotal"]').attr('disabled',false);
            //submit
            $('#btn_process').prop('disabled',true);
        }
        //on change bill
        $('a[href^="#tab_"]').on('click', function(ev) {
            $('#accept_terms').prop('checked', false);
            
        });
        //on input or select change, disable submit to re-check values
        $('#tabs_payment input, #tabs_payment select').on('change', function(e){
            var form_id = $('#tabs_payment').find('.tab-pane.active').find('form').attr('id');
            if(!$('#'+form_id).valid())
            {
                $('#accept_terms').prop('checked', false);
                disabled_submit();
            }
        });
        //on accept terms
        $('#accept_terms').bind('click','change', function(e){
            var proceed = false;
            var form_id = $('#tabs_payment').find('.tab-pane.active').find('form').attr('id');
            $('#div_show_errors').css('display','none');
            $('#'+form_id+' .alert-danger').css('display','none');
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
                            if( amex_only>0 )
                                var exp_card =/^3[47][0-9]{13}$/;
                            else 
                                var exp_card =/^(?:(4[0-9]{12}(?:[0-9]{3})?)|(5[1-5][0-9]{14})|(6(?:011|5[0-9]{2})[0-9]{12})|(3[47][0-9]{13})|(3(?:0[0-5]|[68][0-9])[0-9]{11})|((?:2131|1800|35[0-9]{3})[0-9]{11}))$/;
                            if(!exp_card.test(card))
                            {
                                if(amex_only>0)
                                    $('#div_show_errors').html('You must enter a valid Amerian Express credit card');
                                else
                                    $('#div_show_errors').html('You must enter a valid credit card');
                                $('#div_show_errors').css('display','block');
                            }
                            else
                                proceed = true;
                            break;
                        case 'form_swipe':
                            proceed = true;
                            break;
                        case 'form_cash':
                            $('#form_cash div.desglose input').attr('disabled',true);
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
            var form_id = $('#tabs_payment').find('.tab-pane.active').find('form').attr('id');
            if( $('#'+form_id).valid() )
                //$('#'+form_id)[0].submit();
                alert('Form submited');
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
var SwipeCardFunctions = function () {
    
    var initFunctions = function () {
        
        //on click swipe card
        $('a[href="#tab_swipe"]').on('click', function(ev) {
            //reset form here too
            $('#modal_swipe_card').modal('show');
            $('#modal_swipe_card input[name="stripe_card"]').val('');
            $('#modal_swipe_card input[name="stripe_card"]').focus();
        });
        //on modal swipe card on click
        $('#modal_swipe_card').on('click', function(ev) {
            $('#modal_swipe_card input[name="stripe_card"]').val('');
            $('#modal_swipe_card input[name="stripe_card"]').focus();
        });
        //swipe card
        $('#modal_swipe_card input[name="stripe_card"]').blur(function (e) {
            e.preventDefault();
            $('#modal_swipe_card').modal('hide');
            $('#tab_swipe input[name="customer"]').focus();
        }).keyup(function (e) {
            if($(this).val().substr($(this).val().length-1)=="?") 
            {
                if(valid_swipe_credit_card($(this).val()))
                {
                    $('#modal_swipe_card').modal('hide');
                    $('#tab_swipe input[name="customer"]').focus();
                }
            }
        });
        //event to check swipe
        function valid_swipe_credit_card(card_data)
        {
            var card_tracks = card_data.split("?");
            var valid_track1 = /^%B[^\^\W]{0,19}\^[^\^]{2,26}\^\d{4}\w{3}[^?]+\?\w?$/.test(card_tracks[0]+'?');
            var valid_track2 = /;[^=]{0,19}=\d{4}\w{3}[^?]+\?\w?/.test(card_tracks[1]+'?');
            if(valid_track1 && valid_track2)
            {
                var details1 = card_data.split("^");
                var card_number = details1[0];
                card_number = card_number.substring(2);
                var names = details1[1].split("/");
                var first_name = names[1].trim();
                var last_name = names[0].trim();
                var details2 = details1[2].split(";");
                details2 = details2[1].split("=");
                var exp_date = details2[1];
                exp_date = exp_date.substring(0, exp_date.length - 1);
                var month = exp_date.substring(2, 4);
                var year = exp_date.substring(0, 2);
                $('#tab_swipe input[name="UMmagstripe"]').val(card_data);
                $('#tab_swipe input[name="customer"]').val(first_name + ' ' + last_name);
                $('#tab_swipe input[name="card"]').val(card_number);
                $('#tab_swipe input[name="exp_month"]').val(month);
                $('#tab_swipe input[name="exp_year"]').val(year);
                return true;
            }
            else {
                alert('Could not be correctly read the card.');
                return false;
            }
        }
        
    }
    return {
        //main function to initiate the module
        init: function () {
            initFunctions();        
        }
    };
}();
//*****************************************************************************************
var CashFunctions = function () {
    
    var initFunctions = function () {
        //function to calculate cash
        function calculate_cash()
        {
            var subtotal = 0;
            $.each( $('#tab_cash input[name^="x"]'), function () {
                subtotal += parseInt($(this).val()) * parseInt($(this).data('bill'));
            });
            subtotal += $('#tab_cash input[name="change"]').val()/100;
            var pending = -1*parseFloat($('#cost_total').data('total')) + subtotal;
            $('#tab_cash input[name="subtotal"]').val( subtotal.toFixed(2) );
            $('#tab_cash input[name="pending"]').val( pending.toFixed(2) );
            if(pending<0)
            {
                $('#collect_text').html('Collect');
                $('#tab_cash input[name="pending"]').css('color','red');
            }
            else
            {
                $('#collect_text').html('Change');
                $('#tab_cash input[name="pending"]').css('color','green');
            } 
        }
        //on change bill
        $('#tab_cash input[name^="x"]').bind('change','click', function(ev) {
            var bill = parseFloat($(this).val()).toFixed();
            var data_bill = parseInt($(this).data('bill'));
            if(bill<0)
                $(this).val(bill*-1);
            else if(bill>parseInt($(this).attr('max')))
                $(this).val($(this).attr('max'));
            else
                $(this).val(bill);
            $('#tab_cash input[name^="r"]').each( function(k, v) {
                if( parseInt($(this).data('bill')) == data_bill )
                    $(this).val( (bill*data_bill).toFixed(2) );  
            });
            calculate_cash();
        });
        //on change change
        $('#tab_cash input[name="change"]').bind('change','click', function(ev) {
            var change = parseFloat($(this).val()).toFixed();
            if(change<0 || change>parseInt($(this).attr('max')))
                $(this).val($(this).attr('max'));
            else
                $(this).val(change);
            calculate_cash();
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
var SkipValidation = function () {
    // advance validation
    var handleValidation = function() {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation
            var form = $('#form_skip');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);
            form.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "", // validate all fields including form hidden input
                rules: {
                    email: {
                        minlength: 8,
                        maxlength: 200,
                        email: true,
                        required: true
                    },
                    customer: {
                        minlength: 2,
                        maxlength: 100,
                        required: true
                    },
                    phone: {
                        minlength: 10,
                        maxlength: 10,
                        digits: true,
                        required: false
                    }
                },
                invalidHandler: function (event, validator) { //display error alert on form submit   
                    success.hide();
                    error.show();
                    App.scrollTo(error, -200);
                },

                highlight: function (element) { // hightlight error inputs
                   $(element)
                        .closest('.show-error').addClass('has-error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change done by hightlight
                    $(element)
                        .closest('.show-error').removeClass('has-error'); // set error class to the control group
                },

                success: function (label) {
                    label
                        .closest('.show-error').removeClass('has-error'); // set success class to the control group
                },

                submitHandler: function (form) {
                    success.show();
                    error.hide();
                    form[0].submit(); // submit the form
                }
            });
    }
    return {
        //main function to initiate the module
        init: function () {
            handleValidation();
        }
    };
}();
//*****************************************************************************************
var CardValidation = function () {
    // advance validation
    var handleValidation = function() {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation
            var form = $('#form_card');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);
            form.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "", // validate all fields including form hidden input
                rules: {
                    email: {
                        minlength: 8,
                        maxlength: 200,
                        email: true,
                        required: true
                    },
                    customer: {
                        minlength: 2,
                        maxlength: 100,
                        required: true
                    },
                    phone: {
                        minlength: 10,
                        maxlength: 10,
                        digits: true,
                        required: false
                    },
                    card: {
                        minlength: 16,
                        maxlength: 16,
                        creditcard: true,
                        digits: true,
                        required: true
                    },
                    cvv: {
                        minlength: 3,
                        maxlength: 4,
                        digits: true,
                        required: true
                    },
                    exp_month: {
                        range: [1,12],
                        digits: true,
                        required: true
                    },
                    exp_year: {
                        digits: true,
                        required: true
                    },
                    address: {
                        minlength: 5,
                        maxlength: 200,
                        required: true
                    },
                    city: {
                        minlength: 2,
                        maxlength: 100,
                        required: true
                    },
                    zip: {
                        minlength: 5,
                        maxlength: 5,
                        digits: true,
                        range: [10000, 99999],
                        required: true
                    },
                    country: {
                        required: true
                    },
                    state: {
                        required: true
                    }
                },
                invalidHandler: function (event, validator) { //display error alert on form submit   
                    success.hide();
                    error.show();
                    App.scrollTo(error, -200);
                },

                highlight: function (element) { // hightlight error inputs
                   $(element)
                        .closest('.show-error').addClass('has-error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change done by hightlight
                    $(element)
                        .closest('.show-error').removeClass('has-error'); // set error class to the control group
                },

                success: function (label) {
                    label
                        .closest('.show-error').removeClass('has-error'); // set success class to the control group
                },

                submitHandler: function (form) {
                    success.show();
                    error.hide();
                    form[0].submit(); // submit the form
                }
            });
    }
    return {
        //main function to initiate the module
        init: function () {
            handleValidation();
        }
    };
}();
//*****************************************************************************************
var SwipeValidation = function () {
    // advance validation
    var handleValidation = function() {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation
            var form = $('#form_swipe');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);
            form.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "", // validate all fields including form hidden input
                rules: {
                    email: {
                        minlength: 8,
                        maxlength: 200,
                        email: true,
                        required: true
                    },
                    customer: {
                        minlength: 2,
                        maxlength: 100,
                        required: true
                    },
                    phone: {
                        minlength: 10,
                        maxlength: 10,
                        digits: true,
                        required: false
                    },
                    card: {
                        minlength: 16,
                        maxlength: 16,
                        creditcard: true,
                        digits: true,
                        required: true
                    },
                    exp_month: {
                        range: [1,12],
                        digits: true,
                        required: true
                    },
                    exp_year: {
                        minlength: 4,
                        maxlength: 4,
                        digits: true,
                        required: true
                    },
                    UMmagstripe: {
                        required: true
                    }
                },
                invalidHandler: function (event, validator) { //display error alert on form submit   
                    success.hide();
                    error.show();
                    App.scrollTo(error, -200);
                },

                highlight: function (element) { // hightlight error inputs
                   $(element)
                        .closest('.show-error').addClass('has-error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change done by hightlight
                    $(element)
                        .closest('.show-error').removeClass('has-error'); // set error class to the control group
                },

                success: function (label) {
                    label
                        .closest('.show-error').removeClass('has-error'); // set success class to the control group
                },

                submitHandler: function (form) {
                    success.show();
                    error.hide();
                    form[0].submit(); // submit the form
                }
            });
    }
    return {
        //main function to initiate the module
        init: function () {
            handleValidation();
        }
    };
}();
//*****************************************************************************************
var CashValidation = function () {
    // advance validation
    var handleValidation = function() {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation
            var form = $('#form_cash');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);
            form.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "", // validate all fields including form hidden input
                rules: {
                    email: {
                        minlength: 8,
                        maxlength: 200,
                        email: true,
                        required: true
                    },
                    customer: {
                        minlength: 2,
                        maxlength: 100,
                        required: true
                    },
                    phone: {
                        minlength: 10,
                        maxlength: 10,
                        digits: true,
                        required: false
                    },
                    pending: {
                        min: 0,
                        number: true,
                        required: true
                    }
                },
                invalidHandler: function (event, validator) { //display error alert on form submit   
                    success.hide();
                    error.show();
                    App.scrollTo(error, -200);
                },

                highlight: function (element) { // hightlight error inputs
                   $(element)
                        .closest('.show-error').addClass('has-error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change done by hightlight
                    $(element)
                        .closest('.show-error').removeClass('has-error'); // set error class to the control group
                },

                success: function (label) {
                    label
                        .closest('.show-error').removeClass('has-error'); // set success class to the control group
                },

                submitHandler: function (form) {
                    success.show();
                    error.hide();
                    form[0].submit(); // submit the form
                }
            });
    }
    return {
        //main function to initiate the module
        init: function () {
            handleValidation();
        }
    };
}();
//*****************************************************************************************
var CouponValidation = function () {
    // advance validation
    var handleValidation = function() {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation
            var form = $('#form_coupon');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);
            form.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "", // validate all fields including form hidden input
                rules: {
                    coupon: {
                        minlength: 2,
                        maxlength: 50,
                        required: true
                    }
                },
                invalidHandler: function (event, validator) { //display error alert on form submit   
                    success.hide();
                    error.show();
                    App.scrollTo(error, -200);
                },

                highlight: function (element) { // hightlight error inputs
                   $(element)
                        .closest('.show-error').addClass('has-error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change done by hightlight
                    $(element)
                        .closest('.show-error').removeClass('has-error'); // set error class to the control group
                },

                success: function (label) {
                    label
                        .closest('.show-error').removeClass('has-error'); // set success class to the control group
                },

                submitHandler: function (form) {
                    success.show();
                    error.hide();
                    form[0].submit(); // submit the form
                }
            });
    }
    return {
        //main function to initiate the module
        init: function () {
            handleValidation();
        }
    };
}();
//*****************************************************************************************
var UpdateShoppingcartFunctions = function () {
    
    var initFunctions = function (cart) {
        
        
        
    }
    return {
        //main function to initiate the module
        init: function (cart) {
            initFunctions(cart);        
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    ShareFunctions.init();
    SwipeCardFunctions.init();
    PurchaseFunctions.init();
    CashFunctions.init();
    SkipValidation.init();
    CardValidation.init();
    SwipeValidation.init();
    CashValidation.init();
    CouponValidation.init();
    SubmitFunctions.init();
});