var ContactFunctions = function () {    
    var initFunctions = function () {
        //contact
        $(".form_datetime").datetimepicker({
            autoclose: true,
            isRTL: App.isRTL(),
            format: "yyyy-mm-dd hh:ii",
            pickerPosition: ("bottom-left")
        });
        //groupon msg
        var groupon_text = 'Groupons are subject to the fine print listed on the Groupon deal. In all cases, Groupons can only be redeemed in person at the show’s box office. Reservations cannot be made online or on the phone. You may go to the box office at any time after purchasing the Groupon and before the show. We recommend going as soon as possible to increase your chances of getting the show date you want. All Groupon redemptions are on a first come, first serve basis. If regular ticket sales have sold out the show, you will need to redeem your Groupon on another show date.';
        //function check groupon text
        function groupon_check(){
            var msg = $('#form_contact_us [name="message"]').val().toLowerCase().trim().replace(/\s/g,'');
            if(/groupon/.test(msg)) 
                return false;
            else return true;
        }
        //check groupon msg
        $('#form_contact_us [name="message"]').on('keyup',function(ev){
            if(groupon_check()===false)
                alert(groupon_text);
        });
        //function send
        $('#btn_contact_send').on('click', function(ev) {
            $('#modal_contact_us').modal('hide');
            var invalid = groupon_check();
            if($('#form_contact_us').valid() && invalid)
            {  
                swal({
                    title: "Sending message",
                    text: "Please, wait.",
                    type: "info",
                    showConfirmButton: false
                });
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/general/contact', 
                    data: $('#form_contact_us').serializeArray(), 
                    success: function(data) {
                        if(data.success) 
                        {
                            $('#form_contact_us').trigger('reset');
                            swal({
                                title: "<span style='color:green;'>Send!</span>",
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
                                $('#modal_contact_us').modal('show');
                            });
                        }
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to send the message.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_contact_us').modal('show');
                        });
                    }
                }); 
            } 
            else
            {                
                if(invalid)
                    var error = "The form is not valid!<br>Please check the information again.";
                else
                    var error = groupon_text;
                swal({
                    title: "<span style='color:red;'>Error!</span>",
                    text: error,
                    html: true,
                    type: "error"
                },function(){
                    $('#modal_contact_us').modal('show');
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
var ContactValidation = function () {
    // advance validation
    var handleValidation = function() {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation
            var form = $('#form_contact_us');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);
            form.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "", // validate all fields including form hidden input
                rules: {
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
    ContactFunctions.init();
    ContactValidation.init();
});