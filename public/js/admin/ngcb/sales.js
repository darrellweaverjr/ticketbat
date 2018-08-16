var TableDatatablesButtons = function () {

    var initTable = function () {
        
        var buttons = [
                {
                    text: 'Report Q <i class="fa fa-docs"></i>',
                    className: 'btn sbold grey-salsa',
                    action: function () {
                        report_quarter();
                    }
                },
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
                    download: 'open'
                },
                {
                    extend: 'csv',
                    text:      'CSV <i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'CSV',
                    className: 'btn sbold bg-green'
                }
            ];
        MainDataTableCreator.init('tb_model',[  ],10,false,buttons);
        
        //PERSONALIZED FUNCTIONS
        var report_quarter = function() { 
            var start = $('#form_model_search input[name="soldtime_start_date"]').val();
            var end = $('#form_model_search input[name="soldtime_end_date"]').val();
            //default check
            if(start==$('#modal_model_quarter :radio[value="Q1_current"]').data('start') && end==$('#modal_model_quarter :radio[value="Q1_current"]').data('end'))
                $('#modal_model_quarter :radio[value="Q1_current"]').prop('checked',true);
            else if(start==$('#modal_model_quarter :radio[value="Q2_current"]').data('start') && end==$('#modal_model_quarter :radio[value="Q2_current"]').data('end'))
                $('#modal_model_quarter :radio[value="Q2_current"]').prop('checked',true);
            else if(start==$('#modal_model_quarter :radio[value="Q3_current"]').data('start') && end==$('#modal_model_quarter :radio[value="Q3_current"]').data('end'))
                $('#modal_model_quarter :radio[value="Q3_current"]').prop('checked',true);
            else if(start==$('#modal_model_quarter :radio[value="Q4_current"]').data('start') && end==$('#modal_model_quarter :radio[value="Q4_current"]').data('end'))
                $('#modal_model_quarter :radio[value="Q4_current"]').prop('checked',true);
            else if(start==$('#modal_model_quarter :radio[value="Q1_previous"]').data('start') && end==$('#modal_model_quarter :radio[value="Q1_previous"]').data('end'))
                $('#modal_model_quarter :radio[value="Q1_previous"]').prop('checked',true);
            else if(start==$('#modal_model_quarter :radio[value="Q2_previous"]').data('start') && end==$('#modal_model_quarter :radio[value="Q2_previous"]').data('end'))
                $('#modal_model_quarter :radio[value="Q2_previous"]').prop('checked',true);
            else if(start==$('#modal_model_quarter :radio[value="Q3_previous"]').data('start') && end==$('#modal_model_quarter :radio[value="Q3_previous"]').data('end'))
                $('#modal_model_quarter :radio[value="Q3_previous"]').prop('checked',true);
            else if(start==$('#modal_model_quarter :radio[value="Q4_previous"]').data('start') && end==$('#modal_model_quarter :radio[value="Q4_previous"]').data('end'))
                $('#modal_model_quarter :radio[value="Q4_previous"]').prop('checked',true);
            else
                $('#modal_model_quarter :radio[value="Q0_custom"]').prop('checked',true);
            //show modal
            $('#modal_model_quarter').modal('show');
        };
        //function select report quarter
        $('#btn_model_quarter').on('click', function(ev) {
            var option = $('#modal_model_quarter :radio[name="report_quarter"]:checked');
            if(option.val() != '')
            {
                var start = option.data('start');
                var end = option.data('end');
                $('#form_model_search input[name="soldtime_start_date"]').val(start);
                $('#form_model_search input[name="soldtime_end_date"]').val(end);
                
                $('#form_model_search')[0].submit();
            }
            else
                $('#modal_model_quarter').modal('hide');
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
