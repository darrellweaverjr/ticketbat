var PurchasesFunctions = function () {    
    var initFunctions = function () {
        //load purchases share
        $('#tb_purchases button').on('click', function(ev) {
            var purchase_id = $(this).data('id');
            var qty = parseInt($(this).data('qty'));
            var availables = qty;
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/production/user/purchases/share', 
                data: { id: purchase_id }, 
                success: function(data) {
                    if(data.success) 
                    {
                        $('#form_share_tickets input[name="purchases_id"]').val(purchase_id);
                        //fill out table
                        $('#tb_shared_tickets_body').empty();
                        $.each(data.tickets,function(k, v) {
                            var qty_t = v.tickets.split(','); availables -= qty_t.length;
                            var row_first_name = '<td><input type="text" name="first_name['+v.id+']" class="form-control" value="'+v.first_name+'"></td>';
                            var row_last_name = '<td><input type="text" name="last_name['+v.id+']" class="form-control" value="'+v.last_name+'"></td>';
                            var row_email = '<td><input type="text" name="email['+v.id+']" class="form-control" value="'+v.email+'"></td>';
                            var row_qty = '<td><select class="form-control" name="qty['+v.id+']">';
                            for (i = qty_t.length; i > 0; i--) 
                                row_qty += '<option value="'+i+'">'+i+'</option>';
                            row_qty += '</select></td>';
                            var row_comment = '<td><input type="text" name="comment['+v.id+']" class="form-control" value="'+v.comment+'"></td>';
                            var row_delete = '<td><center><button type="button" class="btn btn-large btn-danger">X</button></center></td>';
                            $('#tb_shared_tickets_body').prepend('<tr>'+row_first_name+row_last_name+row_email+row_qty+row_comment+row_delete+'</tr>');
                            //reorder select values and autoselect the first one
                            var select_qty = $('#tb_shared_tickets_body select[name="qty['+v.id+']"]');
                            select_qty.find('option').each(function(){
                                $(this).prependTo(select_qty).attr('selected','selected');
                            });
                        });
                        //set up values for select and calculate
                        $('#share_tickets_availables').data('qty', qty);
                        $('#share_tickets_availables').data('availables', availables);
                        //fill out select availables
                        $('#share_tickets_availables').empty().prepend('<option selected value="0">0 ticket(s) available(s)</option>');
                        for (i = 1; i <= qty; i++) 
                            $('#share_tickets_availables').prepend('<option value="'+i+'">'+i+' ticket(s) available(s)</option>');
                        $('#share_tickets_availables').children('option').filter(function() {
                            return this.value > availables;
                        }).prop('disabled', true);                        
                        $('#share_tickets_availables').children(':enabled:first').prop('selected',true);
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
        //show error on share modals
        function show_error_share(text){
            $('#modal_share_tickets').modal('hide');
            swal({
                title: "<span style='color:red;'>Error!</span>",
                text: text,
                html: true,
                type: "error"
            },function(){
                $('#modal_share_tickets').modal('show');
            });
        }
        //update qty available in select asign
        function update_available(qty)
        {
            var availables = parseInt($('#share_tickets_availables').data('availables'));
            $('#share_tickets_availables').data('availables', availables+qty);
            $('#share_tickets_availables').children('option').prop('disabled', true);
            $('#share_tickets_availables').children('option').filter(function() {
                return this.value <= availables+qty;
            }).prop('disabled', false);
            $('#share_tickets_availables').children(':enabled:first').prop('selected',true);
        }
        //check if valid first row
        function check_rows()
        {
            if($('#tb_shared_tickets_body').children().length)
            {
                var row = $('#tb_shared_tickets_body').children('tr:first');
                var first_name = $.trim(row.find('input[name*="first_name"]').val());
                var last_name = $.trim(row.find('input[name*="last_name"]').val());
                var email = $.trim(row.find('input[name*="email"]').val());
                var errors = '';
                if(!first_name.match('^[a-zA-Z]{3,50}$'))
                    errors += 'The field first name must be fill out correctly. At least 3 letters.<br>';
                if(!last_name.match('^[a-zA-Z]{3,50}$'))
                    errors += 'The field last name must be fill out correctly. At least 3 letters.<br>';
                if(!CheckValidEmail(email))
                    errors += 'The field email must be fill out correctly. That is not a valid email address.<br>';
                if(errors!='')
                {
                    show_error_share(errors);
                    return false;
                }
                return true;
            }
            return true;
        }
        //share to person button event
        $('#new_person_share').on('click', function(ev) {
            var share = parseInt($('#share_tickets_availables').val());
            if(share>0 && check_rows())
            {
                //create new id for row
                var id = '_'+$.now();
                //create new row on table
                var row_first_name = '<td><input type="text" name="first_name['+id+']" class="form-control" placeholder="John"></td>';
                var row_last_name = '<td><input type="text" name="last_name['+id+']" class="form-control" placeholder="Doe"></td>';
                var row_email = '<td><input type="text" name="email['+id+']" class="form-control" placeholder="mail@gmail.com"></td>';
                var row_qty = '<td><select class="form-control" name="qty['+id+']" data-prev="'+share+'">';
                for (i = share; i > 0; i--) 
                    row_qty += '<option value="'+i+'">'+i+'</option>';
                row_qty += '</select></td>';
                var row_comment = '<td><input type="text" name="comment['+id+']" class="form-control" placeholder="My comment"></td>';
                var row_delete = '<td><center><button type="button" class="btn btn-large btn-danger">X</button></center></td>';
                $('#tb_shared_tickets_body').prepend('<tr>'+row_first_name+row_last_name+row_email+row_qty+row_comment+row_delete+'</tr>');
                update_available(share*-1);
            }
        });
        //remove row share
        $('#tb_shared_tickets_body').on('click', 'button', function(e){
            var row = $(this).closest('tr');
            var qty = parseInt(row.children().find('select').val());
            update_available(qty);
            row.remove();
        });
        //row share select on change
        $('#tb_shared_tickets_body').on('change', 'select', function(e){
            var availables = parseInt($('#share_tickets_availables').data('availables'));
            var prev = parseInt($(this).data('prev'));
            var qty = parseInt($(this).children('option:selected').val());
            var new_qty = prev - qty;
            if(new_qty>0) //dicrease qty, increase available
                update_available(new_qty);
            else if(new_qty<0) //increase qty, decrease available
            {
                if(new_qty*-1 <= availables)
                    update_available(new_qty);
                else
                {
                    qty = prev;
                    $(this).find('option:selected').attr('selected', false);
                    $(this).val(prev);
                    show_error_share("You have not that quantity of tickets availables to share");
                }
            }
            $(this).data('prev', qty);
        });
        //function login
        $('#btn_share_tickets').on('click', function(ev) {
            if(check_rows())
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