var TableDatatablesManaged = function () {

    var initTable = function () {

        var table = MainDataTableCreator.init('tb_model',false,[],10,false,'',[],false,false,false);

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
var AppCalendar = function() {

    return {
        //main function to initiate the module
        init: function() {
            this.initCalendar();
        },

        initCalendar: function() {
            if (!jQuery().fullCalendar) {
                return;
            }
            //size of calendar
            if ($('#cal_model').parents(".portlet").width() <= 720) {
                $('#cal_model').addClass("mobile");
            } else {
                $('#cal_model').removeClass("mobile");
            }
            //fill out events
            var calendarEvents = [];
            var events = $('#cal_model').data('info');
            var slug = $('#cal_model').data('slug');
            var go_to_date = (events && events.length>0)? events[0].show_time : new Date();
            $.each(events,function(k, v) {
                var display = (v.time_alternative)? v.time_alternative : v.show_hour;
                calendarEvents.push( {
                    id: v.id,
                    title: '<strong class="text-center">'+display+' <i class="fa fa-arrow-circle-right"></i></strong>',
                    start: v.show_time,
                    backgroundColor: 'bg-blue',
                    allDay: false,
                    url: (v.ext_slug)? v.ext_slug : slug+'/'+v.id
                });
            });
            //predefined events
            $('#cal_model').fullCalendar('destroy'); // destroy the calendar
            $('#cal_model').fullCalendar({ //re-initialize the calendar
                header: { left: 'title', center: '', right: 'prev,next, month, today' },
                defaultView: 'month', // change default view with available options from http://arshaw.com/fullcalendar/docs/views/Available_Views/
                slotMinutes: 15,
                editable: false,
                droppable: false,
                events: calendarEvents,
                eventRender: function (event, element) {
                    element.find('.fc-title').html(event.title);
                }
            });
            $('#cal_model').fullCalendar('render');
            $('#cal_model').fullCalendar('gotoDate', go_to_date);

            //render calendar when showtimes tab is clicked
            $('a[href="#showtimes_calendar"]').on('click', function(ev) {
                window.setTimeout(function(){
                    $('#cal_model').fullCalendar('render');
                    $('#cal_model').fullCalendar('gotoDate', go_to_date);
                },1);
            });

        }
    };
}();
//*****************************************************************************************
var GalleryImages = function () {

    var initGallery = function () {
        //banners carousel
        $('#myBanners').cubeportfolio({
            layoutMode: 'slider',
            defaultFilter: '*',
            animationType: 'fadeOut', // quicksand
            gapHorizontal: 30,
            gapVertical: 30,
            mediaQueries: [{ width: 320, cols: 1 }],
            gridAdjustment: 'responsive',
            caption: 'opacity',
            displayType: 'default',
            displayTypeSpeed: 1,
            auto:true,
            autoTimeout: 1500,
            drag:true,
            showNavigation: false,
            showPagination: false,
            rewindNav: true
        });
        //gallery carousel
        $('#myGallery').cubeportfolio({
            layoutMode: 'slider',
            defaultFilter: '*',
            animationType: 'fadeOut', // quicksand
            gapHorizontal: 30,
            gapVertical: 30,
            gridAdjustment: 'responsive',
            mediaQueries: [{ width: 1440, cols: 5 },{ width: 1024, cols: 4 },{ width: 800, cols: 3 }, { width: 480, cols: 2 }, { width: 320, cols: 1 }],
            caption: 'overlayBottomAlong',
            displayType: 'default',
            displayTypeSpeed: 1,
            auto:true,
            autoTimeout: 2000,
            drag:true,
            showNavigation: true,
            showPagination: false,
            rewindNav: true
        });
    }
    return {
        //main function to initiate map samples
        init: function () {
            initGallery();
        }
    };

}();
//*****************************************************************************************
var MapsGoogle = function () {
    
    return {
        //main function to initiate map samples
        init: function () {
            var map;
            $(document).ready(function(){
                var lat = $('#event_gmap').data('lat');
                var lng = $('#event_gmap').data('lng');
                var address = $('#event_gmap').data('address');
                var venue = $('#event_gmap').data('venue');
                map = new GMaps({
                    div: '#event_gmap',
                    lat: lat,
                    lng: lng
                });
                var marker = map.addMarker({
                    lat: lat,
                    lng: lng,
                    title: venue,
                    infoWindow: {
                        content: '<span style="color:#000"><b>'+venue+'</b><br>'+address+'</span>'
                    }
                });
                marker.infoWindow.open(map, marker);
            });
        }
    };

}();
//*****************************************************************************************
var RatingStars = function () {

    var rating = function () {
        
        //modal review on open
        $('a[href="#modal_write_reviewx"]').on('click', function(e) {
            e.preventDefault();
            $('#form_write_review a.rating-star').find('i').removeClass('fa-star-o').addClass('fa-star-o');
            $('#form_write_review input[name="rating"]').val(0);
            $('#form_write_review')[0].reset();
            $('#modal_write_review').modal('show');
        });
        //star on click
        $('#form_write_review a.rating-star').on('click', function(ev) {
            var star = $(this).data('star');
            $('#form_write_review input[name="rating"]').val(star);
            $('#form_write_review a.rating-star').find('i').removeClass('fa-star-o');
            $('#form_write_review a.rating-star').each(function(k, v) {
                if($(v).data('star') > star)
                    $(v).find('i').addClass('fa-star-o');
            });
        });
        //function on post
        $('#btn_review_send').on('click', function(ev) {
            $('#form_write_review input[name="show_id"]').val( $('#show_id').val() );
            if($('#form_write_review input[name="rating"]').val()<1)
            {
                $('#modal_write_review').modal('hide');
                swal({
                    title: "<span style='color:red;'>Error!</span>",
                    text: "You must pick up a rating star.",
                    html: true,
                    type: "error"
                },function(){
                    $('#modal_write_review').modal('show');
                });
            }
            else
            {
                if($('#form_write_review').valid())
                {
                    $('#modal_write_review').modal('hide');
                    swal({
                        title: "Posting...",
                        text: "Please, wait.",
                        type: "info",
                        showConfirmButton: false
                    });
                    jQuery.ajax({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        type: 'POST',
                        url: '/event/reviews',
                        data: $('#form_write_review').serializeArray(),
                        success: function(data) {
                            if(data.success)
                            {
                                $('#posts_reviews').html(data.posts);
                                swal({
                                    title: "<span style='color:green;'>Posted!</span>",
                                    text: data.msg,
                                    html: true,
                                    timer: 1500,
                                    type: "success",
                                    showConfirmButton: false
                                });
                            }
                            else{
                                swal({
                                    title: "<span style='color:red;'>Error!</span>",
                                    text: data.msg,
                                    html: true,
                                    type: "error"
                                },function(){
                                    $('#modal_write_review').modal('show');
                                });
                            }
                        },
                        error: function(){
                            swal({
                                title: "<span style='color:red;'>Error!</span>",
                                text: "There was an error trying to post your review.",
                                html: true,
                                type: "error"
                            },function(){
                                $('#modal_write_review').modal('show');
                            });
                        }
                    });
                }
            }

        });
    }
    return {
        //main function to initiate map samples
        init: function () {
            rating();
        }
    };

}();
//*****************************************************************************************
var ReviewValidation = function () {
    return {
        //main function to initiate the module
        init: function () {
            // advance validation
            var rules = {
                review: {
                    minlength: 5,
                    maxlength: 1000,
                    required: true
                }
            };
            MainFormValidation.init('form_write_review',rules,{});
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    TableDatatablesManaged.init();
    AppCalendar.init();
    GalleryImages.init();
    MapsGoogle.init();
    ReviewValidation.init();
    RatingStars.init();
});
