var FunctionsGuest = function () {
    
    var initFunctions = function () {
        
        //$('#modal_login_guest').modal('show');
        //$('#tb_items input[type="number"]').TouchSpin({ initval:1,min:1,step:1,decimals:0 });
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