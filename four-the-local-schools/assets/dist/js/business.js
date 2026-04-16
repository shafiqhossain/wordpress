/**
* Package: 4TheLocalSchool - v1.0.0
* Description: Various customization for the site
* Last build: 2021-04-14
* @author Shafiq Hossain
* @license GPL-2.0+
*/
jQuery(function($) {
    $('body').on('change', '.acf-business-states select', function() {
        var stateid = $(this).val();
        if(stateid != '' && stateid != 'Select State') {
            var data = {
                'action': 'get_counties_by_state_by_ajax',
                'state': stateid,
                'security': business_counties.security
            }

            $.post(business_counties.ajaxurl, data, function(response) {
                if( response ){
                    // Disable 'Select County' field until country is selected
    				$('#acf-business-counties select').empty();
                    $( '#acf-business-counties select' ).html( $('<option></option>').val('0').html('Select County').attr({ selected: 'selected', disabled: 'disabled'}) );

                    var counties = response.data.split(',');

                    // Add counties to select field options
                    $.each(counties, function(val, text) {
                        $( '#acf-business-counties select' ).append( $('<option></option>').val(text).html(text) );
                    });
                };


            });
        }
    });
});

