var FunctionsGuest = function () {
    
    var initFunctions = function () {
        
        //function show msg empty
        swal({
            title: "<span style='color:black;'>Information</span>",
            text: 'You have no items in the shopping cart.<br>You will redirect to the home page now.',
            html: true,
            timer: 3000,
            type: "warning",
            showConfirmButton: false
        },function(){
            window.location = '/production/home';
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
    FunctionsGuest.init();
});