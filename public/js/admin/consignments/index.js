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
            "pageLength": 15,            
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
                [0, "asc"]
            ] // set first column as a default sort by asc
        }); 
        
        table.on('change', 'tbody tr .checkboxes', function () {
            $(this).parents('tr').toggleClass("active");
        });
        
        //PERSONALIZED FUNCTIONS  
        //on venue select
        $('#modal_model_update [name="venue_id"]').on('change', function () {
            var venue_id = $('#modal_model_update [name="venue_id"] option:selected').val();
            if(venue_id)
            {
                $('#modal_model_update [name="show_time_id"]').empty();
                $('#modal_model_update [name="ticket_id"]').empty();
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/consignments', 
                    data: {venue_id:venue_id}, 
                    success: function(data) {
                        if(data.success) 
                        {
                            $('#modal_model_update [name="show_id"]').empty().append('<option disabled selected value=""></option>');
                            $.each(data.shows,function(key, value) {
                                $('#modal_model_update [name="show_id"]').append('<option value="'+value.id+'">'+value.name+'</option>');
                            });
                        }
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to get the information!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        });
                    }
                });
            }
            else
            {
                swal({
                    title: "<span style='color:red;'>Info</span>",
                    text: "You must select a valid Venue",
                    html: true,
                    type: "info"
                });
            }
        });
        //on show select
        $('#modal_model_update [name="show_id"]').on('change', function () {
            var show_id = $('#modal_model_update [name="show_id"] option:selected').val();
            if(show_id)
            {
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/consignments', 
                    data: {show_id:show_id}, 
                    success: function(data) {
                        if(data.success) 
                        {
                            $('#modal_model_update [name="show_time_id"]').empty().append('<option disabled selected value=""></option>');
                            $.each(data.show_times,function(key, value) {
                                value.show_time = moment(value.show_time, 'YYYY-MM-DD HH:mm').format('MM/DD/YYYY - hh:mm a');
                                $('#modal_model_update [name="show_time_id"]').append('<option value="'+value.id+'">'+value.show_time+'</option>');
                            });
                            $('#modal_model_update [name="ticket_id"]').empty().append('<option disabled selected value=""></option>');
                            $.each(data.tickets,function(key, value) {
                                $('#modal_model_update [name="ticket_id"]').append('<option value="'+value.id+'">'+value.ticket_type+'</option>');
                            });
                        }
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to get the information!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        });
                    }
                });
            }
            else
            {
                swal({
                    title: "<span style='color:red;'>Info</span>",
                    text: "You must select a valid Venue",
                    html: true,
                    type: "info"
                });
            }
        });
        //on ticket select
        $('#modal_model_update [name="ticket_id"]').on('change', function () {
            var ticket_id = $('#modal_model_update [name="ticket_id"] option:selected').val();
            if(ticket_id)
            {
                $('#modal_model_update [name="retail_price"]').val('');
                $('#modal_model_update [name="processing_fee"]').val('');
                $('#modal_model_update [name="percent_commission"]').val('');
                
                //$('#seats_to_add').html('');
                //$('#seats_to_add').multiSelect({ selectableOptgroup: true });
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/consignments', 
                    data: {ticket_id:ticket_id,available:1}, 
                    success: function(data) {
                        if(data.success) 
                        {
                            $('#modal_model_update [name="retail_price"]').val(data.ticket.retail_price);
                            $('#modal_model_update [name="processing_fee"]').val(data.ticket.processing_fee);
                            $('#modal_model_update [name="percent_commission"]').val((data.ticket.retail_price*data.ticket.percent_commission/100).toFixed(2));
                            $('#seats_to_add').append('<optgroup label="'+$('#modal_model_update [name="ticket_id"] option:selected').text()+'"></optgroup>');
                            $.each(data.seats,function(key, value) {                                
                                $('#seats_to_add').multiSelect('addOption', { value: value.id, text: value.seat, index: 0, nested: $('#modal_model_update [name="ticket_id"] option:selected').text() });
                            });
                        }
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to get the information!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        });
                    }
                });
            }
            else
            {
                swal({
                    title: "<span style='color:red;'>Info</span>",
                    text: "You must select a valid Venue",
                    html: true,
                    type: "info"
                });
            }
        });
        //on stage select seats
        $('#modal_model_seats [name="stage_id"]').on('change', function () {
            var stage_id = $('#modal_model_seats [name="stage_id"] option:selected').val();
            if(stage_id)
            {
                $('#tb_seats').empty();
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/consignments', 
                    data: {stage_id:stage_id}, 
                    success: function(data) {
                        if(data.success) 
                        {
                            $('#modal_model_seats [name="ticket_id"]').empty().append('<option disabled selected value=""></option>');
                            $.each(data.tickets,function(key, value) {
                                if(value.title != 'None') value.ticket_type = value.ticket_type+' ('+value.title+')';
                                $('#modal_model_seats [name="ticket_id"]').append('<option value="'+value.id+'">'+value.ticket_type+'</option>');
                            });
                        }
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to get the information!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        });
                    }
                });
            }
            else
            {
                swal({
                    title: "<span style='color:red;'>Info</span>",
                    text: "You must select a valid Venue",
                    html: true,
                    type: "info"
                });
            }
        });
        //on stage select seats
        $('#modal_model_seats [name="ticket_id"]').on('change', function () {
            var ticket_id = $('#modal_model_seats [name="ticket_id"] option:selected').val();
            if(ticket_id)
            {
                $('#tb_seats').empty();
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/consignments', 
                    data: {ticket_id:ticket_id}, 
                    success: function(data) {
                        if(data.success) 
                        {
                            $.each(data.seats,function(key, value) {
                                $('#tb_seats').append('<tr><td>'+$('#modal_model_seats [name="ticket_id"] option:selected').text()+'</td><td><input type="hidden" name="seat[]" value="'+value.seat+'"/>'+value.seat+'</td><td><input type="button" value="Delete Seat" class="btn sbold bg-red"><td></tr>');
                            });
                        }
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to get the information!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        });
                    }
                });
            }
            else
            {
                swal({
                    title: "<span style='color:red;'>Info</span>",
                    text: "You must select a valid Venue",
                    html: true,
                    type: "info"
                });
            }
        });
        //on stage add seats
        $('#btn_model_add_seat').on('click', function(ev) {
            if($("#form_model_seats").valid())
            {
                var start_seat = $('#form_model_seats [name="start_seat"]').val();
                var end_seat = $('#form_model_seats [name="end_seat"]').val();
                for (var i = start_seat; i <= end_seat; i++) {
                    if($('#tb_seats :input[value="'+i+'"]').length < 1)
                    {
                        $('#tb_seats').append('<tr><td>'+$('#modal_model_seats [name="ticket_id"] option:selected').text()+'</td><td><input type="hidden" name="seat[]" value="'+i+'"/>'+i+'</td><td><input type="button" value="Delete Seat" class="btn sbold bg-red"><td></tr>');
                    }
                }
            }
            else
            {
                $('#modal_model_seats').modal('hide');
                swal({
                    title: "<span style='color:red;'>Error!</span>",
                    text: "You must select the stage and the section/row to add a seat.",
                    html: true,
                    type: "error"
                },function(){
                    $('#modal_model_seats').modal('show');
                });
            }
        });  
        //on delete seat rows
        $('#tb_seats').on('click', 'input[type="button"]', function(e){
            $(this).closest('tr').remove();
        });
        //spinners for start seat and end seat
        $('#form_model_seats [name="start_seat"]').TouchSpin({ initval:1,min:1,max:500 });
        $('#form_model_seats [name="end_seat"]').TouchSpin({ initval:1,min:1,max:500 });
        $('#form_model_seats [name="start_seat"]').on('change', function () {
            if($('#form_model_seats [name="start_seat"]').val() >= $('#form_model_seats [name="end_seat"]').val() && $('#form_model_seats [name="start_seat"]').val()<=500)
                $('#form_model_seats [name="end_seat"]').val($('#form_model_seats [name="start_seat"]').val());
        });
        $('#form_model_seats [name="end_seat"]').on('change', function () {
            if($('#form_model_seats [name="start_seat"]').val() >= $('#form_model_seats [name="end_seat"]').val() && $('#form_model_seats [name="end_seat"]').val()<=500)
                $('#form_model_seats [name="start_seat"]').val($('#form_model_seats [name="end_seat"]').val());
        });
        //due_date
        $('#due_date').datepicker({
            autoclose: true,
            isRTL: App.isRTL(),
            format: "yyyy-mm-dd",
            minDate: moment()
        });
        //save seats for stage
        $('#btn_model_save_seat').on('click', function(ev) {
            $('#modal_model_seats').modal('hide');
            if($('#form_model_seats').valid())
            {
                swal({
                    title: "Saving seats' information",
                    text: "Please, wait.",
                    type: "info",
                    showConfirmButton: false
                });
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/consignments/save_seats', 
                    data: $('#form_model_seats').serializeArray(), 
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
                                $('#modal_model_seats').modal('show');
                            });
                        }
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "The form is not valid!<br>Please check the information again.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_seats').modal('show');
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
                    $('#modal_model_update').modal('show');
                });
            }       
        });
        //function on consignment status select
        $('#tb_model select[name="status"]').on('change', function(ev) {
            var id = $(this).attr('ref');
            var status = $(this).val();
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/consignments/save', 
                data: {consignment_id:id,status:status}, 
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
        //function seats
        $('#btn_model_seats').on('click', function(ev) {
            $('#tb_seats').empty();
            $("#form_model_seats").trigger('reset');
            //show modal
            $('#modal_model_seats').modal('show');
        });  
        //function add
        $('#btn_model_add').on('click', function(ev) {
            $('#seats_to_add').multiSelect('refresh');
            $("#form_model_update").trigger('reset');
            //show modal
            $('#modal_model_update').modal('show');
        });  
        //function edit
        $('#btn_model_edit').on('click', function(ev) {
            $("#form_model_update2 input[name='id']:hidden").val('').trigger('change');
            $('#tb_seats_consignment_edit').empty();
            $("#form_model_update2").trigger('reset');
            var set = $('.group-checkable').attr("data-set");
            var id = $(set+"[type=checkbox]:checked")[0].id;
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/consignments', 
                data: {id:id}, 
                success: function(data) {
                    if(data.success) 
                    {
                        for(var key in data.consignment)
                        {
                            var e = $('#form_model_update2 [name="'+key+'"]');
                            e.val(data.consignment[key]);
                        }
                        for(var key in data.seats)
                        {
                            var e = data.seats[key]; 
                            switch(e.status)
                            {
                                case 'Created':
                                    var checkable = ''; 
                                    var style = 'label-primary';
                                    break;
                                case 'Voided':
                                    var checkable = ''; 
                                    var style = 'label-danger';
                                    break;
                                case 'Sold':
                                    var checkable = ''; 
                                    var style = 'label-warning';
                                    break;
                                case 'Checked':
                                    var checkable = 'disabled'; 
                                    var style = 'label-success';
                                    break;
                                default:
                                    var checkable = 'disabled'; 
                                    var style = 'label-info';
                            }
                            var first_col = '<td><label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input type="checkbox" name="seat[]" class="checkboxes" '+checkable+' value="'+e.id+'" /><span></span></label></td>';
                            var second_col = '<td>'+e.ticket_type+'</td><td>'+e.seat+'</td>';
                            var third_col = '<td><span class="label label-sm sbold '+style+'" '+checkable+'>'+e.status+'</span></td>';
                            $('#tb_seats_consignment_edit').append('<tr>'+first_col+second_col+third_col+'</tr>');
                        }
                        $('#form_model_update2 [name="moveto"]').empty().append('<option disabled selected value=""></option>');
                        $.each(data.moveto,function(key, value) {
                            value.show_time = moment(value.show_time, 'YYYY-MM-DD HH:mm').format('MM/DD/YYYY hh:mm a');
                            $('#form_model_update2 [name="moveto"]').append('<option value="'+value.id+'">'+value.email+' ('+value.show_name+' - '+value.show_time+') ['+value.status+']</option>');
                        });
                        $('#modal_model_update2').modal('show');
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
                        text: "There was an error trying to get the consignment's information!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    });
                }
            });
        });
        //function save 
        function save_consignment(modal,form) {
            $('#'+modal).modal('hide');
            if($('#'+form).valid())
            {
                swal({
                    title: "Saving consignment's information",
                    text: "Please, wait.",
                    type: "info",
                    showConfirmButton: false
                });
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/consignments/save', 
                    data: new FormData($('#'+form)[0]), 
                    cache: false, 
                    contentType: false,
                    processData:false, 
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
                                $('#'+modal).modal('show');
                            });
                        }
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to save the consignment's information!<br>The request could not be sent to the server.",
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
                    text: "The form is not valid!<br>Please check the information again.",
                    html: true,
                    type: "error"
                },function(){
                    $('#'+modal).modal('show');
                });
            }       
        };
        //function tickets
        $('#btn_model_tickets').on('click', function(ev) {
            var id = $("#tb_model [name=radios]:checked").val();
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
                    window.open('/admin/consignments/tickets/S/'+id);
                } else {
                    window.open('/admin/consignments/tickets/C/'+id);
                }
            });
        });  
        //function save on add
        $('#btn_model_save').on('click', function(ev) {
            //if shows not ours you must add tickets, if our shows, you can add and purchase later
            if(!$('#form_model_update [name="purchase"]:checkbox').is(':checked') && !$('#form_model_update select[name="seats[]"] option:selected').length)
            {
                $('#modal_model_update').modal('hide');
                swal({
                    title: "<span style='color:red;'>Error!</span>",
                    text: "You must add tickets for this show!<br>Please check the information again.",
                    html: true,
                    type: "error"
                },function(){
                    $('#modal_model_update').modal('show');
                });
            } 
            else save_consignment('modal_model_update','form_model_update');      
        });
        //function save on edit
        $('#btn_model_save2').on('click', function(ev) {
            save_consignment('modal_model_update2','form_model_update2');     
        });
        //init functions
        //enable function buttons on check radio 
        $('input:radio[name=radios]').change(function () {
            if($('input:radio[name=radios]:checked').length > 0)
            {
                $('#btn_model_tickets').prop('disabled',false);
                $('#btn_model_edit').prop('disabled',false);
            }
        });
        $('#seats_to_add').multiSelect({ selectableOptgroup: true });
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
var FormValidation = function () {
    // advance validation
    var handleValidation = function() {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation

            var form = $('#form_model_update');
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
                    venue_id: {
                        required: true
                    },
                    show_id: {
                        required: true
                    },
                    ticket_id: {
                        required: true
                    },
                    seller_id: {
                        required: true
                    },
                    show_time_id: {
                        required: true
                    },
                    retail_price: {
                        required: true,
                        number: true,
                        range: [0.01, 99999.99]
                    },
                    processing_fee: {
                        required: true,
                        number: true,
                        range: [0.01, 99999.99]
                    },
                    percent_commission: {
                        required: true,
                        number: true,
                        range: [0, 100]
                    },
                    due_date: {
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
    TableDatatablesManaged.init();
    FormValidation.init();
});