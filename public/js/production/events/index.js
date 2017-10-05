var TableDatatablesManaged = function () {
    
    var initTable = function () {
        
        var table = $('#tb_model');
        // begin first table
        table.dataTable({
            // Internationalisation. For more info refer to http://datatables.net/manual/i18n
            "language": {
                "emptyTable": "No events available",
                "infoEmpty": "No events available",
                "zeroRecords": "No events available",
                "paginate": {
                    "previous":"Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            },
            "bStateSave": false, 
            "pageLength": 7,            
            "pagingType": "bootstrap_full_number",
            "info" : false,
            "lengthChange": false,
            "searching": false,
            "ordering": false
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
            var date = new Date();
            var d = date.getDate();
            var m = date.getMonth();
            var y = date.getFullYear();
            if ($('#cal_model').parents(".portlet").width() <= 720) {
                $('#cal_model').addClass("mobile");
            } else {
                $('#cal_model').removeClass("mobile");
            }
            //predefined events
            $('#cal_model').fullCalendar('destroy'); // destroy the calendar
            $('#cal_model').fullCalendar({ //re-initialize the calendar
                header: { left: 'title', center: '', right: 'prev,next, agendaDay, agendaWeek, month, today' },
                defaultView: 'month', // change default view with available options from http://arshaw.com/fullcalendar/docs/views/Available_Views/ 
                slotMinutes: 15,
                editable: false,
                droppable: false
            });
            var showtimes = $('a[href="#showtimes_calendar"]').data('times');
            $.each(showtimes,function(k, v) {
                $('#cal_model').fullCalendar('renderEvent', {
                    id:v.id,
                    title: v.show_time,
                    start: v.show_time,
                    end: v.show_time,
                    backgroundColor: App.getBrandColor('blue'),
                    allDay: false,
                    url: 'http://google.com/'
                }, true); 
            });
            //render calendar when showtimes tab is clicked
            $('a[href="#showtimes_calendar"]').on('click', function(ev) {
                window.setTimeout(function(){
                     $('#cal_model').fullCalendar('render'); 
                 },1);
            });
        }

    };

}();
//*****************************************************************************************
jQuery(document).ready(function() {
    TableDatatablesManaged.init();
    AppCalendar.init(); 
});