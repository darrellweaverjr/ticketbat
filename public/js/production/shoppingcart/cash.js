var CashFunctions = function () {
    
    var initFunctions = function () {
        //on change bill
        $('#tab_cash input[name^="x"]').bind('change','click', function(ev) {
            var bill = parseFloat($(this).val()).toFixed();
            var data_bill = parseInt($(this).data('bill'));
            if(bill<0)
                $(this).val(bill*-1);
            else if(bill>parseInt($(this).attr('max')))
                $(this).val($(this).attr('max'));
            else
                $(this).val(bill);
            $('#tab_cash input[name^="r"]').each( function(k, v) {
                if( parseInt($(this).data('bill')) == data_bill )
                    $(this).val( (bill*data_bill).toFixed(2) );  
            });
            calcFunctions();
        });
        //on change change
        $('#tab_cash input[name="change"]').bind('change','click', function(ev) {
            var change = parseFloat($(this).val()).toFixed();
            if(change<0 || change>parseInt($(this).attr('max')))
                $(this).val($(this).attr('max'));
            else
                $(this).val(change);
            calcFunctions();
        });
        
    }
    
    var calcFunctions = function () {
        //function to calculate cash
        var subtotal = 0;
        $.each( $('#tab_cash input[name^="x"]'), function () {
            subtotal += parseInt($(this).val()) * parseInt($(this).data('bill'));
        });
        subtotal += $('#tab_cash input[name="change"]').val()/100;
        var pending = -1*parseFloat($('#cost_total').data('total')) + subtotal;
        $('#tab_cash input[name="subtotal"]').val( subtotal.toFixed(2) );
        $('#tab_cash input[name="pending"]').val( pending.toFixed(2) );
        if(pending<0)
        {
            $('#collect_text').html('Collect');
            $('#tab_cash input[name="pending"]').css('color','red');
        }
        else
        {
            $('#collect_text').html('Change');
            $('#tab_cash input[name="pending"]').css('color','green');
        } 
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