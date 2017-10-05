var PortfolioManaged = function () {
    
    var initPortfolio = function () {
        //grid event
        $('#myEvent').cubeportfolio({
            layoutMode: 'grid',
            defaultFilter: '*',
            animationType: 'fadeOut', // quicksand
            gapHorizontal: 0,
            gapVertical: 0,
            gridAdjustment: 'responsive', 
            mediaQueries: [{ width: 480, cols: 2 }, { width: 320, cols: 1 }],
            caption: 'overlayBottomAlong', 
            displayType: 'default', 
            displayTypeSpeed: 1,
            //lightboxDelegate: '.cbp-lightbox',
            //lightboxGallery: true,
            //lightboxTitleSrc: 'data-title',
            //lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',
            singlePageDelegate: '.cbp-singlePage',
            singlePageDeeplinking: true,
            singlePageStickyNavigation: true,
            singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} of {{total}}</div>'
        }); 
    }
    return {
        //main function to initiate the module
        init: function () {
            initPortfolio();        
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    PortfolioManaged.init();
});