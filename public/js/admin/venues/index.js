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
        //link to venues page
        $('#go_to_slug').on('click', function(ev) {
            var id = $('#form_model_update [name="id"]').val()
            var slug = $('#form_model_update [name="slug"]').val();
            if(id && slug)
                window.open('http://www.ticketbat.com/venue/'+slug);
        });
        //get slug on name change
        $('#form_model_update [name="name"]').bind('change',function() {
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
            $('#modal_model_update_title').html('Add Venue');
            $('a[href="#tab_model_update_stages"]').parent().css('display','none');
            $('a[href="#tab_model_update_images"]').parent().css('display','none');
            $('a[href="#tab_model_update_banners"]').parent().css('display','none');
            $('a[href="#tab_model_update_videos"]').parent().css('display','none');
            $("#form_model_update").trigger('reset');
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
            $('a[href="#tab_model_update_stages"]').parent().css('display','block');
            $('a[href="#tab_model_update_images"]').parent().css('display','block');
            $('a[href="#tab_model_update_banners"]').parent().css('display','block');
            $('a[href="#tab_model_update_videos"]').parent().css('display','block');
            $('#modal_model_update_title').html('Edit Venue');
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/venues', 
                data: {id:id}, 
                success: function(data) {
                    if(data.success) 
                    {
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
                            if(e.is('input:checkbox'))
                                $('#form_model_update .make-switch:checkbox[name="'+key+'"]').bootstrapSwitch('state', (data.venue[key])? true : false, true);
                            else
                                e.val(data.venue[key]);
                        }
                        //fill out stages
                        $('#grid_venue_stages .cbp-item').remove();
                        $('#grid_venue_stages').trigger('resize.cbp');
                        if(data.stages && data.stages.length)
                        {
                            var html = '';
                            $.each(data.stages,function(k, v) {
                                html = html + fn_venue_stages(v); 
                            });
                            $('#grid_venue_stages').cubeportfolio('appendItems', html);
                            $('#grid_venue_stages').trigger('resize.cbp');
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
                    var form_delete = $('#form_model_delete');
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
        //function with show_passwords  *****************************************************************************************************   SHOW PASSWORD BEGIN
        // init images
        $('#grid_venue_stages').cubeportfolio({
            layoutMode: 'grid',
            defaultFilter: '*',
            animationType: 'quicksand',
            gapHorizontal: 0,
            gapVertical: 0,
            gridAdjustment: 'responsive',
            mediaQueries: [{ width: 800, cols: 3 }, { width: 480, cols: 2 }, { width: 320, cols: 1 }],
            caption: 'overlayBottomReveal',
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
            return  '<div class="cbp-item stage_'+stage.id+'" style="padding:5px"><div class="cbp-caption" style="width:290px;"><div class="cbp-caption-defaultWrap"><img src="'+stage.image_url+'" alt=""></div>'+
                    '<div class="cbp-caption-activeWrap"><div class="cbp-l-caption-alignCenter"><div class="cbp-l-caption-body">'+
                    '<a class="cbp-l-caption-buttonLeft btn yellow uppercase edit" rel="'+stage.id+'"><i class="fa fa-edit"></i></a>'+
                    '<a class="cbp-l-caption-buttonLeft btn red uppercase delete" rel="'+stage.id+'"><i class="fa fa-remove"></i></a>'+
                    '<a href="'+stage.image_url+'" class="cbp-lightbox cbp-l-caption-buttonRight btn green uppercase" onclick="$(\'#modal_model_update\').modal(\'hide\');" data-title="'+stage.name+'<br>'+stage.description+'"><i class="fa fa-search"></i></a>'+
                    '</div></div></div></div>'+
                    '<div class="cbp-l-grid-projects-title uppercase text-center">'+(stage.name.substr(0,47)+'...')+'</div>'+
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
            //$('#subform_venue_stages').css('display','block');
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
            //$('#subform_venue_stages').css('display','none');
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
        //function with venue_stages  *****************************************************************************************************   VENUE PASSWORD END
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
            caption: 'overlayBottomReveal',
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
            return  '<div class="cbp-item '+image.image_type+' image_'+image.id+'" style="padding:5px"><div class="cbp-caption" style="width:290px;"><div class="cbp-caption-defaultWrap"><img src="'+image.url+'" alt=""></div>'+
                    '<div class="cbp-caption-activeWrap"><div class="cbp-l-caption-alignCenter"><div class="cbp-l-caption-body">'+
                    '<a class="cbp-l-caption-buttonLeft btn yellow uppercase edit" rel="'+image.id+'"><i class="fa fa-edit"></i></a>'+
                    '<a class="cbp-l-caption-buttonLeft btn red uppercase delete" rel="'+image.id+'"><i class="fa fa-remove"></i></a>'+
                    '<a href="'+image.url+'" class="cbp-lightbox cbp-l-caption-buttonRight btn green uppercase" onclick="$(\'#modal_model_update\').modal(\'hide\');" data-title="'+image.image_type+'<br>'+image.caption+'"><i class="fa fa-search"></i></a>'+
                    '</div></div></div></div>'+
                    '<div class="cbp-l-grid-projects-title uppercase text-center">'+image.image_type+'</div>'+
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
            caption: 'overlayBottomReveal',
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
            return  '<div class="cbp-item banner_'+image.id+'" style="padding:5px"><div class="cbp-caption" style="width:290px;"><div class="cbp-caption-defaultWrap"><img src="'+image.file+'" alt=""></div>'+
                    '<div class="cbp-caption-activeWrap"><div class="cbp-l-caption-alignCenter"><div class="cbp-l-caption-body">'+
                    '<a class="cbp-l-caption-buttonLeft btn yellow uppercase edit" rel="'+image.id+'"><i class="fa fa-edit"></i></a>'+
                    '<a class="cbp-l-caption-buttonLeft btn red uppercase delete" rel="'+image.id+'"><i class="fa fa-remove"></i></a>'+
                    '<a href="'+image.file+'" class="cbp-lightbox cbp-l-caption-buttonRight btn green uppercase" onclick="$(\'#modal_model_update\').modal(\'hide\');" data-title="'+image.type+'<br>'+image.url+'"><i class="fa fa-search"></i></a>'+
                    '</div></div></div></div>'+
                    '<div class="cbp-l-grid-projects-desc uppercase text-center"><b>'+(image.type.substr(0,38)+'...')+'</b></div>'+
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
            caption: 'overlayBottomReveal',
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
                    '<a class="cbp-l-caption-buttonLeft btn yellow uppercase edit" rel="'+video.id+'"><i class="fa fa-edit"></i></a>'+
                    '<a class="cbp-l-caption-buttonLeft btn red uppercase delete" rel="'+video.id+'"><i class="fa fa-remove"></i></a>'+
                    '</div></div></div></div>'+
                    '<div class="cbp-l-grid-projects-title uppercase text-center">'+video.video_type+'</div>'+
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
        //function with venue_videos  *****************************************************************************************************   SHOW VIDEOS END
       
        //init functions
        check_models(); 
        $('#form_model_update [name="default_processing_fee"]').TouchSpin({ initval:0.00,min:0.00,step:0.5,decimals:2,max:1000000,prefix:'$' });
        $('#form_model_update [name="default_percent_pfee"]').TouchSpin({ initval:0.00,min:0.00,step:0.5,decimals:2,max:100.00,postfix:'%' });
        $('#form_model_update [name="default_fixed_commission"]').TouchSpin({ initval:0.00,min:0.00,step:0.5,decimals:2,max:1000000,prefix:'$' });
        $('#form_model_update [name="default_percent_commission"]').TouchSpin({ initval:0.00,min:0.00,step:0.5,decimals:2,max:100.00,postfix:'%' });
        
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
                        minlength: 1,
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
                        range: [10000, 99999],
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
    TableDatatablesManaged.init();
    FormValidation.init();
});