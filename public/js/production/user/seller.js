var SellerFunctions = function () {    
    var initFunctions = function () {
        
        //function open drawer
        $('#btn_seller_open').on('click', function(ev) {
            $('#modal_seller_open').modal('hide');
            swal({
                title: "Opening drawer",
                text: "Please, wait.",
                type: "info",
                showConfirmButton: false
            });
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/user/seller/drawer_open', 
                data: $('#form_seller_open').serializeArray(), 
                success: function(data) {
                    if(data.success) 
                    {
                        swal({
                            title: "<span style='color:green;'>Opened!</span>",
                            text: data.msg,
                            html: true,
                            timer: 1500,
                            type: "success",
                            showConfirmButton: false
                        },function(){
                            location.reload();
                        });
                    }
                    else{
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_seller_open').modal('show');
                        });
                    }
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to open the drawer.",
                        html: true,
                        type: "error"
                    },function(){
                        $('#modal_seller_open').modal('show');
                    });
                }
            }); 
        });
        
        //function continue drawer
        $('#btn_seller_continue').on('click', function(ev) {
            $('#modal_seller_continue').modal('hide');
            swal({
                title: "Updating drawer",
                text: "Please, wait.",
                type: "info",
                showConfirmButton: false
            });
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/user/seller/drawer_continue', 
                data: $('#form_seller_continue').serializeArray(), 
                success: function(data) {
                    if(data.success) 
                    {
                        swal({
                            title: "<span style='color:green;'>Updated!</span>",
                            text: data.msg,
                            html: true,
                            timer: 1500,
                            type: "success",
                            showConfirmButton: false
                        },function(){
                            location.reload();
                        });
                    }
                    else{
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_seller_continue').modal('show');
                        });
                    }
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to close the drawer.",
                        html: true,
                        type: "error"
                    },function(){
                        $('#modal_seller_continue').modal('show');
                    });
                }
            }); 
        });
        //function continue drawer
        $('#btn_seller_closing').on('click', function(ev) {
            $('#modal_seller_continue').modal('hide');
            $('#modal_seller_close').modal('show');             
        });
        
        //function close drawer
        $('#btn_seller_close').on('click', function(ev) {
            $('#modal_seller_close').modal('hide');
            swal({
                title: "Closing drawer",
                text: "Please, wait.",
                type: "info",
                showConfirmButton: false
            });
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/user/seller/drawer_close', 
                data: $('#form_seller_close').serializeArray(), 
                success: function(data) {
                    if(data.success) 
                    {
                        swal({
                            title: "<span style='color:green;'>Closed!<br><b>Cash out: $"+data.cash_out+"</b></span>",
                            text: data.msg,
                            html: true,
                            type: "success",
                            showConfirmButton: true
                        },function(){
                            location.reload();
                        });
                    }
                    else{
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error"
                        },function(){
                            location.reload();
                        });
                    }
                },
                error: function(){
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to close the drawer.",
                        html: true,
                        type: "error"
                    },function(){
                        $('#modal_seller_close').modal('show');
                    });
                }
            }); 
        });
        
        //function tally drawer
        $('#open_seller_tally').on('click', function(ev) {
            swal({
                title: "Opening tally",
                text: "Please, wait.",
                type: "info",
                showConfirmButton: false
            });
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/user/seller/tally', 
                success: function(data) {
                    if(data.success) 
                    {
                        $('#table_tally_body').empty();
                        if(data.tally)
                        {
                            $.each(data.tally,function(k, v) {
                                var t_o = (v.time_out)? '<td>'+v.time_out+'</td>' : '<td>Pending</td>';
                                var c_o = (v.cash_out)? '<td>$ '+v.cash_out+'</td>' : '<td>Pending</td>';
                                var color = (v.time_out)? 'danger' : 'success';
                                $('#table_tally_body').append('<tr class="'+color+'"><td>'+(k+1)+'</td><td>'+v.time_in+'</td>'+t_o+'<td>$ '+v.cash_in+'</td>'+c_o+'<td>'+v.transactions+'</td><td>'+v.tickets+'</td><td>$ '+v.total+'</td></tr>');
                            });
                        }
                        swal.close();
                        $('#modal_seller_tally').modal('show'); 
                    }
                    else{
                        swal.close();
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: data.msg,
                            html: true,
                            type: "error"
                        });
                    }
                },
                error: function(){
                    swal.close();
                    swal({
                        title: "<span style='color:red;'>Error!</span>",
                        text: "There was an error trying to open the tally.",
                        html: true,
                        type: "error"
                    });
                }
            });       
        });
        
        // on load modal to send the reports
        $('#open_seller_report').on('click', function(e) {
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/user/seller/report',
                data: {action:0},
                success: function(data) {
                    if(data.success)
                    {
                        //reset email to
                        $('#form_seller_report').trigger('reset');
                        //reset send to
                        $('#events_list').empty();
                        if(data.events)
                        {
                            $.each(data.events,function(k, v) {
                                var date = moment(v.show_time);
                                $('#events_list').append('<label class="mt-checkbox"><input type="checkbox" name="show_times[]" value="'+v.id+'" />#'+v.id+' => '+date.format('M/DD/YYYY @ h:mmA')+' ('+v.purchases+' purchases)<br>'+v.name+'<span></span></label><br>');
                            });
                        }
                        $('#modal_seller_report').modal('show');
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
                        text: "There was an error trying to open the send report modal!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    });
                }
            });
        });
        
        //function send report by email
        $('#btn_seller_report').on('click', function(ev) {
            $('#modal_seller_report').modal('hide');
            if($('#form_seller_report [type=checkbox]:checked').length > 0)
            {
                swal({
                    title: "Sending report by email",
                    text: "Please, wait.",
                    type: "info",
                    showConfirmButton: false
                });
                jQuery.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '/user/seller/report',
                    data: $('#form_seller_report').serializeArray(),
                    success: function(data) {
                        if(data.success)
                        {
                            swal({
                                title: "<span style='color:green;'>Sent!</span>",
                                text: data.msg,
                                html: true,
                                type: "success",
                                showConfirmButton: true
                            });
                        }
                        else{
                            swal({
                                title: "<span style='color:red;'>Error!</span>",
                                text: data.msg,
                                html: true,
                                type: "error"
                            },function(){
                                $('#modal_seller_report').modal('show');
                            });
                        }
                    },
                    error: function(){
                        swal({
                            title: "<span style='color:red;'>Error!</span>",
                            text: "There was an error trying to send the email!<br>The request could not be sent to the server.",
                            html: true,
                            type: "error"
                        },function(){
                            $('#modal_seller_report').modal('show');
                        });
                    }
                });
            }
            else
            {
                swal({
                    title: "<span style='color:red;'>Error!</span>",
                    text: "There was an error trying to send the email!<br>You must check some events to create the report.",
                    html: true,
                    type: "error"
                },function(){
                    $('#modal_seller_report').modal('show');
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
var SellerValidation = function () {
    return {
        //main function to initiate the module
        init: function () {
            // advance validation
            /*var rules = {
                name: {
                    minlength: 3,
                    maxlength: 200,
                    required: false
                },
                email: {
                    minlength: 5,
                    maxlength: 200,
                    email: true,
                    required: true
                },
                phone: {
                    digits: true,
                    range: [1000000000,9999999999],
                    required: false
                },  
                event: {
                    minlength: 5,
                    maxlength: 200,
                    required: false
                },
                date: {
                    minlength: 16,
                    maxlength: 16,
                    date:true,
                    required: false
                },
                message: {
                    minlength: 5,
                    maxlength: 250,
                    required: true
                }
            };
            MainFormValidation.init('form_contact_us',rules,{});*/
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    SellerFunctions.init();
    SellerValidation.init();
});