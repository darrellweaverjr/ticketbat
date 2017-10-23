var ShareFunctions = function () {
    
    var initFunctions = function () {
        
        //load share tickets
        $('#tb_items tr > td:nth-child(6) button').on('click', function(ev) {
            var qty = parseInt($(this).data('qty'));            
            //load values for x form
            var data;            
            //ShareTicketsFunctions.load(data,qty); 
            $('#modal_share_tickets').modal('show');
        });
        
        //function save
        $('#btn_share_tickets').on('click', function(ev) {
            //submit values of tickets
            if( ShareTicketsFunctions.check() )
            {  
                $('#modal_share_tickets').modal('hide');
                swal({
                    title: "Sharing your tickets...",
                    text: "Please, wait.",
                    type: "info",
                    showConfirmButton: false
                });
                swal({
                    title: "<span style='color:green;'>Saved!</span>",
                    //text: data.msg,
                    text: 'tickets saved', 
                    html: true,
                    timer: 1500,
                    type: "success",
                    showConfirmButton: false
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
var PurchaseFunctions = function () {
    
    var initFunctions = function () {
        
        //remove item
        $('#tb_items tr > td:last-child button').on('click', function(ev) {
            alert('removed');
        });
        
        //on change country select
        $('select[name="country"]').on('change', function(ev) {
            var country_code = $(this).val();
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/production/general/region', 
                data: { country: country_code }, 
                success: function(data) {
                    if(data.success) 
                    {
                        $('select[name="state"]').empty();
                        $.each(data.regions,function(k, v) {
                            $('select[name="state"]').append('<option value="'+v.code+'">'+v.name+'</option>');
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
        
    }
    return {
        //main function to initiate the module
        init: function () {
            initFunctions();        
        }
    };
}();
//*****************************************************************************************
var SwipeCardFunctions = function () {
    
    var initFunctions = function () {
        
        //on click swipe card
        $('a[href="#tab_swipe"]').on('click', function(ev) {
            //reset form here too
            $('#modal_swipe_card').modal('show');
            $('#modal_swipe_card input[name="stripe_card"]').val('');
            $('#modal_swipe_card input[name="stripe_card"]').focus();
        });
        //on modal swipe card on click
        $('#modal_swipe_card').on('click', function(ev) {
            $('#modal_swipe_card input[name="stripe_card"]').val('');
            $('#modal_swipe_card input[name="stripe_card"]').focus();
        });
        //swipe card
        $('#modal_swipe_card input[name="stripe_card"]').blur(function (e) {
            e.preventDefault();
            $('#modal_swipe_card').modal('hide');
            $('#tab_swipe input[name="customer"]').focus();
        }).keyup(function (e) {
            if($(this).val().substr($(this).val().length-1)=="?") 
            {
                if(valid_swipe_credit_card($(this).val()))
                {
                    $('#modal_swipe_card').modal('hide');
                    $('#tab_swipe input[name="customer"]').focus();
                }
            }
        });
        //event to check swipe
        function valid_swipe_credit_card(card_data)
        {
            var card_tracks = card_data.split("?");
            var valid_track1 = /^%B[^\^\W]{0,19}\^[^\^]{2,26}\^\d{4}\w{3}[^?]+\?\w?$/.test(card_tracks[0]+'?');
            var valid_track2 = /;[^=]{0,19}=\d{4}\w{3}[^?]+\?\w?/.test(card_tracks[1]+'?');
            if(valid_track1 && valid_track2)
            {
                var details1 = card_data.split("^");
                var card_number = details1[0];
                card_number = card_number.substring(2);
                var names = details1[1].split("/");
                var first_name = names[1].trim();
                var last_name = names[0].trim();
                var details2 = details1[2].split(";");
                details2 = details2[1].split("=");
                var exp_date = details2[1];
                exp_date = exp_date.substring(0, exp_date.length - 1);
                var month = exp_date.substring(2, 4);
                var year = exp_date.substring(0, 2);
                $('#tab_swipe input[name="UMmagstripe"]').val(card_data);
                $('#tab_swipe input[name="customer"]').val(first_name + ' ' + last_name);
                $('#tab_swipe input[name="card"]').val(card_number);
                $('#tab_swipe input[name="exp_month"]').val(month);
                $('#tab_swipe input[name="exp_year"]').val(year);
                return true;
            }
            else {
                alert('Could not be correctly read the card.');
                return false;
            }
        }
        
    }
    return {
        //main function to initiate the module
        init: function () {
            initFunctions();        
        }
    };
}();
//*****************************************************************************************
//*****************************************************************************************
var CashFunctions = function () {
    
    var initFunctions = function () {
        
        function calculate_cash()
        {
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
            calculate_cash();
        });
        
        //on change change
        $('#tab_cash input[name="change"]').bind('change','click', function(ev) {
            var change = parseFloat($(this).val()).toFixed();
            if(change<0 || change>parseInt($(this).attr('max')))
                $(this).val($(this).attr('max'));
            else
                $(this).val(change);
            calculate_cash();
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
    ShareFunctions.init();
    SwipeCardFunctions.init();
    PurchaseFunctions.init();
    CashFunctions.init();
});