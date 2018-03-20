//*****************************************************************************************
var SkipValidation = function () {
    // advance validation
    var handleValidation = function() {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation
            var form = $('#form_skip');
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
                        range: [00500, 99999],
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
var SwipeValidation = function () {
    // advance validation
    var handleValidation = function() {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation
            var form = $('#form_swipe');
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
                        minlength: 16,
                        maxlength: 16,
                        creditcard: true,
                        digits: true,
                        required: true
                    },
                    month: {
                        range: [1,12],
                        digits: true,
                        required: true
                    },
                    year: {
                        minlength: 2,
                        maxlength: 4,
                        digits: true,
                        required: true
                    },
                    UMmagstripe: {
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
var CashValidation = function () {
    // advance validation
    var handleValidation = function() {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation
            var form = $('#form_cash');
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
                    subtotal: {
                        min: 0,
                        number: true,
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
            }).showErrors({
                "subtotal": "You must collect at least the quantity required here to proceed."
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
var CouponValidation = function () {
    // advance validation
    var handleValidation = function() {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation
            var form = $('#form_coupon');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);
            form.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "", // validate all fields including form hidden input
                rules: {
                    coupon: {
                        minlength: 2,
                        maxlength: 50,
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
    SkipValidation.init();
    CardValidation.init();
    SwipeValidation.init();
    CashValidation.init();
    CouponValidation.init();
});