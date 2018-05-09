var RefundedDatatablesManaged = function () {
    
    var initTable = function () {
        
        MainDataTableCreator.init('tb_model_refunded',[],10);
        
        //PERSONALIZED FUNCTIONS
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
    RefundedDatatablesManaged.init();
});