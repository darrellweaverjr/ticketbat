var FunctionsManaged = function () {
    
    var initFunctions = function () {
        
        //update ticket on select 
        $('#form_model_update input[name="ticket_id"]').bind('click','change',function(){
            update_price();
        });
        //update qty select
        $('#form_model_update select[name="qty"]').change(function(){
            var price = parseFloat($(this).data('price'));
            var qty = $(this).find('option:selected').val();
            var totals = (price*qty).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");;
            $('#totals').html('$ '+totals);
        });
        //function to get the price of selected radio
        function update_price()
        {
            var e = $('#form_model_update input[name="ticket_id"]:checked');
            var price = parseFloat(e.data('price'));
            $('#form_model_update select[name="qty"]').data('price', price);
            var max = parseInt(e.data('max'));
            $('#form_model_update select[name="qty"]').empty();
            for(var i = 1; i <= max; i++)
                $('#form_model_update select[name="qty"]').append('<option value="'+i+'">'+i+'</option>');
            $('#form_model_update select[name="qty"]').val(1).trigger('change');
        }
        //autoselect first one
        $('#form_model_update input:radio.default_radio').attr('checked',true).trigger('change');
        update_price();
                                
        
    }
    return {
        //main function to initiate the module
        init: function () {
            initFunctions();        
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    FunctionsManaged.init();
});