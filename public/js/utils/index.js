
//*****************************************************************************************
//FORM IMAGE UPLOAD ANY FILE
var FormImageUpload = function (image_type,modal_callback,image_callback) {
    //variables biggest dimensions of image to upload
    var maxWidth = 1350;
    var maxHeight = 800;
    //fix dimensions for each type of file to upload
    switch (image_type) { 
	case 'bands.image_url': 
                var fixWidth = 600;
                var fixHeight = 600;
                break;  
        case 'shows.sponsor_logo_id': 
                var fixWidth = 100;
                var fixHeight = 50;
                break;  
        case 'images.logo': 
                var fixWidth = 600;
                var fixHeight = 300;
		break;  
        case 'venues.logo': 
                var fixWidth = 600;
                var fixHeight = 200;
		break;   
        case 'images.image': 
                var fixWidth = 800;
                var fixHeight = 800;
		break;  
        case 'images.header':
                var fixWidth = 1350;
                var fixHeight = 372;
		break;  
        case 'images.header medium': 
                var fixWidth = 1350;
                var fixHeight = 372;
                break;  
        case 'banners.file': 
                var fixWidth = 768;
                var fixHeight = 90;
                break;  
        case 'sliders.image_url': 
                var fixWidth = 1350;
                var fixHeight = 372;
		break;   
        case 'stages.image_url': 
                var fixWidth = 518;
                var fixHeight = 512;
		break; 
        case 'stages.url': 
                var fixWidth = 518;
                var fixHeight = 512;
		break; 
        case 'deals.image_url': 
                var fixWidth = 120;
                var fixHeight = 120;
		break; 
        case 'ads.regular': 
                var fixWidth = 300;
                var fixHeight = 250;
		break; 
        case 'ads.horizontal': 
                var fixWidth = 728;
                var fixHeight = 90;
		break; 
        case 'ads.vertical': 
                var fixWidth = 160;
                var fixHeight = 600;
		break; 
        case 'restaurants.items': 
                var fixWidth = 160;
                var fixHeight = 600;
		break; 
	default:
                var fixWidth = 600;
                var fixHeight = 600;
    }
    //reset form on event function
    function reset_form()
    {
        $('#image_preview').empty();
        $('#input_media_picture_name').html('- No image yet -');
        $("#form_media_picture_load").trigger('reset');
    }
    //reset form at init
    reset_form();
    //load modal
    $('#modal_media_picture_load').modal('show'); 
    if(modal_callback)
        $(modal_callback).modal('hide');
    //function update coord when crop
    function updateCoords(c)
    {
        $('#form_media_picture_load [name="crop_width"]').val(c.w);
        $('#form_media_picture_load [name="crop_height"]').val(c.h);
        $('#form_media_picture_load [name="crop_x"]').val(c.x);
        $('#form_media_picture_load [name="crop_y"]').val(c.y);
    };
    //add crop functionatily
    function addJcrop()
    {
        $('#image_edit').Jcrop({
            onSelect:    updateCoords,
            onChange:    updateCoords,
            bgColor:     'black',
            bgOpacity:   .7,
            setSelect:   [ fixWidth, fixHeight, 0, 0 ],
            minSize : [fixWidth,fixHeight],
            maxSize : [fixWidth,fixHeight]
        });
    }           
    //function when select picture
    $('#file_media_picture_upload').on('change', function(ev) {  
        //init
        var file = this.files[0];
        if(file.size > 1048576)
        {
            reset_form();
            alert('ERROR: This file is larger than 1 Mb.');
        }    
        else if(!(file.type==='image/jpg' || file.type==='image/png' || file.type==='image/jpeg' || file.type==='image/gif'))
        {
            reset_form();
            alert('ERROR: This file is not a valid image. It must be: *.jpg, *.png, *.jpeg or *.gif');
        }  
        else
        {
            var image = new Image();
            image.onload = function () {
                var currentWidth = this.width;
                var currentHeight = this.height;
                //max width and height permitted to the form
                if(currentWidth > maxWidth || currentHeight > maxHeight)
                {
                    reset_form();
                    alert('ERROR: This file has '+currentWidth+' width x '+currentHeight+' height. The required values must be less or equal than '+maxWidth+' width x '+maxHeight+' height.');
                }  
                //min width and height permitted according to the type of image to upload
                else if(currentWidth < fixWidth || currentHeight < fixHeight)
                {
                    reset_form();
                    alert('ERROR: This file has '+currentWidth+' width x '+currentHeight+' height. The required values must be more or equal than '+fixWidth+' width x '+fixHeight+' height.');
                }
                //if ok update form
                else
                {
                    //fill out the image info
                    $('#input_media_picture_name').html(file.name);
                    $('#form_media_picture_load [name="pic_size"]').val((file.size/1024).toFixed(1));
                    $('#form_media_picture_load [name="pic_width"]').val(currentWidth);
                    $('#form_media_picture_load [name="pic_height"]').val(currentHeight);
                    $('#form_media_picture_load [name="resize_width"]').val(fixWidth);
                    $('#form_media_picture_load [name="resize_height"]').val(fixHeight);
                }
            };
            if(file)
            {
                $('#image_preview').empty();
                image.src = window.URL.createObjectURL(file);
                image.id = 'image_edit';
                $('#image_preview').append(image);
                addJcrop();
                //place image preview
                function setAction(image)
                {
                    if($('input[name="action"]:checked','#form_media_picture_load').val()==='crop')
                    {
                        $('.jcrop-holder').css('background-color','rgb(0,0,0)');
                        $('#image_preview img').css('width',$('#form_media_picture_load [name="pic_width"]').val()+'px'); 
                        $('#image_preview img').css('height',$('#form_media_picture_load [name="pic_height"]').val()+'px'); 
                    }
                    else
                    {
                        $('.jcrop-holder').css('background-color','rgb(255,255,255)');
                        $('#image_preview img').css('width',fixWidth+'px'); 
                        $('#image_preview img').css('height',fixHeight+'px'); 
                    }
                }
                //setAction(image);
                $('#form_media_picture_load input[type=radio]').on('change', function() {
                    setAction(image);
                });
            }
        }
    });     
    //function on submit media
    $('#btn_upload_image').on('click', function(ev) {
        if($('#file_media_picture_upload')[0].files.length)
        {
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/media/upload_image', 
                data: new FormData($('#form_media_picture_load')[0]), 
                cache: false, 
                contentType: false,
                processData:false, 
                success: function(data) {
                    if(data.success)
                    {
                        if(image_callback)
                        {
                            $(image_callback).attr('src','./'+data.file);
                            $(image_callback).val(data.file);
                            image_callback = null;
                        } 
                        if(modal_callback)
                        {
                            $(modal_callback).modal('show');
                            modal_callback = null;
                        } 
                        $('#modal_media_picture_load').modal('hide'); 
                    }
                }
            });         
        }
        else
            alert('ERROR: There is not file to submit.');
    });    
    //function on reset form
    $('#btn_reset_image').on('click', function(ev) {
        reset_form();
    });  
    //function on close form
    $('#btn_close_image').on('click', function(ev) {
        if(modal_callback)
        {
            $(modal_callback).modal('show');
            modal_callback = null;
        } 
        $('#modal_media_picture_load').modal('hide'); 
    });
};
//*****************************************************************************************
//check valid url
var CheckValidURL = function (url) {
    return /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url);
};
//*****************************************************************************************
//check valid email
var CheckValidEmail = function (email) {
    return email.match('[_A-Za-z0-9-]+(\\.[_A-Za-z0-9-]+)*@[A-Za-z0-9]+(\\.[A-Za-z0-9]+)*(\\.[A-Za-z]{2,})$');
};
//*****************************************************************************************
//function save profile
$('#submit_model_update_profile').on('click', function(ev) {
    $('#modal_model_update_profile').modal('hide');
    if($('#form_model_update_profile').valid())
    {
        swal({
            title: "Saving user's information",
            text: "Please, wait.",
            type: "info",
            showConfirmButton: false
        });
        jQuery.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: 'POST',
            url: '/admin/users/profile', 
            data: $('#form_model_update_profile').serializeArray(), 
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
                        $('#modal_model_update_profile').modal('show');
                    });
                }
            },
            error: function(){
                swal({
                    title: "<span style='color:red;'>Error!</span>",
                    text: "Conexion error!<br>Please check the information again.",
                    html: true,
                    type: "error"
                },function(){
                    $('#modal_model_update_profile').modal('show');
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
            $('#modal_model_update_profile').modal('show');
        });
    }       
});
//*****************************************************************************************
//function impersonate
$('#impersonate_menu').on('click', function(ev) {
    $('#impersonate_link').html('');
    $('#modal_model_impersonate').modal('hide');
    if($('#impersonate_user_type').is(':empty') || $('#modal_model_impersonate select[name="user_id"]').has('option').length <= 1)
    {
        swal({
            title: "Searching users to impersonate",
            text: "Please, wait.",
            type: "info",
            showConfirmButton: false
        });
        jQuery.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: 'POST',
            url: '/admin/users/impersonate', 
            data: {action:0}, 
            success: function(data) {
                if(data.success) 
                {
                    //reset values
                    $('#impersonate_user_type').empty();
                    $('#modal_model_impersonate select[name="user_id"]').html('<option disabled selected value=""></option>');
                    //fill out values
                    $.each(data.user_types,function(k, v) {
                        $('#impersonate_user_type').append('<label class="mt-checkbox"><input type="checkbox" checked="true" name="user_type[]" value="'+v+'"/>'+v+'<span></span></label>');
                    });
                    $.each(data.users,function(k, v) {
                        $('#modal_model_impersonate select[name="user_id"]').append('<option value="'+v.id+'" rel="'+v.user_type+'">'+v.name+' ['+v.email+']'+' ('+v.user_type+')'+'</option>');
                    });
                    swal.close();
                    //show modal
                    $('#modal_model_impersonate').modal('show');
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
                    text: "Conexion error!<br>Please contact an administrator.",
                    html: true,
                    type: "error"
                });
            }
        }); 
    }
    else
        $('#modal_model_impersonate').modal('show');
});

$('#impersonate_user_type').on('click', 'input[type="checkbox"]', function(e){
    $('#modal_model_impersonate select[name="user_id"] option').css('display','none');
    $('[name="user_type[]"]:checked').each(function () {
        $('#modal_model_impersonate select[name="user_id"] option[rel="'+$(this).val()+'"]').css('display','block');
    });
    $('#modal_model_impersonate select[name="user_id"] option[value=""]').css('display','block');
    $('#modal_model_impersonate select[name="user_id"]').val('');
});
$('#submit_model_impersonate').on('click', function(ev) {
    var user_id = $('#modal_model_impersonate select[name="user_id"]').val();   
    if(user_id && user_id != '')
    {
        jQuery.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: 'POST',
            url: '/admin/users/impersonate', 
            data: {user_id:user_id}, 
            success: function(data) {
                if(data.success) 
                {
                    $('#impersonate_link').html('<br><a class="btn sbold blue" target="_blank" href="https://ticketbat.com/impersonate/'+data.link+'">TicketBat Public Site</a>'
                                               +' / <a class="btn sbold green" href="/admin/users/impersonate/'+data.link+'">TicketBat Admin</a> '
                                               +'<b style="color:red"> (valid only on '+moment().format('MM/DD/YYYY @ h:mma')+')</b>');
                }
                else{
                    $('#modal_model_impersonate').modal('hide');
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: data.msg,
                        html: true,
                        type: "error"
                    },function(){
                        $('#modal_model_impersonate').modal('show');
                    });
                }
            },
            error: function(){
                $('#modal_model_impersonate').modal('hide');
                swal({
                    title: "<span style='color:red;'>Error!</span>",
                    text: "Conexion error!<br>Please check the information again.",
                    html: true,
                    type: "error"
                },function(){
                    $('#modal_model_impersonate').modal('show');
                });
            }
        }); 
    }
    else
    {
        $('#modal_model_impersonate').modal('hide');
        swal({
            title: "<span style='color:red;'>Error!</span>",
            text: "The form is not valid!<br>Please select a valid user.",
            html: true,
            type: "error"
        },function(){
            $('#modal_model_impersonate').modal('show');
        });
    }       
});