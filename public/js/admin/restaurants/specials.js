var TableSpecialsDatatablesManaged = function () {
    
    var update_specials = function (items) {
        $('#tb_restaurant_specials').empty();
        var row_edit = '<td><button type="button" class="btn sbold bg-yellow edit"><i class="fa fa-edit"></i></button></td><td><button type="button" class="btn sbold bg-red delete"><i class="fa fa-remove"></i></button></td>';
        $.each(items,function(k, v) {
            //default style
            if(v.enabled>0)
                v.enabled = '<span class="label label-sm sbold label-success"> Yes </span>';
            else
                v.enabled = '<span class="label label-sm sbold label-danger"> No </span>';
            //image
            if(v.image_id)
                v.image_id = '<img width="80px" height="80px" src="'+v.image_id+'"/>';
            else
                v.image_id = '-No image-';
            $('#tb_restaurant_specials').append('<tr data-id="'+v.id+'"><td>'+v.order+'</td><td>'+v.title+'</td><td>'+v.description+'</td><td>'+v.enabled+'</td><td>'+v.image_id+'</td>'+row_edit+'</tr>');
        });   
    }
    
    var update_specials_order = function (add=0) {
        var positions = $('#tb_restaurant_specials >tr').length;
        if(add) positions++;
        $('#form_model_restaurant_specials select[name="order"]').empty();
        if(positions>=1)
        {
            while(positions > 0)
            {
                $('#form_model_restaurant_specials select[name="order"]').prepend('<option value="'+positions+'">'+positions+'</option>');
                positions--;
            }
        }
        else
            $('#form_model_restaurant_specials select[name="order"]').append('<option value="">Last</option>');
    }
    
    var initTable = function () {
        
        //on select btn_model_specials_add
        $('#btn_model_specials_add').on('click', function(ev) {
            $('#form_model_restaurant_specials').trigger('reset');
            $('#form_model_restaurant_specials input[name="id"]:hidden').val('').trigger('change');
            $('#form_model_restaurant_specials input[name="restaurants_id"]:hidden').val( $('#form_model_update [name="id"]').val() );
            $('#form_model_restaurant_specials input[name="action"]:hidden').val( 1 );
            update_specials_order(1);    
            $('#modal_model_restaurant_specials').modal('show');
        });
        
        //function submit restaurant_specials
        $('#submit_model_restaurant_specials').on('click', function(ev) {
            $('#modal_model_restaurant_specials').modal('hide');
            $('#modal_model_update').modal('hide');
            if($('#form_model_restaurant_specials').valid())
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
                    url: '/admin/restaurants/specials', 
                    data: $('#form_model_restaurant_specials').serializeArray(), 
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
                            update_specials(data.specials);
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
                                $('#modal_model_restaurant_specials').modal('show');
                            });
                        }
                    },
                    error: function(){	
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to save the special's information!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_update').modal('show');
                            $('#modal_model_restaurant_specials').modal('show');
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
                    $('#modal_model_restaurant_specials').modal('show');
                });
            }    
        });
        
        //function edit or remove
        $('#tb_restaurant_specials').on('click', 'button', function(e){
            var row = $(this).closest('tr');
            //edit
            if($(this).hasClass('edit')) 
            {
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/restaurants/specials', 
                    data: {action:0,id:row.data('id')}, 
                    success: function(data) {
                        if(data.success) 
                        {
                            $('#form_model_restaurant_specials').trigger('reset');
                            $('#form_model_restaurant_specials input[name="id"]:hidden').val(data.special.id).trigger('change');
                            //order
                            update_specials_order(0);   
                            //fill out 
                            for(var key in data.special)
                            {
                                var e = $('#form_model_restaurant_specials [name="'+key+'"]');
                                if(e.is('input:checkbox'))
                                    $('#form_model_restaurant_specials .make-switch:checkbox[name="'+key+'"]').bootstrapSwitch('state', (data.special[key]>0)? true : false, true);
                                else if(e.is('img'))
                                    e.src = data.special[key];
                                else
                                    e.val(data.special[key]);
                            }
                            //modal                         
                            $('#modal_model_restaurant_specials').modal('show');
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
                            text: "There was an error trying to get the special's information!<br>The request could not be sent to the server.",
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
                var restaurants_id = $('#form_model_update [name="id"]').val();
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/restaurants/specials', 
                    data: {action:-1,id:row.data('id'),restaurants_id:restaurants_id}, 
                    success: function(data) {
                        if(data.success) 
                        {
                            update_specials(data.specials);
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
                            text: "There was an error trying to delete the special!<br>The request could not be sent to the server.",
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
        //function load form to upload image
        $('#btn_restaurant_special_upload_image').on('click', function(ev) {
            FormImageUpload('restaurants.specials','#modal_model_restaurant_specials','#form_model_restaurant_specials [name="image_id"]');       
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
        update_specials: function (items) {
            update_specials(items);        
        }
    };
}();
//*****************************************************************************************
var FormSpecialsValidation = function () {
    return {
        //main function to initiate the module
        init: function () {
            // advance validation
            var rules = {
                title: {
                    minlength: 3,
                    maxlength: 45,
                    required: true
                },
                description: {
                    minlength: 5,
                    maxlength: 2000,
                    required: false
                }
            };
            MainFormValidation.init('form_model_restaurant_specials',rules,{});
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    TableSpecialsDatatablesManaged.init();
    FormSpecialsValidation.init();
});