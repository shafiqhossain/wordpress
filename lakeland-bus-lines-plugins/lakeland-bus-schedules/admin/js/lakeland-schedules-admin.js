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

	    $('.add-stop-schedule').on('click', function() {
	    	var obj;
	    	obj = $('.stop-schedule:first').clone();
	    	obj.find('td:first').append('<span class="remove-stop-schedule">X</span>');
	    	var cnt;

	    	cnt = $('.stop-schedule').length+1;
	    	obj.find('input.s-time').attr('name', 's_time[' +  cnt + '][]');
	    	obj.find('input.s-time').val('');
	    	obj.find('input.s-name').attr('name', 's_name[' +  cnt + ']');
	    	obj.find('input.s-name').val('');
	    	obj.find('input.s-schedule').val('00:00');
	    	obj.find('input.s-schedule').attr('name', 'schedule_detail[' +  cnt + ']');
	    	$(obj).insertAfter($('.stop-schedule:last'));
	    })

	    $('.add-label-schedule').on('click', function() {

	    	var obj;
	    	obj = $('.stop-label-schedule-template').clone();
	    	obj.addClass('stop-schedule');
	    	obj.removeClass('stop-label-schedule-template');
	    	obj.find('td:first').css('padding', '7px 0 0 50px').append('<span class="remove-stop-schedule">X</span>');
	    	var cnt;
	    	cnt = $('.stop-schedule').length+1;
	    	obj.find('input.s-label-caption').attr('name', 's_name[' +  cnt + ']');
	    	obj.find('input.s-label-caption').val('-lbl-');
	    	obj.find('input.s-label').attr('name', 's_time[' +  cnt + '][]');
	    	$(obj).insertAfter($('.stop-schedule:last'));
	    })

	    $('body').on('click', '.remove-stop-schedule', function() {
	    	$(this).parent().parent().remove();
	    	//need to implement ajax for deletion
	    	if(jQuery(this).attr('data-id')) {
	    		  jQuery.ajax({
						type: "GET",
						url: "/wp-admin/admin-ajax.php",
						dataType: 'html',
						beforeSend: function() {
							$('.schedule-list-container .loader').removeClass('display-none');
						},
						data:
							{
								action: 'schedule_delete',
								id: jQuery(this).attr('data-id')
							}
						,
						success: function(data){


							 $('.schedule-list-container .loader').addClass('display-none');
							 //window.location.href = data;
						}

					});
	    	}

	    })

 		$('.stop-schedule-container').scroll(function(){
 		  var element_offset;
 		  var window_left_scroll;
		  element_offset = $('.lakeland-col-name').offset();
		  window_left_scroll = $('.stop-schedule-container').scrollLeft();

		  $('.lakeland-col-name').css('left',   (window_left_scroll) +'px').css('position', 'relative').css('z-index', '9999');
		});

	 })




})( jQuery );
