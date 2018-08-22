var SwipeFunctions = function () {

    var initFunctions = function () {
        
        //event to check swipe
        function valid_swipe_credit_card(card_data)
        {
            $('#swipe_msg').html('Processing data.');
            var card_tracks = card_data.split("?");
            var valid_track1 = /^%B[^\^\W]{0,19}\^[^\^]{2,26}\^\d{4}\w{3}[^?]+\?\w?$/.test(card_tracks[0]+'?');
            var valid_track2 = /;[^=]{0,19}=\d{4}\w{3}[^?]+\?\w?/.test(card_tracks[1]+'?');
            if(valid_track1 || valid_track2)
            {
                var details1 = card_data.split("^");
                var card_number = details1[0];
                card_number = card_number.substring(2);
                if(details1[1].trim()=='')
                {
                    $('#swipe_msg').html('Data processed: That credit card has no client name on it.');
                    return false;
                }
                var names = details1[1].split("/");
                var first_name = names[1].trim();
                var last_name = names[0].trim();
                var details2 = details1[2].split(";");
                details2 = details2[1].split("=");
                var exp_date = details2[1];
                exp_date = exp_date.substring(0, exp_date.length - 1);
                var month = exp_date.substring(2, 4);
                var year = exp_date.substring(0, 2);
                $('#tab_swipe input[name="UMmagstripe"]').val(card_data);
                $('#tab_swipe input[name="customer"]').val(first_name + ' ' + last_name);
                $('#tab_swipe input[name="card"]').val(card_number);
                $('#tab_swipe input[name="month"]').val(month);
                $('#tab_swipe input[name="year"]').val(year);
                $('#swipe_msg').html('Card processed correctly.');
                return true;
            }
            else {
                $('#swipe_msg').html('Card swipe did not work. Please, try the card manually.');
                return false;
            }
        };

        //process data
        function readCard(rawData) {                    
            if($('#modal_swipe_card').is(':visible'))
            {
                $('#swipe_msg').html('Data read.');
                var cardData = valid_swipe_credit_card(rawData);
                if(cardData)
                    $('#modal_swipe_card').modal('hide');                        
                return cardData;
            }
        };   

        // Initialize the plugin to swipe card
        $.cardswipe({
            parser: readCard
        });

        $('a[href="#tab_swipe"]').on('click', function(ev) {
            //reset form here too
            $('#swipe_msg').html('Swipe the card now.');
            $('#modal_swipe_card').modal('show');
                     
            
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
var SwipeValidation = function () {
    return {
        //main function to initiate the module
        init: function () {
            // advance validation
            var rules = {
                email: {
                    minlength: 8,
                    maxlength: 200,
                    email: true,
                    required: true
                },
                customer: {
                    minlength: 2,
                    maxlength: 100,
                    required: true
                },
                phone: {
                    minlength: 10,
                    maxlength: 10,
                    digits: true,
                    required: false
                },
                card: {
                    minlength: 16,
                    maxlength: 16,
                    creditcard: true,
                    digits: true,
                    required: true
                },
                month: {
                    range: [1,12],
                    digits: true,
                    required: true
                },
                year: {
                    minlength: 2,
                    maxlength: 4,
                    digits: true,
                    required: true
                },
                UMmagstripe: {
                    required: true
                }
            };
            MainFormValidation.init('form_swipe',rules,{});
        }
    };
}();
//*****************************************************************************************
jQuery(document).ready(function() {
    SwipeFunctions.init();
    SwipeValidation.init();
});
