var TableAwardsDatatablesManaged = function () {
    
    var update_awards = function (items) {
        $('#tb_restaurant_awards').empty();
        var row_edit = '<td><button type="button" class="btn sbold bg-yellow edit"><i class="fa fa-edit"></i></button></td><td><button type="button" class="btn sbold bg-red delete"><i class="fa fa-remove"></i></button></td>';
        $.each(items,function(k, v) {
            v.posted = moment(v.posted).format('ddd, MMM D, YYYY')+'<br>'+moment(v.posted).format('h:mm A');
            //image
            if(v.image_id)
                v.image_id = '<img width="80px" height="80px" src="'+v.image_id+'"/>';
            else
                v.image_id = '-No image-';
            $('#tb_restaurant_awards').append('<tr data-id="'+v.id+'"><td>'+v.image_id+'</td><td>'+v.name+'</td><td>'+v.posted+'</td>'+row_edit+'</tr>');
        });   
    }
    
    var initTable = function () {
        
        //posted
        $('#posted_awards').datetimepicker({
            autoclose: true,
            isRTL: App.isRTL(),
            format: "yyyy-mm-dd hh:ii",
            pickerPosition: (App.isRTL() ? "bottom-right" : "bottom-left"),
            todayBtn: true,
            defaultDate:'now'
        });
        
        //on select ticket_type
        $('#btn_model_awards_add').on('click', function(ev) {
            $('#form_model_restaurant_awards').trigger('reset');
            $('#form_model_restaurant_awards input[name="id"]:hidden').val('').trigger('change');
            $('#form_model_restaurant_awards input[name="restaurants_id"]:hidden').val( $('#form_model_update [name="id"]').val() );
            $('#form_model_restaurant_awards input[name="action"]:hidden').val( 1 );
            $('#modal_model_restaurant_awards').modal('show');
        });
        
        //function submit restaurant_awards
        $('#submit_model_restaurant_awards').on('click', function(ev) {
            $('#modal_model_restaurant_awards').modal('hide');
            $('#modal_model_update').modal('hide');
            if($('#form_model_restaurant_awards').valid())
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
                    url: '/admin/restaurants/awards', 
                    data: $('#form_model_restaurant_awards').serializeArray(), 
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
                            update_awards(data.awards);
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
                                $('#modal_model_restaurant_awards').modal('show');
                            });
                        }
                    },
                    error: function(){	
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to save the award's information!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_update').modal('show');
                            $('#modal_model_restaurant_awards').modal('show');
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
                    $('#modal_model_restaurant_awards').modal('show');
                });
            }    
        });
        
        //function edit or remove
        $('#tb_restaurant_awards').on('click', 'button', function(e){
            var row = $(this).closest('tr');
            //edit
            if($(this).hasClass('edit')) 
            {
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/restaurants/awards', 
                    data: {action:0,id:row.data('id')}, 
                    success: function(data) {
                        if(data.success) 
                        {
                            $('#form_model_restaurant_awards').trigger('reset');
                            $('#form_model_restaurant_awards input[name="id"]:hidden').val(data.award.id).trigger('change');
                            //fill out 
                            for(var key in data.award)
                            {
                                var e = $('#form_model_restaurant_awards [name="'+key+'"]');
                                if(e.is('img'))
                                    e.src = data.award[key];
                                else
                                    e.val(data.award[key]);
                            }
                            //modal                         
                            $('#modal_model_restaurant_awards').modal('show');
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
                            text: "There was an error trying to get the award's information!<br>The request could not be sent to the server.",
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
                var restaurants_id = $('#form_model_restaurant_awards input[name="restaurants_id"]:hidden').val();
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/restaurants/awards', 
                    data: {action:-1,id:row.data('id'), restaurants_id:restaurants_id}, 
                    success: function(data) {
                        if(data.success) 
                        {
                            update_awards(data.awards);
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
                            text: "There was an error trying to delete the award!<br>The request could not be sent to the server.",
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
        $('#btn_restaurant_award_upload_image').on('click', function(ev) {
            FormImageUpload('restaurants.awards','#modal_model_restaurant_awards','#form_model_restaurant_awards [name="image_id"]');       
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
        update_awards: function (items) {
            update_awards(items);        
        }
    };
}();
//*****************************************************************************************
var FormAwardsValidation = function () {
    return {
        //main function to initiate the module
        init: function () {
            // advance validation
            var rules = {
                restaurant_media_id: {
                    required: true
                },
                posted: {
                    date: true,
                    required: true
                },
                description: {
                    minlength: 5,
                    maxlength: 2000,
                    required: true
                }
            };
            MainFormValidation.init('form_model_restaurant_awards',rules,{});
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    TableAwardsDatatablesManaged.init();
    FormAwardsValidation.init();
});