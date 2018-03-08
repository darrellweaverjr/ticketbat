var CashFunctions = function () {
    
    var initFunctions = function () {
        
        //on button click
        $('#form_cash button[name^="cash_"]').bind('click', function(ev) {
            var value = $(this).val();
            var cashed = $('#form_cash input[name="cashed"]').val();
            if($.isNumeric(value))
            {
                if(value=='0')
                    value = parseFloat(cashed*10).toFixed(2);
                else
                {
                    cashed = cashed.replace('.','').replace(/\b0+/g, '')+value;
                    value = (parseFloat(cashed)/100).toFixed(2);
                }
            }
            else 
                value = '0.00';
            $('#form_cash input[name="cashed"]').val( value ).trigger('change');
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
        $('#form_cash input[name="subtotal"]').val(total*-1);
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
jQuery(document).ready(function() {
    CashFunctions.init();
});