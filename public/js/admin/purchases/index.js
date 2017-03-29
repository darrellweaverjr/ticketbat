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
        $('#btn_model_move').on('click', function(ev) {
            var purchase_id = $("#tb_model [name=radios]:checked").val();
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/purchases', 
                data: {action:0,purchase_id:purchase_id}, 
                success: function(data) {
                    if(data.success) 
                    {
                        $('#form_model_move input[name="purchase_id"]:hidden').val(purchase_id);
                        $('#form_model_move input[name="ticket_id"]:hidden').val(data.ticket.id);
                        $('#form_model_move select[name="show_time_id"]').append('<option disabled selected value=""></option>');
                        $.each(data.showtimes,function(k, v) {
                            var date = moment(v.show_time);
                            $('#form_model_move select[name="show_time_id"]').append('<option value="'+v.id+'">'+date.format('MM/DD/YYYY @ h:mma')+' - Active</option>');
                        });
                        $('#tb_purchase_tickets').html('<tr><td><b>Current</b></td><td>'+data.ticket.ticket_type+'</td><td>'+data.ticket.retail_price+'</td><td>'+data.ticket.processing_fee+
                                                         '</td><td>'+data.ticket.percent_pf+'</td><td>'+data.ticket.fixed_commission+'</td><td>'+data.ticket.percent_commission+
                                                         '</td><td>'+data.ticket.max_tickets+'</td><td>'+data.ticket.is_active+'</td></tr>'); 
                        $('#modal_model_move').modal('show');
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
        //on select showtimes date change
        $('#form_model_move select[name="show_time_id"]').on('change', function(ev) {
            var show_time_id = $(this).val();
            var ticket_id = $('#form_model_move input[name="ticket_id"]:hidden').val();
            if(show_time_id)
            {
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/purchases', 
                    data: {action:1,show_time_id:show_time_id,ticket_id:ticket_id},
                    success: function(data) {
                        if(data.success) 
                        {
                            $('#tb_purchase_tickets').children('tr:not(:first)').remove();
                            $('#tb_purchase_tickets').append('<tr><td><b>Target</b></td><td>'+data.ticket.ticket_type+'</td><td>'+data.ticket.retail_price+'</td><td>'+data.ticket.processing_fee+
                                                            '</td><td>'+data.ticket.percent_pf+'</td><td>'+data.ticket.fixed_commission+'</td><td>'+data.ticket.percent_commission+
                                                            '</td><td>'+data.ticket.max_tickets+'</td><td>'+data.ticket.is_active+'</td></tr>'); 
                            $.each($('#tb_purchase_tickets').children('tr:first').children(),function(k, v) {
                                if(k>0)
                                {
                                    var e = $('#tb_purchase_tickets').children('tr:last').children()[k];
                                    if(e.innerHTML != v.innerHTML)
                                        e.innerHTML = '<span class="label label-sm sbold label-danger">'+e.innerHTML+'</span>';
                                }
                            });
                        }
                        else{
                            $('#modal_model_move').modal('hide');					
                            swal({
                                title: "<span style='color:red;'>Error!</span>",
                                text: data.msg,
                                html: true,
                                type: "error"
                            },function(){
                                $('#modal_model_move').modal('show');
                            });
                        }
                    },
                    error: function(){
                        $('#modal_model_move').modal('hide');	
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to get the ticket's information!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_move').modal('show');
                        });
                    }
                }); 
            }
            else 
            {
                $('#modal_model_move').modal('hide');
                swal({
                    title: "<span style='color:red;'>Error!</span>",
                    text: "You must select a valid showtime!",
                    html: true,
                    type: "error"
                },function(){
                    $('#modal_model_move').modal('show');
                });
            }
        });
        //function save
        $('#btn_model_save').on('click', function(ev) {
            $('#modal_model_move').modal('hide');
            if($('#form_model_move').valid())
            {
                swal({
                    title: "Saving band's information",
                    text: "Please, wait.",
                    type: "info",
                    showConfirmButton: false
                });
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/purchases/save', 
                    data: $('#form_model_move').serializeArray(), 
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
                                $('#modal_model_move').modal('show');
                            });
                        }
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to save the band's information!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_move').modal('show');
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
                    $('#modal_model_move').modal('show');
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