var ResetFunctions = function () {    
    var initFunctions = function () {
        //function logout on close modal
        $('#btn_reset_password_close').on('click', function(ev) {
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/user/logout', 
                success: function(data) {
                    if(data.success) 
                        location.reload();
                    else{
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_reset_password').modal('show');
                        });
                    }
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to update the password.",
                        html: true,
                        type: "error"
                    },function(){
                        $('#modal_reset_password').modal('show');
                    });
                }
            }); 
        });
        //function recover
        $('#btn_reset_password').on('click', function(ev) {
            if($('#form_reset_password').valid())
            {  
                $('#modal_reset_password').modal('hide');
                swal({
                    title: "Updating the password...",
                    text: "Please, wait.",
                    type: "info",
                    showConfirmButton: false
                });
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/user/reset_password', 
                    data: $('#form_reset_password').serializeArray(), 
                    success: function(data) {
                        if(data.success) 
                        {
                            $('#form_reset_password').trigger('reset');
                            swal({
                                title: "<span style='color:green;'>Updated!</span>",
                                text: data.msg,
                                html: true,
                                timer: 1500,
                                type: "success",
                                showConfirmButton: false
                            });
                            location.reload();
                        }
                        else{
                            swal({
                                title: "<span style='color:red;'>Error!</span>",
                                text: data.msg,
                                html: true,
                                type: "error"
                            },function(){
                                $('#modal_reset_password').modal('show');
                            });
                        }
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to update the password.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_reset_password').modal('show');
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
var ResetValidation = function () {
    return {
        //main function to initiate the module
        init: function () {
            // advance validation
            var rules = {
                password: {
                    minlength: 8,
                    maxlength: 20,
                    required: true
                },
                password2: {
                    equalTo: '#reset_password',
                    required: true
                }
            };
            MainFormValidation.init('form_reset_password',rules,{});
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    ResetFunctions.init();
    ResetValidation.init();
});