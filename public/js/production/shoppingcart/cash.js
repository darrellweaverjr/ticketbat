var CashFunctions = function () {

    var initFunctions = function () {

        //on button click number
        $('#form_cash button[name^="cash_"]').bind('click', function(ev) {
            var value = $(this).val();
            var cashed = $('#form_cash input[name="cashed"]').data('number');
            if(value=='')
                cashed = '0';
            else
                cashed = (cashed == '0')? ( (value=='.')? '0.' : value ) : ( cashed + value );
            $('#form_cash input[name="cashed"]').data('number', cashed);
            $('#form_cash input[name="cashed"]').val( parseFloat(cashed).toFixed(2) ).trigger('change');
        });

        //on button click plus
        $('#form_cash button[name^="plus_"]').bind('click', function(ev) {
            var value = $(this).val();
            var cashed = $('#form_cash input[name="cashed"]').data('number');
            cashed = (cashed == '0')? ( value ) : ( (parseFloat(cashed)+parseFloat(value)) );
            $('#form_cash input[name="cashed"]').data('number', cashed);
            $('#form_cash input[name="cashed"]').val( parseFloat(cashed).toFixed(2) ).trigger('change');
        });

        //on change cashed
        $('#form_cash input[name="cashed"]').bind('change','click', function(ev) {
            var cashed = parseFloat($(this).val()).toFixed(2);
            if(cashed<0)
                cashed = 0;
            $('#form_cash input[name="cashed"]').val( cashed );
            calcFunctions();
        });
    }

    var calcFunctions = function () {
        //function to calculate cash
        var cashed = parseFloat($('#form_cash input[name="cashed"]').val()).toFixed(2);
        var pending = parseFloat($('#form_cash input[name="pending"]').val()).toFixed(2);
        var total = parseFloat(pending-cashed).toFixed(2);
        if(total>0)
        {
            $('#label_total').html('Due ($):');
            $('#form_cash input[name="subtotal"]').css('color','red');
        }
        else if(total<0)
        {
            $('#label_total').html('Change ($):');
            $('#form_cash input[name="subtotal"]').css('color','green');
        }
        else
        {
            $('#form_cash input[name="subtotal"]').css('color','black');
        }
        $('#form_cash input[name="subtotal"]').val( (total*-1).toFixed(2) );
        $('#form_cash input[name="subtotal"]').validate();
    }

    return {
        //main function to initiate the module
        init: function () {
            initFunctions();
        },
        calculate: function () {
            calcFunctions();
        }
    };
}();
//*****************************************************************************************
var CashValidation = function () {
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
                },
                subtotal: {
                    min: 0,
                    number: true,
                    required: true
                }
            };
            var showErrors = { "subtotal":"You must collect at least the quantity required here to proceed." };
            MainFormValidation.init('form_cash',rules,showErrors);
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    CashFunctions.init();
    CashValidation.init();
});
