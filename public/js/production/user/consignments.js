var ConsignmentsFunctions = function () {    
    var initFunctions = function () {
        
        //search consignments on checkbox change
        $('#form_search_consignments input:checkbox').on('change', function(ev) {
            swal({
                title: "Loading...",
                text: "Please, wait.",
                type: "info",
                showConfirmButton: false
            });
            $('#form_search_consignments').submit();
        });
        //open form to edit consignments
        function modal_edit_consignments(consignment_id)
        {
            swal({
                title: "Loading consignment...",
                text: "Please, wait.",
                type: "info",
                showConfirmButton: false
            });
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/user/consignments', 
                data: {id:consignment_id}, 
                success: function(data) {
                    if(data.success) 
                    {
                        $('#form_update_consignment input[name="consignment_id"]').val(consignment_id);
                        var shoppingcart_items = [];
                        //fill out summary
                        var def_v = '# 0<br>$ 0.00';
                        $('#summary_available').html(def_v);
                        $('#summary_sold').html(def_v);
                        $('#summary_checked').html(def_v);
                        $('#summary_voided').html(def_v);
                        $('#summary_shoppingcart').html(def_v);
                        $.each(data.summary,function(k, v) {
                            switch(v.status)
                            {
                                case 'Created':
                                    $('#summary_available').html('# '+v.qty+'<br>$ '+v.total);
                                    break;
                                case 'Sold':
                                    $('#summary_sold').html('# '+v.qty+'<br>$ '+v.total);
                                    break;
                                case 'Checked':
                                    $('#summary_checked').html('# '+v.qty+'<br>$ '+v.total);
                                    break;
                                case 'Voided':
                                    $('#summary_voided').html('# '+v.qty+'<br>$ '+v.total);
                                    break;
                                case 'Shoppingcart':
                                    $('#summary_shoppingcart').html('# '+v.qty+'<br>$ '+v.total);
                                    shoppingcart_items = v.seats;
                                    break;
                                default:
                                    break;
                            }
                        });
                        //reset checked values
                        $('#form_update_consignment input[name="total_qty"]').val(0);
                        $('#form_update_consignment input[name="total_money"]').val(0);
                        //fill out table
                        $('#tb_update_consignment_body').empty();
                        $.each(data.seats,function(k, v) {
                            var row_number = '<td>'+(parseInt(k)+1)+'</td>';
                            var row_section = '<td>'+v.ticket_type+'</td>';
                            var row_seat = '<td><center>'+v.seat+'</center></td>';
                            var row_retail_price = '<td style="text-align:right">$ '+v.retail_price+'</td>';
                            var row_processing_fee = '<td style="text-align:right">$ '+v.processing_fee+'</td>';
                            var row_total = '<td style="text-align:right">$ '+v.total+'</td>';
                            if(jQuery.inArray( v.id, shoppingcart_items )>=0)
                                v.status = 'Shoppingcart';
                            switch(v.status)
                            {
                                case 'Created':
                                    var row_color = '<tr>';
                                    var row_status = '<td><center><span class="label label-sm label-success"><b>Available</b></span></center></td>';
                                    var row_check = '<td><center><label class="mt-checkbox"><input class="checkboxes" type="checkbox" data-price="'+v.total+'" name="seat[]" value="'+v.id+'"><span></span><i class="fa fa-check"></i></label></center></td>';
                                    break;
                                case 'Sold':
                                    var row_color = '<tr style="color:LightSlateGray!important">';
                                    var row_status = '<td><center><span class="label label-sm label-warning"><b>'+v.status+'</b></span></center></td>';
                                    var row_check = '<td><center><i class="fa fa-close"></i></center></td>';
                                    break;
                                case 'Checked':
                                    var row_color = '<tr style="color:LightSlateGray!important">';
                                    var row_status = '<td><center><span class="label label-sm label-danger"><b>'+v.status+'</b></span></center></td>';
                                    var row_check = '<td><center><i class="fa fa-close"></i></center></td>';
                                    break;
                                case 'Voided':
                                    var row_color = '<tr style="color:LightSlateGray!important">';
                                    var row_status = '<td><center><span class="label label-sm label-default"><b>'+v.status+'</b></span></center></td>';
                                    var row_check = '<td><center><i class="fa fa-close"></i></center></td>';
                                    break;
                                case 'Shoppingcart':
                                    var row_color = '<tr style="color:LightSlateGray!important">';
                                    var row_status = '<td><center><span class="label label-sm label-info"><b>'+v.status+'</b></span></center></td>';
                                    var row_check = '<td><center><i class="fa fa-close"></i></center></td>';
                                    break;
                                default:
                                    var row_color = '<tr style="color:LightSlateGray!important">';
                                    var row_status = '<td><center><span class="label label-sm label-default"><b>'+v.status+'</b></span></center></td>';
                                    var row_check = '<td><center><i class="fa fa-close"></i></center></td>';
                                    break;
                            }
                            $('#tb_update_consignment_body').append(row_color+row_number+row_section+row_seat+row_retail_price+row_processing_fee+row_total+row_status+row_check+'</tr>');
                            
                        });
                        //show modal
                        swal.close();
                        $('#modal_update_consignment').modal('show');
                    }
                    else{
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error",
                            showConfirmButton: true
                        });
                    }
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to load the purchase.",
                        html: true,
                        type: "error",
                        showConfirmButton: true
                    });
                }
            });
        }
        //open form to sign consignments
        function modal_sign_consignments(consignment_id)
        {
            swal({
                title: "Loading agreement...",
                text: "Please, wait.",
                type: "info",
                showConfirmButton: false
            });
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/user/consignments/contract', 
                data: {id:consignment_id}, 
                success: function(data) {
                    if(data.success) 
                    {
                        $('#contract_agreement').html(data.contract);
                        $('#btn_sign_consignment').data('id',consignment_id);
                        swal.close();
                        $('#modal_sign_consignment').modal('show');
                    }
                    else{
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error",
                            showConfirmButton: true
                        });
                    }
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to load the contract.",
                        html: true,
                        type: "error",
                        showConfirmButton: true
                    });
                }
            });
        }
        //open consignments edit form
        $('#tb_consignments button').on('click', function(ev) {
            var consignment_id = $(this).data('id');
            if(consignment_id)
                modal_edit_consignments(consignment_id);
            else
            {
                var consignment_id = $(this).data('sign');
                modal_sign_consignments(consignment_id);
            }    
        });
        //check the row when click the body of the table
        $('#tb_update_consignment_body').on('click', 'tr td:not(:last-child)', function () {
            var action = $(this).parent().find('.checkboxes').is(':checked');
            $(this).parent().find('.checkboxes').prop('checked',!action).trigger('change');
        });
        //checkbox tickets change update totals
        $(document).on('change', '#form_update_consignment input:checkbox', function(e){
            var qty = 0;
            var total = 0.00;
            $('#form_update_consignment input:checkbox:checked').each(function() {
                qty ++;
                total += parseFloat($(this).data('price'));
            });
            $('#form_update_consignment input[name="total_qty"]').val(qty);
            $('#form_update_consignment input[name="total_money"]').val(total.toFixed(2));
            ($(this).prop('checked'))? $(this).parents('tr').addClass('success') : $(this).parents('tr').removeClass('success');
        }); 
                
        //function confirm buy tickets
        function confirm_tickets(consignment_id=false){
            swal({
                title: "Updating tickets...",
                text: "Please, wait.",
                type: "info",
                showConfirmButton: false
            });
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/user/consignments/save', 
                data: $('#form_update_consignment').serializeArray(),
                success: function(data) {
                    if(data.success)
                    {
                        $('#modal_confirm_consignment').modal('hide');
                        swal.close();
                        ShoppingcartQtyItems.init();
                        if(consignment_id!=false)
                            modal_edit_consignments(consignment_id);
                        else
                            window.location.href = '/shoppingcart/viewcart';
                    }
                    else swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error"
                        },function(){
                        $('#modal_update_consignment').modal('show');
                    });
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error updating the ticket(s)!",
                        html: true,
                        type: "error"
                    },function(){
                        $('#modal_update_consignment').modal('show');
                    });
                }
            });
        };
        //open confirm modal 
        $('#btn_update_consignment').on('click', function(ev) {
            var seats = $('#form_update_consignment input:checkbox:checked');
            $('#modal_update_consignment').modal('hide');
            if(seats.length>0)
            {
                $('#confirm_body input[name="total_qty"]').val( $('#form_update_consignment input[name="total_qty"]').val() );
                $('#confirm_body input[name="total_money"]').val( $('#form_update_consignment input[name="total_money"]').val() );
                seats.each(function(k, v) {
                    var number = (parseInt(k)+1);
                    var section = $(this).closest('tr').find('td:nth-child(2)').text();
                    var seat = $(this).closest('tr').find('td:nth-child(3)').text();
                    var price = $(this).closest('tr').find('td:nth-child(6)').text();
                    $('#tb_confirm_consignment_body').append('<tr><td>'+number+'</td><td>'+section+'</td><td>'+seat+'</td><td>'+price+'</td></tr>');
                });
                $('#modal_confirm_consignment').modal('show');
            }
            else
            {
                swal({
                    title: "<span style='color:red;'>Error!</span>",
                    text: 'You must select at least one ticket to purchase.',
                    html: true,
                    type: "error"
                },function(){
                    $('#modal_update_consignment').modal('show');
                });
            }
        });
        //purchase and return to purchase modal
        $('#btn_confirm_consignment').on('click', function(ev) {
            var consignment_id = $('#form_update_consignment input[name="consignment_id"]').val();
            confirm_tickets(consignment_id);
        });
        //purchase and go to shopping cart
        $('#btn_confirm_shoppingcart').on('click', function(ev) {
            confirm_tickets();
        });
        //cancel confirm modal
        $('#btn_confirm_cancel').on('click', function () { 
            $('#modal_confirm_consignment').modal('hide');
            $('#modal_update_consignment').modal('show');
        });
        //sign consignment form
        $('#btn_sign_consignment').on('click', function(ev) {
            $('#modal_sign_consignment').modal('hide');
            var consignment_id = $('#btn_sign_consignment').data('id');
            if(consignment_id)
            {
                swal({
                    title: "You are about to sign and receive the consignment tickets #"+consignment_id+'</b>',
                    text: 'Are you sure?',
                    html: true,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Sign & receive",
                    cancelButtonText: "Cancel",
                    closeOnConfirm: false,
                    closeOnCancel: true
                  },
                  function(isConfirm) {
                    if (isConfirm) {
                        swal({
                            title: "Signing agreement...",
                            text: "Please, wait.",
                            type: "info",
                            showConfirmButton: false
                        });
                        jQuery.ajax({
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            type: 'POST',
                            url: '/user/consignments/contract', 
                            data: {id:consignment_id, signed:1}, 
                            success: function(data) {
                                if(data.success) 
                                {
                                    swal({
                                        title: "<span style='color:green;'>Accepted!</span>",
                                        text: data.msg,
                                        html: true,
                                        timer: 1500,
                                        type: "success",
                                        showConfirmButton: false
                                    },function(){
                                        location.reload();
                                    });
                                }
                                else{
                                    swal({
                                        title: "<span style='color:red;'>Error!</span>",
                                        text: data.msg,
                                        html: true,
                                        type: "error",
                                        showConfirmButton: true
                                    },function(){
                                        $('#modal_sign_consignment').modal('show');
                                    });
                                }
                            },
                            error: function(){
                                swal({
                                    title: "<span style='color:red;'>Error!</span>",
                                    text: "There was an error trying to load the contract.",
                                    html: true,
                                    type: "error",
                                    showConfirmButton: true
                                },function(){
                                    $('#modal_sign_consignment').modal('show');
                                });
                            }
                        });
                    } 
                });
            }
            else
            {
                swal({
                    title: "<span style='color:red;'>Error!</span>",
                    text: 'You must select at least one consignment to sign.',
                    html: true,
                    type: "error"
                },function(){
                    $('#modal_sign_consignment').modal('show');
                });
            }
        });
        //init
        $('input[name="total_qty"]').TouchSpin({ initval:0,min:0,decimals:0,max:1000,prefix:'#',buttondown_class: "hidden",buttonup_class: "hidden" });
        $('input[name="total_money"]').TouchSpin({ initval:0.00,min:0.00,decimals:2,max:1000000,prefix:'$',buttondown_class: "hidden",buttonup_class: "hidden" });
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
    ConsignmentsFunctions.init();
});