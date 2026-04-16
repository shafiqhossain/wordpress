/**
* Package: 4TheLocalSchool - v1.0.0
* Description: Various customization for the site
* Last build: 2021-04-14
* @author Shafiq Hossain
* @license GPL-2.0+
*/
jQuery(function($) {
    // Add default 'Select one'
    //$( '#acf-subscription-states select' ).prepend( $('<option></option>').val('0').html('Select State').attr({ selected: 'selected', disabled: 'disabled'}) );
    //$( '#acf-subscription-states select' ).removeAttr( 'disabled' );

	//load counties by state
    $('body').on('change', '.acf-subscription-states select', function() {
		//clear previous schools list
    	$('#acf-subscription-schools select').empty();
    	$( '#acf-subscription-schools select' ).prepend( $('<option></option>').val('').html('Select School').attr({ selected: 'selected', disabled: 'disabled'}) );
    	//clear previous boosters list
		$( '#acf-subscription-boosters .acf-checkbox-list' ).html('');
		//clear previous students list
		$( '#acf-subscription-students .acf-checkbox-list' ).html('');

        var stateid = $(this).val();
        if(stateid != '' && stateid != 'Select State') {
            var data = {
                'action': 'get_counties_by_state_by_ajax',
                'state': stateid,
                'security': subscription_counties.security
            }

            $.post(subscription_counties.ajaxurl, data, function(response) {
                if( response ){
                    // Disable 'Select County' field until country is selected
    				$('#acf-subscription-counties select').empty();
                    $('#acf-subscription-counties select' ).html( $('<option></option>').val('0').html('Select County').attr({ selected: 'selected', disabled: 'disabled'}) );

                    var counties = response.data.split(',');

                    // Add counties to select field options
                    $.each(counties, function(val, text) {
                        $( '#acf-subscription-counties select' ).append( $('<option></option>').val(text).html(text) );
                    });
                };
            });
        }
    });

	//load schools by state and county
    $('body').on('change', '.acf-subscription-counties select', function() {
    	//clear previous boosters list
		$( '#acf-subscription-boosters .acf-checkbox-list' ).html('');
		//clear previous students list
		$( '#acf-subscription-students .acf-checkbox-list' ).html('');

        var stateid = $('#acf-subscription-states select').val();
        var countyname = $(this).val();
        if(countyname != '' && countyname != 'Select County') {
            var data = {
                'action': 'get_schools_by_county_by_ajax',
                'state': stateid,
                'county': countyname,
                'security': subscription_schools.security
            }

            $.post(subscription_schools.ajaxurl, data, function(response) {
                if( response ){
                    // Disable 'Select School' field until country is selected
    				$('#acf-subscription-schools select').empty();
                    $('#acf-subscription-schools select' ).html( $('<option></option>').val('0').html('Select School').attr({ selected: 'selected', disabled: 'disabled'}) );

                    var schools = response.data;

                    // Add schools to select field options
                    $.each(schools, function(val, text) {
                        $( '#acf-subscription-schools select' ).append( $('<option></option>').val(text).html(text) );
                    });

                };

            });
        }
    });

	//load boosters by state, county and school
    $('body').on('change', '.acf-subscription-schools select', function() {
		//clear previous students list
		$( '#acf-subscription-students .acf-checkbox-list' ).html('');

        var stateid = $('#acf-subscription-states select').val();
        var countyname = $('#acf-subscription-counties select').val();
        var schoolname = $(this).val();
        if(schoolname != '' && schoolname != 'Select School') {
            var data = {
                'action': 'get_boosters_by_schools_by_ajax',
                'state': stateid,
                'county': countyname,
                'school': schoolname,
                'security': subscription_boosters.security
            }

            $.post(subscription_boosters.ajaxurl, data, function(response) {
                if( response ){
                    // clear the content
                    $( '#acf-subscription-boosters .acf-checkbox-list' ).html('');

                    var boosters = response.data;

                    // Add boosters to select field options
   					var count = 0;
                    $.each(boosters, function(val, text) {
                        $( '#acf-subscription-boosters .acf-checkbox-list' ).append( $('<li><label><input type="checkbox" id="acf-field_60744d6f070c8-'+count+'" name="acf[field_60744d6f070c8][]" value="'+text+'"> '+text+'</label></li>'));
                        count++;
                    });
                };
            });
        }
    });

	//load students by state, county, school and boosters
    $('body').on('change', '.acf-subscription-boosters input[type=checkbox]', function() {
        var stateid = $('#acf-subscription-states select').val();
        var countyname = $('#acf-subscription-counties select').val();
        var schoolname = $('#acf-subscription-schools select').val();
        var boosternames = [];

		var i = 0;
		$('input[name="acf[field_60744d6f070c8][]"]:checked').each(function() {
          boosternames[i] = $(this).val();
          i++;
        });
        var boosternames_values = boosternames.join('||');

        if(boosternames_values != '') {
            var data = {
                'action': 'get_students_by_boosters_by_ajax',
                'state': stateid,
                'county': countyname,
                'school': schoolname,
                'boosters': boosternames_values,
                'security': subscription_students.security
            }

            $.post(subscription_students.ajaxurl, data, function(response) {
                if( response ){
                    // clear the content
                    $( '#acf-subscription-students .acf-checkbox-list' ).html('');

                    // Add boosters to select field options
   					var count = 0;
                    $.each(response.data, function(val, text) {
                        $( '#acf-subscription-students .acf-checkbox-list' ).append( $('<li><label><input type="checkbox" id="acf-field_60744d81070c9-'+count+'" name="acf[field_60744d81070c9][]" value="'+text+'"> '+text+'</label></li>'));
                        count++;
                    });
                };
            });
        }
        else {
		  // clear the content
		  $( '#acf-subscription-students .acf-checkbox-list' ).html('');
		}
    });
});
