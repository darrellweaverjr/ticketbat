var PendingDatatablesManaged = function () {
    
    var initTable = function () {
        
        var table = MainDataTableCreator.init('tb_model_pendings',[ [0, "desc"] ],10);
        
        table.find('.group-checkable').change(function () {
            var set = jQuery(this).attr("data-set");
            var checked = jQuery(this).is(":checked");
            jQuery(set).each(function () {
                if (checked) {
                    $(this).prop("checked", true);
                    $(this).parents('tr').addClass("active");
                } else {
                    $(this).prop("checked", false);
                    $(this).parents('tr').removeClass("active");
                }
            });
            check_models();
        });

        table.on('click', 'tbody tr td:not(:first-child)', function () {
            var action = $(this).parent().find('.checkboxes').is(':checked');
            if(!action)
                table.find('.checkboxes').prop('checked',false);
            $(this).parent().find('.checkboxes').prop('checked',!action);
            check_models();
        });

        table.on('change', 'tbody tr .checkboxes', function () {
            check_models();
            $(this).parents('tr').toggleClass("active");
        });

        //PERSONALIZED FUNCTIONS
        //check/uncheck all
        var check_models = function(){
            var set = $('.group-checkable').attr("data-set");
            var checked = $(set+"[type=checkbox]:checked").length;
            if(checked >= 1)
                $('#btn_model_refund').prop("disabled",false);
            else
                $('#btn_model_refund').prop("disabled",true);
        }
        
        //PERSONALIZED FUNCTIONS
        //function resend
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
                url: '/admin/refunds/check', 
                data: {ids:ids}, 
                success: function(data) {
                    if(data.success) 
                    {
                        $('#form_model_refund [name="id"]').val(ids); swal.close();
                        if(data.qty==1)
                        {
                            $('#purchase_details').css('display','none');
                            $('#refund_details').css('display','block');
                            $('#tb_body_pendings').empty();
                            if(data.refunds.purchase)
                            {
                                var created = moment(data.refunds.purchase.created);
                                $('#tb_body_pendings').append('<tr class="success"><td>P</td><td>'+data.refunds.purchase.id+'</td><td>'+data.refunds.purchase.quantity+'</td><td>'+data.refunds.purchase.retail_price+'</td><td>-'+data.refunds.purchase.savings
                                                         +'</td><td>'+data.refunds.purchase.processing_fee+'</td><td>'+data.refunds.purchase.sales_taxes+'</td><td>'+data.refunds.purchase.price_paid+'</td><td>'+created.format('M/DD/YY h:mmA')+'</td></tr>');
                            } 
                            $.each(data.refunds.refunds,function(k, v) {
                                var created = moment(v.created);
                                $('#tb_body_pendings').append('<tr class="danger"><td>R</td><td>'+v.id+'</td><td>-'+v.quantity+'</td><td>-'+v.retail_price+'</td><td>'+v.savings
                                                         +'</td><td>-'+v.processing_fee+'</td><td>-'+v.sales_taxes+'</td><td>-'+v.amount+'</td><td>'+created.format('M/DD/YY h:mmA')+'</td></tr>');
                            });
                            $.each(data.refunds.available,function(k, v) {
                                $('#tb_foot_pendings input[name="'+k+'"]').val(v);
                                $('#tb_foot_pendings input[name="'+k+'"]').attr('max',v);
                            });
                            $('#tb_body_pendings').append('<tr class="bold"><td colspan="2">Available</td><td>'+data.refunds.available.quantity+'</td><td>'+data.refunds.available.retail_price+'</td><td>-'+data.refunds.available.savings
                                                         +'</td><td>'+data.refunds.available.processing_fee+'</td><td>'+data.refunds.available.sales_taxes+'</td><td>'+data.refunds.available.amount+'</td><td></td></tr>');
                        }
                        else
                        {
                            $('#purchase_details').css('display','block');
                            $('#refund_details').css('display','none');
                            $.each(data.purchases,function(k, v) {
                                $('#purchase_details').append('<div class="col-md-6">Order #'+k+' => '+v.paid+' ('+v.refunded+') = '+v.available+'</div>');
                            });                            
                        }
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
        //function send
        $('#btn_model_save').on('click', function(ev) {
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
                url: '/admin/refunds/save', 
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
        //init functions
        check_models();
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
    PendingDatatablesManaged.init();
});