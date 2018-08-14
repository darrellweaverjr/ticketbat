var TableDatatablesManaged = function () {
    
    var initTable = function () {
        
        MainDataTableCreator.init('tb_model',[ [0, "desc"] ],10);
        
        //PERSONALIZED FUNCTIONS
        //start_end_date
        $('#start_end_date').daterangepicker({
            "ranges": {
                'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                    'Last 7 Days': [moment().subtract('days', 6), moment()],
                    'Last 30 Days': [moment().subtract('days', 29), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
            },
            "locale": {
                "format": "MM/DD/YYYY",
                "separator": " - ",
                "applyLabel": "Apply",
                "cancelLabel": "Cancel",
                "fromLabel": "From",
                "toLabel": "To",
                "customRangeLabel": "Custom",
                "daysOfWeek": [ "Su", "Mo", "Tu", "We", "Th", "Fr", "Sa" ]
            },
            startDate: moment().subtract('days', 29),
            endDate: moment(),
            opens: (App.isRTL() ? 'right' : 'left')
        }, function(start, end, label) {
                $('#form_model_search [name="start_date"]').val(start.format('YYYY-MM-DD'));
                $('#form_model_search [name="end_date"]').val(end.format('YYYY-MM-DD'));
                $('#start_end_date span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                $( "#form_model_search" ).submit();
        });
        $('#start_end_date span').html(moment($('#form_model_search [name="start_date"]').val()).format('MMMM D, YYYY') + ' - ' + moment($('#form_model_search [name="end_date"]').val()).format('MMMM D, YYYY'));
        $('#start_end_date').show(); 
        
        //reset all selects
        function reset_purchase_status()
        {
            $.each($('#tb_model td:nth-child(5)'),function(k, v) {
                $(v).html('<center>'+$(v).data('status')+'</center>');
            });
        }
        $('#tb_model td:not(:nth-child(5))').click(function() {
            reset_purchase_status();
        })

        //create editable status for purchase
        $('#tb_model td:nth-child(5)').click(function() {
            var status = $(this);
            var id = status.closest('tr').data('id');
            reset_purchase_status();
            var select = '<select data-id="'+id+'" class="form-control" name="status">';
            $.each($('#tb_model').data('status'),function(k, v) {
                if(v == status.data('status'))
                    select+= '<option selected value="'+k+'">'+v+'</option>';
                else
                    select+= '<option value="'+k+'">'+v+'</option>';
            });
            select+= '</select>';
            status.html(select);
        });
        
        //function on status select
        $(document).on('change', '#tb_model select[name="status"]', function(ev){
            var id = $(this).data('id');
            var old_status = $(this).parent('td').data('status');
            var status = $(this).val();
            if(old_status != status)
            {
                swal({
                    title: "Changing contact's status",
                    text: "Please, wait.",
                    type: "info",
                    showConfirmButton: false
                });
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/admin/contacts/save',
                    data: {id:id,status:status},
                    success: function(data) {
                        if(data.success)
                        {
                            $('#tb_model select[name="status"]').parent('td').data('status',status);
                            $('#tb_model select[name="status"]').val(  status  );
                            swal({
                                title: "<span style='color:green;'>Updated!</span>",
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
                            },function(){
                                $('#tb_model select[name="status"]').val(  $('#tb_model select[name="status"]').parent('td').data('status')  );
                            });
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to set the status!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#tb_model select[name="status"]').val(  $('#tb_model select[name="status"]').parent('td').data('status')  );
                        });
                    }
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
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    TableDatatablesManaged.init();
});