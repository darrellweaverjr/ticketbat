var TableItemsDatatablesManaged = function () {
    
    var update_items = function (items) {
        $('#tb_restaurant_items').empty();
        $.each(items,function(k, v) {
            //default style
            if(v.disabled==1)
                v.disabled = '<span class="label label-sm sbold label-success"> Yes </span>';
            else
                v.disabled = '<span class="label label-sm sbold label-danger"> No </span>';
            //image
            if(v.url)
                v.url = '<img src="'+v.url+'"/>';
            else
                v.url = '-No image-';
            $('#tb_restaurant_items').append('<tr data-id="'+v.id+'"><td>'+v.menu+'</td><td>'+v.order+'</td><td>'+v.name+'</td><td>$'+v.price+'</td><td>'+v.disabled+'</td><td>'+v.url+'</td><td><input type="button" value="Edit" class="btn sbold bg-yellow edit"></td><td><input type="button" value="Remove" class="btn sbold bg-red delete"></td></tr>');
        });   
    }
    
    var update_items_order = function (add=0) {
        var positions = $('#tb_restaurant_items >tr').length;
        if(add) positions++;
        $('#form_model_restaurant_items select[name="order"]').empty();
        if(positions>1)
        {
            while(positions > 0)
            {
                $('#form_model_restaurant_items select[name="order"]').prepend('<option value="'+positions+'">'+positions+'</option>');
                positions--;
            }
        }
        else
            $('#form_model_restaurant_items select[name="order"]').append('<option value="">Last</option>');
    }
    
    var initTable = function () {
        
        //on select ticket_type
        $('#btn_model_items_add').on('click', function(ev) {
            $('#form_model_restaurant_items').trigger('reset');
            $('#form_model_restaurant_items input[name="id"]:hidden').val('').trigger('change');
            $('#form_model_restaurant_items input[name="restaurants_id"]:hidden').val( $('#form_model_update [name="id"]').val() );
            $('#form_model_restaurant_items input[name="action"]:hidden').val( 1 );
            update_items_order(1);    
            $('#modal_model_restaurant_items').modal('show');
        });
        
        //function submit restaurant_items
        $('#submit_model_restaurant_items').on('click', function(ev) {
            $('#modal_model_restaurant_items').modal('hide');
            $('#modal_model_update').modal('hide');
            if($('#form_model_restaurant_items').valid())
            {
                swal({
                    title: "Saving restaurant's information",
                    text: "Please, wait.",
                    type: "info",
                    showConfirmButton: false
                });
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/restaurants/items', 
                    data: $('#form_model_restaurant_items').serializeArray(), 
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
                            update_items(data.items);
                            //show modal
                            $('#modal_model_update').modal('show');
                        }
                        else{					
                            swal({
                                title: "<span style='color:red;'>Error!</span>",
                                text: data.msg,
                                html: true,
                                type: "error"
                            },function(){
                                $('#modal_model_update').modal('show');
                                $('#modal_model_restaurant_items').modal('show');
                            });
                        }
                    },
                    error: function(){	
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to save the item's information!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_update').modal('show');
                            $('#modal_model_restaurant_items').modal('show');
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
                    $('#modal_model_update').modal('show');
                    $('#modal_model_restaurant_items').modal('show');
                });
            }    
        });
        
        //function edit or remove
        $('#tb_restaurant_items').on('click', 'input[type="button"]', function(e){
            var row = $(this).closest('tr');
            //edit
            if($(this).hasClass('edit')) 
            {
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/restaurants/items', 
                    data: {action:0,id:row.data('id')}, 
                    success: function(data) {
                        if(data.success) 
                        {
                            $('#form_model_restaurant_items').trigger('reset');
                            $('#form_model_restaurant_items input[name="id"]:hidden').val(data.item.id).trigger('change');
                            //order
                            update_items_order(0);   
                            //fill out 
                            for(var key in data.item)
                            {
                                var e = $('#form_model_restaurant_items [name="'+key+'"]');
                                if(e.is('input:checkbox'))
                                    $('#form_model_restaurant_items .make-switch:checkbox[name="'+key+'"]').bootstrapSwitch('state', (data.item[key]>0)? true : false, true);
                                else if(e.is('img'))
                                    e.src = data.item[key];
                                else
                                    e.val(data.item[key]);
                            }
                            //modal                         
                            $('#modal_model_restaurant_items').modal('show');
                        }
                        else{
                            $('#modal_model_update').modal('hide');
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
                        $('#modal_model_update').modal('hide');
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to get the password's information!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_update').modal('show');
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
                    url: '/admin/restaurants/items', 
                    data: {action:-1,id:row.data('id')}, 
                    success: function(data) {
                        if(data.success) 
                        {
                            update_items(data.items);
                        }
                        else{
                            $('#modal_model_update').modal('hide');
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
			$('#modal_model_update').modal('hide');	   	
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to delete the item!<br>The request could not be sent to the server.",
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
var FormItemsValidation = function () {
    // advance validation
    var handleValidation = function() {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation
            var form = $('#form_model_restaurant_items');
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
                    restaurant_menu_id: {
                        required: true
                    },
                    name: {
                        minlength: 3,
                        maxlength: 45,
                        required: true
                    },
                    notes: {
                        minlength: 3,
                        maxlength: 45,
                        required: false
                    },
                    description: {
                        minlength: 5,
                        maxlength: 2000,
                        required: false
                    },
                    price: {
                        min: 0.01,
                        //step: 0.01,
                        //number:true,
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
    TableItemsDatatablesManaged.init();
    FormItemsValidation.init();
});