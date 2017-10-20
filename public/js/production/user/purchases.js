var PurchasesFunctions = function () {    
    var initFunctions = function () {
        //load purchases share
        $('#tb_purchases button').on('click', function(ev) {
            var purchase_id = $(this).data('id');
            var qty = parseInt($(this).data('qty'));
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/production/user/purchases/share', 
                data: { id: purchase_id }, 
                success: function(data) {
                    if(data.success) 
                    {
                        $('#form_share_tickets input[name="purchases_id"]').val(purchase_id);
                        ShareTicketsFunctions.load(data,qty); 
                        $('#modal_share_tickets').modal('show');
                    }
                    else{
                            swal({
                                title: "<span style='color:red;'>Error!</span>",
                                text: data.msg,
                                html: true,
                                type: "error",
                                showConfirmButton: true
                            });
                        }
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to load the shared tickets.",
                        html: true,
                        type: "error",
                        showConfirmButton: true
                    });
                }
            }); 
        });
        
        //function save
        $('#btn_share_tickets').on('click', function(ev) {
            //submit values of tickets
            if( ShareTicketsFunctions.check() )
            {  
                $('#modal_share_tickets').modal('hide');
                swal({
                    title: "Sharing your tickets...",
                    text: "Please, wait.",
                    type: "info",
                    showConfirmButton: false
                });
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/production/user/purchases/share', 
                    data: $('#form_share_tickets').serializeArray(), 
                    success: function(data) {
                        if(data.success) 
                        {
                            swal({
                                title: "<span style='color:green;'>Saved!</span>",
                                text: data.msg,
                                html: true,
                                timer: 1500,
                                type: "success",
                                showConfirmButton: false
                            });
                        }
                        else{
                            swal({
                                title: "<span style='color:red;'>Error!</span>",
                                text: data.msg,
                                html: true,
                                type: "error"
                            },function(){
                                $('#modal_share_tickets').modal('show');
                            });
                        }
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to create the user.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_share_tickets').modal('show');
                        });
                    }
                }); 
            }  
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
    PurchasesFunctions.init();
});