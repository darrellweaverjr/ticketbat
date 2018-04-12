var CardFunctions = function () {

    var initFunctions = function () {

        //on change country select
        $('#form_card select[name="country"]').on('change', function(ev) {
            var country_code = $(this).val();
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/general/region',
                data: { country: country_code },
                success: function(data) {
                    if(data.success)
                    {
                        //fill out states
                        $('select[name="state"]').find('option').not(':first').remove();
                        $.each(data.regions,function(k, v) {
                            $('select[name="state"]').append('<option value="'+v.code+'">'+v.name+'</option>');
                        });
                        $('select[name="state"]').val('');
                        //check the zip code
                        $('input[name="zip"]').rules('remove');
                        if(country_code=='US' || country_code=='UM')
                        {
                            $('input[name="zip"]').rules('add', {
                                minlength: 5,
                                maxlength: 5,
                                digits: true,
                                range: [00500, 99999],
                                required: true
                            });
                        }
                        else
                        {
                            $('input[name="zip"]').rules('add', {
                                minlength: 3,
                                maxlength: 10,
                                required: true
                            });
                        }
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
                        minlength: 15,
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
                    month: {
                        range: [1,12],
                        digits: true,
                        required: true
                    },
                    year: {
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
                        range: [00100, 99999],
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
jQuery(document).ready(function() {
    CardFunctions.init();
    CardValidation.init();
});
