var PendingDatatablesManaged = function () {
    
    var initTable = function () {
        
        var table = MainDataTableCreator.init('tb_model_pendings',[ [0, "desc"] ],10);
        
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
        //check/uncheck all
        var check_models = function(){
            var set = $('.group-checkable').attr("data-set");
            var checked = $(set+"[type=checkbox]:checked").length;
            if(checked >= 1)
                $('#btn_model_refund').prop("disabled",false);
            else
                $('#btn_model_refund').prop("disabled",true);
        }
        
        //PERSONALIZED FUNCTIONS
        //function resend
        $('#btn_model_refund').on('click', function(ev) {
            var ids = [];
            var set = $('.group-checkable').attr("data-set");
            var checked = $(set+"[type=checkbox]:checked");
            jQuery(checked).each(function (key, item) {
                ids.push(item.id);
            });
            $('#form_model_refund [name="id"]').val(ids.join('-'));
            $('#modal_model_refund').modal('show');
        }); 
        //function send
        $('#btn_model_save').on('click', function(ev) {
            $('#modal_model_refund').modal('hide');
            swal({
                title: "Refunding purchase(s)",
                text: "Please, wait.",
                type: "info",
                showConfirmButton: false
            });
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/refunds/save', 
                data: $('#form_model_refund').serializeArray(), 
                success: function(data) {
                    if(data.success) 
                    {
                        swal({
                            title: "<span style='color:orange;'>Process!</span>",
                            text: data.msg,
                            html: true,
                            type: "warning",
                            showConfirmButton: true
                        },function(){
                            location.reload();
                        });
                    }
                    else swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error",
                            showConfirmButton: true
                        },function(){
                            location.reload();
                        });
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to get the purchase information!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    },function(){
                        $('#modal_model_refund').modal('show');
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
jQuery(document).ready(function() {
    PendingDatatablesManaged.init();
});