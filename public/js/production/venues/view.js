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
jQuery(document).ready(function() {
    MapsGoogle.init(); 
});