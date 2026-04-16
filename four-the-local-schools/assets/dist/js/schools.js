/**
* Package: 4TheLocalSchool - v1.0.0
* Description: Various customization for the site
* Last build: 2021-04-14
* @author Shafiq Hossain
* @license GPL-2.0+
*/
jQuery(function($) {
    $('body').on('change', '.acf-school-states select', function() {
        var stateid = $(this).val();
        if(stateid != '' && stateid != 'Select State') {
            var data = {
                'action': 'get_counties_by_state_by_ajax',
                'state': stateid,
                'security': school_counties.security
            }

            $.post(school_counties.ajaxurl, data, function(response) {
                if( response ){
                    // Disable 'Select County' field until state is selected
    				$('#acf-school-counties select').empty();
                    $( '#acf-school-counties select' ).html( $('<option></option>').val('0').html('Select County').attr({ selected: 'selected', disabled: 'disabled'}) );

                    var counties = response.data.split(',');

                    // Add counties to select field options
                    $.each(counties, function(val, text) {
                        $( '#acf-school-counties select' ).append( $('<option></option>').val(text).html(text) );
                    });
                };
            });
        }
    });
});

