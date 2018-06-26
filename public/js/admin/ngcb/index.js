var NGCBDatatablesManaged = function () {
    
    var initTable = function () {
        
        swal({
                title: "NGCB Reports",
                text: "New route for NGCB resources.",
                type: "info",
                showConfirmButton: false
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
jQuery(document).ready(function() {
    NGCBDatatablesManaged.init();
});