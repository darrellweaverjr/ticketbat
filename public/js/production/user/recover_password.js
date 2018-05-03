var RecoverFunctions = function () {    
    var initFunctions = function () {
        //function recover
        $('#btn_recover_password').on('click', function(ev) {
            if($('#form_recover_password').valid())
            {  
                $('#modal_recover_password').modal('hide');
                swal({
                    title: "Sending new password...",
                    text: "Please, wait.",
                    type: "info",
                    showConfirmButton: false
                });
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/user/recover_password', 
                    data: $('#form_recover_password').serializeArray(), 
                    success: function(data) {
                        if(data.success) 
                        {
                            $('#form_recover_password').trigger('reset');
                            swal({
                                title: "<span style='color:green;'>Sent!</span>",
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
                                $('#modal_recover_password').modal('show');
                            });
                        }
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to send the password.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_recover_password').modal('show');
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
var RecoverValidation = function () {
    return {
        //main function to initiate the module
        init: function () {
            // advance validation
            var rules = {
                email: {
                    minlength: 8,
                    maxlength: 200,
                    email: true,
                    required: true
                }
            };
            MainFormValidation.init('form_recover_password',rules,{});
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    RecoverFunctions.init();
    RecoverValidation.init();
});