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
        
        //on select btn_model_reservations_add
        $('#btn_model_reservations_add').on('click', function(ev) {
            $('#form_model_restaurant_reservations').trigger('reset');
            $('#form_model_restaurant_reservations input[name="id"]:hidden').val('').trigger('change');
            $('#form_model_restaurant_reservations input[name="restaurants_id"]:hidden').val( $('#form_model_update [name="id"]').val() );
            $('#form_model_restaurant_reservations input[name="action"]:hidden').val( 1 );
            $('#modal_model_restaurant_reservations').modal('show');
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
var FormReservationsValidation = function () {
    // advance validation
    var handleValidation = function() {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation
            var form = $('#form_model_restaurant_reservations');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);
            //IMPORTANT: update CKEDITOR textarea with actual content before submit
            form.on('submit', function() {
                for(var instanceName in CKEDITOR.instances) {
                    CKEDITOR.instances[instanceName].updateElement();
                }
            })
            form.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "", // validate all fields including form hidden input
                rules: {
                    schedule: {
                        date: true,
                        required: true
                    },
                    people: {
                        digits: true,
                        range: [1, 10],
                        required: true
                    },
                    first_name: {
                        minlength: 3,
                        maxlength: 15,
                        required: true
                    },
                    last_name: {
                        minlength: 3,
                        maxlength: 25,
                        required: true
                    },
                    phone: {
                        digits: true,
                        minlength: 10,
                        maxlength: 10,
                        required: false
                    },
                    email: {
                        email: true,
                        required: false
                    },
                    occasion: {
                        required: true
                    },
                    special_request: {
                        minlength: 5,
                        maxlength: 2000,
                        required: false
                    },
                    status: {
                        required: true
                    }
                },
                invalidHandler: function (event, validator) { //display error alert on form submit   
                    success.hide();
                    error.show();
                    App.scrollTo(error, -200);
                },

                highlight: function (element) { // hightlight error inputs
                   $(element)
                        .closest('.show-error').addClass('has-error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change done by hightlight
                    $(element)
                        .closest('.show-error').removeClass('has-error'); // set error class to the control group
                },

                success: function (label) {
                    label
                        .closest('.show-error').removeClass('has-error'); // set success class to the control group
                },

                submitHandler: function (form) {
                    success.show();
                    error.hide();
                    form[0].submit(); // submit the form
                }
            });
    }
    return {
        //main function to initiate the module
        init: function () {
            handleValidation();
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    TableReservationsDatatablesManaged.init();
    FormReservationsValidation.init();
});