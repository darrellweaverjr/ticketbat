var FunctionsManaged = function () {
    
    var initFunctions = function () {
        
        //update stage images
        function update_stage_images(e)
        {
            var image_type = $('#stage_images img[data-type="'+$(e).data('type')+'"]');
            $('#stage_images img').css('display','none');
            (image_type.length)? image_type.css('display','block') : $('#stage_images img[data-type="default"]').css('display','block');
        }
        //on click check images for ticket types
        $('#tickets_accordion a.accordion-toggle').on('click',function(){
            update_stage_images($(this));
        });
        //update ticket on select 
        $('#form_model_update input[name="ticket_id"]').bind('click','change',function(){
            update_price();
        });
        //update qty select
        $('#form_model_update select[name="qty"]').change(function(){
            var price = parseFloat($(this).data('price'));
            var qty = $(this).find('option:selected').val();
            var totals = (price*qty).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");;
            $('#totals').html('$ '+totals);
        });
        //function to get the price of selected radio
        function update_price()
        {
            var e = $('#form_model_update input[name="ticket_id"]:checked');
            if(e.length>0)
            {
                var price = parseFloat(e.data('price'));
                var max = parseInt(e.data('max'));
            }
            else
                var price = max = 0;
            $('#form_model_update select[name="qty"]').data('price', price);
            $('#form_model_update select[name="qty"]').empty();
            if(max>0)
            {
                for(var i = 1; i <= max; i++)
                    $('#form_model_update select[name="qty"]').append('<option value="'+i+'">'+i+'</option>');
                $('#btn_add_shoppingcart').show();
            }
            else
            {
                $('#form_model_update select[name="qty"]').append('<option value="0">0</option>');
                $('#btn_add_shoppingcart').hide();
            } 
            $('#form_model_update select[name="qty"] option:first').attr('selected','selected').trigger('change');
        }
        //add item to the shoppingcart
        $('#btn_add_shoppingcart').on('click', function(ev) {
            $('#form_model_update input[name="password"]').val('');
            var e = $('#form_model_update input[name="ticket_id"]:checked');
            if(parseInt(e.data('pass'))>0)
            {
                swal({
                    title: "Please enter the password for the event",
                    type: "input",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    inputPlaceholder: "Password"
                }, function (inputUserType) {
                    if (inputUserType === false) return false;
                    if ($.trim(inputUserType) === "") {
                      swal.showInputError("You need to write something!");
                      return false;
                    }
                    else
                    {
                        $('#form_model_update input[name="password"]').val(inputUserType);
                        add_item();
                    }
                });
            }
            else
                add_item();
        });
        //submit value
        function add_item()
        {
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/shoppingcart/add', 
                data: $('#form_model_update').serializeArray(), 
                success: function(data) {
                    if(data.success) 
                    {
                        ShoppingcartQtyItems.init();
                        Countdown.reset();
                        swal({
                            title: "<span style='color:green;'>Added to the cart!</span>",
                            text: data.msg,
                            html: true,
                            timer: 1500,
                            type: "success",
                            showConfirmButton: false
                        },function(){
                            // Facebook track adding the shopping cart
                            FBPixel.init();
                            //location.reload(); 
                        });
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
                        text: "There was an error trying to add the ticket(s) to the cart.",
                        html: true,
                        type: "error"
                    });
                }
            }); 
        }
        //autoselect first one
        update_stage_images( $('#tickets_accordion a.accordion-toggle')[0] );
        $('#form_model_update input:radio.default_radio').attr('checked',true).trigger('change');
        update_price();        
    }
    return {
        //main function to initiate the module
        init: function () {
            initFunctions();        
        }
    };
}();
//*****************************************************************************************
var FBPixel = function () {
    
    return {
        //main function to initiate the module
        init: function () {
            fbq('track', 'AddToCart', {event: fbq_events[0]});       
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    FunctionsManaged.init();
});