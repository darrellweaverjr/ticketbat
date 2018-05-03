var RegisterFunctions = function () {    
    var initFunctions = function () {
        //reset modal on load
        $('#modal_register').on('shown.bs.modal', function() { 
            $('#form_register').trigger('reset');
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/general/country', 
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
                url: '/general/region', 
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
                    url: '/user/register', 
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
            };
            MainFormValidation.init('form_register',rules,{});
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    RegisterFunctions.init();
    RegisterValidation.init();
});