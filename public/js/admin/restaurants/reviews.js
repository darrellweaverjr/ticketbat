var TableReviewsDatatablesManaged = function () {
    
    var update_reviews = function (items) {
        $('#tb_restaurant_reviews').empty();
        var row_edit = '<td><button type="button" class="btn sbold bg-yellow edit"><i class="fa fa-edit"></i></button></td><td><button type="button" class="btn sbold bg-red delete"><i class="fa fa-remove"></i></button></td>';
        $.each(items,function(k, v) {
            v.posted = moment(v.posted).format('ddd, MMM D, YYYY')+'<br>'+moment(v.posted).format('h:mm A');
            //image
            if(v.image_id)
                v.image_id = '<img width="80px" height="80px" src="'+v.image_id+'"/>';
            else
                v.image_id = '-No image-';
            $('#tb_restaurant_reviews').append('<tr data-id="'+v.id+'"><td>'+v.image_id+'</td><td>'+v.title+'</td><td>'+v.notes+'</td><td>'+v.posted+'</td>'+row_edit+'</tr>');
        });   
    }
    
    var initTable = function () {
        
        //posted
        $('#posted_reviews').datetimepicker({
            autoclose: true,
            isRTL: App.isRTL(),
            format: "yyyy-mm-dd hh:ii",
            pickerPosition: (App.isRTL() ? "bottom-right" : "bottom-left"),
            todayBtn: true,
            defaultDate:'now'
        });
        
        //on select ticket_type
        $('#btn_model_reviews_add').on('click', function(ev) {
            $('#form_model_restaurant_reviews').trigger('reset');
            $('#form_model_restaurant_reviews input[name="id"]:hidden').val('').trigger('change');
            $('#form_model_restaurant_reviews input[name="restaurants_id"]:hidden').val( $('#form_model_update [name="id"]').val() );
            $('#form_model_restaurant_reviews input[name="action"]:hidden').val( 1 );
            $('#modal_model_restaurant_reviews').modal('show');
        });
        
        //function submit restaurant_reviews
        $('#submit_model_restaurant_reviews').on('click', function(ev) {
            $('#modal_model_restaurant_reviews').modal('hide');
            $('#modal_model_update').modal('hide');
            if($('#form_model_restaurant_reviews').valid())
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
                    url: '/admin/restaurants/reviews', 
                    data: $('#form_model_restaurant_reviews').serializeArray(), 
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
                            update_reviews(data.reviews);
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
                                $('#modal_model_restaurant_reviews').modal('show');
                            });
                        }
                    },
                    error: function(){	
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to save the review's information!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_update').modal('show');
                            $('#modal_model_restaurant_reviews').modal('show');
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
                    $('#modal_model_restaurant_reviews').modal('show');
                });
            }    
        });
        
        //function edit or remove
        $('#tb_restaurant_reviews').on('click', 'button', function(e){
            var row = $(this).closest('tr');
            //edit
            if($(this).hasClass('edit')) 
            {
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/restaurants/reviews', 
                    data: {action:0,id:row.data('id')}, 
                    success: function(data) {
                        if(data.success) 
                        {
                            $('#form_model_restaurant_reviews').trigger('reset');
                            $('#form_model_restaurant_reviews input[name="id"]:hidden').val(data.review.id).trigger('change');
                            //fill out 
                            for(var key in data.review)
                                $('#form_model_restaurant_reviews [name="'+key+'"]').val(data.review[key]);
                            //modal                         
                            $('#modal_model_restaurant_reviews').modal('show');
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
                var restaurants_id = $('#form_model_restaurant_reviews input[name="restaurants_id"]:hidden').val();
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/restaurants/reviews', 
                    data: {action:-1,id:row.data('id'), restaurants_id:restaurants_id}, 
                    success: function(data) {
                        if(data.success) 
                        {
                            update_reviews(data.reviews);
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
            FormImageUpload('restaurants.reviews','#modal_model_restaurant_reviews','#form_model_restaurant_reviews [name="image_id"]');       
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
        update_reviews: function (items) {
            update_reviews(items);        
        }
    };
}();
//*****************************************************************************************
var FormReviewsValidation = function () {
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
                title: {
                    minlength: 3,
                    maxlength: 100,
                    required: true
                },
                link: {
                    url: true,
                    required: true
                },
                notes: {
                    minlength: 3,
                    maxlength: 45,
                    required: false
                }
            };
            MainFormValidation.init('form_model_restaurant_reviews',rules,{});
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    TableReviewsDatatablesManaged.init();
    FormReviewsValidation.init();
});