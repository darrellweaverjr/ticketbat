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
                                title: "<span style='color:green;'>Thanks for your email!</span>",
                                text: data.msg,
                                html: true,
                                type: "success"
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
    return {
        //main function to initiate the module
        init: function () {
            // advance validation
            var rules = {
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
            };
            MainFormValidation.init('form_contact_us',rules,{});
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    ContactFunctions.init();
    ContactValidation.init();
});