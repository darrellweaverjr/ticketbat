var FunctionsGuest = function () {
    
    var initFunctions = function () {
        
        //function login
        $('#btn_login_guest').on('click', function(ev) {
            if($('#form_login_guest').valid())
            {  
                $('#modal_login_guest').modal('hide');
                swal({
                    title: "Loging in...",
                    text: "Please, wait.",
                    type: "info",
                    showConfirmButton: false
                });
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/user/login', 
                    data: $('#form_login_guest').serializeArray(), 
                    success: function(data) {
                        if(data.success) 
                        {
                            swal({
                                title: "<span style='color:green;'>Accepted!</span>",
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
                                $('#modal_login_guest').modal('show');
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
                            $('#modal_login_guest').modal('show');
                        });
                    }
                }); 
            }  
        });
        //function guest
        $('#btn_guest_login').on('click', function(ev) {
            if($('#form_guest_login').valid())
            {  
                $('#modal_login_guest').modal('hide');
                swal({
                    title: "Validating...",
                    text: "Please, wait.",
                    type: "info",
                    showConfirmButton: false
                });
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/user/guest', 
                    data: $('#form_guest_login').serializeArray(), 
                    success: function(data) {
                        if(data.success) 
                        {
                            swal({
                                title: "<span style='color:green;'>Accepted!</span>",
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
                                $('#modal_login_guest').modal('show');
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
                            $('#modal_login_guest').modal('show');
                        });
                    }
                }); 
            }  
        });
        //on enter submit forms
        $('#form_guest_login input').keypress(function(e) {
            if(e.which == 13) {
                $('#btn_guest_login').focus().click();
            }
        });
        $('#form_login_guest input').keypress(function(e) {
            if(e.which == 13) {
                $('#btn_login_guest').focus().click();
            }
        });
        //autoload form
        $('#modal_login_guest').modal('show');
    }
    return {
        //main function to initiate the module
        init: function () {
            initFunctions();        
        }
    };
}();
//*****************************************************************************************
var LoginGuestValidation = function () {
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
            MainFormValidation.init('form_login_guest',rules,{});
        }
    };
}();
//*****************************************************************************************
var GuestLoginValidation = function () {
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
                }
            };
            MainFormValidation.init('form_guest_login',rules,{});
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    FunctionsGuest.init();
    LoginGuestValidation.init();
    GuestLoginValidation.init();
});