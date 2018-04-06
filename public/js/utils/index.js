//*****************************************************************************************
//FORM IMAGE UPLOAD ANY FILE
var FormImageUpload = function (image_type, modal_callback, image_callback) {
    //variables biggest dimensions of image to upload
    var maxWidth = 1350;
    var maxHeight = 800;
    //fix dimensions for each type of file to upload
    switch (image_type) {
        case 'bands.image_url':
            var fixWidth = 600;
            var fixHeight = 600;
            break;
        case 'venues.logo':
            var fixWidth = 300;
            var fixHeight = 300;
            break;
        case 'venues.logo_url':
            var fixWidth = 300;
            var fixHeight = 300;
            break;
        case 'venues.header_url':
            var fixWidth = 1350;
            var fixHeight = 270;
            break;
        case 'shows.logo_url':
            var fixWidth = 600;
            var fixHeight = 300;
            break;
        case 'shows.header_url':
            var fixWidth = 1350;
            var fixHeight = 270;
            break;
        case 'shows.sponsor_logo_id':
            var fixWidth = 100;
            var fixHeight = 50;
            break;
        case 'images.logo':
            var fixWidth = 600;
            var fixHeight = 300;
            break;
        case 'images.image':
            var fixWidth = 800;
            var fixHeight = 800;
            break;
        case 'images.header':
            var fixWidth = 1350;
            var fixHeight = 270;
            break;
        case 'images.mobile header':
            var fixWidth = 500;
            var fixHeight = 140;
            break;
        case 'images.thumbnail':
            var fixWidth = 60;
            var fixHeight = 60;
            break;
        case 'images.header medium':
            var fixWidth = 1350;
            var fixHeight = 270;
            break;
        case 'banners.file':
            var fixWidth = 768;
            var fixHeight = 90;
            break;
        case 'sliders.image_url':
            var fixWidth = 1350;
            var fixHeight = 270;
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
            var fixWidth = 600;
            var fixHeight = 600;
            break;
        default:
            var fixWidth = 600;
            var fixHeight = 600;
    }

    //reset form on event function
    function reset_form() {
        $('#image_preview').empty();
        $('#input_media_picture_name').html('- No image yet -');
        $("#form_media_picture_load").trigger('reset');
    }

    //reset form at init
    reset_form();
    //load modal
    $('#modal_media_picture_load').modal('show');
    if (modal_callback)
        $(modal_callback).modal('hide');

    //function update coord when crop
    function updateCoords(c) {
        $('#form_media_picture_load [name="crop_width"]').val(c.w);
        $('#form_media_picture_load [name="crop_height"]').val(c.h);
        $('#form_media_picture_load [name="crop_x"]').val(c.x);
        $('#form_media_picture_load [name="crop_y"]').val(c.y);
    };

    //add crop functionatily
    function addJcrop() {
        $('#image_edit').Jcrop({
            onSelect: updateCoords,
            onChange: updateCoords,
            bgColor: 'black',
            bgOpacity: .7,
            setSelect: [fixWidth, fixHeight, 0, 0],
            minSize: [fixWidth, fixHeight],
            maxSize: [fixWidth, fixHeight]
        });
    }

    //function when select picture
    $('#file_media_picture_upload').on('change', function (ev) {
        //init
        var file = this.files[0];
        if (file.size > 204800) {
            reset_form();
            alert('ERROR: This file is larger than 200 Kb.');
        }
        else if (!(file.type === 'image/jpg' || file.type === 'image/png' || file.type === 'image/jpeg' || file.type === 'image/gif')) {
            reset_form();
            alert('ERROR: This file is not a valid image. It must be: *.jpg, *.png, *.jpeg or *.gif');
        }
        else {
            var image = new Image();
            image.onload = function () {
                var currentWidth = this.width;
                var currentHeight = this.height;
                //fill out the image info
                $('#input_media_picture_name').html(file.name);
                $('#form_media_picture_load [name="pic_size"]').val((file.size / 1024).toFixed(1));
                $('#form_media_picture_load [name="pic_width"]').val(currentWidth);
                $('#form_media_picture_load [name="pic_height"]').val(currentHeight);
                $('#form_media_picture_load [name="resize_width"]').val(fixWidth);
                $('#form_media_picture_load [name="resize_height"]').val(fixHeight);
            };
            if (file) {
                $('#image_preview').empty();
                image.src = window.URL.createObjectURL(file);
                image.id = 'image_edit';
                $('#image_preview').append(image);
                addJcrop();

                //place image preview
                function setAction(image) {
                    if ($('input[name="action"]:checked', '#form_media_picture_load').val() === 'crop') {
                        $('.jcrop-holder').css('background-color', 'rgb(0,0,0)');
                        $('#image_preview img').css('width', $('#form_media_picture_load [name="pic_width"]').val() + 'px');
                        $('#image_preview img').css('height', $('#form_media_picture_load [name="pic_height"]').val() + 'px');
                    }
                    else {
                        $('.jcrop-holder').css('background-color', 'rgb(255,255,255)');
                        $('#image_preview img').css('width', fixWidth + 'px');
                        $('#image_preview img').css('height', fixHeight + 'px');
                    }
                }

                //setAction(image);
                $('#form_media_picture_load input[type=radio]').on('change', function () {
                    setAction(image);
                });
            }
        }
    });
    //function on submit media
    $('#btn_upload_image').on('click', function (ev) {
        if ($('#file_media_picture_upload')[0].files.length) {
            jQuery.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: 'POST',
                url: '/admin/media/upload_image',
                data: new FormData($('#form_media_picture_load')[0]),
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data.success) {
                        if (image_callback) {
                            $(image_callback).attr('src', './' + data.file);
                            $(image_callback).val(data.file).trigger('change');
                            image_callback = null;
                        }
                        if (modal_callback) {
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
    $('#btn_reset_image').on('click', function (ev) {
        reset_form();
    });
    //function on close form
    $('#btn_close_image').on('click', function (ev) {
        if (modal_callback) {
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
$('#submit_model_update_profile').on('click', function (ev) {
    $('#modal_model_update_profile').modal('hide');
    if ($('#form_model_update_profile').valid()) {
        swal({
            title: "Saving user's information",
            text: "Please, wait.",
            type: "info",
            showConfirmButton: false
        });
        jQuery.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'POST',
            url: '/admin/users/profile',
            data: $('#form_model_update_profile').serializeArray(),
            success: function (data) {
                if (data.success) {
                    swal({
                        title: "<span style='color:green;'>Saved!</span>",
                        text: data.msg,
                        html: true,
                        timer: 1500,
                        type: "success",
                        showConfirmButton: false
                    });
                }
                else {
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: data.msg,
                        html: true,
                        type: "error"
                    }, function () {
                        $('#modal_model_update_profile').modal('show');
                    });
                }
            },
            error: function () {
                swal({
                    title: "<span style='color:red;'>Error!</span>",
                    text: "Conexion error!<br>Please check the information again.",
                    html: true,
                    type: "error"
                }, function () {
                    $('#modal_model_update_profile').modal('show');
                });
            }
        });
    }
    else {
        swal({
            title: "<span style='color:red;'>Error!</span>",
            text: "The form is not valid!<br>Please check the information again.",
            html: true,
            type: "error"
        }, function () {
            $('#modal_model_update_profile').modal('show');
        });
    }
});
