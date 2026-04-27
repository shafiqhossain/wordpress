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
		 $('.tour-title a ').on('click', function() {

			  jQuery.ajax({
				type: "GET",
				url: "/wp-admin/admin-ajax.php",
				dataType: 'html',
				beforeSend: function() {
					$('.tour-list-container .loader').removeClass('display-none');
				},
				data: 
					{
						action: 'loadMore', 
						id: jQuery(this).attr('data-id')
					}
				,
				success: function(data){  
					$('.tour-img-container').remove();
					$('.tour-list-container').slideUp('slow');
					$(data).insertAfter('.tour-list-container').show();
					$('.tour-list-container .loader').addClass('display-none');
				}

			}); 
		}); 
		
	  
		 $('body').on('click', '.back-to-listing', function() {

		 	   $('.tour-detail-popup').hide('fast');
		 	   $('.tour-list-container').show('slow');
		});  
	});
})( jQuery );
