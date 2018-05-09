var TableDatatablesButtons = function () {

    var initTable = function () {
        
        var buttons = [
                {
                    text: 'Filter <i class="fa fa-filter"></i>',
                    className: 'btn sbold grey-salsa',
                    action: function () {
                        $('#modal_model_search').modal('show');
                    }
                },
                {
                    extend: 'print',
                    text:      'Print <i class="fa fa-print"></i>',
                    titleAttr: 'Print',
                    className: 'btn sbold yellow',
                    orientation: 'landscape',
                    customize: function ( win ) {
                        var t = '<hr><div style="font-size:14px">Venue: '+$('#form_model_search select[name="venue"] option:selected').text()+'<br>'+
                                'Show: '+$('#form_model_search select[name="show"] option:selected').text()+'<br>'+
                                'Show Time: '+$('#form_model_search input[name="showtime_start_date"]').val()+' <-> '+$('#form_model_search input[name="showtime_end_date"]').val()+'<br>'+
                                'Sold Date: '+$('#form_model_search input[name="soldtime_start_date"]').val()+' <-> '+$('#form_model_search input[name="soldtime_end_date"]').val()+'</div>';
                        t = t + '<hr><table width="100%"><thead><tr>';
                        $.each($('#totals .details').clone(),function(k, v) {
                            t = t + '<th valign="top" style="text-align:right" width="16.5%">'+v.innerHTML+'</th>';
                        });
                        t = t + '</tr></thead></table><hr>';
                        $(win.document.body).find('h1').append(t);
                        $(win.document.body).find('table').addClass('compact').css('font-size','9pt');
                    }
                },
                {
                    extend: 'copy',
                    text:      'Copy <i class="fa fa-files-o"></i>',
                    titleAttr: 'Copy',
                    className: 'btn sbold blue'
                },
                {
                    extend: 'pdf',
                    text:      'PDF <i class="fa fa-file-pdf-o"></i>',
                    titleAttr: 'PDF',
                    className: 'btn sbold red',
                    orientation: 'landscape'
                },
                {
                    extend: 'csv',
                    text:      'CSV <i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'CSV',
                    className: 'btn sbold bg-green'
                }
            ];
        MainDataTableCreator.init('tb_model',[ [2, "asc"] ],10,false,buttons);
        
        //PERSONALIZED FUNCTIONS
        //show_times_date
        $('#show_times_date').daterangepicker({
                opens: (App.isRTL() ? 'left' : 'right'),
                format: 'M/DD/YYYY',
                separator: ' to '
            },
            function (start, end) {
                $('#form_model_search input[name="showtime_start_date"]').val(start.format('M/DD/YYYY'));
                $('#form_model_search input[name="showtime_end_date"]').val(end.format('M/DD/YYYY'));
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
                format: 'M/DD/YYYY',
                separator: ' to '
            },
            function (start, end) {
                $('#form_model_search input[name="soldtime_start_date"]').val(start.format('M/DD/YYYY'));
                $('#form_model_search input[name="soldtime_end_date"]').val(end.format('M/DD/YYYY'));
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
            $('#form_model_search select[name="show"]').html('<option selected value="">All</option>');
            var shows = $('#form_model_search select[name="show"]').data('content');
            if(shows)
            {
                $.each(shows,function(k, v) {
                    if(v.venue_id == venue)
                        $('#form_model_search select[name="show"]').append('<option value="'+v.id+'">'+v.name+'</option>');
                });
            }
        });
        // charts totals
        var graph_channel = $('#chart_channel').data('info');
        var graph_show = $('#chart_show').data('info');
        var channels=[],shows=[];
        $.each(graph_channel,function(k, v) {
            channels.push({"channel":v.channel,"value":v.amount});
        });
        $.each(graph_show,function(k, v) {
            shows.push({"show":v.show_name,"value":v.amount});
        });
        // chart channels
	AmCharts.makeChart("chart_channel", {
            "type": "pie",
            "fontFamily": 'Open Sans',
            "color":    '#888',
            "dataProvider": channels,
            "valueField": "value",
            "titleField": "channel",
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
	AmCharts.makeChart("chart_show", {
            "type": "pie",
            "fontFamily": 'Open Sans',
            "color":    '#888',
            "dataProvider": shows,
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
