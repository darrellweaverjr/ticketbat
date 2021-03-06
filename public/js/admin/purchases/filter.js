var FilterSearchManaged = function () {

    var initFilter = function () {
        
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
                //timePicker: true,
                //timePickerIncrement: 1,
                format: 'MM/DD/YYYY',
                separator: ' to '
            },
            function (start, end) {
                $('#form_model_search input[name="soldtime_start_date"]').val(start.format('MM/DD/YYYY'));
                $('#form_model_search input[name="soldtime_end_date"]').val(end.format('MM/DD/YYYY'));
            }
        );
        //clear sold_times_date
        $('#clear_sold_times_date').on('click', function(ev) {
            $('#form_model_search [name="soldtime_start_date"]').val('');
            $('#form_model_search [name="soldtime_end_date"]').val('');
        });
        //search venue on select
        $('#form_model_search select[name="venue"]').bind('change', function() {
            var venue_id = $(this).val();
            $('#form_model_search select[name="show"]').html('<option selected value="">All</option>');
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/purchases/filter',
                data: {venue_id:venue_id},
                success: function(data) {
                    if(data.success)
                    {
                        $.each(data.values,function(k, v) {
                            $('#form_model_search select[name="show"]').append('<option value="'+v.id+'">'+v.name+'</option>');
                        });
                    }
                }
            });
        });
        //search show on select
        $('#form_model_search select[name="show"]').bind('change', function() {
            var show_id = $(this).val();
            $('#form_model_search select[name="ticket"]').html('<option selected value="">All</option>');
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/purchases/filter',
                data: {show_id:show_id},
                success: function(data) {
                    if(data.success)
                    {
                        $.each(data.values,function(k, v) {
                            $('#form_model_search select[name="ticket"]').append('<option value="'+v.id+'">'+v.name+'</option>');
                        });
                    }
                }
            });
        });
        //function autoshow modal search
        if(parseInt($('#modal_model_search').data('modal')) > 0)
            $('#modal_model_search').modal('show');
    }
    return {
        //main function to initiate the module
        init: function () {
            if (!jQuery().dataTable) {
                return;
            }
            initFilter();
        }
    };
}();
//*****************************************************************************************
var FilterSearchHtml = function () {
    return {
        //main function to initiate the module
        init: function () {
            //function show the filter code to merge into multiples report
            var graph = ($('#form_model_search input[name="replace_chart"]:checked').length)? 'Yes' : 'No';
            var coupons = ($('#form_model_search input[name="coupon_report"]:checked').length)? 'Yes' : 'No';
            var f_venue = $('#form_model_search select[name="venue"] option:selected').text();
            var f_show = $('#form_model_search select[name="show"] option:selected').text();
            var f_st = $('#form_model_search input[name="showtime_start_date"]').val()+' <-> '+$('#form_model_search input[name="showtime_end_date"]').val();
            var f_sd = $('#form_model_search input[name="soldtime_start_date"]').val()+' <-> '+$('#form_model_search input[name="soldtime_end_date"]').val();
            var f_paymt = $('#form_model_search [name="payment_type[]"]:checked').map(function() { return $(this).attr('data-value'); } ).get().join(',');
            var f_usr = $('#form_model_search select[name="user"] option:selected').text();
            var f_mirr = $('#form_model_search input[name="mirror_period"]').val();
            var t = '<hr><div style="font-size:12px;float:left">Venue: '+f_venue+'<br>'+
                    'Show: '+f_show+'<br>'+
                    'Show Time: '+f_st+'<br>'+
                    'Sold Date: '+f_sd+'<br>'+
                    'Payment Types: '+f_paymt+'<br>'+
                    '</div><div style="font-size:12px;float:left">User: '+f_usr+'<br>'+
                    'Qty of mirror period: '+f_mirr+'<br>'+
                    'Show Graph instead of Table: '+graph+'<br>'+
                    'Show Coupon\'s Report: '+coupons+'</div>';
            if($('#totals').length)
            {
                t = t + '<hr><table width="100%"><thead>';
                $.each($('#totals').clone(),function(k, v) {
                    t = t + '<th valign="top" style="text-align:right" width="16.5%">'+v.innerHTML+'</th>';
                });
                t = t + '</thead></table>';
            }            
            return t;
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    FilterSearchManaged.init();
});
