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
	$(document).on('click', '#search-keyword-button', function(event) {
		event.preventDefault();

		var keyword = $('#search-filter-keyword').val();
		if (!keyword) {
		  return false;
		}

		var action = $('.search-keywords-wrapper').attr('action');
		document.location.href = action + '?keyword=' + keyword;
	});

	$(document).on('change', '.county-search-dropdown', function() {
		var county = $(this).val();
		if (!$(this).val()) {
			return false;
		}
		document.location.href = county;
	});

	$(document).on('change', '.job-search-dropdown', function() {
		var job = $(this).val();
		if (!$(this).val()) {
			return false;
		}
		document.location.href = job;
	});


})( jQuery );
