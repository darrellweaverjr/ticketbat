var TableReservationsDatatablesManaged = function () {
    
    var update_reservations = function (items) {
        $('#tb_restaurant_reservations').empty();
        $.each(items,function(k, v) {
            var row_date = '<td>'+moment(v.schedule).format('ddd, MMM D, YYYY')+'<br>'+moment(v.schedule).format('h:mm A')+'</td>';
            var row_guests = '<td>'+v.people+'</td>';
            var row_client = '<td>'+v.first_name+'<br>'+v.last_name+'</td>';
            var row_contact = '<td>'+v.phone+'<br>'+v.email+'</td>';
            var row_occassion = '<td>'+v.occasion+'</td>';
            if(v.special_request)
            {
                var row_request = '<td>Yes</td>';
                var title = 'SPECIAL REQUEST: ' + v.special_request;
            }
            else
            {
                var row_request = '<td>No</td>';
                var title = '';
            }
            switch (v.status)
            {
                case 'Requested':
                    v.status = '<span class="label label-sm sbold label-warning"> Requested </span>';
                    break;
                case 'Checked':
                    v.status = '<span class="label label-sm sbold label-success"> Checked </span>';
                    break;  
                case 'Cancelled':
                    v.status = '<span class="label label-sm sbold label-danger"> Cancelled </span>';
                    break; 
                case 'Denied':
                    v.status = '<span class="label label-sm sbold label-danger"> Denied </span>';
                    break; 
            }
            var row_status = '<td>'+v.status+'<br>'+moment(v.created).format('M/D/YYYY h:mmA')+'</td>'; //created here
            var row_edit = '<td><input type="button" value="Edit" class="btn sbold bg-yellow edit" disabled="true"></td>';
            $('#tb_restaurant_reservations').append('<tr data-id="'+v.id+'" title="'+title+'">'+ row_date + row_guests + row_client + row_contact + row_occassion + row_request + row_status + row_edit +'</tr>');
        });   
    }
    
    var initTable = function () {
        
        //function refresh
        $('#btn_model_reservations_refresh').on('click', function(ev) {
            jQuery.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '/admin/restaurants/reservations', 
                data: {restaurants_id: $('#form_model_update input[name="id"]').val() }, 
                success: function(data) {
                    if(data.success) 
                    {
                        update_reservations(data.reservations);
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
                        text: "There was an error trying to load the reservations' information!<br>The request could not be sent to the server.",
                        html: true,
                        type: "error"
                    });
                }
            }); 
        });
        
    }
    return {
        //main function to initiate the module
        init: function () {
            if (!jQuery().dataTable) {
                return;
            }
            initTable();        
        },
        update_reservations: function (items) {
            update_reservations(items);        
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    TableReservationsDatatablesManaged.init();
});