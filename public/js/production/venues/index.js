var PortfolioManaged = function () {
    
    var initPortfolio = function () {
        //grid myVenues
        $('.myVenues').cubeportfolio({
            layoutMode: 'grid',
            defaultFilter: '*',
            animationType: 'fadeOut', // quicksand
            gapHorizontal: 0,
            gapVertical: 0,
            gridAdjustment: 'responsive', 
            mediaQueries: [{ width: 1440, cols: 5 },{ width: 1024, cols: 4 },{ width: 800, cols: 3 }, { width: 480, cols: 2 }, { width: 320, cols: 1 }],
            caption: 'overlayBottomAlong', 
            displayType: 'default', 
            displayTypeSpeed: 1,
            lightboxDelegate: '.cbp-lightbox',
            lightboxGallery: true,
            lightboxTitleSrc: 'data-title',
            lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',
            singlePageDelegate: '.cbp-singlePage',
            singlePageDeeplinking: true,
            singlePageStickyNavigation: true,
            singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} of {{total}}</div>'
        });   
        //link to venue details
        $('.myVenues div.cbp-item').bind('click',function (e) {
            if($(e.target).is('a') || $(e.target).is('i'))
               return;
            window.location = $(this).data('href');
        });
        function resizeVenues()
        {
            $('div.myVenues').each(function(k,v){
                var y2 = $(v).find('.cbp-item.filtered:not(.hidden):last').position().top;
                // $(v).height( parseInt((y2+(k+2)*400)) );
                console.log(v);
            });
        }
        $(window).resize(function(){
            setTimeout(resizeVenues, 500);
        });
        setTimeout(resizeVenues, 1);
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