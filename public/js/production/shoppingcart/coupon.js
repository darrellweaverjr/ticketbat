//*****************************************************************************************
var CouponValidation = function () {
    return {
        //main function to initiate the module
        init: function () {
            // advance validation
            var rules = {
                coupon: {
                    minlength: 2,
                    maxlength: 50,
                    required: true
                }
            };
            MainFormValidation.init('form_coupon',rules,{});
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    CouponValidation.init();
});
