/* global venue_id */

var TableDatatablesManaged = function () {

    var initTable = function () {
        
        var table = MainDataTableCreator.init('tb_model',[ [1, "asc"] ],5,true);

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
        //link to venues page
        $('#go_to_slug').on('click', function(ev) {
            var id = $('#form_model_update [name="id"]').val();
            var slug = $('#form_model_update [name="slug"]').val();
            if(id && slug)
                window.open('http://www.ticketbat.com/venue/'+slug);
        });
        //get slug on name change
        $('#form_model_update [name="name"]').bind('change',function() {
            var id = $('#form_model_update [name="id"]').val();
            if(!id || id=='')
            {
                if($('#form_model_update [name="name"]').val().length >= 5)
                {
                    jQuery.ajax({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        type: 'POST',
                        url: '/admin/venues/slug',
                        data: {
                            name:$('#form_model_update [name="name"]').val(),
                            venue_id:$('#form_model_update [name="id"]').val()
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
            }
        });
        //check/uncheck all
        var check_models = function(){
            var set = $('.group-checkable').attr("data-set");
            var checked = $(set+"[type=checkbox]:checked").length;
            if(checked == 1)
            {
                $('#btn_model_edit').prop("disabled",false);
                $('#btn_model_preview').prop("disabled",false);
                $('#btn_model_remove').prop("disabled",false);
            }
            else if(checked > 1)
            {
                $('#btn_model_edit').prop("disabled",true);
                $('#btn_model_preview').prop("disabled",true);
                $('#btn_model_remove').prop("disabled",false);
            }
            else
            {
                $('#btn_model_edit').prop("disabled",true);
                $('#btn_model_preview').prop("disabled",true);
                $('#btn_model_remove').prop("disabled",true);
            }
            $('#btn_model_add').prop("disabled",false);
        }
        //function full reset form
        var fullReset = function(){
            $("#form_model_update input[name='id']:hidden").val('').trigger('change');
            $("#form_model_update").trigger('reset');
            $('#tb_venue_ads').empty();
            $('#form_model_update img[name="logo_url"]').attr('src','');
            $('#form_model_update img[name="header_url"]').attr('src','');
        };
        //function add
        $('#btn_model_add').on('click', function(ev) {
            fullReset();
            if($('#modal_model_update_header').hasClass('bg-yellow'))
                $('#modal_model_update_header,#btn_model_save').removeClass('bg-yellow').addClass('bg-green');
            else $('#modal_model_update_header,#btn_model_save').addClass('bg-green');
            $('#modal_model_update_title').html('Add Venue');
            $('a[href="#tab_model_update_stages"]').parent().css('display','none');
            $('a[href="#tab_model_update_stage_images"]').parent().css('display','none');
            $('a[href="#tab_model_update_images"]').parent().css('display','none');
            $('a[href="#tab_model_update_banners"]').parent().css('display','none');
            $('a[href="#tab_model_update_videos"]').parent().css('display','none');
            $('a[href="#tab_model_update_ads"]').parent().css('display','none');
            $('#modal_model_update').modal('show');
        });
        //funcion load modal by id
        function loadModal(data) {
            //reset modal
            fullReset();
            if($('#modal_model_update_header').hasClass('bg-green'))
                $('#modal_model_update_header,#btn_model_save').removeClass('bg-green').addClass('bg-yellow');
            else $('#modal_model_update_header,#btn_model_save').addClass('bg-yellow');
            $('a[href="#tab_model_update_stages"]').parent().css('display','block');
            $('a[href="#tab_model_update_stage_images"]').parent().css('display','block');
            $('a[href="#tab_model_update_images"]').parent().css('display','block');
            $('a[href="#tab_model_update_banners"]').parent().css('display','block');
            $('a[href="#tab_model_update_videos"]').parent().css('display','block');
            $('a[href="#tab_model_update_ads"]').parent().css('display','block');
            $('#modal_model_update_title').html(data.venue.name);
            //fill out defaults
            $('#form_model_update [name="id"]').val(data.venue.id).change();
            $('#form_model_venue_stages input[name="venue_id"]:hidden').val(data.venue.id).trigger('change');
            $('#form_model_venue_images input[name="venue_id"]:hidden').val(data.venue.id).trigger('change');
            $('#form_model_venue_banners input[name="parent_id"]:hidden').val(data.venue.id).trigger('change');
            $('#form_model_venue_videos input[name="venue_id"]:hidden').val(data.venue.id).trigger('change');
            //fill out venues
            for(var key in data.venue)
            {
                //fill out
                var e = $('#form_model_update [name="'+key+'"]');
                if(key=='logo_url' || key=='header_url')
                {
                    $('#form_model_update img[name="'+key+'"]').attr('src',data.venue[key]);
                    $('#form_model_update input[name="'+key+'"]').val(data.venue[key]);
                }
                else if(e.is('input:checkbox'))
                    $('#form_model_update .make-switch:checkbox[name="'+key+'"]').bootstrapSwitch('state', (data.venue[key]>0)? true : false, true);
                else
                    e.val(data.venue[key]);
            }
            //fill out stages
            $('#grid_venue_stages .cbp-item').remove();
            $('#grid_venue_stages').trigger('resize.cbp');
            $('#form_model_venue_stage_images select[name="stage"]').empty();
            if(data.stages && data.stages.length)
            {
                var html = '';
                $.each(data.stages,function(k, v) {
                    html = html + fn_venue_stages(v);
                    $('#form_model_venue_stage_images select[name="stage_id"]').append('<option value="'+v.id+'">'+v.name+'</option>');
                });
                $('#grid_venue_stages').cubeportfolio('appendItems', html);
                $('#grid_venue_stages').trigger('resize.cbp');
            }

            //fill out stage images
            $('#grid_venue_stage_images .cbp-item').remove();
            $('#grid_venue_stage_images').trigger('resize.cbp');
            if(data.stage_images && data.stage_images.length)
            {
                var html = '';
                $.each(data.stage_images,function(k, v) {
                    html = html + fn_venue_stage_images(v);
                });
                $('#grid_venue_stage_images').cubeportfolio('appendItems', html);
                $('#grid_venue_stage_images').trigger('resize.cbp');
            }
            //fill out images
            $('#grid_venue_images .cbp-item').remove();
            $('#grid_venue_images').trigger('resize.cbp');
            if(data.images && data.images.length)
            {
                var html = '';
                $.each(data.images,function(k, v) {
                    html = html + fn_venue_images(v);
                });
                $('#grid_venue_images').cubeportfolio('appendItems', html);
                $('#grid_venue_images').trigger('resize.cbp');
            }
            //fill out banners
            $('#grid_venue_banners .cbp-item').remove();
            $('#grid_venue_banners').trigger('resize.cbp');
            if(data.banners && data.banners.length)
            {
                var html = '';
                $.each(data.banners,function(k, v) {
                    html = html + fn_venue_banners(v);
                });
                $('#grid_venue_banners').cubeportfolio('appendItems', html);
                $('#grid_venue_banners').trigger('resize.cbp');
            }
            //fill out videos
            $('#grid_venue_videos .cbp-item').remove();
            $('#grid_venue_videos').trigger('resize.cbp');
            if(data.videos && data.videos.length)
            {
                var html = '';
                $.each(data.videos,function(k, v) {
                    html = html + fn_venue_videos(v);
                });
                $('#grid_venue_videos').cubeportfolio('appendItems', html);
                $('#grid_venue_videos').trigger('resize.cbp');
            }
            //fill out ads
            if(data.ads && data.ads.length)
            {
                update_ads(data.ads);
            }
            //show modal
            $('#modal_model_update').modal('show');
        }
        //function preview
        $('#btn_model_preview').on('click', function(ev) {
            var set = $('.group-checkable').attr("data-set");
            var link = $(set+"[type=checkbox]:checked:first").data('preview');
            window.open(link, '_blank');
        });
        //function edit
        $('#btn_model_edit').on('click', function(ev) {
            var set = $('.group-checkable').attr("data-set");
            var id = $(set+"[type=checkbox]:checked")[0].id;
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/venues',
                data: {id:id},
                success: function(data) {
                    if(data.success)
                    {
                        loadModal(data);
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
                        text: "There was an error trying to get the venue's information!<br>The request could not be sent to the server.",
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
                    title: "Saving venue's information",
                    text: "Please, wait.",
                    type: "info",
                    showConfirmButton: false
                });
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/venues/save',
                    data: $('#form_model_update').serializeArray(),
                    success: function(data) {
                        if(data.success)
                        {
                            if(typeof(data.venue) != 'undefined' && data.venue !== null)
                            {
                                swal.close();
                                loadModal(data);
                            }
                            else
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
                            text: "There was an error trying to save the venue's information!<br>The request could not be sent to the server.",
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
                var tabs = $('#form_model_update .has-error').closest('.tab-pane');
                var names = [];
                $.each(tabs,function(k, v) {
                    names.push( $('a[href="#'+v.id+'"]').html() );
                });
                swal({
                    title: "<span style='color:red;'>Error!</span>",
                    text: "The form is not valid!<br>Please check the information again in tab(s): "+names.join(', '),
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
                title: "The following venue(s) will be removed, please confirm action: ",
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
                    jQuery.ajax({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        type: 'POST',
                        url: '/admin/venues/remove',
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
                                text: "There was an error deleting the venue(s)!<br>They might have some dependences<br>or<br>the request could not be sent to the server.",
                                html: true,
                                type: "error"
                            });
                        }
                    });
                }
            });
        });
        //function load form to upload logo
        $('#btn_venues_upload_logo_url').on('click', function(ev) {
            FormImageUpload('venues.logo_url','#modal_model_update','#form_model_update [name="logo_url"]');
        });
        //function load form to upload header
        $('#btn_venues_upload_header_url').on('click', function(ev) {
            FormImageUpload('venues.header_url','#modal_model_update','#form_model_update [name="header_url"]');
        });

        //function with venue_reports  *****************************************************************************************************   VENUE REPORTS BEGIN
        //function overwrite pos fee for all sub shows
        $('#btn_report_email_weekly, #btn_report_email_accounting').on('click', function(ev) {
            var venue_id = $('#form_model_update input[name="id"]').val();
            if($(this).attr('id')=='btn_report_email_weekly')
            {
                var value = $('#form_model_update input[name="weekly_email"]').val();
                var action = 'emails';
            }
            else if($(this).attr('id')=='btn_report_email_accounting')
            {
                var value = $('#form_model_update input[name="accounting_email"]').val();
                var action = 'accounting_email';
            }
            else venue_id = false;
            $('#modal_model_update').modal('hide');
            if(venue_id)
            {
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/venues/reports',
                    data: {action:action, venue_id:venue_id, value:value},
                    success: function(data) {
                        if(data.success) {
                            swal({
                                title: "<span style='color:green;'>Saved!</span>",
                                text: data.msg,
                                html: true,
                                timer: 1500,
                                type: "success",
                                showConfirmButton: false
                            });
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
                            });
                        }
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to update the email's information!<br>The request could not be sent to the server.",
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
                    text: "There is an error. Please, contact an administrator.",
                    html: true,
                    type: "error"
                },function(){
                    $('#modal_model_update').modal('show');
                });
            }
        });
        //function with venue_reports  *****************************************************************************************************   VENUE REPORTS BEGIN

        //function with venue_stages  *****************************************************************************************************   VENUE STAGES BEGIN
        // init images
        $('#grid_venue_stages').cubeportfolio({
            layoutMode: 'grid',
            defaultFilter: '*',
            animationType: 'quicksand',
            gapHorizontal: 0,
            gapVertical: 0,
            gridAdjustment: 'responsive',
            mediaQueries: [{ width: 800, cols: 3 }, { width: 480, cols: 2 }, { width: 320, cols: 1 }],
            caption: 'fadeIn',
            displayType: 'default',
            displayTypeSpeed: 1,
            lightboxDelegate: '.cbp-lightbox',
            lightboxGallery: true,
            lightboxTitleSrc: 'data-title',
            lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',
            singlePageDelegate: '.cbp-singlePage',
            singlePageDeeplinking: true,
            singlePageStickyNavigation: true,
            singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} of {{total}}</div>'
        });
        //fn fill out images
        var fn_venue_stages = function(stage)
        {
            return  '<div class="cbp-item stage_'+stage.id+'" style="padding:5px"><div class="cbp-caption" style="width:290px;"><div class="cbp-caption-defaultWrap"><img src="'+stage.image_url+'" alt="'+stage.image_url+'"></div>'+
                    '<div class="cbp-caption-activeWrap"><div class="cbp-l-caption-alignCenter"><div class="cbp-l-caption-body">'+
                    '</div></div></div></div>'+
                    '<center><a class="cbp-l-caption-buttonLeft btn yellow uppercase edit" rel="'+stage.id+'"><i class="fa fa-edit"></i></a>'+
                    '<a class="cbp-l-caption-buttonLeft btn red uppercase delete" rel="'+stage.id+'"><i class="fa fa-remove"></i></a>'+
                    '<a href="'+stage.image_url+'" class="cbp-lightbox cbp-l-caption-buttonRight btn green uppercase" onclick="$(\'#modal_model_update\').modal(\'hide\');" data-title="'+stage.name+'<br>'+stage.description+'"><i class="fa fa-search"></i></a>'+
                    '</center><div class="cbp-l-grid-projects-title uppercase text-center">'+(stage.name.substr(0,47)+'...')+'</div>'+
                    '<div class="cbp-l-grid-projects-desc text-center">'+(stage.description.substr(0,47)+'...')+'</div>'+
                    '</div>';
        };
        //add
        $('#btn_model_stage_add').on('click', function(ev) {
            $('#form_model_venue_stages').trigger('reset');
            $('#form_model_venue_stages input[name="id"]:hidden').val('').trigger('change');
            $('#form_model_venue_stages input[name="action"]:hidden').val('1').trigger('change');
            $('#form_model_venue_stages input[name="image_url"]:hidden').val('').trigger('change');
            $('#form_model_venue_stages img[name="image_url"]').attr('src','');
            $('#modal_model_venue_stages').modal('show');
        });
        //edit
        $(document).on('click', '#grid_venue_stages a.edit', function(){
            var id = $(this).attr('rel');
            $('#form_model_venue_stages').trigger('reset');
            $('#form_model_venue_stages input[name="id"]:hidden').val(id).trigger('change');
            $('#form_model_venue_stages input[name="action"]:hidden').val('0').trigger('change');
            $('#form_model_venue_stages input[name="image_url"]:hidden').val('').trigger('change');
            $('#form_model_venue_stages img[name="image_url"]').attr('src','');
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/venues/stages',
                data: {id:id},
                success: function(data) {
                    if(data.success)
                    {
                        $('#form_model_venue_stages [name="name"]').val(data.stage.name);
                        $('#form_model_venue_stages [name="description"]').val(data.stage.description);
                        $('#form_model_venue_stages [name="image_url"]:hidden').val(data.stage.image_url);
                        $('#form_model_venue_stages img[name="image_url"]').attr('src',data.stage.image_url);
                        if(data.stage.ticket_order && data.stage.ticket_order.length)
                        {
                            $.each(data.stage.ticket_order,function(k, v) {
                                $('#form_model_venue_stages select[name="ticket_type['+parseInt(k+1)+']"]').val(v);
                            });
                        }
                        $('#modal_model_venue_stages').modal('show');
                    }
                    else
                    {
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
                        text: "There was an error trying to get the stage's information!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    },function(){
                        $('#modal_model_update').modal('show');
                    });
                }
            });
        });
        //remove
        $(document).on('click', '#grid_venue_stages a.delete', function(){
            var id = $(this).attr('rel');
            var venue_id = $('#form_model_venue_stages [name="venue_id"]:hidden').val();
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/venues/stages',
                data: {action:-1,id:id,venue_id:venue_id},
                success: function(data) {
                    if(data.success)
                    {
                        $('#grid_venue_stages .stage_'+id).remove();
                    }
                    else
                    {
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
                        text: "There was an error trying to delete the stage's information!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    },function(){
                        $('#modal_model_update').modal('show');
                    });
                }
            });
        });
        //function submit stages
        $('#submit_model_venue_stages').on('click', function(ev) {
            $('#modal_model_venue_stages').modal('hide');
            if($('#form_model_venue_stages [name="action"]').val()=='0' || ($('#form_model_venue_stages [name="action"]').val()=='1' && $('#form_model_venue_stages [name="image_url"]').attr('src')!=''))
            {
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/venues/stages',
                    data: $('#form_model_venue_stages').serializeArray(),
                    success: function(data) {
                        if(data.success)
                        {
                            //delete or update
                            if(data.action <= 0)
                            {
                                var id = $('#form_model_venue_stages [name="id"]:hidden').val();
                                $('#grid_venue_stages .stage_'+id).remove();
                            }
                            //add or update
                            if(data.action >= 0)
                            {
                                var html = fn_venue_stages(data.stage);
                                $('#grid_venue_stages').cubeportfolio('appendItems', html);
                                $('#grid_venue_stages').trigger('resize.cbp');
                            }
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
                                $('#modal_model_venue_stages').modal('show');
                            });
                        }
                    },
                    error: function(){
			$('#modal_model_update').modal('hide');
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to save the stage's information!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_update').modal('show');
                            $('#modal_model_venue_stages').modal('show');
                        });
                    }
                });
            }
            else
            {
                $('#modal_model_update').modal('hide');
                swal({
                    title: "<span style='color:red;'>Error!</span>",
                    text: "You must fill out correctly the form.",
                    html: true,
                    type: "error"
                },function(){
                    $('#modal_model_update').modal('show');
                    $('#modal_model_venue_stages').modal('show');
                });
            }
        });
        //function load form to upload image
        $('#btn_venue_upload_stages').on('click', function(ev) {
            FormImageUpload('stages.image_url','#modal_model_venue_stages','#form_model_venue_stages [name="image_url"]');
        });
        //function with venue_stages  *****************************************************************************************************   VENUE STAGES END
        ////function with venue_stage_images  *****************************************************************************************************   VENUE STAGE IMAGES BEGIN
        // init images
        $('#grid_venue_stage_images').cubeportfolio({
            layoutMode: 'grid',
            defaultFilter: '*',
            animationType: 'quicksand',
            gapHorizontal: 0,
            gapVertical: 0,
            gridAdjustment: 'responsive',
            mediaQueries: [{ width: 800, cols: 3 }, { width: 480, cols: 2 }, { width: 320, cols: 1 }],
            caption: 'fadeIn',
            displayType: 'default',
            displayTypeSpeed: 1,
            lightboxDelegate: '.cbp-lightbox',
            lightboxGallery: true,
            lightboxTitleSrc: 'data-title',
            lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',
            singlePageDelegate: '.cbp-singlePage',
            singlePageDeeplinking: true,
            singlePageStickyNavigation: true,
            singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} of {{total}}</div>'
        });
        //fn fill out images
        var fn_venue_stage_images = function(stage_images)
        {
            return  '<div class="cbp-item stage_images_'+stage_images.id+'" style="padding:5px"><div class="cbp-caption" style="width:290px;"><div class="cbp-caption-defaultWrap"><img src="'+stage_images.url+'" alt="'+stage_images.url+'"></div>'+
                    '<div class="cbp-caption-activeWrap"><div class="cbp-l-caption-alignCenter"><div class="cbp-l-caption-body">'+
                    '</div></div></div></div>'+
                    '<center><a class="cbp-l-caption-buttonLeft btn red uppercase delete" rel="'+stage_images.id+'"><i class="fa fa-remove"></i></a>'+
                    '<a href="'+stage_images.url+'" class="cbp-lightbox cbp-l-caption-buttonRight btn green uppercase" onclick="$(\'#modal_model_update\').modal(\'hide\');" data-title="'+stage_images.name+'<br>'+stage_images.ticket_type+'"><i class="fa fa-search"></i></a>'+
                    '</center><div class="cbp-l-grid-projects-title uppercase text-center">'+(stage_images.name.substr(0,47)+'...')+'</div>'+
                    '<div class="cbp-l-grid-projects-desc text-center">'+(stage_images.ticket_type.substr(0,47)+'...')+'</div>'+
                    '</div>';
        };
        //add
        $('#btn_model_stage_images_add').on('click', function(ev) {
            $('#form_model_venue_stage_images').trigger('reset');
            $('#form_model_venue_stage_images input[name="id"]:hidden').val('').trigger('change');
            $('#form_model_venue_stage_images input[name="action"]:hidden').val('1').trigger('change');
            $('#form_model_venue_stage_images input[name="url"]:hidden').val('').trigger('change');
            $('#form_model_venue_stage_images img[name="url"]').attr('src','');
            $('#modal_model_venue_stage_images').modal('show');
        });
        //remove
        $(document).on('click', '#grid_venue_stage_images a.delete', function(){
            var id = $(this).attr('rel');
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/venues/stage_images',
                data: {action:-1,id:id},
                success: function(data) {
                    if(data.success)
                    {
                        $('#grid_venue_stage_images .stage_images_'+id).remove();
                    }
                    else
                    {
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
                        text: "There was an error trying to delete the stage images's information!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    },function(){
                        $('#modal_model_update').modal('show');
                    });
                }
            });
        });
        //function submit stage_images
        $('#submit_model_venue_stage_images').on('click', function(ev) {
            $('#modal_model_venue_stage_images').modal('hide');
            if($('#form_model_venue_stage_images [name="action"]').val()=='1' && $('#form_model_venue_stage_images [name="image_url"]').attr('src')!='')
            {
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/venues/stage_images',
                    data: $('#form_model_venue_stage_images').serializeArray(),
                    success: function(data) {
                        if(data.success)
                        {
                            //delete
                            if(data.action < 0)
                            {
                                var id = $('#form_model_venue_stage_images [name="id"]:hidden').val();
                                $('#grid_venue_stage_images .stage_images_'+id).remove();
                            }
                            //add
                            if(data.action > 0)
                            {
                                var html = fn_venue_stage_images(data.stage_images);
                                $('#grid_venue_stage_images').cubeportfolio('appendItems', html);
                                $('#grid_venue_stage_images').trigger('resize.cbp');
                            }
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
                                $('#modal_model_venue_stage_images').modal('show');
                            });
                        }
                    },
                    error: function(){
			$('#modal_model_update').modal('hide');
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to save the stage images's information!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_update').modal('show');
                            $('#modal_model_venue_stage_images').modal('show');
                        });
                    }
                });
            }
            else
            {
                $('#modal_model_update').modal('hide');
                swal({
                    title: "<span style='color:red;'>Error!</span>",
                    text: "You must fill out correctly the form.",
                    html: true,
                    type: "error"
                },function(){
                    $('#modal_model_update').modal('show');
                    $('#modal_model_venue_stage_images').modal('show');
                });
            }
        });
        //function load form to upload image
        $('#btn_venue_upload_stage_images').on('click', function(ev) {
            FormImageUpload('stages.url','#modal_model_venue_stage_images','#form_model_venue_stage_images [name="url"]');
        });
        //function with venue_stage_images  *****************************************************************************************************   VENUE STAGES IMAGES END
        //function with venue_images  *****************************************************************************************************   VENUE IMAGES BEGIN
        // init images
        $('#grid_venue_images').cubeportfolio({
            layoutMode: 'grid',
            defaultFilter: '*',
            animationType: 'quicksand',
            gapHorizontal: 0,
            gapVertical: 0,
            gridAdjustment: 'responsive',
            mediaQueries: [{ width: 800, cols: 3 }, { width: 480, cols: 2 }, { width: 320, cols: 1 }],
            caption: 'fadeIn',
            displayType: 'default',
            displayTypeSpeed: 1,
            lightboxDelegate: '.cbp-lightbox',
            lightboxGallery: true,
            lightboxTitleSrc: 'data-title',
            lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',
            singlePageDelegate: '.cbp-singlePage',
            singlePageDeeplinking: true,
            singlePageStickyNavigation: true,
            singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} of {{total}}</div>'
        });
        //onclose preview show modal
        $(document).on('click', 'div.cbp-popup-close', function(){
            $('#modal_model_update').modal('show');
        });
        //fn fill out images
        var fn_venue_images = function(image)
        {
            if(!image.caption) image.caption = '';
            return  '<div class="cbp-item '+image.image_type+' image_'+image.id+'" style="padding:5px"><div class="cbp-caption" style="width:290px;"><div class="cbp-caption-defaultWrap"><img src="'+image.url+'" alt="'+image.url+'"></div>'+
                    '<div class="cbp-caption-activeWrap"><div class="cbp-l-caption-alignCenter"><div class="cbp-l-caption-body">'+
                    '</div></div></div></div>'+
                    '<center><a class="cbp-l-caption-buttonLeft btn yellow uppercase edit" rel="'+image.id+'"><i class="fa fa-edit"></i></a>'+
                    '<a class="cbp-l-caption-buttonLeft btn red uppercase delete" rel="'+image.id+'"><i class="fa fa-remove"></i></a>'+
                    '<a href="'+image.url+'" class="cbp-lightbox cbp-l-caption-buttonRight btn green uppercase" onclick="$(\'#modal_model_update\').modal(\'hide\');" data-title="'+image.image_type+'<br>'+image.caption+'"><i class="fa fa-search"></i></a>'+
                    '</center><div class="cbp-l-grid-projects-title uppercase text-center">'+image.image_type+'</div>'+
                    '<div class="cbp-l-grid-projects-desc text-center">'+(image.caption.substr(0,47)+'...')+'</div>'+
                    '</div>';
        };
        //add
        $('#btn_model_image_add').on('click', function(ev) {
            $('#form_model_venue_images').trigger('reset');
            $('#form_model_venue_images input[name="id"]:hidden').val('').trigger('change');
            $('#form_model_venue_images input[name="action"]:hidden').val('1').trigger('change');
            $('#form_model_venue_images input[name="url"]:hidden').val('').trigger('change');
            $('#form_model_venue_images img[name="url"]').attr('src','');
            $('#subform_venue_images').css('display','block');
            $('#modal_model_venue_images').modal('show');
        });
        //edit
        $(document).on('click', '#grid_venue_images a.edit', function(){
            var id = $(this).attr('rel');
            $('#form_model_venue_images').trigger('reset');
            $('#form_model_venue_images input[name="id"]:hidden').val(id).trigger('change');
            $('#form_model_venue_images input[name="action"]:hidden').val('0').trigger('change');
            $('#form_model_venue_images input[name="url"]:hidden').val('').trigger('change');
            $('#form_model_venue_images img[name="url"]').attr('src','');
            $('#subform_venue_images').css('display','none');
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/venues/images',
                data: {id:id},
                success: function(data) {
                    if(data.success)
                    {
                        $('#form_model_venue_images [name="caption"]').val(data.image.caption);
                        $('#form_model_venue_images [name="image_type"]').val(data.image.image_type);
                        $('#modal_model_venue_images').modal('show');
                    }
                    else
                    {
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
                        text: "There was an error trying to get the image's information!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    },function(){
                        $('#modal_model_update').modal('show');
                    });
                }
            });
        });
        //remove
        $(document).on('click', '#grid_venue_images a.delete', function(){
            var id = $(this).attr('rel');
            var venue_id = $('#form_model_venue_images [name="venue_id"]:hidden').val();
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/venues/images',
                data: {action:-1,id:id,venue_id:venue_id},
                success: function(data) {
                    if(data.success)
                    {
                        $('#grid_venue_images .image_'+id).remove();
                    }
                    else
                    {
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
                        text: "There was an error trying to delete the image's information!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    },function(){
                        $('#modal_model_update').modal('show');
                    });
                }
            });
        });
        //function submit images
        $('#submit_model_venue_images').on('click', function(ev) {
            $('#modal_model_venue_images').modal('hide');
            if($('#form_model_venue_images [name="action"]').val()=='0' || ($('#form_model_venue_images [name="action"]').val()=='1' && $('#form_model_venue_images [name="url"]').attr('src')!=''))
            {
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/venues/images',
                    data: $('#form_model_venue_images').serializeArray(),
                    success: function(data) {
                        if(data.success)
                        {
                            //delete or update
                            if(data.action <= 0)
                            {
                                var id = $('#form_model_venue_images [name="id"]:hidden').val();
                                $('#grid_venue_images .image_'+id).remove();
                            }
                            //add or update
                            if(data.action >= 0)
                            {
                                var html = fn_venue_images(data.image);
                                $('#grid_venue_images').cubeportfolio('appendItems', html);
                                $('#grid_venue_images').trigger('resize.cbp');
                            }
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
                                $('#modal_model_venue_images').modal('show');
                            });
                        }
                    },
                    error: function(){
			$('#modal_model_update').modal('hide');
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to save the image's information!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_update').modal('show');
                            $('#modal_model_venue_images').modal('show');
                        });
                    }
                });
            }
            else
            {
                $('#modal_model_update').modal('hide');
                swal({
                    title: "<span style='color:red;'>Error!</span>",
                    text: "You must fill out correctly the form.",
                    html: true,
                    type: "error"
                },function(){
                    $('#modal_model_update').modal('show');
                    $('#modal_model_venue_images').modal('show');
                });
            }
        });
        //function load form to upload image
        $('#btn_venue_upload_images').on('click', function(ev) {
            var type = $('#form_model_venue_images [name="image_type"]').val().toLowerCase();
            if(type=='logo')
                FormImageUpload('venues.'+type,'#modal_model_venue_images','#form_model_venue_images [name="url"]');
            else
                FormImageUpload('images.'+type,'#modal_model_venue_images','#form_model_venue_images [name="url"]');
        });
        //function with venue_images  *****************************************************************************************************   VENUE IMAGES END
        //function with venue_banners  ****************************************************************************************************   VENUE BANNERS BEGIN
        // init banners
        $('#grid_venue_banners').cubeportfolio({
            layoutMode: 'grid',
            defaultFilter: '*',
            animationType: 'quicksand',
            gapHorizontal: 0,
            gapVertical: 0,
            gridAdjustment: 'responsive',
            mediaQueries: [{ width: 800, cols: 3 }, { width: 480, cols: 2 }, { width: 320, cols: 1 }],
            caption: 'fadeIn',
            displayType: 'default',
            displayTypeSpeed: 1,
            lightboxDelegate: '.cbp-lightbox',
            lightboxGallery: true,
            lightboxTitleSrc: 'data-title',
            lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',
            singlePageDelegate: '.cbp-singlePage',
            singlePageDeeplinking: true,
            singlePageStickyNavigation: true,
            singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} of {{total}}</div>'
        });
        //fn fill out banners
        var fn_venue_banners = function(image)
        {
            if(!image.type) image.type = '';
            if(!image.url)
            {
                image.url = '';
                var link = '';
            }
            else
                var link = '<a href="'+image.url+'" target="_blank">'+(image.url.substr(0,47)+'...')+'</a>';
            return  '<div class="cbp-item banner_'+image.id+'" style="padding:5px"><div class="cbp-caption" style="width:290px;"><div class="cbp-caption-defaultWrap"><img src="'+image.file+'" alt="'+image.file+'<br>"></div>'+
                    '<div class="cbp-caption-activeWrap"><div class="cbp-l-caption-alignCenter"><div class="cbp-l-caption-body">'+
                    '</div></div></div></div>'+
                    '<center><a class="cbp-l-caption-buttonLeft btn yellow uppercase edit" rel="'+image.id+'"><i class="fa fa-edit"></i></a>'+
                    '<a class="cbp-l-caption-buttonLeft btn red uppercase delete" rel="'+image.id+'"><i class="fa fa-remove"></i></a>'+
                    '<a href="'+image.file+'" class="cbp-lightbox cbp-l-caption-buttonRight btn green uppercase" onclick="$(\'#modal_model_update\').modal(\'hide\');" data-title="'+image.type+'<br>'+image.url+'"><i class="fa fa-search"></i></a>'+
                    '</center><div class="cbp-l-grid-projects-desc uppercase text-center"><b>'+(image.type.substr(0,38)+'...')+'</b></div>'+
                    '<div class="cbp-l-grid-projects-desc text-center">'+link+'</div>'+
                    '</div>';
        };
        //add
        $('#btn_model_banner_add').on('click', function(ev) {
//            $('#form_model_venue_banners').trigger('reset');
            $('#form_model_venue_banners input[name="id"]:hidden').val('').trigger('change');
            $('#form_model_venue_banners input[name="action"]:hidden').val('1').trigger('change');
            $('#form_model_venue_banners input[name="file"]:hidden').val('').trigger('change');
            $('#form_model_venue_banners img[name="file"]').attr('src','');
            $('#subform_venue_banners').css('display','block');
            $('#modal_model_venue_banners').modal('show');
        });
        //edit
        $(document).on('click', '#grid_venue_banners a.edit', function(){
            var id = $(this).attr('rel');
            $('#form_model_venue_banners').trigger('reset');
            $('#form_model_venue_banners input[name="id"]:hidden').val(id).trigger('change');
            $('#form_model_venue_banners input[name="action"]:hidden').val('0').trigger('change');
            $('#form_model_venue_banners input[name="file"]:hidden').val('').trigger('change');
            $('#form_model_venue_banners img[name="file"]').attr('src','');
            $('#subform_venue_banners').css('display','none');
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/venues/banners',
                data: {id:id},
                success: function(data) {
                    if(data.success)
                    {
                        //banners
                        $('#form_model_venue_banners [name="url"]').val(data.banner.url);
                        if(data.banner.type && data.banner.type!='')
                        {
                            data.banner.type = data.banner.type.split(',');
                            $.each(data.banner.type,function(k, t) {
                                $('#form_model_venue_banners :checkbox[value="'+t+'"]').prop('checked',true);
                            });
                        }
                        $('#modal_model_venue_banners').modal('show');
                    }
                    else
                    {
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
                        text: "There was an error trying to get the banner's information!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    },function(){
                        $('#modal_model_update').modal('show');
                    });
                }
            });
        });
        //remove
        $(document).on('click', '#grid_venue_banners a.delete', function(){
            var id = $(this).attr('rel');
            var show_id = $('#form_model_venue_banners [name="parent_id"]:hidden').val();
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/venues/banners',
                data: {action:-1,id:id,parent_id:show_id},
                success: function(data) {
                    if(data.success)
                    {
                        $('#grid_venue_banners .banner_'+id).remove();
                    }
                    else
                    {
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
                        text: "There was an error trying to delete the banner's information!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    },function(){
                        $('#modal_model_update').modal('show');
                    });
                }
            });
        });
        //function submit banners
        $('#submit_model_venue_banners').on('click', function(ev) {
            $('#modal_model_venue_banners').modal('hide');
            if($('#form_model_venue_banners [name="action"]').val()=='0' || ($('#form_model_venue_banners [name="action"]').val()=='1' && $('#form_model_venue_banners [name="file"]').attr('src')!=''))
            {
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/venues/banners',
                    data: $('#form_model_venue_banners').serializeArray(),
                    success: function(data) {
                        if(data.success)
                        {
                            //delete or update
                            if(data.action <= 0)
                            {
                                var id = $('#form_model_venue_banners [name="id"]:hidden').val();
                                $('#grid_venue_banners .banner_'+id).remove();
                            }
                            //add or update
                            if(data.action >= 0)
                            {
                                var html = fn_venue_banners(data.banner);
                                $('#grid_venue_banners').cubeportfolio('appendItems', html);
                                $('#grid_venue_banners').trigger('resize.cbp');
                            }
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
                                $('#modal_model_venue_banners').modal('show');
                            });
                        }
                    },
                    error: function(){
			$('#modal_model_update').modal('hide');
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to save the banner's information!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_update').modal('show');
                            $('#modal_model_venue_banners').modal('show');
                        });
                    }
                });
            }
            else
            {
                $('#modal_model_update').modal('hide');
                swal({
                    title: "<span style='color:red;'>Error!</span>",
                    text: "You must fill out correctly the form.",
                    html: true,
                    type: "error"
                },function(){
                    $('#modal_model_update').modal('show');
                    $('#modal_model_venue_banners').modal('show');
                });
            }
        });
        //function load form to upload banners
        $('#btn_venue_upload_banners').on('click', function(ev) {
            FormImageUpload('banners.file','#modal_model_venue_banners','#form_model_venue_banners [name="file"]');
        });
        //function with venue_banners  ****************************************************************************************************   VENUE IMAGES END
        //function with venue_videos  *****************************************************************************************************   VENUE VIDEOS BEGIN
        // init videos
        $('#grid_venue_videos').cubeportfolio({
            layoutMode: 'grid',
            defaultFilter: '*',
            animationType: 'quicksand',
            gapHorizontal: 0,
            gapVertical: 0,
            gridAdjustment: 'responsive',
            mediaQueries: [{ width: 800, cols: 3 }, { width: 480, cols: 2 }, { width: 320, cols: 1 }],
            caption: 'fadeIn',
            displayType: 'default',
            displayTypeSpeed: 1,
            lightboxDelegate: '.cbp-lightbox',
            lightboxGallery: true,
            lightboxTitleSrc: 'data-title',
            lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',
            singlePageDelegate: '.cbp-singlePage',
            singlePageDeeplinking: true,
            singlePageStickyNavigation: true,
            singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} of {{total}}</div>'
        });
        //fn fill out videos
        var fn_venue_videos = function(video)
        {
            if(!video.description) video.description = '';
            var vid = $($.parseHTML(video.embed_code)); vid.width(310); vid.height(200);
            return  '<div class="cbp-item video_'+video.id+'" style="padding:5px;width:290px;"><div class="cbp-caption"><div class="cbp-caption-defaultWrap">'+vid.prop('outerHTML')+'</div>'+
                    '<div class="cbp-caption-activeWrap"><div class="cbp-l-caption-alignCenter"><div class="cbp-l-caption-body">'+
                    '</div></div></div></div>'+
                    '<center><a class="cbp-l-caption-buttonLeft btn yellow uppercase edit" rel="'+video.id+'"><i class="fa fa-edit"></i></a>'+
                    '<a class="cbp-l-caption-buttonRight btn red uppercase delete" rel="'+video.id+'"><i class="fa fa-remove"></i></a>'+
                    '</center><div class="cbp-l-grid-projects-title uppercase text-center">'+video.video_type+'</div>'+
                    '<div class="cbp-l-grid-projects-desc text-center">'+(video.description.substr(0,47)+'...')+'</div>'+
                    '</div>';
        };
        //add
        $('#btn_model_video_add').on('click', function(ev) {
            $('#form_model_venue_videos').trigger('reset');
            $('#form_model_venue_videos input[name="id"]:hidden').val('').trigger('change');
            $('#form_model_venue_videos input[name="action"]:hidden').val('1').trigger('change');
            $('#form_model_venue_videos input[name="file"]:hidden').val('').trigger('change');
            $('#form_model_venue_videos img[name="file"]').attr('src','');
            $('#subform_venue_videos').css('display','block');
            $('#modal_model_venue_videos').modal('show');
        });
        //edit
        $(document).on('click', '#grid_venue_videos a.edit', function(){
            var id = $(this).attr('rel');
            $('#form_model_venue_videos').trigger('reset');
            $('#form_model_venue_videos input[name="id"]:hidden').val(id).trigger('change');
            $('#form_model_venue_videos input[name="action"]:hidden').val('0').trigger('change');
            $('#subform_venue_videos').css('display','none');
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/venues/videos',
                data: {id:id},
                success: function(data) {
                    if(data.success)
                    {
                        $('#form_model_venue_videos [name="video_type"]').val(data.video.video_type);
                        $('#form_model_venue_videos [name="embed_code"]').val(data.video.embed_code);
                        $('#form_model_venue_videos [name="description"]').val(data.video.description);
                        $('#modal_model_venue_videos').modal('show');
                    }
                    else
                    {
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
                        text: "There was an error trying to get the video's information!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    },function(){
                        $('#modal_model_update').modal('show');
                    });
                }
            });
        });
        //remove
        $(document).on('click', '#grid_venue_videos a.delete', function(){
            var id = $(this).attr('rel');
            var venue_id = $('#form_model_venue_videos [name="venue_id"]:hidden').val();
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/venues/videos',
                data: {action:-1,id:id,venue_id:venue_id},
                success: function(data) {
                    if(data.success)
                    {
                        $('#grid_venue_videos .video_'+id).remove();
                    }
                    else
                    {
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
                        text: "There was an error trying to delete the video's information!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    },function(){
                        $('#modal_model_update').modal('show');
                    });
                }
            });
        });
        //function submit videos
        $('#submit_model_venue_videos').on('click', function(ev) {
            $('#modal_model_venue_videos').modal('hide');
            if($('#form_model_venue_videos [name="action"]').val()=='0' || ($('#form_model_venue_videos [name="action"]').val()=='1' && $('#form_model_venue_videos [name="file"]').attr('src')!=''))
            {
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/venues/videos',
                    data: $('#form_model_venue_videos').serializeArray(),
                    success: function(data) {
                        if(data.success)
                        {
                            //delete or update
                            if(data.action <= 0)
                            {
                                var id = $('#form_model_venue_videos [name="id"]:hidden').val();
                                $('#grid_venue_videos .video_'+id).remove();
                            }
                            //add or update
                            if(data.action >= 0)
                            {
                                var html = fn_venue_videos(data.video);
                                $('#grid_venue_videos').cubeportfolio('appendItems', html);
                                //$('#grid_venue_videos').trigger('resize.cbp');
                            }
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
                                $('#modal_model_venue_videos').modal('show');
                            });
                        }
                    },
                    error: function(){
			$('#modal_model_update').modal('hide');
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to save the video's information!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_update').modal('show');
                            $('#modal_model_venue_videos').modal('show');
                        });
                    }
                });
            }
            else
            {
                $('#modal_model_update').modal('hide');
                swal({
                    title: "<span style='color:red;'>Error!</span>",
                    text: "You must fill out correctly the form.",
                    html: true,
                    type: "error"
                },function(){
                    $('#modal_model_update').modal('show');
                    $('#modal_model_venue_videos').modal('show');
                });
            }
        });
        //function with venue_videos  *****************************************************************************************************   VENUE VIDEOS END
        //function with venue_ads  *******************************************************************************************************    VENUE ADS BEGIN

        function update_ads(items)
        {
            $('#tb_venue_ads').empty();
            var row_edit = '<td><button type="button" class="btn sbold bg-yellow edit"><i class="fa fa-edit"></i></button></td><td><button type="button" class="btn sbold bg-red delete"><i class="fa fa-remove"></i></button></td>';
            $.each(items,function(k, v) {
                var start_date = moment(v.start_date).format('M/DD/YY h:ma');
                var end_date = moment(v.end_date).format('M/DD/YY h:ma');
                var type = v.type.slice(0,1);
                var size = ' width="100px" height="100px" ';
                if(type=='H')
                    size = ' width="100px" height="50px" ';
                else if(type=='V')
                    size = ' width="50px" height="100px" ';
                $('#tb_venue_ads').append('<tr class="'+v.id+'"><td><img '+size+' src="'+v.image+'"></td><td>'+type+'</td><td>'+v.url+'</td><td>'+v.order+'</td><td>'+v.price+'</td><td>'+v.clicks+'</td><td>'+start_date+'</td><td>'+end_date+'</td>'+row_edit+'</tr>');
            });
        }
        $('#ads_date').daterangepicker({
                opens: (App.isRTL() ? 'left' : 'right'),
                format: 'YYYY-MM-DD HH:mm',
                separator: ' to ',
                startDate: moment(),
                endDate: moment().add('days', 29),
                minDate: moment()
            },
            function (start, end) {
                $('#ads_date input[name="start_date"]').val(start.format('YYYY-MM-DD HH:mm'));
                $('#ads_date input[name="end_date"]').val(end.format('YYYY-MM-DD HH:mm'));
            }
        );
        $('#form_model_venue_ads select[name="type"]').on('change',function(e){
            var type = $(this).val();
            var width = height = '200px';
            if(type=='Horizontal') {
                width = '400px';
                height = '100px';
            }
            else if(type=='Vertical') {
                width = '100px';
                height = '250px';
            }
            $('#form_model_venue_ads img[name="image"]').css('width',width).css('height',height);
        });
        $('#btn_model_ads_add').on('click', function(ev) {
            $('#form_model_venue_ads input[name="id"]:hidden').val('');
            $('#form_model_venue_ads').trigger('reset');
            $('#form_model_venue_ads [name="type"]').trigger('change');
            $('#form_model_venue_ads img[name="image"]').attr('src','');
            $('#form_model_venue_ads input[name="venue_id"]:hidden').val($('#modal_model_update input[name="id"]').val());
            $('#form_model_venue_ads input[name="action"]:hidden').val(1);
            $('#modal_model_venue_ads').modal('show');
        });
        $('#tb_venue_ads').on('click', 'button', function(e){
            var row = $(this).closest('tr');
            //edit
            if($(this).hasClass('edit'))
            {
                $('#form_model_venue_ads input[name="id"]:hidden').val('');
                $('#form_model_venue_ads').trigger('reset');
                $('#form_model_venue_ads [name="type"]').trigger('change');
                $('#form_model_venue_ads img[name="image"]').attr('src','');
                $('#form_model_venue_ads input[name="action"]:hidden').val(0);
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/venues/ads',
                    data: {ads_id:row.prop('class')},
                    success: function(data) {
                        if(data.success)
                        {
                            $('#form_model_venue_ads').trigger('reset');
                            $('#form_model_venue_ads input[name="id"]:hidden').val(data.ads.id);
                            $('#form_model_venue_ads input[name="venue_id"]:hidden').val(data.ads.venue_id);
                            //fill out ad
                            for(var key in data.ads)
                                $('#form_model_venue_ads [name="'+key+'"]').val(data.ads[key]);
                            $('#form_model_venue_ads img[name="image"]').attr('src',data.ads.image);
                            $('#modal_model_venue_ads').modal('show');
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
                            text: "There was an error trying to get the Ad's information!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_update').modal('show');
                        });
                    }
                });
            }
            //remove
            else if($(this).hasClass('delete'))
            {
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/venues/ads',
                    data: {action:-1,id:row.prop('class')},
                    success: function(data) {
                        if(data.success)
                        {
                            update_ads(data.ads);
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
                            text: "There was an error trying to remove the Ad!<br>The request could not be sent to the server.",
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
        //function submit venue_ads
        $('#submit_model_venue_ads').on('click', function(ev) {
            $('#modal_model_venue_ads').modal('hide');
            if($('#form_model_venue_ads').valid() || true)
            {
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/venues/ads',
                    data: $('#form_model_venue_ads').serializeArray(),
                    success: function(data) {
                        if(data.success)
                        {
                            update_ads(data.ads);
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
                                $('#modal_model_venue_ads').modal('show');
                            });
                        }
                    },
                    error: function(){
			$('#modal_model_update').modal('hide');
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to save the Ad's information!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_update').modal('show');
                            $('#modal_model_venue_ads').modal('show');
                        });
                    }
                });
            }
            else
            {
                $('#modal_model_update').modal('hide');
                swal({
                    title: "<span style='color:red;'>Error!</span>",
                    text: "You must fill out correctly the form",
                    html: true,
                    type: "error"
                },function(){
                    $('#modal_model_update').modal('show');
                    $('#modal_model_venue_ads').modal('show');
                });
            }
        });
        //function load form to upload image
        $('#btn_venue_upload_ads').on('click', function(ev) {
            var type = $('#form_model_venue_ads [name="type"]').val().toLowerCase();
            FormImageUpload('ads.'+type,'#modal_model_venue_ads','#form_model_venue_ads [name="image"]');
        });
        //function with venue_ads   *******************************************************************************************************   VENUE ADS END

        //init functions
        check_models();
        $('#form_model_update [name^="default_processing_fee"]').TouchSpin({ initval:0.00,min:0.00,step:0.01,decimals:2,max:1000000,prefix:'$' });
        $('#form_model_update [name^="default_percent_pfee"]').TouchSpin({ initval:0.00,min:0.00,step:0.01,decimals:2,max:100.00,postfix:'%' });
        $('#form_model_update [name^="default_fixed_commission"]').TouchSpin({ initval:0.00,min:0.00,step:0.01,decimals:2,max:1000000,prefix:'$' });
        $('#form_model_update [name^="default_percent_commission"]').TouchSpin({ initval:0.00,min:0.00,step:0.01,decimals:2,max:100.00,postfix:'%' });
        
        $('#form_model_update [name^="default_sales_taxes_percent"]').TouchSpin({ initval:0.00,min:0.00,step:0.01,decimals:2,max:100.00,postfix:'%' });

        $('#form_model_venue_ads [name="order"]').TouchSpin({ initval:1,min:1,step:1,decimals:0,max:10,prefix:'#' });
        $('#form_model_venue_ads [name="price"]').TouchSpin({ initval:0.00,min:0.00,step:0.01,decimals:2,max:1000000,prefix:'$' });
        $('#form_model_venue_ads [name="clicks"]').TouchSpin({ initval:0,min:0,step:1,decimals:0,max:1000000,prefix:'*' });

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
                name: {
                    minlength: 3,
                    maxlength: 50,
                    required: true
                },
                ticket_info: {
                    minlength: 5,
                    maxlength: 2000,
                    required: true
                },
                description: {
                    minlength: 5,
                    maxlength: 2000,
                    required: false
                },
                youtube: {
                    minlength: 5,
                    maxlength: 500,
                    required: false
                },
                facebook: {
                    minlength: 5,
                    maxlength: 500,
                    required: false
                },
                twitter: {
                    minlength: 5,
                    maxlength: 500,
                    required: false
                },
                googleplus: {
                    minlength: 5,
                    maxlength: 500,
                    required: false
                },
                instagram: {
                    minlength: 5,
                    maxlength: 500,
                    required: false
                },
                yelpbadge: {
                    minlength: 5,
                    maxlength: 500,
                    required: false
                },
                address: {
                    minlength: 5,
                    maxlength: 200,
                    required: true
                },
                city: {
                    minlength: 2,
                    maxlength: 100,
                    required: true
                },
                state: {
                    minlength: 2,
                    maxlength: 2,
                    required: true
                },
                zip: {
                    minlength: 5,
                    maxlength: 5,
                    digits: true,
                    range: [00100, 99999],
                    required: true
                },
                logo_url: {
                    //url: true,
                    required: true
                },
                header_url: {
                    //url: true,
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
