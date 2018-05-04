var TableDatatablesManaged = function () {
    
    var initTable = function () {
        
        MainDataTableCreator.init('tb_model',[],10);
        
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
    TableDatatablesManaged.init();
});