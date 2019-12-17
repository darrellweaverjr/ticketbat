var RefundDatatablesManaged = function () {
    
    var initTable = function () {
        
        //PERSONALIZED FUNCTIONS
        //function refund on load modal
        $('#btn_model_refund').on('click', function(ev) {
            var ids = [];
            var set = $('.group-checkable').attr("data-set");
            var checked = $(set+"[type=checkbox]:checked");
            jQuery(checked).each(function (key, item) {
                ids.push(item.id);
            });
            ids = ids.join('-');      
            $('#tb_body_pendings').empty();
            $('#form_model_refund').trigger('reset');
            swal({
                title: "Checking purchase(s) to be refunded",
                text: "Please, wait.",
                type: "info",
                showConfirmButton: false
            });
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/purchases/check', 
                data: {ids:ids}, 
                success: function(data) {
                    if(data.success) 
                    {
                        $('#form_model_refund [name="id"]').val(ids); 
                        if(data.qty==1)
                        {
                            $('#purchase_details').css('display','none');
                            $('#refund_details').css('display','block');
                            $('#tb_body_pendings').empty();
                            if(data.refunds.purchase)
                            {
                                var created = moment(data.refunds.purchase.created);
                                $('#tb_body_pendings').append('<tr class="success"><td>'+data.refunds.purchase.id+'</td><td>'+data.refunds.purchase.quantity+'</td><td>'+data.refunds.purchase.retail_price+'</td><td>-'+data.refunds.purchase.savings
                                                         +'</td><td>'+data.refunds.purchase.processing_fee+'</td><td>'+data.refunds.purchase.printed_fee+'</td><td>'+data.refunds.purchase.sales_taxes+'</td><td>'+data.refunds.purchase.commission_percent
                                                         +'</td><td>'+data.refunds.purchase.price_paid+'</td><td>'+created.format('M/DD/YYYY @ h:mmA')+'</td></tr>');
                            } 
                            $.each(data.refunds.refunds,function(k, v) {
                                var created = moment(v.created);
                                $('#tb_body_pendings').append('<tr class="danger"><td>'+v.id+'</td><td>-'+v.quantity+'</td><td>-'+v.retail_price+'</td><td>'+v.savings
                                                         +'</td><td>-'+v.processing_fee+'</td><td>-'+v.printed_fee+'</td><td>-'+v.sales_taxes+'</td><td>-'+v.commission_percent+'</td><td>-'+v.amount
                                                         +'</td><td>'+created.format('M/DD/YYYY @ h:mmA')+'</td></tr>');
                            });
                            $.each(data.refunds.available,function(k, v) {
                                $('#tb_foot_pendings input[name="'+k+'"]').val(v);
                                $('#tb_foot_pendings input[name="'+k+'"]').attr('max',v);
                            });
                            $('#tb_body_pendings').append('<tr class="bold"><td>Avail.</td><td>'+data.refunds.available.quantity+'</td><td>'+data.refunds.available.retail_price+'</td><td>-'+data.refunds.available.savings
                                                         +'</td><td>'+data.refunds.available.processing_fee+'</td><td>'+data.refunds.available.printed_fee+'</td><td>'+data.refunds.available.sales_taxes
                                                         +'</td><td>'+data.refunds.available.commission_percent+'</td><td>'+data.refunds.available.amount+'</td><td></td></tr>');
                        }
                        else
                        {
                            $('#purchase_details').css('display','block');
                            $('#refund_details').css('display','none');
                            $('#purchase_details').empty();
                            $.each(data.purchases,function(k, v) {
                                $('#purchase_details').append('<h3 class="col-md-6"><b>#'+k+'</b> => $'+v.paid+' ($'+v.refunded+') = <b>$'+v.available+'</b></h3>');
                            });                            
                        }
                        swal.close();
                        $('#modal_model_refund').modal('show');
                    }
                    else swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error",
                            showConfirmButton: true
                        });
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to get the purchase information!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    });
                }
            });
        }); 
        //function on change inputs of modal
        $('#tb_foot_pendings input').bind('change','click',function(ev) {
            //check data on change
            if(parseFloat($(this).val()) > parseFloat($(this).attr('max')))
                $(this).val( $(this).attr('max') );
            if(parseFloat($(this).val()) < parseFloat($(this).attr('min')))
                $(this).val( $(this).attr('min') );
            //if change qty
            if($(this).attr('name')=='quantity')
            {
                var qty = parseInt( $(this).val() );
                $(this).val(qty);
                var ticket_price = $('#tb_foot_pendings input[name="ticket_price"]').val();
                var ticket_fee = $('#tb_foot_pendings input[name="ticket_fee"]').val();
                $('#tb_foot_pendings input[name="retail_price"]').val( (qty * parseFloat(ticket_price)).toFixed(2) );
                $('#tb_foot_pendings input[name="processing_fee"]').val( (qty * parseFloat(ticket_fee)).toFixed(2) );
            }
            //calculate subtotal without taxes
            var reta = $('#tb_foot_pendings input[name="retail_price"]').val();
            $('#tb_foot_pendings input[name="retail_price"]').val( parseFloat(reta).toFixed(2) );
            var fees = $('#tb_foot_pendings input[name="processing_fee"]').val();
            $('#tb_foot_pendings input[name="processing_fee"]').val( parseFloat(fees).toFixed(2) );
            var savi = $('#tb_foot_pendings input[name="savings"]').val();
            $('#tb_foot_pendings input[name="savings"]').val( parseFloat(savi).toFixed(2) );
            var prin = $('#tb_foot_pendings input[name="printed_fee"]').val();  
            $('#tb_foot_pendings input[name="printed_fee"]').val( parseFloat(prin).toFixed(2) );  
            var percent = $('#tb_foot_pendings input[name="sales_percent"]').val();
            $('#tb_foot_pendings input[name="sales_percent"]').val( parseFloat(percent).toFixed(2) );
            var subt = parseFloat(reta)+parseFloat(fees)-parseFloat(savi)+parseFloat(prin); 
            var taxes =  subt * parseFloat(percent) ;
            $('#tb_foot_pendings input[name="sales_taxes"]').val( (taxes).toFixed(2) );
            $('#tb_foot_pendings input[name="amount"]').val( (subt+taxes).toFixed(2) );
            
        });
        //function send
        $('#btn_model_process').on('click', function(ev) {
            $('#modal_model_refund').modal('hide');
            swal({
                title: "Refunding purchase(s)",
                text: "Please, wait.",
                type: "info",
                showConfirmButton: false
            });
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/purchases/refund', 
                data: $('#form_model_refund').serializeArray(), 
                success: function(data) {
                    if(data.success) 
                    {
                        swal({
                            title: "<span style='color:orange;'>Process!</span>",
                            text: data.msg,
                            html: true,
                            type: "warning",
                            showConfirmButton: true
                        },function(){
                            location.reload();
                        });
                    }
                    else swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error",
                            showConfirmButton: true
                        },function(){
                            location.reload();
                        });
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to get the purchase information!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    },function(){
                        $('#modal_model_refund').modal('show');
                    });
                }
            });
        });
    }
    return {
        //main function to initiate the module
        init: function () {
            if (!jQuery().dataTable) {
                return;
            }
            initTable();        
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    RefundDatatablesManaged.init();
});