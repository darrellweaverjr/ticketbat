var PermissionsDatatablesManaged = function () {
    
    var initTable = function () {
        
        var table = MainDataTableCreator.init('tb_model_permissions',[ [0, "asc"] ],20);
        
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
        //active/desactive permissions
        $('#tb_acls .checkboxes').change(function() {   
            $('#tb_acls .checkboxes').each(function() {
                alert($(this).id);
                if($(this).is(':checked'))
                {
                    $('#tb_acls .checkboxes_'+$(this).value).prop('disabled',false);
                }
                else
                {
                    $('#tb_acls .checkboxes_'+$(this).value).prop('checked',false);
                    $('#tb_acls .checkboxes_'+$(this).value).prop('disabled',true);
                }
            });
        });  
        //check/uncheck all
        var check_models = function(){
            var set = $('#tb_model_permissions .group-checkable').attr("data-set");
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
            $('#modal_model_update_title').html('Add ACLs');
            //show modal
            $('#modal_model_update').modal('show');
        });
        //function edit
        $('#btn_model_edit').on('click', function(ev) {
            fullReset();
            if($('#modal_model_update_header').hasClass('bg-green'))
                $('#modal_model_update_header,#btn_model_save').removeClass('bg-green').addClass('bg-yellow');
            else $('#modal_model_update_header,#btn_model_save').addClass('bg-yellow');
            var set = $('#tb_model_permissions .group-checkable').attr("data-set");
            var id = $(set+"[type=checkbox]:checked")[0].id;
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/acls', 
                data: {id:id}, 
                success: function(data) {
                    if(data.success) 
                    {
                        $('#modal_model_update_title').html(data.permission.permission);
                        for(var key in data.permission)
                        {
                            var e = $('#form_model_update [name="'+key+'"]');
                            if(key==='user_type_permissions')
                            {
                                var user_type_permissions = data.permission[key];
                                for(var user_type_id in user_type_permissions)
                                {
                                    var scope = user_type_permissions[user_type_id].permission_scope;
                                    $('#form_model_update [name="user_type_permissions['+user_type_id+'][permission_scope]"]').val(scope);
                                    for(var permission_type in user_type_permissions[user_type_id].permission_type)
                                    {
                                        var type = user_type_permissions[user_type_id].permission_type;
                                        $('#form_model_update [name="user_type_permissions['+user_type_id+'][permission_type][]"]:checkbox[value='+type[permission_type]+']').prop('checked',true);
                                    }
                                }
                            }
                            else
                                e.val(data.permission[key]);
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
                        text: "There was an error trying to get the acl's information!<br>The request could not be sent to the server.",
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
                    title: "Saving acl's information",
                    text: "Please, wait.",
                    type: "info",
                    showConfirmButton: false
                });
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/acls/save', 
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
                            text: "The form is not valid!<br>Please check the information again.",
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
                title: "The following acl(s) will be removed, please confirm action: ",
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
                        url: '/admin/acls/remove', 
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
                                text: "There was an error deleting the acl(s)!<br>They might have some dependences<br>or<br>the request could not be sent to the server.",
                                html: true,
                                type: "error"
                            });
                        }
                    });
                } 
            });            
        });  
        //function user _types
        $('#btn_model_user_type').on('click', function(ev) {
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/user_types', 
                success: function(data) {
                    if(data.success)
                    {
                        swal({
                            title: "<span style='color:lightBlue;'><b>User Types</b></span>",
                            text: data.msg,
                            html: true,
                            type: "info",
                            showCancelButton: true,
                            confirmButtonClass: "btn-success",
                            confirmButtonText: "Add new",
                            cancelButtonText: "Close",
                            closeOnConfirm: false,
                            closeOnCancel: true
                        },
                        function(isConfirm) {
                            if (isConfirm) {
                                swal({
                                    title: "Enter the new user type",
                                    type: "input",
                                    showCancelButton: true,
                                    closeOnConfirm: false,
                                    inputPlaceholder: "New User Type"
                                }, function (inputUserType) {
                                    if (inputUserType === false) return false;
                                    if ($.trim(inputUserType) === "") {
                                      swal.showInputError("You need to write something!");
                                      return false;
                                    }
                                    swal({
                                        title: "Enter the description for the user type: "+inputUserType,
                                        type: "input",
                                        showCancelButton: true,
                                        closeOnConfirm: false,
                                        inputPlaceholder: "Description"
                                      }, function (inputDescription) {
                                        if (inputDescription === false) return false;
                                        if ($.trim(inputDescription) === "") {
                                          swal.showInputError("You need to write something!");
                                          return false;
                                        }
                                        jQuery.ajax({
                                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                                            type: 'POST',
                                            url: '/admin/user_types', 
                                            data: {user_type:inputUserType,description:inputDescription}, 
                                            success: function(data) {
                                                if(data.success)
                                                {
                                                    swal({
                                                        title: "<span style='color:green;'>Added!</span>",
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
                                                    text: "There was an error adding the User Type(s)!<br>The request could not be sent to the server.",
                                                    html: true,
                                                    type: "error"
                                                });
                                            }
                                        });
                                      });
                                });
                            } 
                        });
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
                        text: "There was an error getting the user types!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    });
                }
            });
        });
        //init functions
        check_models();        
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
var PermissionsFormValidation = function () {
    return {
        //main function to initiate the module
        init: function () {
            // advance validation
            var rules = {
                permission: {
                    minlength: 2,
                    maxlength: 200,
                    required: true
                },
                code: {
                    minlength: 2,
                    maxlength: 20,
                    required: true
                },
                description: {
                    minlength: 2,
                    maxlength: 1000,
                    required: true
                }
            };
            MainFormValidation.init('form_model_update',rules,{});
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    PermissionsDatatablesManaged.init();
    PermissionsFormValidation.init();
});