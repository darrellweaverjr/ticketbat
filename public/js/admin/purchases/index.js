var TableDatatablesManaged = function () {
    
    var initTable = function () {
        var table = $('#tb_model');
        // begin first table
        table.dataTable({
            // Internationalisation. For more info refer to http://datatables.net/manual/i18n
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                },
                "emptyTable": "No data available in table",
                "info": "Showing _START_ to _END_ of _TOTAL_ records",
                "infoEmpty": "No records found",
                "infoFiltered": "(filtered1 from _MAX_ total records)",
                "lengthMenu": "Show _MENU_",
                "search": "Search:",
                "zeroRecords": "No matching records found",
                "paginate": {
                    "previous":"Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            },
            //"ajax": '/admin/users/ajax',
            "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
            "lengthMenu": [
                [10, 15, 20, -1],
                [10, 15, 20, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 10,            
            "pagingType": "bootstrap_full_number",
            "columnDefs": [
                {  // set default column settings
                    'orderable': false,
                    'targets': [0]
                }, 
                {
                    "searchable": false,
                    "targets": [0]
                },
                {
                    "className": "dt-right"
                }
            ],
            "order": [
                [5, "desc"]
            ] // set first column as a default sort by asc
        });
        
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
            if(checked == 1)
            {
                $('button[id*="btn_model_"]').prop("disabled",false);
            }
            else if(checked > 1)
            {
                $('button[id*="btn_model_"]').prop("disabled",true);
                $('#btn_model_email').prop("disabled",false);
            }
            else
            {
                $('button[id*="btn_model_"]').prop("disabled",true);
            }
            $('#btn_model_search').prop("disabled",false);
        } 
       
        //function on status select
        $('#tb_model select[name="status"]').on('change', function(ev) {
            var id = $(this).attr('ref');
            var status = $(this).val();
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/purchases/save', 
                data: {id:id,status:status}, 
                success: function(data) {
                    if(data.success) 
                    {
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
                        });
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to set the status!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    });
                }
            });
        });
        //function search
        $('#btn_model_search').on('click', function(ev) {
            $('#modal_model_search').modal('show');
        });
        //show_times_date
        $('#show_times_date').daterangepicker({
                opens: (App.isRTL() ? 'left' : 'right'),
                format: 'YYYY-MM-DD',
                separator: ' to '
            },
            function (start, end) {
                $('#form_model_search input[name="showtime_start_date"]').val(start.format('YYYY-MM-DD'));
                $('#form_model_search input[name="showtime_end_date"]').val(end.format('YYYY-MM-DD'));
            }
        ); 
        //clear show_times_date
        $('#clear_show_times_date').on('click', function(ev) {
            $('#form_model_search [name="showtime_start_date"]').val('');
            $('#form_model_search [name="showtime_end_date"]').val('');
            $('#show_times_date').datetimepicker('update');
        }); 
        //sold_times_date
        $('#sold_times_date').daterangepicker({
                opens: (App.isRTL() ? 'left' : 'right'),
                format: 'YYYY-MM-DD',
                separator: ' to '
            },
            function (start, end) {
                $('#form_model_search input[name="soldtime_start_date"]').val(start.format('YYYY-MM-DD'));
                $('#form_model_search input[name="soldtime_end_date"]').val(end.format('YYYY-MM-DD'));
            }
        ); 
        //clear sold_times_date
        $('#clear_sold_times_date').on('click', function(ev) {
            $('#form_model_search [name="soldtime_start_date"]').val('');
            $('#form_model_search [name="soldtime_end_date"]').val('');
            $('#sold_times_date').datetimepicker('update');
        }); 
        //search venue on select
        $('#form_model_search select[name="venue"]').bind('change click', function() {
            var venue = $(this).val();
            $('#form_model_search select[name="show"]').html('<option selected value="">All</option>');
            var shows = $('#form_model_search select[name="show"]').data('content');
            if(shows)
            {
                $.each(shows,function(k, v) {
                    if(v.venue_id == venue)
                        $('#form_model_search select[name="show"]').append('<option value="'+v.id+'">'+v.name+'</option>');
                });
            }
        });
        //function show move modal window
        $('#btn_model_edit').on('click', function(ev) {
            $('#form_model_edit').trigger('reset');
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
                        //fill out current
                        for(var key in data.current){
                            if(key == 'show_time')
                                data.current[key] = moment(data.current[key]).format('M/D/YY h:mma');
                            $('#form_model_edit input[name="'+key+'"]').val(data.current[key]);
                        }     
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
        //on change edit field
        $('#form_model_edit select[name="to_show_time_id"], #form_model_edit select[name="to_ticket_id"], #form_model_edit select[name="to_discount_id"], #form_model_edit input[name="to_quantity"]').on('change', function() {
            var purchase_id = $('#form_model_edit input[name="purchase_id"]:hidden').val();
            var to_show_time_id = $('#form_model_edit select[name="to_show_time_id"]').val();
            var to_ticket_id = $('#form_model_edit select[name="to_ticket_id"]').val();
            var to_discount_id = $('#form_model_edit select[name="to_discount_id"]').val();
            var to_quantity = $('#form_model_edit input[name="to_quantity"]').val();
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/purchases', 
                data: {purchase_id:purchase_id,to_show_time_id:to_show_time_id,to_ticket_id:to_ticket_id,to_discount_id:to_discount_id,to_quantity:to_quantity},
                success: function(data) {
                    if(data.success) {
                        //remove style
                        $('#form_model_edit input').css('border-color','').css('background','').css('font-weight','normal');
                        //fill out
                        for(var key in data.target) {
                            if(key == 't_show_time')
                                data.target[key] = moment(data.target[key]).format('M/D/YY h:mma');
                            $('#form_model_edit input[name="'+key+'"]').val(data.target[key]);
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
                        //check price
                        var from_price = parseFloat($('#form_model_edit input[name="price_paid"]').val());
                        var to_price = parseFloat($('#form_model_edit input[name="t_price_paid"]').val());
                        if(from_price > to_price)
                            $('#form_model_edit input[name="t_price_paid"]').css('background','#C9F9C4').css('font-weight','bold');
                        else if(from_price < to_price)
                            $('#form_model_edit input[name="t_price_paid"]').css('background','#F7D9D8').css('font-weight','bold');
                    }  
                    else{
                        $('#modal_model_edit').modal('hide');					
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
                    $('#modal_model_edit').modal('hide');	
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
            var to_discount_id = $('#form_model_edit select[name="to_discount_id"]').val();
            $('#modal_model_edit').modal('hide');
            if($('#form_model_edit').valid() && (to_show_time_id || to_ticket_id || to_quantity || to_discount_id))
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
            swal({
                title: "Send Email to Customers",
                text: "Select the email type",
                type: "info",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Re-send Receipt",
                cancelButtonText: "Send Custom",
                closeOnConfirm: true,
                closeOnCancel: true
            },
              function(isConfirm) {
                if (isConfirm) {
                    if(id)
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
                            data: {id:id,action:'receipt'}, 
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
                                    });
                            },
                            error: function(){
                                swal({
                                    title: "<span style='color:red;'>Error!</span>",
                                    text: "There was an error trying to send the email!<br>The request could not be sent to the server.",
                                    html: true,
                                    type: "error"
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
                        });
                    }
                } else {
                    $("#form_model_email").trigger('reset');
                    $('#modal_model_email').modal('show');
                }
            });
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
        //init functions
        $('#form_model_email [name="body"]').summernote({height:150});
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
    TableDatatablesManaged.init();
});