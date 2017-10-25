var ShareFunctions = function () {
    
    var initFunctions = function () {
        
        //load share tickets
        $('#tb_items tr > td:nth-child(6) button').on('click', function(ev) {
            var qty = parseInt($(this).closest('tr').data('qty'));
            var shoppingcart_id = $(this).closest('tr').attr('id'); 
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/production/shoppingcart/share', 
                data: { id: shoppingcart_id }, 
                success: function(data) {
                    if(data.success) 
                    {
                        $('#form_share_tickets input[name="purchases_id"]').val(shoppingcart_id);
                        ShareTicketsFunctions.load(data,qty,1); 
                        $('#modal_share_tickets').modal('show');
                    }
                    else{
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error"
                        });
                    }
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to load the shared tickets.",
                        html: true,
                        type: "error"
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
                    url: '/production/shoppingcart/share', 
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
    ShareFunctions.init();
});