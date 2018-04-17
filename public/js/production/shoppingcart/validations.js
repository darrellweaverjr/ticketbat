var ValidateFunctions = function () {

    var initFunctions = function () {

        //on change any element re-check
        $('a[href^="#tab_"]').on('click', function(ev) {
            $('#accept_terms').prop('checked', false);

        });
        //on input or select change, disable submit to re-check values
        $('#tabs_payment input, #tabs_payment select').on('change', function(e){
            var form_id = $('#tabs_payment').find('.tab-pane.active:not(.hidden)').find('form').attr('id');
            if(!$('#'+form_id).validate().element( $(this) ))
            {
                $('#accept_terms').prop('checked', false);
                $('#btn_process').prop('disabled',true);
            }
        });

        //on seller button shorcut to check and process
        $('#btn_check_pay').on('click', function(ev) {
            $('#accept_terms').trigger('click');
            if($('#btn_process').prop('disabled')==false)
                $('#btn_process').trigger('click');
        });

        //on accept terms
        $('#accept_terms').bind('click','change', function(e){
            var proceed = false;
            var form_id = $('#tabs_payment').find('.tab-pane.active:not(.hidden)').find('form').attr('id');
            $('.alert-warning', $('#'+form_id) ).hide();
            $('.alert-danger', $('#'+form_id) ).hide();
            if( $(this).is(':checked') )
            {
                if( $('#'+form_id).valid() )
                {
                    switch(form_id)
                    {
                        case 'form_skip':
                            proceed = true;
                            break;
                        case 'form_card':
                            var amex_only = $('#form_card input[name="card"]').data('amex');
                            var year = parseInt($('#form_card select[name="year"]').val());
                            var month = parseInt($('#form_card select[name="month"]').val())-1;
                            if( amex_only>0 )
                                var exp_card =/^3[47][0-9]{13}$/;
                            else
                                var exp_card =/^(?:(4[0-9]{12}(?:[0-9]{3})?)|(5[1-5][0-9]{14})|(6(?:011|5[0-9]{2})[0-9]{12})|(3[47][0-9]{13})|(3(?:0[0-5]|[68][0-9])[0-9]{11})|((?:2131|1800|35[0-9]{3})[0-9]{11}))$/;
                            if(!exp_card.test( $('#form_card input[name="card"]').val() ))
                            {
                                if(amex_only>0)
                                    $('.alert-warning', $('#form_card') ).html('You must enter a valid Amerian Express credit card').show();
                                else
                                    $('.alert-warning', $('#form_card') ).html('You must enter a valid credit card').show();
                            }
                            else if(new Date() > new Date(year,month,1))
                            {
                                $('.alert-warning', $('#form_card') ).html('The credit card has expire. Please, check the month and year or try a different one.').show();
                            }
                            else
                                proceed = true;
                            break;
                        case 'form_swipe':
                            proceed = true;
                            break;
                        case 'form_cash':
                            proceed = true;
                            break;
                    }
                }
                //uncheck btn
                if(proceed)
                {
                    $('#btn_process').prop('disabled',false);
                }
                else
                {
                    $('#btn_process').prop('disabled',true);
                    e.preventDefault();
                }
            }
            else
                $('#btn_process').prop('disabled',true);
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
jQuery(document).ready(function() {
    ValidateFunctions.init();
});
