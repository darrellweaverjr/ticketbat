/* global venue_id */

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
                [5, 10, 15, 20, -1],
                [5, 10, 15, 20, "All"] // change per page values here
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
                [1, "asc"]
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
        //on_sale_date
        $('#on_sale_date').datetimepicker({
            autoclose: true,
            isRTL: App.isRTL(),
            format: "yyyy-mm-dd hh:ii",
            pickerPosition: (App.isRTL() ? "bottom-right" : "bottom-left"),
            todayBtn: true,
            minuteStep: 15,
            defaultDate:'now'
        });
        $('#amex_only_date').daterangepicker({
                opens: (App.isRTL() ? 'left' : 'right'),
                format: 'YYYY-MM-DD HH:mm',
                separator: ' to ',
                startDate: moment(),
                endDate: moment().add('days', 29),
                minDate: moment()
            },
            function (start, end) {
                $('#amex_only_date input[name="amex_only_start_date"]').val(start.format('YYYY-MM-DD HH:mm'));
                $('#amex_only_date input[name="amex_only_end_date"]').val(end.format('YYYY-MM-DD HH:mm'));
            }
        );  
        $('#show_passwords_date').daterangepicker({
                opens: (App.isRTL() ? 'left' : 'right'),
                format: 'YYYY-MM-DD HH:mm',
                separator: ' to ',
                startDate: moment(),
                endDate: moment().add('days', 29),
                minDate: moment()
            },
            function (start, end) {
                $('#form_model_show_passwords input[name="start_date"]').val(start.format('YYYY-MM-DD HH:mm'));
                $('#form_model_show_passwords input[name="end_date"]').val(end.format('YYYY-MM-DD HH:mm'));
            }
        );  
        //clear onsale_date
        $('#clear_onsale_date').on('click', function(ev) {
            $('#form_model_update [name="on_sale"]').val('');
            $('#on_sale_date').datetimepicker('update');
        });
        //clear amex_only_date
        $('#clear_amex_only_date').on('click', function(ev) {
            $('#form_model_update [name="amex_only_start_date"]').val('');
            $('#form_model_update [name="amex_only_end_date"]').val('');
            $('#on_sale_date').datetimepicker('update');
        });    
        //get slug on name change
        $('#form_model_update [name="name"]').bind('change',function() {
            if($('#form_model_update [name="name"]').val().length >= 5)
            {
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/shows/slug', 
                    data: {
                        name:$('#form_model_update [name="name"]').val(),
                        venue_id:$('#form_model_update [name="venue_id"]').val(),
                        show_id:$('#form_model_update [name="id"]').val()
                    }, 
                    success: function(data) {
                        if(data) $('#form_model_update [name="slug"]').val(data);
                        else $('#form_model_update [name="slug"]').val('');
                    },
                    error: function(){
                        $('#form_model_update [name="slug"]').val('');
                    }
                });
            }
            else $('#form_model_update [name="slug"]').val('');
        });
        //function with show_passwords
        $('#btn_model_password_add').on('click', function(ev) {
            $('#form_model_show_passwords input[name="id"]:hidden').val('').trigger('change');
            $('#form_model_show_passwords').trigger('reset');
            $('#modal_model_show_passwords').modal('show');
        });
        $('#tb_show_passwords').on('click', 'input[type="button"]', function(e){
            var row = $(this).closest('tr');
            //edit
            if($(this).hasClass('edit')) 
            {
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/shows/passwords', 
                    data: {action:0,id:row.prop('class')}, 
                    success: function(data) {
                        if(data.success) 
                        {
                            $('#form_model_show_passwords').trigger('reset');
                            $('#form_model_show_passwords input[name="id"]:hidden').val(data.password.id).trigger('change');
                            $('#form_model_show_passwords input[name="password"]').val(data.password.password);
                            $('#form_model_show_passwords input[name="start_date"]').val(data.password.start_date);
                            $('#form_model_show_passwords input[name="end_date"]').val(data.password.end_date);
                            $.each(data.password.ticket_types,function(k, t) {
                                $('#form_model_show_passwords :checkbox[value="'+t+'"]').prop('checked',true);   
                            });
                            $('#modal_model_show_passwords').modal('show');
                        }
                        else {
                            alert(data.msg);
                        }
                    },
                    error: function(){
                        alert("There was an error trying to get the password's information!<br>The request could not be sent to the server.");
                    }
                });
            }
            //delete
            else if($(this).hasClass('delete')) 
            {
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/shows/passwords', 
                    data: {action:-1,id:row.prop('class')}, 
                    success: function(data) {
                        if(data.success) 
                        {
                            //remove row
                            row.remove();                        
                        }
                        else{
                            alert(data.msg);
                        }
                    },
                    error: function(){
                        alert("There was an error trying to delete the password!<br>The request could not be sent to the server.");
                    }
                });
            }
            else alert('Invalid Option');
        });
        //function submit show_passwords
        $('#submit_model_show_passwords').on('click', function(ev) {
            if($('#form_model_show_passwords').valid() && $('#form_model_show_passwords [name="ticket_types[]"]:checked').length)
            {
                $('#modal_model_show_passwords').modal('hide');
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/shows/passwords', 
                    data: $('#form_model_show_passwords').serializeArray(), 
                    success: function(data) {
                        if(data.success) 
                        {
                            var v = data.password;
                            //update row
                            if($('#tb_show_passwords').find('tr[class="'+v.id+'"]').length)
                                $('#tb_show_passwords').find('tr[class="'+v.id+'"]').html('<td class="password">'+v.password+'</td><td class="start_date">'+v.start_date+'</td><td class="end_date">'+v.end_date+'</td><td class="ticket_types">'+v.ticket_types+'</td><td><input type="button" value="Edit" class="btn sbold bg-yellow edit"></td><td><input type="button" value="Delete" class="btn sbold bg-red delete"></td>');
                            //add row
                            else
                                $('#tb_show_passwords').append('<tr class="'+v.id+'"><td class="password">'+v.password+'</td><td class="start_date">'+v.start_date+'</td><td class="end_date">'+v.end_date+'</td><td class="ticket_types">'+v.ticket_types+'</td><td><input type="button" value="Edit" class="btn sbold bg-yellow edit"></td><td><input type="button" value="Delete" class="btn sbold bg-red delete"></td></tr>');
                        }
                        else{
                            alert(data.msg);
                            $('#modal_model_show_passwords').modal('show');
                        }
                    },
                    error: function(){
                        alert("There was an error trying to save the password's information!<br>The request could not be sent to the server.");
                        $('#modal_model_show_passwords').modal('show');
                    }
                }); 
            }
            else alert('You must fill out correctly the form');
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
            //$('#form_model_update [name="image_url"]').attr('src','');
            $("#form_model_update input[name='id']:hidden").val('').trigger('change');
            //$("#form_model_update input[name='image_url']:hidden").val('').trigger('change');
            $("#form_model_update").trigger('reset');
        };
        //function add
        $('#btn_model_add').on('click', function(ev) {
            fullReset();
            if($('#modal_model_update_header').hasClass('bg-yellow'))
                $('#modal_model_update_header,#btn_model_save').removeClass('bg-yellow').addClass('bg-green');
            else $('#modal_model_update_header,#btn_model_save').addClass('bg-green');
            $('#modal_model_update_title').html('Add Show');
            $('a[href="#tab_model_update_checking"]').parent().css('display','none');
            $('#form_model_update .ticket_types_lists').empty();
            $('a[href="#tab_model_update_passwords"]').parent().css('display','none');
            $('a[href="#tab_model_update_showtimes"]').parent().css('display','none');
            $('a[href="#tab_model_update_tickets"]').parent().css('display','none');
            $('a[href="#tab_model_update_bands"]').parent().css('display','none');
            $('a[href="#tab_model_update_multimedia"]').parent().css('display','none');
            $("#form_model_update").trigger('reset');
            $('#modal_model_update').modal('show');
        });
        //function load form to upload image
        $('#btn_sponsor_upload_image').on('click', function(ev) {
            FormImageUpload('logo','#modal_model_update','#form_model_update [name="sponsor_logo_id"]');       
        }); 
        //on select venue
        $('#form_model_update [name="venue_id"]').on('change', function(ev) {
            //show stages
            var venue_id = $('#form_model_update [name="venue_id"] option:selected').val(); 
            $('#form_model_update [name="stage_id"]').children('option').css('display','none'); 
            $('#form_model_update [name="stage_id"]').children('option[class="venue_'+venue_id+'"]').css('display','block'); 
            $('#form_model_update [name="stage_id"]').val($('#form_model_update [name="stage_id"] option[class="venue_'+venue_id+'"]:first').val());
            //show restrictions
            var venue_rest = $('#form_model_update [name="venue_id"] option:selected').attr('class');
            $('#form_model_update [name="restrictions"] option').each(function()
            {
                if($(this).val() == venue_rest)
                {
                    $(this).prop('selected',true);
                    $(this).text($(this).val()+' - Venue default');                   
                }
                else
                {
                    $(this).prop('selected',false);
                    $(this).text($(this).val()+' - WARNING: Not venue default');                   
                }
            });
        });
        
        //function edit
        $('#btn_model_edit').on('click', function(ev) {
            fullReset();
            if($('#modal_model_update_header').hasClass('bg-green'))
                $('#modal_model_update_header,#btn_model_save').removeClass('bg-green').addClass('bg-yellow');
            else $('#modal_model_update_header,#btn_model_save').addClass('bg-yellow');
            var set = $('.group-checkable').attr("data-set");
            var id = $(set+"[type=checkbox]:checked")[0].id;
            $('a[href="#tab_model_update_checking"]').parent().css('display','block');
            $('#form_model_update .ticket_types_lists').empty();
            $('a[href="#tab_model_update_passwords"]').parent().css('display','block');
            $('#form_model_show_passwords .ticket_types_lists').empty();
            $('#tb_show_passwords').empty();
            $('a[href="#tab_model_update_showtimes"]').parent().css('display','block');
            $('a[href="#tab_model_update_tickets"]').parent().css('display','block');
            $('a[href="#tab_model_update_bands"]').parent().css('display','block');
            $('a[href="#tab_model_update_multimedia"]').parent().css('display','block');
            $('#modal_model_update_title').html('Edit Show');
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/shows', 
                data: {id:id}, 
                success: function(data) {
                    if(data.success) 
                    {
                        //fill out defaults
                        $('#form_model_update [name="venue_id"]').val(data.show['venue_id']).change();
                        $('#form_model_show_passwords input[name="show_id"]:hidden').val(data.show['id']).trigger('change');
                        //fill out shows
                        for(var key in data.show)
                        {
                            //checking
                            if(key=='on_sale' || key=='amex_only_start_date' || key=='amex_only_end_date')
                                if(data.show[key]=='0000-00-00 00:00:00')
                                    data.show[key] = '';
                            //fill out
                            var e = $('#form_model_update [name="'+key+'"]');
                            if(e.is('img'))
                                e.attr('src',data.show[key]);
                            else if(e.is('input:checkbox'))
                                //e.prop('checked',(data.show[key])? true : false);
                                $('#form_model_update .make-switch:checkbox[name="'+key+'"]').bootstrapSwitch('state', (data.show[key])? true : false, true);
                            else
                                e.val(data.show[key]);
                        }
                        //fill out checking ticket 
                        if(data.show.amex_only_ticket_types && data.show.amex_only_ticket_types!='')
                        {
                            var amex_tt = data.show.amex_only_ticket_types.split(',');
                            var tt_inactive = data.ticket_types_inactive.split(',');
                            $.each(data.tickets,function(k, v) {
                                if(v.is_active == 1 && tt_inactive.indexOf(v.ticket_type)<0)
                                {
                                    if(amex_tt.indexOf(v.ticket_type)>=0) 
                                        var checked = 'checked';
                                    else var checked = '';
                                    $('#modal_model_update .ticket_types_lists').append('<label class="mt-checkbox"><input type="checkbox" name="ticket_types[]" value="'+v.id+'" '+checked+' />'+v.ticket_type+'<span></span></label>');
                                    $('#modal_model_show_passwords .ticket_types_lists').append('<label class="mt-checkbox"><input type="checkbox" name="ticket_types[]" value="'+v.id+'" />'+v.ticket_type+'<span></span></label>');
                                }
                            });
                        }
                        //fill out passwords
                        if(data.passwords && data.passwords.length)
                        {
                            $.each(data.passwords,function(k, v) {
                                $('#tb_show_passwords').append('<tr class="'+v.id+'"><td class="password">'+v.password+'</td><td class="start_date">'+v.start_date+'</td><td class="end_date">'+v.end_date+'</td><td class="ticket_types">'+v.ticket_types+'</td><td><input type="button" value="Edit" class="btn sbold bg-yellow edit"></td><td><input type="button" value="Delete" class="btn sbold bg-red delete"></td></tr>');
                            });
                        }
                        //show modal
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
                        text: "There was an error trying to get the band's information!<br>The request could not be sent to the server.",
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
                    title: "Saving show's information",
                    text: "Please, wait.",
                    type: "info",
                    showConfirmButton: false
                });
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/shows/save', 
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
                            text: "There was an error trying to save the show's information!<br>The request could not be sent to the server.",
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
        //function remove
        $('#btn_model_remove').on('click', function(ev) {
            var html = '<ol>';
            var ids = [];
            var set = $('.group-checkable').attr("data-set");
            var checked = $(set+"[type=checkbox]:checked");
            jQuery(checked).each(function (key, item) {
                html += '<li>'+item.value+'</li>';
                ids.push(item.id);
            });             
            swal({
                title: "The following band(s) will be removed, please confirm action: ",
                text: "<span style='text-align:left;color:red;'>"+html+"</span>",
                html: true,
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Confirm",
                cancelButtonText: "Cancel",
                closeOnConfirm: false,
                closeOnCancel: true
              },
              function(isConfirm) {
                if (isConfirm) {
                    var form_delete = $('#form_model_delete');
                    jQuery.ajax({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        type: 'POST',
                        url: '/admin/bands/remove', 
                        data: {id:ids}, 
                        success: function(data) {
                            if(data.success)
                            {
                                swal({
                                    title: "<span style='color:green;'>Deleted!</span>",
                                    text: data.msg,
                                    html: true,
                                    timer: 1500,
                                    type: "success",
                                    showConfirmButton: false
                                });
                                location.reload(); 
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
                                text: "There was an error deleting the band(s)!<br>They might have some dependences<br>or<br>the request could not be sent to the server.",
                                html: true,
                                type: "error"
                            });
                        }
                    });
                } 
            });            
        });     
        //function load social media
        $('#btn_load_social_media').on('click', function(ev) {
            var website = $('#form_model_update [name="url"]').val();
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/media/load', 
                data: {url:website}, 
                success: function(data) {
                    if(data) 
                        for(var key in data)
                            if(data[key] !== '')
                                $('#form_model_update [name="'+key+'"]').val(data[key]);
                }
            });            
        });        
        //function load form to upload image
        $('#btn_bands_upload_image').on('click', function(ev) {
            FormImageUpload('logo','#modal_model_update','#form_model_update [name="image_url"]');       
        }); 
        //init functions
        check_models(); 
        $('#form_model_update [name="cutoff_hours"]').TouchSpin({ initval: 1 });
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
                    name: {
                        minlength: 5,
                        maxlength: 50,
                        required: true
                    },
                    short_description: {
                        minlength: 5,
                        maxlength: 500,
                        required: true
                    },
                    description: {
                        minlength: 5,
                        maxlength: 2000,
                        required: false
                    },  
                    youtube: {
                        minlength: 5,
                        maxlength: 100,
                        required: false
                    },
                    facebook: {
                        minlength: 5,
                        maxlength: 100,
                        required: false
                    },
                    twitter: {
                        minlength: 5,
                        maxlength: 100,
                        required: false
                    },
                    googleplus: {
                        minlength: 5,
                        maxlength: 100,
                        required: false
                    },
                    instagram: {
                        minlength: 5,
                        maxlength: 100,
                        required: false
                    },
                    yelpbadge: {
                        minlength: 5,
                        maxlength: 100,
                        required: false
                    },
                    url: {
                        minlength: 5,
                        maxlength: 100,
                        required: false
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