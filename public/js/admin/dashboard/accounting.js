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
        MainDataTableCreator.init('tb_model',[],10,false,buttons);
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
