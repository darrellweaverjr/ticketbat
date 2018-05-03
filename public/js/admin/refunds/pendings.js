var TableDatatablesManaged = function () {
    
    var initTable = function () {
        
        var table = MainDataTableCreator.init('tb_model',true,[ [0, "desc"] ],10,false);
        
        table.on('click', 'tbody tr', function () {
            $(this).find('[name="radios"]').prop('checked',true).trigger('change');
        });
        
        table.on('change', 'tbody tr .radios', function () {
            $(this).parents('tr').toggleClass("active");
        });
        
        //PERSONALIZED FUNCTIONS
        //function resend
        $('#btn_model_refund').on('click', function(ev) {
            var id = $("#tb_model [name=radios]:checked").val();
            var skip = $("#tb_model [name=radios]:checked").data('skip');
            $('#form_model_refund').trigger('reset');
            $('#form_model_refund [name="id"]').val(id);
            if(skip>0)
            {
                $('#form_model_refund input:radio[name="type"]:last').attr('checked', true);
                $('#credit_return').addClass('hidden');
            }
            else
            {
                $('#credit_return').removeClass('hidden');
                $('#form_model_refund input:radio[name="type"]:first').attr('checked', true);
            }
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
                            title: "<span style='color:green;'>Saved!</span>",
                            text: data.msg,
                            html: true,
                            type: "success",
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
        //enable function buttons on check radio 
        $('input:radio[name=radios]').change(function () {
            if($('input:radio[name=radios]:checked').length > 0)
            {
                $('#btn_model_refund').prop('disabled',false);
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
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    TableDatatablesManaged.init();
});