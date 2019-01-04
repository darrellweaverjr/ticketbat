var TableDatatablesManaged = function () {

    var initTable = function () {
        
        var table = MainDataTableCreator.init('tb_model',[ [6, "desc"] ],10);

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
            {
                if(checked == 1)
                {
                    $('button[id*="btn_model_"]').prop("disabled",false);
                }
                else
                {
                    $('button[id*="btn_model_"]').prop("disabled",true);
                    $('#btn_model_email').prop("disabled",false);
                }
                
                //REFUND IS ALL PENDING
                var ref_disab = false;
                $.each($(set+"[type=checkbox]:checked") ,function(k, v) {
                    var status = $(v).closest('tr').find('td[data-status]').data('status');
                    if(!(status.indexOf('Pending') == 0 || status=='Active'))
                        ref_disab = true; 
                });
                $('#btn_model_refund').prop("disabled",ref_disab);
                $('#btn_model_process').prop("disabled",ref_disab);
            }
            else
            {
                $('button[id*="btn_model_"]').prop("disabled",true);
            }
            $('#btn_model_search').prop("disabled",false);
        }

        //reset all selects
        function reset_purchase_status()
        {
            $.each($('#tb_model td:nth-child(9)'),function(k, v) {
                $(v).html('<center>'+$(v).data('status')+'</center>');
            });
        }
        $('#tb_model td:not(:nth-child(9))').click(function() {
            reset_purchase_status();
        })

        //create editable status for purchase
        $('#tb_model td:nth-child(9)').click(function() {
            var status = $(this);
            var id = status.closest('tr').find('input:checkbox').attr('id');
            reset_purchase_status();
            var select = '<select data-id="'+id+'" class="form-control" name="status">';
            $.each($('#tb_model').data('status'),function(k, v) {
                if(v != 'Refunded' && v != 'Chargeback')
                {
                    if(v == status.data('status'))
                        select+= '<option selected value="'+k+'">'+v+'</option>';
                    else
                        select+= '<option value="'+k+'">'+v+'</option>';
                }
            });
            select+= '</select>';
            status.html(select);
        });
        
        //on change input manual calc
        $('#target_purchase input[type="number"], #target_purchase select[name="t_p_inclusive_fee"]').bind('change','click',function() {
            var incl = $('#target_purchase select[name="t_p_inclusive_fee"]').val();
            var price = $('#target_purchase input[name="t_p_retail_price"]').val();
            var fee = (incl>0)? 0 : $('#target_purchase input[name="t_p_processing_fee"]').val();
            var disc = $('#target_purchase input[name="t_savings"]').val();
            var print = $('#target_purchase input[name="t_printed_fee"]').val();
            var tax = $('#target_purchase input[name="t_sales_taxes"]').val();
            var paid = parseFloat(price) + parseFloat(fee) - parseFloat(disc) + parseFloat(print) + parseFloat(tax);
            $('#target_purchase input[name="t_price_paid"]').val(parseFloat(paid).toFixed(2));    
        });
        
        //updated
        $('#t_updated_date').datetimepicker({
            autoclose: true,
            isRTL: App.isRTL(),
            format: "yyyy-mm-dd hh:ii:ss",  
            pickerPosition: (App.isRTL() ? "bottom-right" : "bottom-left"),
            minuteStep: 15,
            defaultDate: new Date()
        });
        //clear updated
        $('#clear_t_updated_date').on('click', function(ev) {
            $('#form_model_edit input[name="t_updated"]').val('');
            $('#t_updated_date').datetimepicker('update');
        });
        
        //refunded
        $('#t_refunded_date').datetimepicker({
            autoclose: true,
            isRTL: App.isRTL(),
            format: "yyyy-mm-dd hh:ii:ss",
            pickerPosition: (App.isRTL() ? "bottom-right" : "bottom-left"),
            minuteStep: 15,
            defaultDate: new Date()
        });
        //clear refunded
        $('#clear_t_refunded_date').on('click', function(ev) {
            $('#form_model_edit input[name="t_refunded"]').val('');
            $('#t_refunded_date').datetimepicker('update');
        });

        //function to change status to purchase
        function change_status(id,status,reason='')
        {
            swal({
                title: "Changing purchase's status",
                text: "Please, wait.",
                type: "info",
                showConfirmButton: false
            });
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/purchases/save',
                data: {id:id,status:status,reason:reason},
                success: function(data) {
                    if(data.success)
                    {
                        $('#tb_model select[name="status"]').parent('td').data('status',status);
                        $('#tb_model select[name="status"]').val(  status  );
                        $('#note_'+id).html(data.note);
                        swal({
                            title: "<span style='color:green;'>Updated!</span>",
                            text: data.msg,
                            html: true,
                            timer: 1500,
                            type: "success",
                            showConfirmButton: false
                        });
                    }
                    else swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error"
                        },function(){
                            $('#tb_model select[name="status"]').val(  $('#tb_model select[name="status"]').parent('td').data('status')  );
                        });
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to set the status!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    },function(){
                        $('#tb_model select[name="status"]').val(  $('#tb_model select[name="status"]').parent('td').data('status')  );
                    });
                }
            });
        }

        //view details
        $(document).on('click', 'td.modal_details_view', function(ev){
            var id = $(this).data('id');
            if(id)
            {
                swal({
                    title: "Getting purchase's details",
                    text: "Please, wait.",
                    type: "info",
                    showConfirmButton: false
                });
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/purchases',
                    data: {id:id,action:0},
                    success: function(data) {
                        if(data.success)
                        {
                            //fill out shows
                            for(var key in data.purchase)
                            {
                                if(key=='email' || key=='u_email')
                                    $('#modal_model_details b.'+key).html('<i><a href="mailto:'+data.purchase[key]+'" target="_top">'+data.purchase[key]+'</a></i>');
                                else if(key=='referrer_url')
                                    $('#modal_model_details span.'+key).html('<i><a href="'+data.purchase[key]+'" target="_blank">'+data.purchase[key]+'</a></i>');
                                else if(key=='note')
                                    $('#modal_model_details span.'+key+'s').html('<i>'+data.purchase[key]+'</i>');
                                else if(key=='tickets')
                                {
                                    var tickets = '';
                                    $.each(data.purchase[key],function(k, v) {
                                        tickets += '[<b>'+v.tickets+'</b>] <b>'+v.first_name+' '+v.last_name+'</b>, <i><a href="mailto:'+v.email+'" target="_top">'+v.email+'</a></i><br>';
                                    });
                                    $('#modal_model_details span.'+key).html( tickets );
                                }
                                else
                                    $('#modal_model_details b.'+key).html(data.purchase[key]);
                            }
                            swal.close();
                            $('#modal_model_details').modal('show');
                        }
                        else swal({
                                title: "<span style='color:red;'>Error!</span>",
                                text: data.msg,
                                html: true,
                                type: "error"
                            });
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to get the details!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        });
                    }
                });
            }
        });

        //function on status select
        $(document).on('change', '#tb_model select[name="status"]', function(ev){
            var id = $(this).data('id');
            var old_status = $(this).parent('td').data('status');
            var status = $(this).val();
            if(old_status != status)
            {
                if(status.substring(0,7) == 'Pending')
                {
                    swal({
                        title: "Are you sure to change the status from <b>"+old_status+"</b> to <b>"+status+"</b>?",
                        text: "An email will be sent to the admin to complete the action.",
                        type: "input",
                        html:true,
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes, do it!",
                        closeOnConfirm: false,
                        closeOnCancel: true,
                        inputPlaceholder: "Reason for refund"
                    },
                      function(inputValue) {
                        if (inputValue === false) return false;
                        if (inputValue === "") {
                          swal.showInputError("You need to write something!");
                          return false;
                        } 
                        if(id)
                            change_status(id,status,inputValue);
                        else
                        {
                            swal({
                                title: "<span style='color:red;'>Error!</span>",
                                text: "Please, you must select the purchase first.",
                                html: true,
                                type: "error"
                            });
                            $('#tb_model select[name="status"]').val(old_status);
                        } 
                    });
                }
                else if(status=='Active' || old_status=='Active')
                {
                    var status_msg = (status=='Active')? 'active' :'canceled';
                    swal({
                        title: "Are you sure to change the status from <b>"+old_status+"</b> to <b>"+status+"</b>?",
                        text: "An email will be sent to both the customer and venue stating this purchase is "+status_msg,
                        type: "warning",
                        html:true,
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes, do it!",
                        closeOnConfirm: false,
                        closeOnCancel: true
                    },
                      function(isConfirm) {
                        if (isConfirm) {
                            if(id)
                            {
                                change_status(id,status);
                            }
                            else
                            {
                                swal({
                                    title: "<span style='color:red;'>Error!</span>",
                                    text: "Please, you must select the purchase first.",
                                    html: true,
                                    type: "error"
                                });
                                //$('#tb_model select[data-id="'+id+'"]').val(old_status);
                            }
                        } else {
                            //$('#tb_model select[data-id="'+id+'"]').val(old_status);
                        }
                    });
                }
                else
                    change_status(id,status);
            }
        });
        //function search
        $('#btn_model_search').on('click', function(ev) {
            $('#modal_model_search').modal('show');
        });
        
        //function show move modal window
        $('#btn_model_edit').on('click', function(ev) {
            $('#form_model_edit').trigger('reset');
            $('#target_purchase input[name^="t_"]').prop('readonly', true);
            $('#target_purchase select[name^="t_"]').prop('disabled', true);
            $('#form_model_edit input').css('border-color','').css('background','').css('font-weight','normal');
            var set = $('.group-checkable').attr("data-set");
            var id = $(set+"[type=checkbox]:checked")[0].id;
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/purchases',
                data: {id:id},
                success: function(data) {
                    if(data.success)
                    {
                        $('#modal_model_update_title').html('Edit purchase # '+id);
                        //fill out current
                        for(var key in data.current){
                            //if(key == 'show_time' || key == 'updated' || (key == 'refunded' && data.current[key]!=null))
                                //data.current[key] = moment(data.current[key]).format('M/DD/YYYY h:mm a');
                            $('#form_model_edit [name="'+key+'"]').val(data.current[key]);
                        }
                        $('#form_model_edit input[name="id"]:hidden').val(data.current.purchase_id);
                        //fill out showtimes
                        $('#form_model_edit select[name="to_show_time_id"]').html('<option selected value=""></option>');
                        $.each(data.showtimes,function(k, v) {
                            $('#form_model_edit select[name="to_show_time_id"]').append('<option value="'+v.id+'">'+moment(v.show_time).format('MM/DD/YYYY @ h:mma')+' - Active</option>');
                        });
                        //fill out tickets
                        $('#form_model_edit select[name="to_ticket_id"]').html('<option value=""></option>');
                        $.each(data.tickets,function(k, v) {
                            $('#form_model_edit select[name="to_ticket_id"]').append('<option value="'+v.id+'">'+v.ticket_type+' - '+v.title+'</option>');
                        });
                        //fill out discounts
                        $('#form_model_edit select[name="to_discount_id"]').html('<option value=""></option>');
                        $.each(data.discounts,function(k, v) {
                            $('#form_model_edit select[name="to_discount_id"]').append('<option value="'+v.id+'">'+v.code+' - '+v.description+'</option>');
                        });
                        $('#modal_model_edit').modal('show');
                    }
                    else swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error"
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
        //enable/disable force edit purchase
        $('#form_model_edit [name="force_edit"]').bind('click','change', function(ev) {
            if($(this).is(':checked'))
            {
                $('#target_purchase input[name^="t_"]').prop('readonly', false);
                $('#target_purchase select[name^="t_"]').prop('disabled', false);
            }
            else
            {
                $('#form_model_edit input[name^="t_"]').prop('readonly', true);
                $('#target_purchase select[name^="t_"]').prop('disabled', true);
            }
        });
        //on change edit field
        $('#form_model_edit select[name="to_show_time_id"], #form_model_edit select[name="to_ticket_id"], #form_model_edit select[name="to_discount_id"], #form_model_edit input[name="to_quantity"]').on('change', function() {
            var purchase_id = $('#form_model_edit input[name="id"]:hidden').val();
            var to_show_time_id = $('#form_model_edit select[name="to_show_time_id"]').val();
            var to_ticket_id = $('#form_model_edit select[name="to_ticket_id"]').val();
            var to_discount_id = $('#form_model_edit select[name="to_discount_id"]').val();
            var to_quantity = $('#form_model_edit input[name="to_quantity"]').val();
            $('#modal_model_edit').modal('hide');
            swal({
                title: "Calculating",
                text: "Please, wait.",
                type: "info",
                showConfirmButton: false
            });
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/purchases',
                data: {purchase_id:purchase_id,to_show_time_id:to_show_time_id,to_ticket_id:to_ticket_id,to_discount_id:to_discount_id,to_quantity:to_quantity},
                success: function(data) {
                    if(data.success) {
                        //remove style
                        $('#form_model_edit input, #form_model_edit select').css('border-color','').css('background','').css('font-weight','normal');
                        //fill out
                        for(var key in data.target) {
                            //if(key == 't_show_time' || key == 't_updated' || (key == 't_refunded' && data.target[key]!=null) )
                                //data.target[key] = moment(data.target[key]).format('M/DD/YYYY h:mm a');
                            $('#form_model_edit [name="'+key+'"]').val(data.target[key]);
                        }
                        //hightlight changes
                        if(parseFloat($('#form_model_edit input[name="t_quantity"]').val()) != parseFloat($('#form_model_edit input[name="quantity"]').val()))
                            $('#form_model_edit input[name="t_quantity"]').css('border-color','blue');
                        if(parseFloat($('#form_model_edit input[name="t_p_retail_price"]').val()) != parseFloat($('#form_model_edit input[name="p_retail_price"]').val()))
                            $('#form_model_edit input[name="t_p_retail_price"]').css('border-color','blue');
                        if(parseFloat($('#form_model_edit input[name="t_p_processing_fee"]').val()) != parseFloat($('#form_model_edit input[name="p_processing_fee"]').val()))
                            $('#form_model_edit input[name="t_p_processing_fee"]').css('border-color','blue');
                        if(parseFloat($('#form_model_edit input[name="t_savings"]').val()) != parseFloat($('#form_model_edit input[name="savings"]').val()))
                            $('#form_model_edit input[name="t_savings"]').css('border-color','blue');
                        if(parseFloat($('#form_model_edit input[name="t_commission_percent"]').val()) != parseFloat($('#form_model_edit input[name="commission_percent"]').val()))
                            $('#form_model_edit input[name="t_commission_percent"]').css('border-color','blue');
                        if(parseFloat($('#form_model_edit input[name="t_price_paid"]').val()) != parseFloat($('#form_model_edit input[name="price_paid"]').val()))
                            $('#form_model_edit input[name="t_price_paid"]').css('border-color','blue');
                        if($('#form_model_edit input[name="t_show_time"]').val() != $('#form_model_edit input[name="show_time"]').val())
                            $('#form_model_edit input[name="t_show_time"]').css('border-color','blue');
                        if($('#form_model_edit input[name="t_code"]').val() != $('#form_model_edit input[name="code"]').val())
                            $('#form_model_edit input[name="t_code"]').css('border-color','blue');
                        if($('#form_model_edit input[name="t_sales_taxes"]').val() != $('#form_model_edit input[name="sales_taxes"]').val())
                            $('#form_model_edit input[name="t_sales_taxes"]').css('border-color','blue');
                        if($('#form_model_edit input[name="t_cc_fee"]').val() != $('#form_model_edit input[name="cc_fee"]').val())
                            $('#form_model_edit input[name="t_cc_fee"]').css('border-color','blue');
                        if($('#form_model_edit select[name="t_inclusive_fee"]').val() != $('#form_model_edit input[name="p_inclusive_fee"]').val())
                            $('#form_model_edit input[name="t_inclusive_fee"]').css('border-color','blue');
                        if($('#form_model_edit select[name="t_payment_type"]').val() != $('#form_model_edit input[name="payment_type"]').val())
                            $('#form_model_edit input[name="t_payment_type"]').css('border-color','blue');
                        if($('#form_model_edit select[name="t_channel"]').val() != $('#form_model_edit input[name="channel"]').val())
                            $('#form_model_edit input[name="t_channel"]').css('border-color','blue');
                        //check price
                        var from_price = parseFloat($('#form_model_edit input[name="price_paid"]').val());
                        var to_price = parseFloat($('#form_model_edit input[name="t_price_paid"]').val());
                        if(from_price > to_price)
                            $('#form_model_edit input[name="t_price_paid"]').css('background','#C9F9C4').css('font-weight','bold');
                        else if(from_price < to_price)
                            $('#form_model_edit input[name="t_price_paid"]').css('background','#F7D9D8').css('font-weight','bold');
                        swal.close();
                        $('#modal_model_edit').modal('show');
                    }
                    else{
                        swal.close();
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error"
                        },function(){
                            $(this).val('').trigger('change');
                            $('#modal_model_edit').modal('show');
                        });
                    }
                },
                error: function(){
                    swal.close();
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to get the ticket's information!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    },function(){
                        $(this).val('').trigger('change');
                        $('#modal_model_edit').modal('show');
                    });
                }
            });
        });
        //function save
        $('#btn_model_save').on('click', function(ev) {
            var to_show_time_id = $('#form_model_edit select[name="to_show_time_id"]').val();
            var to_ticket_id = $('#form_model_edit select[name="to_ticket_id"]').val();
            var to_quantity = $('#form_model_edit input[name="to_quantity"]').val();
            var to_user = $('#form_model_edit input[name="to_user_email"]').val();
            var to_customer = $('#form_model_edit input[name="to_customer_email"]').val();
            var to_discount_id = $('#form_model_edit select[name="to_discount_id"]').val();
            $('#modal_model_edit').modal('hide');
            if($('#form_model_edit').valid() && (to_show_time_id || to_ticket_id || to_quantity || to_discount_id || to_user || to_customer))
            {
                swal({
                    title: "Saving purchase's information",
                    text: "Please, wait.",
                    type: "info",
                    showConfirmButton: false
                });
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/purchases/save',
                    data: $('#form_model_edit').serializeArray(),
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
                            location.reload();
                        }
                        else{
                            swal({
                                title: "<span style='color:red;'>Error!</span>",
                                text: data.msg,
                                html: true,
                                type: "error"
                            },function(){
                                $('#modal_model_edit').modal('show');
                            });
                        }
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to save the purchase's information!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_edit').modal('show');
                        });
                    }
                });
            }
            else
            {
                swal({
                    title: "<span style='color:red;'>Error!</span>",
                    text: "The form is not valid!<br>Please check the information again.",
                    html: true,
                    type: "error"
                },function(){
                    $('#modal_model_edit').modal('show');
                });
            }
        });
        //function email
        $('#btn_model_email').on('click', function(ev) {
            var set = $('.group-checkable').attr("data-set");
            var id = $(set+"[type=checkbox]:checked")[0].id;
            if(id)
            {
                $("#form_model_email").trigger('reset');
                $('#modal_model_email').modal('show');
            }
            else
            {
                swal({
                    title: "<span style='color:red;'>Error!</span>",
                    text: "Please, you must select at least one purchase first.",
                    html: true,
                    type: "error"
                });
            }
        });
        $('#btn_model_resend').on('click', function(ev) {
            var set = $('.group-checkable').attr("data-set");
            var id = $(set+"[type=checkbox]:checked")[0].id;
            var email = $(set+"[type=checkbox]:checked")[0].value;
            if(id)
            {
                $("#form_model_resend").trigger('reset');
                $("#form_model_resend input[name='purchase_id']").val(id);
                $("#form_model_resend input[name='email']").val(email);
                $('#modal_model_resend').modal('show');
            }
            else
            {
                swal({
                    title: "<span style='color:red;'>Error!</span>",
                    text: "Please, you must select at least one purchase first.",
                    html: true,
                    type: "error"
                });
            }
        });
        //function resend email
        $('#btn_resend_email').on('click', function(ev) {
            var id = $("#form_model_resend input[name='purchase_id']").val();
            var email = $("#form_model_resend input[name='email']").val();
            $('#modal_model_resend').modal('hide');
            if(id && email)
            {
                swal({
                    title: "Sending email",
                    text: "Please, wait.",
                    type: "info",
                    showConfirmButton: false
                });
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/purchases/resend',
                    data: {id:id,email:email},
                    success: function(data) {
                        if(data.success)
                        {
                            swal({
                                title: "<span style='color:green;'>Email Sent Successfully!</span>",
                                text: data.msg,
                                html: true,
                                timer: 1500,
                                type: "success",
                                showConfirmButton: false
                            });
                        }
                        else swal({
                                title: "<span style='color:red;'>Error!</span>",
                                text: data.msg,
                                html: true,
                                type: "error"
                            },function(){
                                $('#modal_model_resend').modal('show');
                            });
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to send the email!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_resend').modal('show');
                        });
                    }
                });
            }
            else
            {
                swal({
                    title: "<span style='color:red;'>Error!</span>",
                    text: "Please, you must select the purchase first.",
                    html: true,
                    type: "error"
                },function(){
                    $('#modal_model_resend').modal('show');
                });
            }
        });
        //function send custom email
        $('#btn_send_custom').on('click', function(ev) {
            var ids = [];
            var set = $('.group-checkable').attr("data-set");
            var checked = $(set+"[type=checkbox]:checked");
            jQuery(checked).each(function (key, item) {
                ids.push(item.id);
            });
            $('#modal_model_email').modal('hide');
            if(ids)
            {
                swal({
                    title: "Sending email",
                    text: "Please, wait.",
                    type: "info",
                    showConfirmButton: false
                });
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/purchases/email',
                    data: {ids:ids,search:$('#form_model_search').serializeArray(),email:$('#form_model_email').serializeArray(),action:'custom'},
                    success: function(data) {
                        if(data.success)
                        {
                            swal({
                                title: "<span style='color:green;'>Email Sent Successfully!</span>",
                                text: data.msg,
                                html: true,
                                timer: 1500,
                                type: "success",
                                showConfirmButton: false
                            });
                        }
                        else swal({
                                title: "<span style='color:red;'>Error!</span>",
                                text: data.msg,
                                html: true,
                                type: "error"
                            },function(){
                                $('#modal_model_email').modal('show');
                            });
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to send the email!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_email').modal('show');
                        });
                    }
                });
            }
            else
            {
                swal({
                    title: "<span style='color:red;'>Error!</span>",
                    text: "Please, you must select the purchase first.",
                    html: true,
                    type: "error"
                },function(){
                    $('#modal_model_email').modal('show');
                });
            }
        });
        //function note
        $('#btn_model_note').on('click', function(ev) {
            var set = $('.group-checkable').attr("data-set");
            var id = $(set+"[type=checkbox]:checked")[0].id;
            swal({
                title: "Add note",
                text: "Write purchase's note:",
                type: "input",
                showCancelButton: true,
                closeOnConfirm: false,
                inputPlaceholder: "Write something"
            }, function (inputNote) {
                if (inputNote === false) return false;
                else if (inputNote.trim() === "") {
                  swal.showInputError("You need to write something!");
                  return false;
                }
                else
                {
                    swal({
                        title: "Adding new note",
                        text: "Please, wait.",
                        type: "info",
                        showConfirmButton: false
                    });
                    jQuery.ajax({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        type: 'POST',
                        url: '/admin/purchases/save',
                        data: {id:id,note:inputNote},
                        success: function(data) {
                            if(data.success)
                            {
                                $('#note_'+id).html(data.note);
                                $('#note_'+id).removeClass('hidden');
                                swal({
                                    title: "<span style='color:green;'>Note Added Successfully!</span>",
                                    text: data.msg,
                                    html: true,
                                    timer: 1500,
                                    type: "success",
                                    showConfirmButton: false
                                });
                            }
                            else swal({
                                    title: "<span style='color:red;'>Error!</span>",
                                    text: data.msg,
                                    html: true,
                                    type: "error"
                                });
                        },
                        error: function(){
                            swal({
                                title: "<span style='color:red;'>Error!</span>",
                                text: "There was an error trying to add the note!<br>The request could not be sent to the server.",
                                html: true,
                                type: "error"
                            });
                        }
                    });
                }
            });
        });
        //function tickets
        $('#btn_model_tickets').on('click', function(ev) {
            var set = $('.group-checkable').attr("data-set");
            var id = $(set+"[type=checkbox]:checked")[0].id;
            swal({
                title: "View tickets",
                text: "Select the way you want to view the tickets",
                type: "info",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "BOCA Ticket Printer",
                cancelButtonText: "Standard Printer",
                closeOnConfirm: true,
                closeOnCancel: true
            },
              function(isConfirm) {
                if (isConfirm) {
                    window.open('/admin/purchases/tickets/S/'+id);
                } else {
                    window.open('/admin/purchases/tickets/C/'+id);
                }
            });
        });
        //function share tickets
        $('#btn_model_share').on('click', function(ev) {
            $('#form_model_edit').trigger('reset');
            $('#form_model_edit input').css('border-color','').css('background','').css('font-weight','normal');
            var set = $('.group-checkable').attr("data-set");
            var e = $(set+"[type=checkbox]:checked")[0];
            var purchase_id = e.id;
            var qty = parseInt( $(e).data('qty'));
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/user/purchases/share',
                data: { id: purchase_id },
                success: function(data) {
                    if(data.success)
                    {
                        $('#form_share_tickets input[name="purchases_id"]').val(purchase_id);
                        ShareTicketsFunctions.load(data,qty);
                        $('#modal_share_tickets').modal('show');
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
                        text: "There was an error trying to load the shared tickets.",
                        html: true,
                        type: "error",
                        showConfirmButton: true
                    });
                }
            });
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
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/user/purchases/share',
                    data: $('#form_share_tickets').serializeArray(),
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
                        }
                        else{
                            swal({
                                title: "<span style='color:red;'>Error!</span>",
                                text: data.msg,
                                html: true,
                                type: "error"
                            },function(){
                                $('#modal_share_tickets').modal('show');
                            });
                        }
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to create the user.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_share_tickets').modal('show');
                        });
                    }
                });
            }
        });

        //init functions
        $('#form_model_email [name="body"]').summernote({height:150});
        check_models();
        //function autoshow modal search
        $('#modal_model_search').modal('show');
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
    TableDatatablesManaged.init();
});
