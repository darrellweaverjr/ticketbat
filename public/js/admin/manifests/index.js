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
                [0, "desc"]
            ] // set first column as a default sort by asc
        });

        table.on('click', 'tbody tr', function () {
            $(this).find('[name="radios"]').prop('checked',true).trigger('change');
        });

        table.on('change', 'tbody tr .radios', function () {
            $(this).parents('tr').toggleClass("active");
        });

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
                "daysOfWeek": [
                    "Su",
                    "Mo",
                    "Tu",
                    "We",
                    "Th",
                    "Fr",
                    "Sa"
                ]
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
        //date_generate
        $('#date_generate').datepicker({
            autoclose: true,
            isRTL: App.isRTL(),
            format: "mm/dd/yyyy",
            maxDate: moment()
        });
        //function csv
        $('#btn_model_csv').on('click', function(ev) {
            var id = $("#tb_model [name=radios]:checked").val();
            window.open('/admin/manifests/view/csv/'+id);
        });
        //function pdf
        $('#btn_model_pdf').on('click', function(ev) {
            var id = $("#tb_model [name=radios]:checked").val();
            window.open('/admin/manifests/view/pdf/'+id);
        });
        //function resend
        $('#btn_model_resend').on('click', function(ev) {
            var id = $("#tb_model [name=radios]:checked").val();
            $('#form_model_resend').trigger('reset');
            $('#form_model_resend [name="id"]').val(id);
            $('#modal_model_resend').modal('show');
        });
        //function generate
        $('#btn_model_generate').on('click', function(ev) {
            $('#modal_generate_manifest').modal('hide');
            swal({
                title: "Generating manifest",
                text: "Please, wait.",
                type: "info",
                showConfirmButton: false
            });
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/manifests/generate',
                data: $('#form_generate_manifest').serializeArray(),
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
                    else swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_generate_manifest').modal('show');
                        });
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to generate the manifest!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    },function(){
                        $('#modal_generate_manifest').modal('show');
                    });
                }
            });
        });
        //function send
        $('#btn_model_save').on('click', function(ev) {
            $('#modal_model_resend').modal('hide');
            swal({
                title: "Re-sending email",
                text: "Please, wait.",
                type: "info",
                showConfirmButton: false
            });
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/manifests/send',
                data: $('#form_model_resend').serializeArray(),
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
                    }
                    else swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_model_resend').modal('show');
                        });
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to get the purchase information!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    },function(){
                        $('#modal_model_resend').modal('show');
                    });
                }
            });
        });
        //enable function buttons on check radio
        $('input:radio[name=radios]').change(function () {
            if($('input:radio[name=radios]:checked').length > 0)
            {
                $('#btn_model_csv').prop('disabled',false);
                $('#btn_model_pdf').prop('disabled',false);
                $('#btn_model_generate').prop('disabled',false);
                $('#btn_model_resend').prop('disabled',false);
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
