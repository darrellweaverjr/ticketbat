var CommandsDatatablesManaged = function () {
    
    var initTable = function () {
        
        var table = MainDataTableCreator.init('tb_model_commands',false,[],10,false);
        
        table.on('click', 'tbody tr', function () {
            $(this).find('[name="radios"]').prop('checked',true).trigger('change');
        });
        
        table.on('change', 'tbody tr .radios', function () {
            $(this).parents('tr').toggleClass("active");
        });
        
        //PERSONALIZED FUNCTIONS
         
        //function run
        $('#btn_model_run').on('click', function(ev) {
            var radio = $("#tb_model_commands [name=radios]:checked");
            var parameters = {command:radio.val()};
            radio.closest('tr').find('td:nth-child(3)').find('input').each(function() {
                parameters[$(this).attr('name')] = $(this).val();
            });
            swal({
                title: "Running the command",
                text: "Please, wait.",
                type: "info",
                showConfirmButton: false
            });
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/acls/commands', 
                data: parameters, 
                success: function(data) {
                    if(data.success) 
                    {
                        swal({
                            title: "<span style='color:green;'>Send!</span>",
                            text: data.msg,
                            html: true,
                            timer: 1500,
                            type: "success",
                            showConfirmButton: false
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
                        text: "There was an error trying to run the command!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    });
                }
            });
            
        }); 
        //enable function buttons on check radio 
        $('#tb_model_commands input:radio[name=radios]').change(function () {
            if($('#tb_model_commands input:radio[name=radios]:checked').length > 0)
                $('#btn_model_run').prop('disabled',false);   
        });
        $('#tb_model_commands input:radio[name=radios]').trigger('change');
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
    CommandsDatatablesManaged.init();
});