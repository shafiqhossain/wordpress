(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
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

	$(document).on('change', '.is-member-filter', function() {
		var isMemberFilter = $(this).val();
		document.location.href = 'admin.php?page=fac-directory/profiles'+isMemberFilter;
	});

	$(document).on('change', '.county-type-filter', function() {
		var countyTypeFilter = $(this).val();
		document.location.href = 'admin.php?page=fac-directory/profiles'+countyTypeFilter;
	});

	$(document).on('change', '.member-type-filter', function() {
		var memberTypeFilter = $(this).val();
		document.location.href = 'admin.php?page=fac-directory/profiles'+memberTypeFilter;
	});

	$(document).on('change', '.is-approved-filter', function() {
		var isApprovedFilter = $(this).val();
		document.location.href = 'admin.php?page=fac-directory/profiles'+isApprovedFilter;
	});

	$(document).on('change', '.is-suspended-filter', function() {
		var isSuspendedFilter = $(this).val();
		document.location.href = 'admin.php?page=fac-directory/profiles'+isSuspendedFilter;
	});

})( jQuery );
