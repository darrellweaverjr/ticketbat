//*****************************************************************************************
var SkipValidation = function () {
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
            };
            MainFormValidation.init('form_skip',rules,{});
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    SkipValidation.init();
});
