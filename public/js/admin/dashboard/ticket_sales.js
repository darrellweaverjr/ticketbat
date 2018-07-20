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
                        var t = FilterSearchHtml.init();
                        if(graph=='Yes')
                            t = t + '<hr>'+$('#ticket_sales_charts').addClass('compact').html();
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
                }/*,
                {
                    text: 'Toggle Total Details <i class="fa fa-list"></i>',
                    className: 'btn sbold purple',
                    action: function () {
                        $('#tb_summary').toggle('display');
                        $('#tb_coupon').toggle('display');
                    }
                }*/
            ];
        MainDataTableCreator.init('tb_model',[  ],10,false,buttons);
        
        //PERSONALIZED FUNCTIONS
        
        // charts totals credit
        var purchased=[],qty=[],amount=[];
        $.each($('#ticket_sales_chart_sales').data('info'),function(k, v) {
            purchased.push(v.purchased);
            qty.push(parseFloat(v.qty));
            amount.push(parseFloat(v.amount));
        });
        // charts totals debit
        var purchased_=[],qty_=[],amount_=[];
        $.each($('#ticket_sales_chart_refunds').data('info'),function(k, v) {
            purchased_.push(v.purchased);
            qty_.push(parseFloat(v.qty));
            amount_.push(parseFloat(v.amount));
        });
        // chart sales
	$('#ticket_sales_chart_sales').highcharts({
            chart : { style: { fontFamily: 'Open Sans' } },
            title: { text: 'Credits', x: -20 },
            xAxis: { categories: purchased },
            yAxis: { title: { text: 'Quantity' },
                     plotLines: [{ value: 0, width: 1, color: '#808080' }]
            },
            tooltip: { valuePrefix: ' ' },
            series: [{
                    name: 'Transactions',
                    data: qty
            }, {
                    name: 'Profit',
                    data: amount
            }]
	});
        // charts refunds
	$('#ticket_sales_chart_refunds').highcharts({
            chart : { style: { fontFamily: 'Open Sans' } },
            title: { text: 'Debits', x: -20 },
            xAxis: { categories: purchased_ },
            yAxis: { title: { text: 'Quantity' },
                     plotLines: [{ value: 0, width: 1, color: '#808080' }]
            },
            tooltip: { valuePrefix: ' ' },
            series: [{
                    name: 'Transactions',
                    data: qty_
            }, {
                    name: 'Debits',
                    data: amount_
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
