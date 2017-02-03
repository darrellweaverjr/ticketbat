var TableDatatablesButtons = function () {

    var initTable = function () {
        var table = $('#tb_model');

        table.dataTable({

            // Internationalisation. For more info refer to http://datatables.net/manual/i18n
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                },
                "emptyTable": "No data available in table",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "No entries found",
                "infoFiltered": "(filtered1 from _MAX_ total entries)",
                "lengthMenu": "_MENU_ entries",
                "search": "Search:",
                "zeroRecords": "No matching records found",
                "paginate": {
                    "previous":"Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            },
            "buttons": [
                {
                    text: 'Search',
                    className: 'btn sbold grey-salsa',
                    action: function () {
                        $('#modal_model_search').modal('show');
                    }
                },
                { extend: 'print', className: 'btn default' },
                { extend: 'copy', className: 'btn default' },
                { extend: 'pdf', className: 'btn default' },
                { extend: 'csv', className: 'btn default' }
            ],
            "order": [
                [0, 'asc']
            ],
            "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
            "lengthMenu": [
                [5, 10, 15, 20, -1],
                [5, 10, 15, 20, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>"
        });
        //PERSONALIZED FUNCTIONS
        //show_times_date
        $('#show_times_date').daterangepicker({
                opens: (App.isRTL() ? 'left' : 'right'),
                format: 'YYYY-MM-DD',
                separator: ' to '
            },
            function (start, end) {
                $('#form_model_search input[name="showtime_start_date"]').val(start.format('YYYY-MM-DD'));
                $('#form_model_search input[name="showtime_end_date"]').val(end.format('YYYY-MM-DD'));
            }
        ); 
        //clear show_times_date
        $('#clear_show_times_date').on('click', function(ev) {
            $('#form_model_search [name="showtime_start_date"]').val('');
            $('#form_model_search [name="showtime_end_date"]').val('');
            $('#show_times_date').datetimepicker('update');
        }); 
        //sold_times_date
        $('#sold_times_date').daterangepicker({
                opens: (App.isRTL() ? 'left' : 'right'),
                format: 'YYYY-MM-DD',
                separator: ' to '
            },
            function (start, end) {
                $('#form_model_search input[name="soldtime_start_date"]').val(start.format('YYYY-MM-DD'));
                $('#form_model_search input[name="soldtime_end_date"]').val(end.format('YYYY-MM-DD'));
            }
        ); 
        //clear sold_times_date
        $('#clear_sold_times_date').on('click', function(ev) {
            $('#form_model_search [name="soldtime_start_date"]').val('');
            $('#form_model_search [name="soldtime_end_date"]').val('');
            $('#sold_times_date').datetimepicker('update');
        }); 
        //venue on select
        $('#form_model_search select[name="venue"]').bind('change click', function() {
            var venue = $(this).val();
            if(venue && venue != '')
            {
                $('#form_model_search select[name="show"] option[rel!="'+venue+'"]').css('display','none');
                $('#form_model_search select[name="show"] option[rel="'+venue+'"]').css('display','block');
                $('#form_model_search select[name="show"] option[value=""]').css('display','block');
            }
            else
            {
                $('#form_model_search select[name="show"] option[value!=""]').css('display','none');
            }
        });
        // charts totals
        var graph = jQuery.parseJSON($('#referral_json').html());
        var referral_url=[],show_name=[];
        $.each(graph,function(k, v) {
            referral_url.push({"url":v.referral_url,"value":v.amount});
            show_name.push({"show":v.show_name,"value":v.amount});
        });
        // chart url
	var referral_chart_tickets = AmCharts.makeChart("referral_chart_tickets", {
            "type": "pie",
            //"theme": "light",
            "fontFamily": 'Open Sans',
            "color":    '#888',
            "dataProvider": referral_url,
            "valueField": "value",
            "titleField": "url",
            "outlineAlpha": 0.4,
            "depth3D": 15,
            "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
            "angle": 30,
            "exportConfig": {
                menuItems: [{
                    icon: '/lib/3/images/export.png',
                    format: 'png'
                }]
            }
        });
        // chart show
	var referral_chart_qty = AmCharts.makeChart("referral_chart_qty", {
            "type": "pie",
            //"theme": "light",
            "fontFamily": 'Open Sans',
            "color":    '#888',
            "dataProvider": show_name,
            "valueField": "value",
            "titleField": "show",
            "outlineAlpha": 0.4,
            "depth3D": 15,
            "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
            "angle": 30,
            "exportConfig": {
                menuItems: [{
                    icon: '/lib/3/images/export.png',
                    format: 'png'
                }]
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
jQuery(document).ready(function() {
    TableDatatablesButtons.init();
});