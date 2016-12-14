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
        
        table.on('change', 'tbody tr .checkboxes', function () {
            check_models();             
            $(this).parents('tr').toggleClass("active");
        });
        
        //PERSONALIZED FUNCTIONS  
        //on venue select
        $('#modal_model_update [name="venue_id"]').on('change', function () {
            var venue_id = $('#modal_model_update [name="venue_id"] option:selected').val();
            if(venue_id)
            {
                $('#modal_model_update [name="show_id"]').empty();
                $('#modal_model_update [name="show_time_id"]').empty();
                $('#modal_model_update [name="ticket_id"]').empty();
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/ticket_codes', 
                    data: {venue_id:venue_id}, 
                    success: function(data) {
                        if(data.success) 
                        {
                            $('#modal_model_update [name="show_id"]').append('<option value=""></option>');
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
                $('#modal_model_update [name="show_time_id"]').empty();
                $('#modal_model_update [name="ticket_id"]').empty();
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/ticket_codes', 
                    data: {show_id:show_id}, 
                    success: function(data) {
                        if(data.success) 
                        {
                            $('#modal_model_update [name="show_time_id"]').append('<option value=""></option>');
                            $.each(data.show_times,function(key, value) {
                                $('#modal_model_update [name="show_time_id"]').append('<option value="'+value.id+'">'+value.show_time+'</option>');
                            });
                            $('#modal_model_update [name="ticket_id"]').append('<option value=""></option>');
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
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/ticket_codes', 
                    data: {ticket_id:ticket_id}, 
                    success: function(data) {
                        if(data.success) 
                        {
                            $('#modal_model_update [name="retail_price"]').val(data.ticket.retail_price);
                            $('#modal_model_update [name="processing_fee"]').val(data.ticket.processing_fee);
                            $('#modal_model_update [name="percent_commission"]').val((data.ticket.retail_price*data.ticket.percent_commission/100).toFixed(2));
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
        //spinners for start seat and end seat
        $('#form_model_update [name="start_seat"]').TouchSpin({ initval:1,min:1,max:500 });
        $('#form_model_update [name="end_seat"]').TouchSpin({ initval:1,min:1,max:500 });
        $('#form_model_update [name="start_seat"]').on('change', function () {
            if($('#form_model_update [name="start_seat"]').val() >= $('#form_model_update [name="end_seat"]').val() && $('#form_model_update [name="start_seat"]').val()<=500)
                $('#form_model_update [name="end_seat"]').val($('#form_model_update [name="start_seat"]').val());
        });
        $('#form_model_update [name="end_seat"]').on('change', function () {
            if($('#form_model_update [name="start_seat"]').val() >= $('#form_model_update [name="end_seat"]').val() && $('#form_model_update [name="end_seat"]').val()<=500)
                $('#form_model_update [name="start_seat"]').val($('#form_model_update [name="end_seat"]').val());
        });
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        //active/desactive ticket types
        $('#tb_model .make-switch[type=checkbox]').on('switchChange.bootstrapSwitch', function (e, state) {
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/ticket_types/save', 
                data: {ticket_type:e.target.value,active:e.target.checked}, 
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
                    else{
                        $('#tb_model .make-switch:checkbox[value="'+e.target.value+'"]').bootstrapSwitch('state', !state, true);
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error"
                        });
                    }  
                },
                error: function(){
                    $('#tb_model .make-switch:checkbox[value="'+e.target.value+'"]').bootstrapSwitch('state', !state, true);
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to set the type's information!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    });
                }
            });
        });
        //check/uncheck all
        var check_models = function(){
            var set = $('.group-checkable').attr("data-set");
            var checked = $(set+"[type=checkbox]:checked").length;
            if(checked == 1)
            {
                $('#btn_model_edit').prop("disabled",false);
                $('#btn_model_remove').prop("disabled",false);
            }
            else if(checked > 1)
            {
                $('#btn_model_edit').prop("disabled",true);
                $('#btn_model_remove').prop("disabled",false);
            }
            else
            {
                $('#btn_model_edit').prop("disabled",true);
                $('#btn_model_remove').prop("disabled",true);
            }
            $('#btn_model_add').prop("disabled",false);
        } 
        //function full reset form
        var fullReset = function(){
            $("#form_model_update input[name='id']:hidden").val('').trigger('change');
            $("#form_model_update").trigger('reset');
        };
        //function add
        $('#btn_model_add').on('click', function(ev) {
            fullReset();
            if($('#modal_model_update_header').hasClass('bg-yellow'))
                $('#modal_model_update_header,#btn_model_save').removeClass('bg-yellow').addClass('bg-green');
            else $('#modal_model_update_header,#btn_model_save').addClass('bg-green');
            $('#modal_model_update_title').html('Add Consignment Ticket');
            $('#form_model_update [name="ticket_type"]').prop('disabled',false);
            $('#div_model_update_advanced').css('display','none');
            //show modal
            $('#modal_model_update').modal('show');
        });  
        //function edit
        $('#btn_model_edit').on('click', function(ev) {
            fullReset();
            if($('#modal_model_update_header').hasClass('bg-green'))
                $('#modal_model_update_header,#btn_model_save').removeClass('bg-green').addClass('bg-yellow');
            else $('#modal_model_update_header,#btn_model_save').addClass('bg-yellow');
            var set = $('.group-checkable').attr("data-set");
            var id = $(set+"[type=checkbox]:checked")[0].id;
            $('#div_model_update_advanced').css('display','block');
            $('#modal_model_update_title').html('Edit Type');
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/ticket_types', 
                data: {id:id}, 
                success: function(data) {
                    if(data.success) 
                    {
                        for(var key in data.ticket_type)
                        {
                            var e = $('#form_model_update [name="'+key+'"]');
                            e.val(data.ticket_type[key]);
                            if(key === 'ticket_type')
                                $('#form_model_update [name="id"]').val(data.ticket_type[key]);
                        }
                        $('#form_model_update [name="ticket_type"]').prop('disabled',true);
                        $('#modal_model_update').modal('show');
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
                        text: "There was an error trying to get the type's information!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    });
                }
            });
        });
        //function save
        $('#btn_model_save').on('click', function(ev) {
            $('#modal_model_update').modal('hide');
            if($('#form_model_update').valid())
            {
                swal({
                    title: "Saving ticket types' information",
                    text: "Please, wait.",
                    type: "info",
                    showConfirmButton: false
                });
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/ticket_types/save', 
                    data: $('#form_model_update').serializeArray(), 
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
                                $('#modal_model_update').modal('show');
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
                            $('#modal_model_update').modal('show');
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
                    ticket_type: {
                        minlength: 2,
                        maxlength: 20,
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