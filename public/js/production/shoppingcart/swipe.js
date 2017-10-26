var SwipeCardFunctions = function () {
    
    var initFunctions = function () {
        
        //on click swipe card
        $('a[href="#tab_swipe"]').on('click', function(ev) {
            //reset form here too
            $('#modal_swipe_card').modal('show');
            $('#modal_swipe_card input[name="stripe_card"]').val('');
            $('#modal_swipe_card input[name="stripe_card"]').focus();
        });
        //on modal swipe card on click
        $('#modal_swipe_card').on('click', function(ev) {
            $('#modal_swipe_card input[name="stripe_card"]').val('');
            $('#modal_swipe_card input[name="stripe_card"]').focus();
        });
        //swipe card
        $('#modal_swipe_card input[name="stripe_card"]').blur(function (e) {
            e.preventDefault();
            $('#modal_swipe_card').modal('hide');
            $('#tab_swipe input[name="customer"]').focus();
        }).keyup(function (e) {
            if($(this).val().substr($(this).val().length-1)=="?") 
            {
                if(valid_swipe_credit_card($(this).val()))
                {
                    $('#modal_swipe_card').modal('hide');
                    $('#tab_swipe input[name="customer"]').focus();
                }
            }
        });
        //event to check swipe
        function valid_swipe_credit_card(card_data)
        {
            var card_tracks = card_data.split("?");
            var valid_track1 = /^%B[^\^\W]{0,19}\^[^\^]{2,26}\^\d{4}\w{3}[^?]+\?\w?$/.test(card_tracks[0]+'?');
            var valid_track2 = /;[^=]{0,19}=\d{4}\w{3}[^?]+\?\w?/.test(card_tracks[1]+'?');
            if(valid_track1 && valid_track2)
            {
                var details1 = card_data.split("^");
                var card_number = details1[0];
                card_number = card_number.substring(2);
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
                return true;
            }
            else {
                alert('Could not be correctly read the card.');
                return false;
            }
        }
        
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
    SwipeCardFunctions.init();
});