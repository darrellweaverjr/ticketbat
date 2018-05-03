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
    return {
        //main function to initiate the module
        init: function () {
            // advance validation
            var rules = {
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
            };
            MainFormValidation.init('form_login',rules,{});
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    LoginFunctions.init();
    LoginValidation.init();
});