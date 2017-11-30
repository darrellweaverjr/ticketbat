var TableMenuDatatablesManaged = function () {
    
    var initTable = function () {
        
        var update_menu = function (items) {
            $('#tb_restaurant_menu').empty();
            $('#form_model_restaurant_menu_add select[name="parent_id"]').html('<option value="0">- No parent -</option>');
            $.each(items,function(k, v) {
                //default style
                if(v.disabled==1)
                    v.disabled = '<span class="label label-sm sbold label-danger"> Yes </span>';
                else
                    v.disabled = '<span class="label label-sm sbold label-success"> No </span>';
                v.notes = (v.notes)? v.notes : '';
                $('#tb_restaurant_menu').append('<tr data-id="'+v.id+'"><td>'+v.name+'</td><td>'+v.notes+'</td><td>'+v.disabled+'</td><td><button type="button" class="btn sbold bg-yellow edit"><i class="fa fa-edit"></i></button></td><td><button type="button" disabled="true" class="btn sbold bg-red delete"><i class="fa fa-remove"></i></button></td></tr>');
                $('#form_model_restaurant_menu_add select[name="parent_id"]').append('<option value="'+v.id+'">&emsp;'+v.name+'</option>');
            });   
        }
        
        //on select main option
        $('#btn_model_menu').on('click', function(ev) {
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/restaurants/menu', 
                //data: {action:0}, 
                success: function(data) {
                    if(data.success) 
                    {
                        update_menu(data.menu);
                        //show modal
                        $('#modal_model_restaurant_menu').modal('show');
                    }
                    else{					
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error"
                        });
                    }
                },
                error: function(){	
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to load the menu's information!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    });
                }
            }); 
        });
        
        //on select main option
        $('#btn_model_menu_add').on('click', function(ev) {
            $('#form_model_restaurant_menu_add').trigger('reset');
            $('#form_model_restaurant_menu_add input[name="id"]:hidden').val('').trigger('change');
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/restaurants/menu', 
                success: function(data) {
                    if(data.success) 
                    {
                        update_menu(data.menu);
                        //show modal
                        $('#modal_model_restaurant_menu_add').modal('show');
                    }
                    else{					
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_restaurant_menu').modal('show');
                        });
                    }
                },
                error: function(){	
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to save the menu's information!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    },function(){
                        $('#modal_model_restaurant_menu').modal('show');
                    });
                }
            }); 
        });
        
        //function submit restaurant_items
        $('#submit_model_restaurant_menu_add').on('click', function(ev) {
            $('#modal_model_restaurant_menu_add').modal('hide');
            $('#modal_model_restaurant_menu').modal('hide');
            if($('#form_model_restaurant_menu').valid())
            {
                swal({
                    title: "Saving menu's information",
                    text: "Please, wait.",
                    type: "info",
                    showConfirmButton: false
                });
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/restaurants/menu', 
                    data: $('#form_model_restaurant_menu_add').serializeArray(), 
                    success: function(data) {
                        if(data.success) 
                        {
                            swal({
                                title: "<span style='color:green;'>Saved!</span>",
                                html: true,
                                timer: 1500,
                                type: "success",
                                showConfirmButton: false
                            });
                            update_menu(data.menu);
                            //show modal
                            $('#modal_model_restaurant_menu').modal('show');
                        }
                        else{					
                            swal({
                                title: "<span style='color:red;'>Error!</span>",
                                text: data.msg,
                                html: true,
                                type: "error"
                            },function(){
                                $('#modal_model_restaurant_menu').modal('show');
                                $('#modal_model_restaurant_menu_add').modal('show');
                            });
                        }
                    },
                    error: function(){	
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to save the menu's information!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_restaurant_menu').modal('show');
                            $('#modal_model_restaurant_menu_add').modal('show');
                        });
                    }
                }); 
            }
            else 
            {	
                swal({
                    title: "<span style='color:red;'>Error!</span>",
                    text: "You must fill out correctly the form'",
                    html: true,
                    type: "error"
                },function(){
                    $('#modal_model_restaurant_menu').modal('show');
                    $('#modal_model_restaurant_menu_add').modal('show');
                });
            }    
        });
        
        //function edit or remove
        $('#tb_restaurant_menu').on('click', 'button', function(e){
            var row = $(this).closest('tr');
            //edit
            if($(this).hasClass('edit')) 
            {
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/restaurants/menu', 
                    data: {action:0,id:row.data('id')}, 
                    success: function(data) {
                        if(data.success) 
                        {
                            $('#modal_model_restaurant_menu_add').trigger('reset');
                            $('#modal_model_restaurant_menu_add input[name="id"]:hidden').val(data.menu.id).trigger('change');
                            //fill out 
                            for(var key in data.menu)
                                $('#modal_model_restaurant_menu_add [name="'+key+'"]').val(data.menu[key]);
                            $('#form_model_restaurant_menu_add select[name="parent_id"]').find('option:disabled').removeAttr('disabled');
                            $('#form_model_restaurant_menu_add select[name="parent_id"]').find('option[value="'+data.menu.id+'"]').attr('disabled','disabled');
                            //modal                         
                            $('#modal_model_restaurant_menu_add').modal('show');
                        }
                        else{
                            $('#modal_model_restaurant_menu').modal('hide');
                            swal({
                                title: "<span style='color:red;'>Error!</span>",
                                text: data.msg,
                                html: true,
                                type: "error"
                            },function(){
                                $('#modal_model_restaurant_menu').modal('show');
                            });
                        }
                    },
                    error: function(){
                        $('#modal_model_restaurant_menu').modal('hide');
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to get the menu's information!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_restaurant_menu').modal('show');
                        });
                    }
                });
            }
            //delete
            else if($(this).hasClass('delete')) 
            {
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/restaurants/menu', 
                    data: {action:-1,id:row.data('id')}, 
                    success: function(data) {
                        if(data.success) 
                        {
                            update_menu(data.menu);
                        }
                        else{
                            $('#modal_model_restaurant_menu').modal('hide');
                            swal({
                                title: "<span style='color:red;'>Error!</span>",
                                text: data.msg,
                                html: true,
                                type: "error"
                            },function(){
                                $('#modal_model_restaurant_menu').modal('show');
                            });
                        }
                    },
                    error: function(){
			$('#modal_model_restaurant_menu').modal('hide');	   	
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to delete the menu!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_restaurant_menu').modal('show');
                        });
                    }
                });
            }
            else
            {
                $('#modal_model_update').modal('hide');	   	
                swal({
                    title: "<span style='color:red;'>Error!</span>",
                    text: "Invalid Option",
                    html: true,
                    type: "error"
                },function(){
                    $('#modal_model_update').modal('show');
                });
            }
        });
    }
    return {
        //main function to initiate the module
        init: function () {
            if (!jQuery().dataTable) {
                return;
            }
            initTable();        
        },
        update_items: function (items) {
            update_items(items);        
        }
    };
}();
//*****************************************************************************************
var FormMenuValidation = function () {
    // advance validation
    var handleValidation = function() {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation
            var form = $('#form_model_restaurant_menu');
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
                    parent_id: {
                        required: true
                    },
                    name: {
                        minlength: 3,
                        maxlength: 45,
                        required: true
                    },
                    notes: {
                        minlength: 5,
                        maxlength: 2000,
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
    TableMenuDatatablesManaged.init();
    FormMenuValidation.init();
});