var CompleteFunctions = function () {
    
    var initFunctions = function () {
        
        $(window).bind('beforeunload', function(e){
            return "Are you sure you want to leave this page? This page will allow you to print your tickets.";
        });
        
    }
    return {
        //main function to initiate the module
        init: function () {
            initFunctions();        
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    CompleteFunctions.init();
});