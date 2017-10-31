var PortfolioManaged = function () {
    
    var initPortfolio = function () {
        //grid shows
        $('#myShows').cubeportfolio({
            layoutMode: 'grid',
            defaultFilter: '*',
            animationType: 'fadeOut', // quicksand
            gapHorizontal: 0,
            gapVertical: 0,
            gridAdjustment: 'responsive', 
            mediaQueries: [{ width: 1440, cols: 5 },{ width: 1024, cols: 4 },{ width: 800, cols: 3 }, { width: 480, cols: 2 }, { width: 320, cols: 1 }],
            caption: 'overlayBottomAlong', 
            displayType: 'default', 
            displayTypeSpeed: 1
        });
        //link to shows details
        $('#myShows div.cbp-item').bind('click',function (e) {
            if($(e.target).is('a') || $(e.target).is('i'))
               return;
            window.location = $(this).data('href');
        });
        //filter_date
        $('#filter_date').daterangepicker({
            "ranges": {
                'All': [moment(), moment().add('year',1).endOf('month')],
                'Today': [moment(), moment()],
                'Tomorrow': [moment().add('days',1), moment().add('days', 1)],
                'Next 7 Days': [moment(), moment().add('days',6)],
                'Next 30 Days': [moment(), moment().add('days',29)],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Next Month': [moment().add('month',1).startOf('month'), moment().add('month', 1).endOf('month')]
            },
            "locale": {
                "format": "YYYY-MM-DD",
                "separator": " - ",
                "applyLabel": "Apply",
                "cancelLabel": "Cancel",
                "fromLabel": "From",
                "toLabel": "To",
                "customRangeLabel": "Custom",                    
                "firstDay": 1
            },
            startDate: moment(),
            endDate: moment(),
            opens: (App.isRTL() ? 'right' : 'left'),
        }, function(start, end, label) {
                $('input[name="filter_start_date"]').val(start.format('YYYY-MM-DD'));
                $('input[name="filter_end_date"]').val(end.format('YYYY-MM-DD'));
                $('#filter_date span').html(label +'<br><small>'+start.format('M/D/YYYY')+' - '+end.format('M/D/YYYY')+'</small>');//MMM D, YYYY
                filter_search();
        }).show();
        $('#filter_date span').html('All<br><small>'+moment().format('M/D/YYYY')+' - '+moment().add('year',1).endOf('month').format('M/D/YYYY'));
        //filter city 
        $('#myFilter select[name="filter_city"]').on('change', function(ev) {
            filter_search();     
        });
        //filter category
        $('#myFilter select[name="filter_category"]').on('change', function(ev) {
            filter_search();     
        });
        //filter name
        $('#myFilter input[name="filter_name"]').on('keyup', function(ev) {
            filter_name();     
        });
        //main filter
        function filter_search(){
            var city = $('#myFilter select[name="filter_city"] option:selected').val();
            var category = $('#myFilter select[name="filter_category"] option:selected').val();
            var start_date = $('#myFilter input[name="filter_start_date"]').val();
            var end_date = $('#myFilter input[name="filter_end_date"]').val();
            if(!(city=='' && category=='' && start_date=='' && end_date==''))
            {
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/production/home/search', 
                    data: {city:city,category:category,start_date:start_date,end_date:end_date}, 
                    success: function(data) {
                        if(data.success) 
                        {
                            $('#myShows .cbp-item').removeClass('hidden filtered').addClass('hidden'); 
                            if(data.shows.length){
                                $.each(data.shows,function(k, v) {
                                    var sh = $('#myShows').find('.cbp-item[data-id="'+v.id+'"]');
                                    if(v.time_alternative && v.time_alternative.length>0)
                                    {
                                        sh.find('.date_next_on').html(v.time_alternative);
                                        sh.find('.date_venue_on').html(v.time_alternative);
                                    }
                                    else
                                    {
                                        sh.find('.date_next_on').html(v.date_next_on);
                                        sh.find('.date_venue_on').html('ON '+v.date_venue_on);
                                    }
                                    sh.removeClass('hidden').addClass('filtered');
                                });
                                filter_name();
                            }else{
                                swal({
                                    title: "There are no events according to the filter info",
                                    type: "info"
                                });
                            }
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
                            text: "There was an error trying to filter the shows!<br>Try again.",
                            html: true,
                            type: "error"
                        });
                        //location.reload();
                    }
                });
            }
            //show all 
            else{
                $('#myShows .cbp-item').removeClass('hidden filtered').addClass('filtered');
                filter_name();
            }
        }
        //search event by name
        function filter_name(){            
            var search_name = $('#myFilter input[name="filter_name"]').val().toLowerCase().trim();
            if(search_name!='')
            {
                $('#myShows .cbp-item.filtered').removeClass('hidden').addClass('hidden'); 
                $('#myShows .cbp-item.filtered').filter(function() {
                    return $(this).data('search').toLowerCase().trim().search(search_name) > -1;
                }).removeClass('hidden');
            }
            else
                $('#myShows .cbp-item.filtered').removeClass('hidden');
            check_images();
        }
        //check for broken images to change
        function check_images(){
            $('#myShows .cbp-item.filtered:not(.hidden) img').each(function(){
                if($(this).height() <= 40) {
                    $(this).attr('src', $('#myShows').data('broken'));
                }
            });
        }
        //check images on load
        $(window).load(function(){
            check_images();
        });
    }
    return {
        //main function to initiate the module
        init: function () {
            initPortfolio();        
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    PortfolioManaged.init();
});