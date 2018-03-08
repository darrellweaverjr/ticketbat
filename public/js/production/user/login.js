var LoginFunctions = function () {    
    var initFunctions = function () {
        //switch_recover_password
        $('#switch_recover_password').on('click', function() {
            $('#modal_login').modal('hide');
            $('#modal_recover_password').modal('show');  
        });
        //switch_register
        $('#switch_register').on('click', function() {
            $('#modal_login').modal('hide');
            $('#modal_register').modal('show');   
        });
        //function login
        $('#btn_login').on('click', function(ev) {
            if($('#form_login').valid())
            {  
                $('#modal_login').modal('hide');
                swal({
                    title: "Logging in...",
                    text: "Please, wait.",
                    type: "info",
                    showConfirmButton: false
                });
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/user/login', 
                    data: $('#form_login').serializeArray(), 
                    success: function(data) {
                        if(data.success) 
                        {
                            $('#form_login').trigger('reset');
                            location.reload();
                        }
                        else{
                            swal({
                                title: "<span style='color:red;'>Error!</span>",
                                text: data.msg,
                                html: true,
                                type: "error"
                            },function(){
                                $('#modal_login').modal('show');
                            });
                        }
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to log in.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_login').modal('show');
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
var LoginValidation = function () {
    // advance validation
    var handleValidation = function() {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation
            var form = $('#form_login');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);
            form.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "", // validate all fields including form hidden input
                rules: {
                    username: {
                        minlength: 8,
                        maxlength: 200,
                        email: true,
                        required: true
                    },
                    password: {
                        minlength: 8,
                        maxlength: 100,
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
    LoginFunctions.init();
    LoginValidation.init();
});