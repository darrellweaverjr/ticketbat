var RegisterFunctions = function () {    
    var initFunctions = function () {
        //reset modal on load
        $('#modal_register').on('shown.bs.modal', function() { 
            $('#form_register').trigger('reset');
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/production/general/country', 
                success: function(data) {
                    if(data.success) 
                    {
                        $('#form_register select[name="country"]').empty();
                        $.each(data.countries,function(k, v) {
                            var selected = (v.code=='US')? 'selected' : '';
                            $('#form_register select[name="country"]').append('<option '+selected+' value="'+v.code+'">'+v.name+'</option>');
                        });
                        $('#form_register select[name="state"]').empty();
                        $.each(data.regions,function(k, v) {
                            $('#form_register select[name="state"]').append('<option value="'+v.code+'">'+v.name+'</option>');
                        });
                    }
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to get the countries. Please, select the first one",
                        html: true,
                        type: "error",
                        showConfirmButton: true
                    });
                }
            }); 
        }) ;
        //on change country select
        $('#form_register select[name="country"]').on('change', function(ev) {
            var country_code = $(this).val();
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/production/general/region', 
                data: { country: country_code }, 
                success: function(data) {
                    if(data.success) 
                    {
                        $('#form_register select[name="state"]').empty();
                        $.each(data.regions,function(k, v) {
                            $('#form_register select[name="state"]').append('<option value="'+v.code+'">'+v.name+'</option>');
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
        //function login
        $('#btn_register').on('click', function(ev) {
            if($('#form_register').valid())
            {  
                $('#modal_register').modal('hide');
                swal({
                    title: "Creating user...",
                    text: "Please, wait.",
                    type: "info",
                    showConfirmButton: false
                });
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/production/user/register', 
                    data: $('#form_register').serializeArray(), 
                    success: function(data) {
                        if(data.success) 
                        {
                            $('#form_register').trigger('reset');
                            location.reload();
                        }
                        else{
                            swal({
                                title: "<span style='color:red;'>Error!</span>",
                                text: data.msg,
                                html: true,
                                type: "error"
                            },function(){
                                $('#modal_register').modal('show');
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
                            $('#modal_register').modal('show');
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
var RegisterValidation = function () {
    // advance validation
    var handleValidation = function() {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation
            var form = $('#form_register');
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
                    first_name: {
                        minlength: 2,
                        maxlength: 50,
                        required: true
                    },
                    last_name: {
                        minlength: 2,
                        maxlength: 50,
                        required: true
                    },
                    phone: {
                        minlength: 10,
                        maxlength: 10,
                        digits: true,
                        required: true
                    },
                    password: {
                        minlength: 8,
                        maxlength: 20,
                        required: true
                    },
                    password2: {
                        equalTo: '#register_password',
                        required: true
                    },
                    address: {
                        minlength: 5,
                        maxlength: 200,
                        required: true
                    },
                    city: {
                        minlength: 3,
                        maxlength: 100,
                        required: true
                    },
                    country: {
                        required: true
                    },
                    state: {
                        required: true
                    },
                    zip: {
                        minlength: 5,
                        maxlength: 10,
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
jQuery(document).ready(function() {
    RegisterFunctions.init();
    RegisterValidation.init();
});