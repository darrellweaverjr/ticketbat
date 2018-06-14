var RefundedDatatablesManaged = function () {
    
    var initTable = function () {
        
        var table = MainDataTableCreator.init('tb_model_refunded',[],10);
        
        table.on('click', 'tbody tr', function () {
            $(this).find('[name="radios"]').prop('checked',true).trigger('change');
        });

        table.on('change', 'tbody tr .radios', function () {
            $(this).parents('tr').toggleClass("active");
        });
        
        //PERSONALIZED FUNCTIONS
        //refunded date
        $('#refund_date_input').datetimepicker({
            autoclose: true,
            isRTL: App.isRTL(),
            format: "m/dd/yyyy H:ii P",   
            pickerPosition: (App.isRTL() ? "bottom-right" : "bottom-left"),
            minuteStep: 5
        });
        //function open modal edit
        $('#btn_model_edit').on('click', function(ev) {
            var id = $("#tb_model_refunded [name=radios]:checked").val();
            $('#form_model_edit').trigger('reset');
            $('#form_model_edit [name="id"]').val(id);
            $('#modal_model_edit').modal('show');
        });
        //function save
        $('#btn_model_save').on('click', function(ev) {
            $('#modal_model_edit').modal('hide');
            swal({
                title: "Editing Refund",
                text: "Please, wait.",
                type: "info",
                showConfirmButton: false
            });
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/refunds/save',
                data: $('#form_model_edit').serializeArray(),
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
                        },function(){
                            location.reload();
                        });
                    }
                    else swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_edit').modal('show');
                        });
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to save the refund information!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    },function(){
                        $('#modal_model_edit').modal('show');
                    });
                }
            });
        });
        //enable function buttons on check radio
        $('input:radio[name=radios]').change(function () {
            if($('input:radio[name=radios]:checked').length > 0)
                $('#btn_model_edit').prop('disabled',false);
            else
                $('#btn_model_edit').prop('disabled',true);
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
    RefundedDatatablesManaged.init();
});