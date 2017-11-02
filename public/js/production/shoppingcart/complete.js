var CompleteFunctions = function () {
    
    var initFunctions = function () {
        
        $(window).bind('beforeunload', function(e){
            return "Are you sure you want to leave this page? This page will allow you to print your tickets.";
        });
        
        //on resend email welcome
        $('#resend_welcome').on('click', function(ev) {
            var user_id = $(this).data('id');
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/production/purchase/welcome', 
                data: { user_id: user_id}, 
                success: function(data) {
                    if(data.success) 
                    {
                        swal({
                            title: "<span style='color:green;'>Sent!</span>",
                            html: true,
                            timer: 1500,
                            type: "success",
                            showConfirmButton: false
                        });
                    }
                    else
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error"
                        });
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to re-send the welcome email. Please, contact us.",
                        html: true,
                        type: "error",
                        showConfirmButton: true
                    });
                }
            }); 
        });
        
        //on resend receipts
        $('#resend_receipts').on('click', function(ev) {
            var purchases = $(this).data('purchases');
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/production/purchase/receipts', 
                data: { purchases: purchases }, 
                success: function(data) {
                    if(data.success && data.sent_receipts) 
                    {
                        swal({
                            title: "<span style='color:green;'>Sent!</span>",
                            html: true,
                            timer: 1500,
                            type: "success",
                            showConfirmButton: false
                        });
                    }
                    else
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: 'There was an error trying to re-send the receipts',
                            html: true,
                            type: "error"
                        });
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to re-send the receipts by email. Please, contact us.",
                        html: true,
                        type: "error",
                        showConfirmButton: true
                    });
                }
            }); 
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
var GalleryImages = function () {

    var initGallery = function () {        
        //banners carousel
        $('#myBanners').cubeportfolio({
            layoutMode: 'slider',
            defaultFilter: '*',
            animationType: 'fadeOut', // quicksand
            gapHorizontal: 30,
            gapVertical: 30,
            mediaQueries: [{ width: 320, cols: 1 }],
            gridAdjustment: 'responsive', 
            caption: 'opacity', 
            displayType: 'default', 
            displayTypeSpeed: 1,
            auto:true,
            autoTimeout: 1500,
            drag:true,
            showNavigation: false,
            showPagination: false,
            rewindNav: true
        });
        //gallery carousel
        /*$('#myGallery').cubeportfolio({
            layoutMode: 'slider',
            defaultFilter: '*',
            animationType: 'fadeOut', // quicksand
            gapHorizontal: 30,
            gapVertical: 30,
            gridAdjustment: 'responsive', 
            mediaQueries: [{ width: 1440, cols: 5 },{ width: 1024, cols: 4 },{ width: 800, cols: 3 }, { width: 480, cols: 2 }, { width: 320, cols: 1 }],
            caption: 'overlayBottomAlong', 
            displayType: 'default', 
            displayTypeSpeed: 1,
            auto:true,
            autoTimeout: 2000,
            drag:true,
            showNavigation: true,
            showPagination: false,
            rewindNav: true
        });*/
    }
    return {
        //main function to initiate map samples
        init: function () {
            initGallery();
        }
    };

}();
//*****************************************************************************************
jQuery(document).ready(function() {
    CompleteFunctions.init();
    GalleryImages.init(); 
});