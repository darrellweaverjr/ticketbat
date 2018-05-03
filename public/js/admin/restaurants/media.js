var TableMediaDatatablesManaged = function () {
    
    var initTable = function () {
        
        var update_media = function (items) {
            $('#tb_restaurant_media').empty();
            var row_edit = '<td><button type="button" class="btn sbold bg-yellow edit"><i class="fa fa-edit"></i></button></td><td><button type="button" class="btn sbold bg-red delete" disabled="true"><i class="fa fa-remove"></i></button></td>';
            $.each(items,function(k, v) {
                //image
                if(v.image_id)
                    v.image_id = '<img width="80px" height="80px" src="'+v.image_id+'"/>';
                else
                    v.image_id = '-No image-';
                $('#tb_restaurant_media').append('<tr data-id="'+v.id+'"><td>'+v.image_id+'</td><td>'+v.name+'</td>'+row_edit+'</tr>');
            });   
        }
        
        //on select main option
        $('#btn_model_media').on('click', function(ev) {
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/restaurants/media', 
                //data: {action:0}, 
                success: function(data) {
                    if(data.success) 
                    {
                        update_media(data.media);
                        //show modal
                        $('#modal_model_restaurant_media').modal('show');
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
                        text: "There was an error trying to load the media's information!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    });
                }
            }); 
        });
        
        //on select main option
        $('#btn_model_media_add').on('click', function(ev) {
            $('#form_model_restaurant_media_add').trigger('reset');
            $('#form_model_restaurant_media_add input[name="id"]:hidden').val('').trigger('change');
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/restaurants/media', 
                success: function(data) {
                    if(data.success) 
                    {
                        update_media(data.media);
                        //show modal
                        $('#modal_model_restaurant_media_add').modal('show');
                    }
                    else{					
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_restaurant_media').modal('show');
                        });
                    }
                },
                error: function(){	
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to save the media's information!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    },function(){
                        $('#modal_model_restaurant_media').modal('show');
                    });
                }
            }); 
        });
        
        //function submit restaurant_items
        $('#submit_model_restaurant_media_add').on('click', function(ev) {
            $('#modal_model_restaurant_media_add').modal('hide');
            $('#modal_model_restaurant_media').modal('hide');
            if($('#form_model_restaurant_media').valid())
            {
                swal({
                    title: "Saving media's information",
                    text: "Please, wait.",
                    type: "info",
                    showConfirmButton: false
                });
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/restaurants/media', 
                    data: $('#form_model_restaurant_media_add').serializeArray(), 
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
                            update_media(data.media);
                            //show modal
                            $('#modal_model_restaurant_media').modal('show');
                        }
                        else{					
                            swal({
                                title: "<span style='color:red;'>Error!</span>",
                                text: data.msg,
                                html: true,
                                type: "error"
                            },function(){
                                $('#modal_model_restaurant_media').modal('show');
                                $('#modal_model_restaurant_media_add').modal('show');
                            });
                        }
                    },
                    error: function(){	
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to save the media's information!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_restaurant_media').modal('show');
                            $('#modal_model_restaurant_media_add').modal('show');
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
                    $('#modal_model_restaurant_media').modal('show');
                    $('#modal_model_restaurant_media_add').modal('show');
                });
            }    
        });
        
        //function edit or remove
        $('#tb_restaurant_media').on('click', 'button', function(e){
            var row = $(this).closest('tr');
            //edit
            if($(this).hasClass('edit')) 
            {
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/restaurants/media', 
                    data: {action:0,id:row.data('id')}, 
                    success: function(data) {
                        if(data.success) 
                        {
                            $('#modal_model_restaurant_media_add').trigger('reset');
                            $('#modal_model_restaurant_media_add input[name="id"]:hidden').val(data.media.id).trigger('change');
                            //fill out 
                            for(var key in data.media)
                                $('#modal_model_restaurant_media_add [name="'+key+'"]').val(data.media[key]);
                            $('#form_model_restaurant_media_add select[name="parent_id"]').find('option:disabled').removeAttr('disabled');
                            $('#form_model_restaurant_media_add select[name="parent_id"]').find('option[value="'+data.media.id+'"]').attr('disabled','disabled');
                            //modal                         
                            $('#modal_model_restaurant_media_add').modal('show');
                        }
                        else{
                            $('#modal_model_restaurant_media').modal('hide');
                            swal({
                                title: "<span style='color:red;'>Error!</span>",
                                text: data.msg,
                                html: true,
                                type: "error"
                            },function(){
                                $('#modal_model_restaurant_media').modal('show');
                            });
                        }
                    },
                    error: function(){
                        $('#modal_model_restaurant_media').modal('hide');
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to get the media's information!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_restaurant_media').modal('show');
                        });
                    }
                });
            }
            //delete
//            else if($(this).hasClass('delete')) 
//            {
//                jQuery.ajax({
//                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
//                    type: 'POST',
//                    url: '/admin/restaurants/menu', 
//                    data: {action:-1,id:row.data('id')}, 
//                    success: function(data) {
//                        if(data.success) 
//                        {
//                            update_menu(data.menu);
//                        }
//                        else{
//                            $('#modal_model_restaurant_menu').modal('hide');
//                            swal({
//                                title: "<span style='color:red;'>Error!</span>",
//                                text: data.msg,
//                                html: true,
//                                type: "error"
//                            },function(){
//                                $('#modal_model_restaurant_menu').modal('show');
//                            });
//                        }
//                    },
//                    error: function(){
//			$('#modal_model_restaurant_menu').modal('hide');	   	
//                        swal({
//                            title: "<span style='color:red;'>Error!</span>",
//                            text: "There was an error trying to delete the menu!<br>The request could not be sent to the server.",
//                            html: true,
//                            type: "error"
//                        },function(){
//                            $('#modal_model_restaurant_menu').modal('show');
//                        });
//                    }
//                });
//            }
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
        $('#btn_restaurant_media_upload_image').on('click', function(ev) {
            FormImageUpload('restaurants.media','#modal_model_restaurant_media_add','#form_model_restaurant_media_add [name="image_id"]');   
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
        update_medias: function (items) {
            update_medias(items);        
        }
    };
}();
//*****************************************************************************************
var FormMediaValidation = function () {
    return {
        //main function to initiate the module
        init: function () {
            // advance validation
            var rules = {
                image_id: {
                    required: true
                },
                name: {
                    minlength: 3,
                    maxlength: 45,
                    required: true
                }
            };
            MainFormValidation.init('form_model_restaurant_media',rules,{});
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    TableMediaDatatablesManaged.init();
    FormMediaValidation.init();
});