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
                        var graph = ($('#form_model_search input[name="replace_chart"]:checked').length)? 'Yes' : 'No';
                        var coupons = ($('#form_model_search input[name="coupon_report"]:checked').length)? 'Yes' : 'No';
                        var t = '<hr><div style="font-size:12px;float:left">Venue: '+$('#form_model_search select[name="venue"] option:selected').text()+'<br>'+
                                'Show: '+$('#form_model_search select[name="show"] option:selected').text()+'<br>'+
                                'Show Time: '+$('#form_model_search input[name="showtime_start_date"]').val()+' <-> '+$('#form_model_search input[name="showtime_end_date"]').val()+'<br>'+
                                'Sold Date: '+$('#form_model_search input[name="soldtime_start_date"]').val()+' <-> '+$('#form_model_search input[name="soldtime_end_date"]').val()+'<br>'+
                                'Payment Types: '+$('#form_model_search [name="payment_type[]"]:checked').map(function() { return $(this).attr('data-value'); } ).get().join(',')+'<br>'+
                                '</div><div style="font-size:12px;float:left">User: '+$('#form_model_search select[name="user"] option:selected').text()+'<br>'+
                                'Qty of mirror period: '+$('#form_model_search input[name="mirror_period"]').val()+'<br>'+
                                'Show Graph instead of Table: '+graph+'<br>'+
                                'Show Coupon\'s Report: '+coupons+'</div>';
                        t = t + '<hr>'+$('#tb_summary').html();
                        if(graph=='Yes')
                        {
                            t = t + '<hr>'+$('#ticket_sales_chart_sales').addClass('compact').html();
                            $(win.document.body).find('table').addClass('compact').css('display','none');
                        }
                        if(coupons=='Yes')
                            t = t + '<hr>'+$('#tb_coupon').addClass('compact').html();
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
                    extend: 'pdfHtml5',
                    text:      'PDF <i class="fa fa-file-pdf-o"></i>',
                    titleAttr: 'PDF',
                    className: 'btn sbold red',
                    orientation: 'landscape',
                    download: 'open',
                    customize: function ( doc ) {
                        var summary = '';
                        function pad(string,num=20) {
                            var char = ' ';
                            for (var i = string.length; i < num; i++)
                                string += char;
                            return string;
                        }
                        $.each($('#tb_summary th'), function( i, v ) {
                            summary += pad($(v).text());
                        });
                        summary += '\n';
                        $.each($('#tb_summary tr'), function( index, value ) {
                            $.each($(value).find('td'), function( i, v ) {
                                summary += pad($(v).text(), ((i)? 28 : 20) );
                            });
                            summary += '\n';
                        });
                        doc.content.splice( 1, 0, {
                            margin: [ 0, 0, 0, 12 ],
                            alignment: 'left',
                            text: summary
                        } );
                    }
                },
                {
                    extend: 'csv',
                    text:      'CSV <i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'CSV',
                    className: 'btn sbold bg-green',
                    customize: function (csv) {
                        var split_csv = csv.split("\n");
                        var summary = [];
                        var header = '';
                        $.each($('#tb_summary th'), function( i, v ) {
                            header += '"' + $(v).text() + '",';
                        });
                        header += '""';
                        summary.push(header);
                        $.each($('#tb_summary tr'), function( index, value ) {
                            var row = '';
                            $.each($(value).find('td'), function( i, v ) {
                                row += '"' + $(v).text() + '",';
                            });
                            row += '""';
                            summary.push(row);
                        });
                        summary.push('');
                        csv = $.merge(summary,split_csv).join("\n");
                        return csv;
                    }
                },
                {
                    text: 'Toggle Total Details <i class="fa fa-list"></i>',
                    className: 'btn sbold purple',
                    action: function () {
                        $('#tb_summary').toggle('display');
                        $('#tb_coupon').toggle('display');
                    }
                }
            ];
        var dom = "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>";
        var table = MainDataTableCreator.init('tb_model',true,[ [4, "desc"] ],10,false,dom,buttons);
        
        //PERSONALIZED FUNCTIONS
        
        //showtime_date
        $('#showtime_date_input').datetimepicker({
            autoclose: true,
            isRTL: App.isRTL(),
            format: "m/dd/yyyy H:ii P",   
            pickerPosition: (App.isRTL() ? "bottom-right" : "bottom-left"),
            minuteStep: 15
        });
        //clear showtime_date
        $('#clear_onsale_date').on('click', function(ev) {
            $('#form_model_search [name="showtime_date"]').val('');
            $('#showtime_date_input').datetimepicker('update');
        });
        
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
            $('#show_times_date').daterangepicker('update');
        });
        //sold_times_date
        $('#sold_times_date').daterangepicker({
                opens: (App.isRTL() ? 'left' : 'right'),
                timePicker: true,
                timePickerIncrement: 1,
                format: 'M/DD/YY h:mm A',
                separator: ' to '
            },
            function (start, end) {
                $('#form_model_search input[name="soldtime_start_date"]').val(start.format('M/DD/YY h:mm A'));
                $('#form_model_search input[name="soldtime_end_date"]').val(end.format('M/DD/YY h:mm A'));
            }
        );
        //clear sold_times_date
        $('#clear_sold_times_date').on('click', function(ev) {
            $('#form_model_search [name="soldtime_start_date"]').val('');
            $('#form_model_search [name="soldtime_end_date"]').val('');
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
        var graph = $('#ticket_sales_chart_sales').data('info');
        var purchased=[],qty=[],amount=[];
        $.each(graph,function(k, v) {
            purchased.push(v.purchased);
            qty.push(parseFloat(v.qty));
            amount.push(parseFloat(v.amount));
        });
        // chart sales
	$('#ticket_sales_chart_sales').highcharts({
            chart : { style: { fontFamily: 'Open Sans' } },
            title: { text: '', x: -20 },
            xAxis: { categories: purchased },
            yAxis: { title: { text: 'Quantity' },
                     plotLines: [{ value: 0, width: 1, color: '#808080' }]
            },
            tooltip: { valuePrefix: ' ' },
            series: [{
                    name: 'Sold Tickets',
                    data: qty
            }, {
                    name: 'Gross Profit',
                    data: amount
            }]
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
