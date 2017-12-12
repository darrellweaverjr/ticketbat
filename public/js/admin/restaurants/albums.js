var TableAlbumsDatatablesManaged = function () {
    
    var update_albums = function (items) {
        $('#tb_restaurant_albums').empty();
        var row_edit = '<td><button type="button" class="btn sbold bg-blue view"><i class="fa fa-image"></i></button></td><td><button type="button" class="btn sbold bg-yellow edit"><i class="fa fa-edit"></i></button></td><td><button type="button" class="btn sbold bg-red delete"><i class="fa fa-remove"></i></button></td>';
        $.each(items,function(k, v) {
            //default style
            if(v.enabled>0)
                v.enabled = '<span class="label label-sm sbold label-success">Yes</span>';
            else
                v.enabled = '<span class="label label-sm sbold label-danger">No</span>';
            $('#tb_restaurant_albums').append('<tr data-id="'+v.id+'"><td>'+v.title+'</td><td>'+v.posted+'</td><td>'+v.enabled+'</td><td>'+v.images+'</td>'+row_edit+'</tr>');
        });   
    }
    
    var initTable = function () {
        
        //posted
        $('#posted_albums').datetimepicker({
            autoclose: true,
            isRTL: App.isRTL(),
            format: "yyyy-mm-dd hh:ii",
            pickerPosition: (App.isRTL() ? "bottom-right" : "bottom-left"),
            todayBtn: true,
            defaultDate:'now'
        });
        
        //on select btn_model_items_add
        $('#btn_model_albums_add').on('click', function(ev) {
            $('#form_model_restaurant_albums').trigger('reset');
            $('#form_model_restaurant_albums input[name="id"]:hidden').val('').trigger('change');
            $('#form_model_restaurant_albums input[name="restaurants_id"]:hidden').val( $('#form_model_update [name="id"]').val() );
            $('#form_model_restaurant_albums input[name="action"]:hidden').val( 1 );
            $('#modal_model_restaurant_albums').modal('show');
        });
        
        //function submit restaurant_items
        $('#submit_model_restaurant_albums').on('click', function(ev) {
            $('#modal_model_restaurant_albums').modal('hide');
            $('#modal_model_update').modal('hide');
            if($('#form_model_restaurant_albums').valid())
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
                    url: '/admin/restaurants/albums', 
                    data: $('#form_model_restaurant_albums').serializeArray(), 
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
                            update_albums(data.albums);
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
                                $('#modal_model_restaurant_albums').modal('show');
                            });
                        }
                    },
                    error: function(){	
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to save the album's information!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_update').modal('show');
                            $('#modal_model_restaurant_albums').modal('show');
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
                    $('#modal_model_restaurant_albums').modal('show');
                });
            }    
        });
        
        //function edit or remove
        $('#tb_restaurant_albums').on('click', 'button', function(e){
            var row = $(this).closest('tr');
            //edit
            if($(this).hasClass('edit')) 
            {
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/restaurants/albums', 
                    data: {action:0,id:row.data('id')}, 
                    success: function(data) {
                        if(data.success) 
                        {
                            $('#form_model_restaurant_albums').trigger('reset');
                            $('#form_model_restaurant_albums input[name="id"]:hidden').val(data.album.id).trigger('change');
                            //fill out 
                            for(var key in data.album)
                            {
                                var e = $('#form_model_restaurant_albums [name="'+key+'"]');
                                if(e.is('input:checkbox'))
                                    $('#form_model_restaurant_albums .make-switch:checkbox[name="'+key+'"]').bootstrapSwitch('state', (data.album[key]>0)? true : false, true);
                                else
                                    e.val(data.album[key]);
                            }
                            //modal                         
                            $('#modal_model_restaurant_albums').modal('show');
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
                            text: "There was an error trying to get the album's information!<br>The request could not be sent to the server.",
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
                    url: '/admin/restaurants/albums', 
                    data: {action:-1,id:row.data('id')}, 
                    success: function(data) {
                        if(data.success) 
                        {
                            update_albums(data.albums);
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
                            text: "There was an error trying to delete the album!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_update').modal('show');
                        });
                    }
                });
            }
            //view gallery
            else if($(this).hasClass('view')) 
            {
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/restaurants/albums', 
                    data: {action:0,restaurant_albums_id:row.data('id')}, 
                    success: function(data) {
                        if(data.success) 
                        {
                            $('#modal_model_restaurant_albums_images').trigger('reset');
                            $('#modal_model_restaurant_albums_images input[name="restaurant_albums_id"]:hidden').val(row.data('id')).trigger('change');
                            update_album_images(data.images,row.data('id'));
                            $('#modal_model_restaurant_albums_images').modal('show');
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
                            text: "There was an error trying to get the gallery for the album!<br>The request could not be sent to the server.",
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
    
    var update_album_images = function (items,album) {
        $('#albumImages').empty();
        $.each(items,function(k, v) {
            var image = '<div class="cbp-item"><div class="cbp-caption"><a class="cbp-caption-defaultWrap">'+
                    '<img src="'+v.url+'" alt="Error image"> </a>'+
                    '<div class="cbp-caption-activeWrap"><div class="cbp-l-caption-alignCenter"><div class="cbp-l-caption-body">'+
                    '<button data-id="'+v.id+'" class="cbp-l-caption btn btn-lg red"><i class="fa fa-remove"></i></button>'+
                    '</div></div></div></div></div>';
            $('#albumImages').append(image);
        });   
        $('#tb_restaurant_albums tr[data-id="'+album+'"] td:nth-child(4)').html(items.length);
    }
    
    var initPortfolio = function () {
        
        //grid shows
        $('#albumImages').cubeportfolio({
            layoutMode: 'grid',
            //defaultFilter: '*',
            animationType: 'fadeOut', // quicksand
            gapHorizontal: 0,
            gapVertical: 0,
            gridAdjustment: 'responsive', 
            mediaQueries: [{ width: 800, cols: 3 }, { width: 480, cols: 2 }, { width: 320, cols: 1 }],
            caption: 'overlayBottomAlong', 
            displayType: 'default', 
            displayTypeSpeed: 1,
            loadMoreAction: 'auto'
        });
        
        //function load form to upload image
        $('#btn_model_album_images_add').on('click', function(ev) {
            $('#form_model_restaurant_albums_images [name="url"]').val('');
            FormImageUpload('albums.url','#modal_model_restaurant_albums_images','#form_model_restaurant_albums_images [name="url"]');   
        }); 
        
        //function upload image
        $('#form_model_restaurant_albums_images [name="url"]').on('change', function(ev) {
            var valid = $(this).val().match(/media\/preview/);
            $('#modal_model_restaurant_albums_images').modal('hide');
            $('#modal_model_update').modal('hide');
            if(valid && valid.length > 0)
            {
                swal({
                    title: "Uploading image to the server",
                    text: "Please, wait.",
                    type: "info",
                    showConfirmButton: false
                });
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/restaurants/albums', 
                    data: $('#form_model_restaurant_albums_images').serializeArray(), 
                    success: function(data) {
                        if(data.success) 
                        {
                            $('#form_model_restaurant_albums_images [name="url"]').val('');
                            update_album_images(data.images,$('#modal_model_restaurant_albums_images input[name="restaurant_albums_id"]:hidden').val());
                            swal({
                                title: "<span style='color:green;'>Uploaded!</span>",
                                text: data.msg,
                                html: true,
                                timer: 1500,
                                type: "success",
                                showConfirmButton: false
                            },function(){
                                $('#modal_model_update').modal('show');
                                $('#modal_model_restaurant_albums_images').modal('show');
                            });
                        }
                        else{
                            swal({
                                title: "<span style='color:red;'>Error!</span>",
                                text: data.msg,
                                html: true,
                                type: "error"
                            },function(){
                                $('#modal_model_update').modal('show');
                                $('#modal_model_restaurant_albums_images').modal('show');
                            });
                        }
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to upload the image!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_update').modal('show');
                            $('#modal_model_restaurant_albums_images').modal('show');
                        });
                    }
                }); 
            }    
            else
            {
                $('#form_model_restaurant_albums_images [name="url"]').val('');
                swal({
                    title: "<span style='color:red;'>Error!</span>",
                    text: "There was an error updating the image.<br>You must select a valid one!",
                    html: true,
                    type: "error"
                },function(){
                    $('#modal_model_update').modal('show');
                    $('#modal_model_restaurant_albums_images').modal('show');
                });
            }
        }); 
        
        //function remove
        $(document).on('click', '#albumImages button', function(ev){
            var id = $(this).data('id');
            var restaurant_albums_id = $('#modal_model_restaurant_albums_images input[name="restaurant_albums_id"]:hidden').val();
            $('#modal_model_restaurant_albums_images').modal('hide');
            $('#modal_model_update').modal('hide');
            swal({
                title: "Removing image",
                text: "Please, wait.",
                type: "info",
                showConfirmButton: false
            });
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/restaurants/albums', 
                data: {action:-1, restaurant_albums_id:restaurant_albums_id, id:id}, 
                success: function(data) {
                    if(data.success) 
                    {
                        update_album_images(data.images,$('#modal_model_restaurant_albums_images input[name="restaurant_albums_id"]:hidden').val());
                        swal({
                            title: "<span style='color:green;'>Deleted!</span>",
                            text: data.msg,
                            html: true,
                            timer: 1500,
                            type: "success",
                            showConfirmButton: false
                        },function(){
                            $('#modal_model_update').modal('show');
                            $('#modal_model_restaurant_albums_images').modal('show');
                        });
                    }
                    else{
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_update').modal('show');
                            $('#modal_model_restaurant_albums_images').modal('show');
                        });
                    }
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to delete the image!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    },function(){
                        $('#modal_model_update').modal('show');
                        $('#modal_model_restaurant_albums_images').modal('show');
                    });
                }
            }); 
                
        });
        
    }
    
    return {
        //main function to initiate the module
        init: function () {
            if (!jQuery().dataTable) {
                return;
            }
            initTable();   
            initPortfolio();
        },
        update_albums: function (items) {
            update_albums(items);        
        }
    };
}();
//*****************************************************************************************
var FormAlbumsValidation = function () {
    // advance validation
    var handleValidation = function() {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation
            var form = $('#form_model_restaurant_albums');
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
    TableAlbumsDatatablesManaged.init();
    FormAlbumsValidation.init();
});