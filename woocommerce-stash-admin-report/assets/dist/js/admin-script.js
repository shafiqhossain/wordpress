/**
* Package: Stash Stock
*/
jQuery( document ).ready(function($) {
	$(document).on('change', '.woo-stash-filter-warehouse', function() {
		var warehouseFilter = $(this).val();
		document.location.href = 'admin.php?page=woo-stash'+warehouseFilter;
	});

	$(document).on('change', '.woo-stash-filter-stock-type', function() {
		var stockTypeFilter = $(this).val();
		document.location.href = 'admin.php?page=woo-stash'+stockTypeFilter;
	});

});


