var MapsGoogle = function () {

    var mapMarker = function () {        
        var lat = $('#event_gmap').data('lat');
        var lng = $('#event_gmap').data('lng');        
        var address = $('#event_gmap').data('address');  
        var venue = $('#event_gmap').data('venue');  
        var map = new GMaps({
            div: '#event_gmap',
            lat: lat,
            lng: lng
        });        
        map.addMarker({
            lat: lat,
            lng: lng,
            title: venue,
            infoWindow: {
                content: '<span style="color:#000"><b>'+venue+'</b><br>'+address+'</span>'
            }
        });        
        map.setZoom(14);
    }
    return {
        //main function to initiate map samples
        init: function () {
            mapMarker();
        }
    };

}();
//*****************************************************************************************
jQuery(document).ready(function() {
    MapsGoogle.init(); 
});