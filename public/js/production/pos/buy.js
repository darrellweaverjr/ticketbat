var POSbuy = function () {

    return {

        //main function to initiate the module
        init: function () {

            $('#pos_tickets input').TouchSpin({ initval:0,min:0,step:1,decimals:0,max:100 });
            $('div.bootstrap-touchspin button').addClass('btn-lg btn-info');
            $('button.bootstrap-touchspin-up').html('<i class="fa fa-plus"></i>');
            $('button.bootstrap-touchspin-down').html('<i class="fa fa-minus"></i>');

            //update page
            function update_page(cart,show_time_id)
            {
                if(cart && cart.success)
                {
                    //update totals
                    $('#qty_total').html(cart.quantity);
                    $('#cost_total').html(parseFloat(cart.total).toFixed(2));
                    //update Tally
                    $('#t_p_tickets').html(cart.quantity);
                    $('#t_p_total').html('$'+parseFloat(cart.total).toFixed(2));
                    if(cart.tally)
                    {
                        $('#t_t_transactions').html(cart.tally.transactions);
                        $('#t_t_tickets').html(cart.tally.tickets);
                        $('#t_t_cash').html('$'+parseFloat(cart.tally.cash).toFixed(2));
                        $('#t_t_total').html('$'+parseFloat(cart.tally.total).toFixed(2));
                    }
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
                    //update cash form
                    $('#form_cash input[name="pending"]').val(cart.total);
                    CashFunctions.calculate();
                    //update items in list
                    $('#tb_items tr:gt(0)').remove();
                    $('#pos_tickets input').val(0);
                    if(cart.items.length>0)
                    {
                        $.each(cart.items,function(k, v) {
                            var show_id = $('#pos_showtimes select[name="show_time_id"]').data('show');
                            var date = moment(v.show_time);
                            //ticket
                            if(show_time_id == v.item_id)
                            {
                                var qty = parseInt( $('#pos_tickets input[name="'+v.ticket_id+'"]').val() );
                                $('#pos_tickets input[name="'+v.ticket_id+'"]').val( qty+v.number_of_items );
                                var rowDate = '<td style="text-align:center">'+date.format('MMM D, YYYY')+'<br>'+date.format('H:mm A')+'</td>';
                            }
                            else {
                                var rowDate = '<td class="label-warning" style="text-align:center">'+date.format('MMM D, YYYY')+'<br>'+date.format('H:mm A')+'</td>';
                            }
                            //cart
                            var product = '<h4 class="bold">('+v.number_of_items+') '+v.product_type;
                            if(v.package)
                                product += '<br><i>'+v.package+'</i>';
                            product += '</h4>';
                            if(show_id != v.show_id)
                                product += '<br><i class="label-warning">'+v.name+'</i>';
                            var rowItem = '<td>'+product+'</td>';
                            //var rowDate = '<td style="text-align:center">'+date.format('MMM D, YYYY')+'<br>'+date.format('H:mmA')+'</td>';
                            var rowSubtotal = '<td style="text-align:right">$'+((parseFloat(v.cost_per_product)*parseFloat(v.number_of_items)).toFixed(2))+'<br>$'+(parseFloat(v.processing_fee)).toFixed(2)+'</td>';
                            var rowBtn = '<td style="text-align:center"><button type="button" class="btn btn-lg btn-danger"><i class="fa fa-remove icon-ban"></i></button></td>';
                            $('#tb_items').append('<tr data-id="'+v.id+'">'+rowItem+rowDate+rowSubtotal+rowBtn+'</tr>');
                        });
                    }
                }
                else
                    location.reload();
            }

            //submit value
            function update_items(ticket_id=0,qty=0,id=0,update=0)
            {
                var show_time_id = $('#pos_showtimes select[name="show_time_id"]').val();
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/pos/update',
                    data: {show_time_id:show_time_id,ticket_id:ticket_id,qty:qty,id:id,update:update},
                    success: function(data) {
                        if(data.success)
                        {
                            update_page(data.cart,show_time_id);
                        }
                        else{
                            swal({
                                title: "<span style='color:red;'>Error!</span>",
                                text: data.msg,
                                html: true,
                                type: "error"
                            },function(){
                                location.reload();
                            });
                        }
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to add the ticket(s) to the cart.",
                            html: true,
                            type: "error"
                        },function(){
                            location.reload();
                        });
                    }
                });
            }

            //showtimes
            $('#pos_showtimes select[name="show_time_id"]').bind('change',function() {
                update_items(0,0,0,1);
            });

            //tickets
            $('#pos_tickets input').bind('change',function() {
                var ticket_id = $(this).attr('name');
                var qty = $(this).val();
                update_items(ticket_id,qty,0,0);
            });

            //cart remove item
            $(document).on('click', '#pos_cart button', function(e){
                var id = $(this).closest('tr').data('id');
                update_items(0,0,id,0);
            });
            
            //onclose modal complete
            $('#btn_continue').on('click', function () {
                location.reload();
            });

            //$('#modal_complete').modal('show');
            
            //function send receipt by email
            $('#btn_receipt_email').on('click', function(ev) {
                $('#modal_complete').modal('hide');
                if($('#form_receipt_email').valid())
                {
                    swal({
                        title: "Sending receipt by email",
                        text: "Please, wait.",
                        type: "info",
                        showConfirmButton: false
                    });
                    jQuery.ajax({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        type: 'POST',
                        url: '/pos/email_receipt',
                        data: $('#form_receipt_email').serializeArray(),
                        success: function(data) {
                            if(data.success)
                            {
                                swal({
                                    title: "<span style='color:green;'>Saved!</span>",
                                    text: data.msg,
                                    html: true,
                                    timer: 1500,
                                    type: "success",
                                    showConfirmButton: false
                                });
                                $('#modal_complete').modal('show');
                            }
                            else{
                                swal({
                                    title: "<span style='color:red;'>Error!</span>",
                                    text: data.msg,
                                    html: true,
                                    type: "error"
                                },function(){
                                    $('#modal_complete').modal('show');
                                });
                            }
                        },
                        error: function(){
                            swal({
                                title: "<span style='color:red;'>Error!</span>",
                                text: "There was an error trying to send the email!<br>The request could not be sent to the server.",
                                html: true,
                                type: "error"
                            },function(){
                                $('#modal_complete').modal('show');
                            });
                        }
                    });
                }
                else
                {
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to send the email!<br>You must enter a valid email.",
                        html: true,
                        type: "error"
                    },function(){
                        $('#modal_complete').modal('show');
                    });
                }
            });
            
            //on print receipt
            $('#btn_receipt_print').on('click', function(ev) {
                var receiptWindow = window.open($(this).data('href'),'TicketBat Receipt','width=300,height=700');
                receiptWindow.print();
                receiptWindow.close();
            });
            
            //on init
            $('#form_cash').trigger('reset');
            $('#form_swipe').trigger('reset');
            $('#form_card').trigger('reset');
            $('#form_skip').trigger('reset');

        } // end init

    };

}();
//*****************************************************************************************
var SubmitFunctions = function () {

    var initFunctions = function () {
               
        //on submit
        $('#btn_process').click( function(){
            var form_id = $('#tabs_payment').find('.tab-pane.active:not(.hidden)').find('form').attr('id');
            if( $('#'+form_id).valid() )
            {
                $('#btn_process').addClass('hidden');
                $('#btn_loading').removeClass('hidden');
                swal({
                    title: "Processing your item(s)",
                    text: "Please, wait.",
                    type: "info",
                    showConfirmButton: false
                });
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/purchase/process',
                    data: $('#'+form_id).serializeArray(),
                    success: function(data) {
                        if(data.success)
                        {
                            $('#btn_receipt_print').data('href','/purchase/printer/'+data.purchases);
                            $('#modal_complete a.ticket_regular').attr('href','/user/purchases/tickets/C/'+data.purchases);
                            $('#modal_complete a.ticket_boca').attr('href','/user/purchases/tickets/S/'+data.purchases);
                            $('#modal_complete a.ticket_wrist').attr('href','/user/purchases/tickets/W/'+data.purchases);
                            $('#form_receipt_email input[name="purchases"]').val(data.purchases);
                            $('#pos_tickets input[type="number"]').val(0);
                            swal.close();
                            $('#modal_complete').modal('show');
                        }
                        else
                        {
                            swal({
                                title: "<span style='color:red;'>Error!</span>",
                                text: data.msg,
                                html: true,
                                type: "error"
                            },function(){
                                $('#btn_loading').addClass('hidden');
                                $('#btn_process').removeClass('hidden');
                                $('#btn_process').prop('disabled',true);
                            });
                        }
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to process the item(s). Please, contact us.",
                            html: true,
                            type: "error",
                            showConfirmButton: true
                        },function(){
                            $('#btn_loading').addClass('hidden');
                            $('#btn_process').removeClass('hidden');
                            $('#btn_process').prop('disabled',true);
                        });
                    }
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
var FormValidation = function () {
    // advance validation
    var handleValidation = function() {
        // for more info visit the official plugin documentation:
        // http://docs.jquery.com/Plugins/Validation
            var form = $('#form_receipt_email');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);
            //IMPORTANT: update CKEDITOR textarea with actual content before submit
            form.on('submit', function() {
                for(var instanceName in CKEDITOR.instances) {
                    CKEDITOR.instances[instanceName].updateElement();
                }
            })
            form.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "", // validate all fields including form hidden input
                rules: {
                    email: {
                        minlength: 8,
                        maxlength: 50,
                        email: true,
                        required: true
                    }
                },
                invalidHandler: function (event, validator) { //display error alert on form submit
                    success.hide();
                    error.show();
                    App.scrollTo(error, -200);
                },

                highlight: function (element) { // hightlight error inputs
                   $(element)
                        .closest('.show-error').addClass('has-error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change done by hightlight
                    $(element)
                        .closest('.show-error').removeClass('has-error'); // set error class to the control group
                },

                success: function (label) {
                    label
                        .closest('.show-error').removeClass('has-error'); // set success class to the control group
                },

                submitHandler: function (form) {
                    success.show();
                    error.hide();
                    form[0].submit(); // submit the form
                }
            });
    }
    return {
        //main function to initiate the module
        init: function () {
            handleValidation();
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
   POSbuy.init();
   SubmitFunctions.init();
   FormValidation.init();
});
