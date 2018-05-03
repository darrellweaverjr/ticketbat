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
            $('#modal_model_update_title').html('Add Type');
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
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/ticket_types', 
                data: {id:id}, 
                success: function(data) {
                    if(data.success) 
                    {
                        $('#modal_model_update_title').html(data.ticket_type.ticket_type);
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
        //function open styles
        $('#btn_model_styles').on('click', function(ev) {
            //show modal
            $('#modal_model_style').modal('show');
        });  
        //add styles
        $('#btn-add-style').on('click', function(ev) {
            $('#modal_model_style').modal('hide');
            swal({
                title: "Enter the new style class",
                type: "input",
                showCancelButton: true,
                closeOnConfirm: true,
                inputPlaceholder: "New Style Class"
            }, function (inputStyle) {
                if (inputStyle === false) 
                {
                    $('#modal_model_style').modal('show');
                    return false;
                }
                if ($.trim(inputStyle) === "") {
                  swal.showInputError("You need to write something!");
                  $('#modal_model_style').modal('show');
                  return false;
                }
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/ticket_types/classes', 
                    data: {action:1,ticket_type_class:inputStyle}, 
                    success: function(data) {
                        if(data.success)
                        {
                            $('#form_model_style select[name="ticket_type_class"]').empty();
                            $.each(data.classes,function(k, v) {
                                $('#form_model_style select[name="ticket_type_class"]').append('<option value="'+k+'">'+v+'</option>');
                            });
                            swal({
                                title: "<span style='color:green;'>Saved!</span>",
                                text: data.msg,
                                html: true,
                                timer: 1500,
                                type: "success",
                                showConfirmButton: false
                            });
                            $('#modal_model_style').modal('show');
                            return true;
                        }
                        else swal({
                                title: "<span style='color:red;'>Error!</span>",
                                text: data.msg,
                                html: true,
                                type: "error"
                            },function(){
                                $('#modal_model_style').modal('show');
                                return false;
                            });
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error adding the Style Class!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_style').modal('show');
                            return false;
                        });
                    }
                });
            });
        });  
        //remove styles
        $('#btn-remove-style').on('click', function(ev) {
            $('#modal_model_style').modal('hide');
            if($('#form_model_style select[name="ticket_type_class"] option:selected').length)
            {
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/ticket_types/classes', 
                    data: {action:-1,ticket_type_class:$('#form_model_style select[name="ticket_type_class"]').val()}, 
                    success: function(data) {
                        if(data.success)
                        {
                            $('#form_model_style select[name="ticket_type_class"]').empty();
                            $.each(data.classes,function(k, v) {
                                $('#form_model_style select[name="ticket_type_class"]').append('<option value="'+k+'">'+v+'</option>');
                            });
                            swal({
                                title: "<span style='color:green;'>Saved!</span>",
                                text: data.msg,
                                html: true,
                                timer: 1500,
                                type: "success",
                                showConfirmButton: false
                            });
                            $('#modal_model_style').modal('show');
                        }
                        else 
                        {
                            swal({
                                title: "<span style='color:red;'>Error!</span>",
                                text: data.msg,
                                html: true,
                                type: "error"
                            },function(){
                                $('#modal_model_style').modal('show');
                            });
                        }
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error removing the Style Class!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_style').modal('show');
                        });
                    }
                });
            }
            else
            {
                swal({
                    title: "<span style='color:red;'>Error!</span>",
                    text: 'You must select at least a class to delete.',
                    html: true,
                    type: "error"
                },function(){
                    $('#modal_model_style').modal('show');
                });
            }
        }); 
        //change styles according to selected class
        $('#form_model_style select[name="ticket_type_class"]').bind('change','click', function (){
            var classes = $(this).val()[0];
            $('#btn-preview').removeClass().addClass('btn btn-block '+classes);
        });
        //upload file styles
        $('#btn-upload-style').on('click', function(ev) {
            $('#modal_model_style').modal('hide');
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/ticket_types/styles', 
                data: {ticket_type_file:$('#form_model_file [name="ticket_type_file"]').val()}, 
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
                        $('#modal_model_style').modal('show');
                    }
                    else 
                    {
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_style').modal('show');
                        });
                    }
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error uploading the Style File!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    },function(){
                        $('#modal_model_style').modal('show');
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
var FormValidation = function () {
    return {
        //main function to initiate the module
        init: function () {
            // advance validation
            var rules = {
                ticket_type: {
                    minlength: 2,
                    maxlength: 20,
                    required: true
                }
            };
            MainFormValidation.init('form_model_update',rules,{});
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    TableDatatablesManaged.init();
    FormValidation.init();
});