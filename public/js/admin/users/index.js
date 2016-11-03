var TableDatatablesManaged = function () {
    
    var initTable = function (table_id) {
        var table = $('#'+table_id);
        // begin first table
        table.dataTable({
            // Internationalisation. For more info refer to http://datatables.net/manual/i18n
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                },
                "emptyTable": "No data available in table",
                "info": "Showing _START_ to _END_ of _TOTAL_ records",
                "infoEmpty": "No records found",
                "infoFiltered": "(filtered1 from _MAX_ total records)",
                "lengthMenu": "Show _MENU_",
                "search": "Search:",
                "zeroRecords": "No matching records found",
                "paginate": {
                    "previous":"Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            },
            "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
            "lengthMenu": [
                [10, 15, 20, -1],
                [10, 15, 20, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 15,            
            "pagingType": "bootstrap_full_number",
            "columnDefs": [
                {  // set default column settings
                    'orderable': false,
                    'targets': [0]
                }, 
                {
                    "searchable": false,
                    "targets": [0]
                },
                {
                    "className": "dt-right", 
                    //"targets": [2]
                }
            ],
            "order": [
                [0, "asc"]
            ] // set first column as a default sort by asc
        });
        
        table.find('.group-checkable').change(function () {
            var set = jQuery(this).attr("data-set");
            var checked = jQuery(this).is(":checked");
            jQuery(set).each(function () {
                if (checked) {
                    $(this).prop("checked", true);
                    $(this).parents('tr').addClass("active");
                } else {
                    $(this).prop("checked", false);
                    $(this).parents('tr').removeClass("active");
                }
            });
            check_users(); 
        });        
        
        table.on('change', 'tbody tr .checkboxes', function () {
            check_users();             
            $(this).parents('tr').toggleClass("active");
        });
        
        //personalized functions
        var check_users = function(){
            var set = $('.group-checkable').attr("data-set");
            var checked = $(set+"[type=checkbox]:checked").length;
            if(checked == 1)
            {
                $('#btn_users_edit').prop("disabled",false);
                $('#btn_users_remove').prop("disabled",false);
            }
            else if(checked > 1)
            {
                $('#btn_users_edit').prop("disabled",true);
                $('#btn_users_remove').prop("disabled",false);
            }
            else
            {
                $('#btn_users_edit').prop("disabled",true);
                $('#btn_users_remove').prop("disabled",true);
            }
        } 
        $('#btn_users_add').on('click', function(ev) {
            $('#modal_users_update_title').html('Add User');
            $('#modal_users_update').modal('show');
        });
        $('#btn_users_edit').on('click', function(ev) {
            $('#modal_users_update_title').html('Edit User');
            $('#modal_users_update').modal('show');
        });
        $('#btn_users_remove').on('click', function(ev) {
            $('#modal_users_update').modal('show');
        });
        //init functions
        check_users();
        
    }
    return {
        //main function to initiate the module
        init: function () {
            if (!jQuery().dataTable) {
                return;
            }
            initTable('tb_users');        
        }
    };
}();
//*****************************************************************************************
var FormValidation = function () {
    // advance validation
    var handleValidation = function(form_id) {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation

            var form = $('#'+form_id);
            var error3 = $('.alert-danger', form);
            var success3 = $('.alert-success', form);

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
                    first_name: {
                        minlength: 2,
                        required: true
                    },
                    last_name: {
                        minlength: 2,
                        required: true
                    },
                    email: {
                        required: true,
                        email: true
                    },  
                    password: {
                        minlength: 5,
                        required: true
                    },
                    address: {
                        minlength: 5,
                        required: true
                    },
                    city: {
                        minlength: 2,
                        required: true
                    },
                    state: {
                        minlength: 2,
                        maxlength: 2,
                        required: true
                    },
                    zip: {
                        minlength: 5,
                        maxlength: 5,
                        required: true
                    },
                    phone: {
                        minlength: 10,
                        maxlength: 10,
                        required: true
                    }
                },

                messages: { // custom messages for radio buttons and checkboxes
                    membership: {
                        required: "Please select a Membership type"
                    },
                    service: {
                        required: "Please select  at least 2 types of Service",
                        minlength: jQuery.validator.format("Please select  at least {0} types of Service")
                    }
                },

                errorPlacement: function (error, element) { // render error placement for each input type
                    if (element.parents('.mt-radio-list') || element.parents('.mt-checkbox-list')) {
                        if (element.parents('.mt-radio-list')[0]) {
                            error.appendTo(element.parents('.mt-radio-list')[0]);
                        }
                        if (element.parents('.mt-checkbox-list')[0]) {
                            error.appendTo(element.parents('.mt-checkbox-list')[0]);
                        }
                    } else if (element.parents('.mt-radio-inline') || element.parents('.mt-checkbox-inline')) {
                        if (element.parents('.mt-radio-inline')[0]) {
                            error.appendTo(element.parents('.mt-radio-inline')[0]);
                        }
                        if (element.parents('.mt-checkbox-inline')[0]) {
                            error.appendTo(element.parents('.mt-checkbox-inline')[0]);
                        }
                    } else if (element.parent(".input-group").size() > 0) {
                        error.insertAfter(element.parent(".input-group"));
                    } else if (element.attr("data-error-container")) { 
                        error.appendTo(element.attr("data-error-container"));
                    } else {
                        error.insertAfter(element); // for other inputs, just perform default behavior
                    }
                },

                invalidHandler: function (event, validator) { //display error alert on form submit   
                    success3.hide();
                    error3.show();
                    App.scrollTo(error3, -200);
                },

                highlight: function (element) { // hightlight error inputs
                   $(element)
                        .closest('.form-group').addClass('has-error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change done by hightlight
                    $(element)
                        .closest('.form-group').removeClass('has-error'); // set error class to the control group
                },

                success: function (label) {
                    label
                        .closest('.form-group').removeClass('has-error'); // set success class to the control group
                },

                submitHandler: function (form) {
                    success3.show();
                    error3.hide();
                    form[0].submit(); // submit the form
                }

            });

             //apply validation on select2 dropdown value change, this only needed for chosen dropdown integration.
            $('.select2me', form).change(function () {
                form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
            });
    }
    return {
        //main function to initiate the module
        init: function () {
            handleValidation('form_users');
        }
    };
}();
//***********************************************************************
jQuery(document).ready(function() {
    TableDatatablesManaged.init();
    FormValidation.init();
});