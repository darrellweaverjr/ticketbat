var UpdateShoppingcartFunctions = function () {

    var initFunctions = function (cart) {

        ShoppingcartQtyItems.init();
        if(cart && cart.success && cart.items.length>0)
        {
            //update totals
            $('#cost_subtotal').html('$ '+parseFloat(cart.retail_price).toFixed(2));
            $('#cost_fees').html('$ '+parseFloat(cart.processing_fee).toFixed(2));
            $('#cost_savings').html('$ '+parseFloat(cart.savings).toFixed(2));
            $('#cost_printed').html('$ '+parseFloat(cart.printed).toFixed(2));
            $('#cost_total').html('$ '+parseFloat(cart.total).toFixed(2));
            $('#cost_total').data('total',parseFloat(cart.total).toFixed(2));
            //hide empty savings
            if(cart.savings>0)
                $('#cost_savings').closest('h6').removeClass('hidden');
            else
                $('#cost_savings').closest('h6').addClass('hidden');
            //hide empty printed tickets
            if(cart.printed>0)
                $('#cost_printed').closest('h6').removeClass('hidden');
            else
                $('#cost_printed').closest('h6').addClass('hidden');
            //update restrictions
            if(cart.restrictions.length>0)
            {
                $('#restrictions_panel').empty();
                $.each(data.restrictions,function(k, v) {
                    $('#restrictions_panel').append('<b style="color:#32c5d2">'+k+'</b> requires attendees to be '+v+' years of age or older.<br>');
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
            //update icons for card
            if(cart.amex_only>0)
            {
                $('#icon-mc').addClass('hidden');
                $('#icon-vs').addClass('hidden');
                $('#icon-dc').addClass('hidden');
            }
            else
            {
                $('#icon-mc').removeClass('hidden');
                $('#icon-vs').removeClass('hidden');
                $('#icon-dc').removeClass('hidden');
            }
            //update card form
            $('#form_card input[name="card"]').data('amex',cart.amex_only);

            //update cash form
            $('#form_cash input[name="pending"]').val(cart.total);
            if(cart.cash_breakdown)
            {
                $('#form_cash input[name="cashed"]').val(cart.total);
                $('#form_cash div.cash_breakdown').css('display','none');
            }
            else
                $('#form_cash div.cash_breakdown').css('display','block');
            CashFunctions.calculate();
            //update items in list
            var items_qty = (cart.items.length>1)? 'items' : 'item';
            $('#count_items').html('You currently have <b>'+cart.items.length+'</b> '+items_qty);
            $.each(cart.items,function(k, v) {
                var row = $('#tb_items tr[id="'+v.id+'"]');
                row.data('qty',v.number_of_items);
                row.find('td:nth-child(2) input').val(v.number_of_items);
                if($.isNumeric( v.available_qty ) && v.available_qty>0)
                    row.find('td:nth-child(2) input').prop('max',v.available_qty);
                else
                    row.find('td:nth-child(2) input').removeAttr('max');
                row.find('td:nth-child(3)').html('$'+((parseFloat(v.cost_per_product)).toFixed(2)));
                row.find('td:nth-child(4)').html('$'+((parseFloat(v.cost_per_product)*parseFloat(v.number_of_items)).toFixed(2)));
                if(v.inclusive_fee>0)
                    row.find('td:nth-child(5)').html('$0.00');
                else
                    row.find('td:nth-child(5)').html('$'+((parseFloat(v.processing_fee)).toFixed(2)));
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
