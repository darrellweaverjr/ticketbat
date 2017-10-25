var UpdateShoppingcartFunctions = function () {
    
    var initFunctions = function (cart) {
        
        if(cart && cart.success && cart.items.length>0)
        {
            //update totals
            $('#cost_subtotal').val('$ '+cart.retail_price);
            $('#cost_fees').val('$ '+cart.processing_fee);
            $('#cost_savings').val('$ '+cart.savings);
            $('#cost_printed').val('$ '+cart.printed);
            $('#cost_total').val('$ '+cart.total);
            //hide empty savings
            if(cart.savings>0)
                $('#cost_savings').closest('h4').removeClass('hidden');
            else
                $('#cost_savings').closest('h4').addClass('hidden');
            //hide empty printed tickets
            if(cart.printed>0)
                $('#cost_printed').closest('h4').removeClass('hidden');
            else
                $('#cost_printed').closest('h4').addClass('hidden');
            //update restrictions
            if(cart.restrictions.length>0)
            {
                $('#restrictions_panel').empty();
                $.each(data.restrictions,function(k, v) {
                    $('#restrictions_panel').append('<b style="color:#32c5d2">'+k+'</b> requires to be '+v+' years of age or older to attend the event.<br>');
                });
            }
            else
                $('#restrictions_panel').closest('div.row').addClass('hidden');
            //update ticket options
            $('select[name="printed_tickets"]').val(cart.printed_tickets.select);
            if(cart.printed_tickets.shows.length>0)
            {
                if(cart.printed_tickets.details < 1)
                    $('#printed_details').addClass('hidden');
                else
                {
                    var multi = (cart.printed_tickets.shows.length>1)? ' these shows ' : ' this show ';
                    $('#printed_details').append('All tickets for '+multi+' will be mailed if you pick a printed ticket option:<br>');
                    $.each(cart.printed_tickets.shows,function(k, v) {
                        $('#printed_details').append('<b style="color:#32c5d2">'+v+'</b><br>');
                    });
                    $('#printed_details').append('Other shows are only available as eTickets and will not be shipped if you choose a printed option.');
                }
            }
            else
                $('select[name="printed_tickets"]').closest('div.row').addClass('hidden');
            //update payment tabs
            if(cart.total>0)
            {
                $('#tab_skip').addClass('hidden');
                $('#tab_card').removeClass('hidden');
                if(cart.seller>0)
                {
                    $('#tab_swipe').removeClass('hidden');
                    $('#tab_cash').removeClass('hidden');
                }
                else
                {
                    $('#tab_swipe').addClass('hidden');
                    $('#tab_cash').addClass('hidden');
                }
            }
            else
            {
                $('#tab_skip').removeClass('hidden');
                $('#tab_card').addClass('hidden');
                $('#tab_swipe').addClass('hidden');
                $('#tab_cash').addClass('hidden');
            }
            //update others
            $('#form_card input[name="card"]').data('amex',cart.amex_only);
            $('#form_cash input[name="pending"]').data('pending',cart.total);
            $('#form_cash input[name="pending"]').val(cart.total*-1);
            //update items in list
            $.each(cart.items,function(k, v) {
                var row = $('#tb_items tr[id="'+v.id+'"]');
                row.data('qty',v.number_of_items);
                row.find('td:nth-child(2) input').val(v.number_of_items);
                if(v.available_qty<0)
                    row.find('td:nth-child(2) input').prop('max',1000);
                else
                    row.find('td:nth-child(2) input').prop('max',v.available_qty));
                row.find('td:nth-child(3)').html('$'+((v.cost_per_product).toFixed(2)));
                row.find('td:nth-child(4)').html('$'+((v.cost_per_product*v.number_of_items).toFixed(2)));
                row.find('td:nth-child(5)').html('$'+((v.processing_fee).toFixed(2)));
            });
        }
        else
            location.reload(); 
    }
    return {
        //main function to initiate the module
        init: function (cart) {
            initFunctions(cart);        
        }
    };
}();
//*****************************************************************************************