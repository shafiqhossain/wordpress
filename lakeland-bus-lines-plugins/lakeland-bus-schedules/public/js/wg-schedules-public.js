(function( $ ) {
//	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write $ code here, so the
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
		$('.get-route-detail').on('click', function() {
			  jQuery.ajax({
				type: "GET",
				url: "/wp-admin/admin-ajax.php",
				dataType: 'html',
				beforeSend: function() {
					$('.schedule-list-container .loader').removeClass('display-none');
				},
				data:
					{
						action: 'schedule',
						id: jQuery(this).attr('data-id')
					}
				,
				success: function(data){
					$('.schedule-list-container').slideUp('slow');

					$(data).insertAfter('.schedule-list-container').show();
					$('.stop-schedule-container').scroll(function(){
				 		  var element_offset;
				 		  var window_left_scroll;
				 		  console.log('abc');
						  element_offset = $('.lakeland-col-name').offset();
						  window_left_scroll = $('.stop-schedule-container').scrollLeft();
						  $('.lakeland-col-name').css('left',   (window_left_scroll) +'px').css('position', 'relative').css('z-index', '9999');
					});

					$( '.stop-schedule').toggle(function() {
							$('.stop-schedule').addClass('deactive');
							$(this).addClass('active');
							$(this).removeClass('deactive');
						},
						function() {
							$('.stop-schedule').removeClass('active');
							$('.stop-schedule').removeClass('deactive');
							$('.stop-schedule').addClass('active');
							$('.stop-schedule').show('slow');
						}
					);

					$('.schedule-list-container .loader').addClass('display-none');
				}

			});
		});

		$('.get-route-pdf').on('click', function() {

			  jQuery.ajax({
				type: "GET",
				url: "/wp-admin/admin-ajax.php",
				dataType: 'html',
				beforeSend: function() {
					$('.schedule-list-container .loader').removeClass('display-none');
				},
				data:
					{
						action: 'schedule_pdf',
						id: jQuery(this).attr('data-id')
					}
				,
				success: function(data){
					 $('.schedule-list-container .loader').addClass('display-none');
					 window.location.href = data;
				}

			});
		});

		 $('body').on('click', '.back-to-schedule', function() {
		 	   $('.schedule-detail-popup').hide('fast');
		 	   $('.schedule-list-container').show('slow');
		});

		 $('body').on('click', '.show-all-route', function() {
		 	$('.stop-schedule').show();

		 });

	});
})( jQuery );
