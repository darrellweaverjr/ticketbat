var TableCommentsDatatablesManaged = function () {
    
    var update_comments = function (items) {
        $('#tb_restaurant_comments').empty();
        $.each(items,function(k, v) {
            var check_row = '<td><label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input type="checkbox" class="checkboxes" id="'+v.id+'" /><span></span></label></td>';
            var posted_row = '<td><b>Name: </b>'+v.name;
            var rating_row = ' <b>Rating: </b>'+v.rating+'/5 <b>Posted: </b>'+v.posted;
            var review_row = '<br><i><small>" '+v.comment+' "</small></i></td>';
            if(v.enabled>0)
                var status_row = '<td><span class="label label-sm label-success"><b>Enabled<b></span></td>';
            else
                var status_row = '<td><span class="label label-sm label-danger"><b>Disabled<b></span></td>';
            $('#tb_restaurant_comments').append('<tr>'+check_row+posted_row+rating_row+review_row+status_row+'</tr>');                                                              
        });
    }
    
    var initTable = function () {
                
        //function approved or deny elements
        $('#btn_model_comments_enable, #btn_model_comments_disable').on('click', function(ev) {
            $('#modal_model_update').modal('hide');
            swal({
                title: "Updating comment's information",
                text: "Please, wait.",
                type: "info",
                showConfirmButton: false
            });
            var status = $(this).data('status');
            var restaurants_id = $('#form_model_update input[name="id"]').val();
            var ids = [];
            var checked = $('#tb_restaurant_comments input[type=checkbox]:checked');
            jQuery(checked).each(function (key, item) {
                ids.push(item.id);
            }); 
            if(ids.length>0)
            {
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/restaurants/comments', 
                    data: {id:ids, status:status, restaurants_id:restaurants_id}, 
                    success: function(data) {
                        if(data.success) 
                        {                            
                            swal({
                                title: "<span style='color:green;'>Updated!</span>",
                                text: data.msg,
                                html: true,
                                timer: 1500,
                                type: "success",
                                showConfirmButton: false
                            });
                            $('#modal_model_update').modal('show');
                            update_comments(data.comments);
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
                            text: "There was an error trying to update the comment's information!<br>The request could not be sent to the server.",
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
                    text: "You must select at least one comment to update.",
                    html: true,
                    type: "error"
                },function(){
                    $('#modal_model_update').modal('show');
                });
            }    
        });
        //function refresh show_reviews
        $('#btn_model_comments_refresh').on('click', function(ev) {
            $('#tb_restaurant_comments').empty();
            var restaurants_id = $('#form_model_update input[name="id"]').val();
            if(restaurants_id)
            {
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/restaurants/comments', 
                    data: {restaurants_id:restaurants_id}, 
                    success: function(data) {
                        if(data.success) 
                        {                            
                            update_comments(data.comments);
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
                            text: "There was an error trying to load the comment's information!<br>The request could not be sent to the server.",
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
                    text: "There is an error. Please, contact an administrator.",
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
        update_comments: function (items) {
            update_comments(items);        
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    TableCommentsDatatablesManaged.init();
});