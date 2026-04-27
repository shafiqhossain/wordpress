(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	 $(document).ready(function() {
	 	$('body').on('click', '.lakeland-import-contact-btn', function() {
			$('#lakeland_file_frame').contents().find("#lakeland_files").trigger('click');
			$('#lakeland_file_frame').contents().find("#lakeland_files").removeClass('inline');
		});

		$("#lakeland_files").on('change', function() {
			if(!$(this).hasClass('inline')) {
				$('#submit').trigger('click');
				window.parent.location.reload();
			}
		})

		$( "#from_date" ).datepicker({
	      defaultDate: "+1w",
	      changeMonth: true,
	      dateFormat : 'yy-mm-dd',
	      numberOfMonths: 1,
	      onClose: function( selectedDate ) {
	        $( "#to" ).datepicker( "option", "minDate", selectedDate, 'dateFormat', 'yy-mm-dd' );
	      }
	    });

	    $( "#to_date" ).datepicker({
	      defaultDate: "+1w",
	      changeMonth: true,
	      dateFormat : 'yy-mm-dd',
	      numberOfMonths: 1,
	      onClose: function( selectedDate ) {
	        $( "#from" ).datepicker( "option", "maxDate", selectedDate );
	      }
	    });

	    $( "#expiry" ).datepicker({
	      changeMonth: true,
	      dateFormat : 'yy-mm-dd'
	    });

	     $( ".trip-date" ).datepicker({
	      changeMonth: true,
	      dateFormat : 'yy-mm-dd'
	    });

	    $('.lakeland-add-more-img').on('click', function() {
	    	var obj;
	    	obj = $('.trip-tmpl-img').clone();
	    	//console.log( $('.trip-tmpl-img').clone());
	    	obj.removeClass('trip-tmpl-img');
	    	obj.find('td').append('<span class="lakeland-tour-remove-img">X</span>');
	    	obj.find('th').html('');
	    	var cnt;
	    	obj.find('input').val('');
	    	cnt = obj.attr('data-img-cnt');
	    	cnt = $('.lakeland-img-row').length+1;
	    	obj.attr('data-img-cnt', cnt);
	    	obj.find('.images-1').attr('name', 'tour_detail[' + cnt + '][images]');
	    	$(obj).insertBefore($('.trip-tmpl-img'));
	    })

	    $('.lakeland-add-more-time').on('click', function() {
	    	var obj, cnt;
	    	obj = $('.lakeland-tmpl-time-location').clone();
	    	//console.log( $('.trip-tmpl-img').clone());
	    	obj.removeClass('lakeland-tmpl-time-location');
	    	obj.find('td:last').append('<span class="lakeland-tour-remove-location">X</span>');
	    	obj.find('th').html('');
	    	obj.find('.no-format').remove();

	    	obj.find('.dept-header').remove();
	    	cnt = $('.lakeland-dept-row-container').length+1;
	    	obj.attr('data-location-cnt', cnt);
	    	obj.find('.dept-location-id').attr('name', 'dept_location['+ cnt +'][id]');
	    	obj.find('.dept-location-name').attr('name', 'dept_location['+ cnt +'][name]');
	    	obj.find('.dept-location-hh').attr('name', 'dept_location['+ cnt +'][dept_hh]');
	    	obj.find('.dept-location-mm').attr('name', 'dept_location['+ cnt +'][dept_mm]');
	    	obj.find('.dept-location-am').attr('name', 'dept_location['+ cnt +'][dept_am]');
	    	obj.find('.dept-location-r-name').attr('name', 'dept_location['+ cnt +'][return_name]');
	    	obj.find('.dept-location-r-hh').attr('name', 'dept_location['+ cnt +'][return_hh]');
	    	obj.find('.dept-location-r-mm').attr('name', 'dept_location['+ cnt +'][return_mm]');
	    	obj.find('.dept-location-r-am').attr('name', 'dept_location['+ cnt +'][return_am]');

	    	$(obj).insertBefore($('.lakeland-add-more-time').parent().parent());
	    })

	    $('.lakeland-add-more-itinerary').on('click', function() {
	    	var obj;
	    	obj = $('.lakeland-tmpl-itinerary').clone();
	    	//console.log( $('.trip-tmpl-img').clone());
	    	obj.removeClass('lakeland-tmpl-itinerary');
	    	obj.find('.no-format').remove();
	    	obj.find('th').html('');
	    	obj.find('.dept-header').remove();

	    	var cnt;
	    	cnt = $('.lakeland-trip-row-container').length+1;;

	    	obj.attr('data-itinerary-cnt', cnt);
	    	obj.find('.trip-date').attr('name', 'trips['+ cnt +'][date]');
	    	obj.find('.trip-return-hh').attr('name', 'trips['+ cnt +'][return_hh]');
	    	obj.find('.trip-return-mm').attr('name', 'trips['+ cnt +'][return_mm]');
	    	obj.find('.trips_return_am').attr('name', 'trips['+ cnt +'][return_am]');
	    	obj.find('.trip-return-am').attr('name', 'trips['+ cnt +'][dept_mm]');
	    	obj.find('.trip-return-hh-to').attr('name', 'trips['+ cnt +'][return_hh_to]');
	    	obj.find('.trip-return-mm-to').attr('name', 'trips['+ cnt +'][return_mm_to]');
	    	obj.find('.trip-return-am-to').attr('name', 'trips['+ cnt +'][return_am_to]');
	    	obj.find('.trip-title').attr('name', 'trips['+ cnt +'][title]');
	    	obj.find('.trip-description').attr('name', 'trips['+ cnt +'][description]');
	    	obj.removeClass('hasDatepicker');
	    	 $( obj).datepicker({
		      changeMonth: true,
		      dateFormat : 'yy-mm-dd'
		    });
	    	$(obj).insertBefore($('.lakeland-add-more-itinerary').parent().parent());
	    })

	    $('body').on('click', '.lakeland-tour-remove-img', function() {
	    	$(this).parent().parent().remove();
	    	//need to implement ajax for deletion
	    })

	    $('body').on('click', '.lakeland-tour-remove-itinerary', function() {
	    	$(this).parent().parent().remove();
	    	//need to implement ajax for deletion
	    })

	    $('body').on('click', '.lakeland-tour-remove-location', function() {
	    	$(this).parent().parent().remove();
	    	//need to implement ajax for deletion
	    })

		$("form[name='lakeland_contact_edit']" ).on('change', "input[name='tour_detail[tour_id]']", function() {
			var title = $("form[name='lakeland_contact_edit'] input[name='tour_detail[tour_id] option:selected").text();
			$("form[name='lakeland_contact_edit'] input[name='tour_detail[title]']" ).text(title);
		});

	 })




})( jQuery );
