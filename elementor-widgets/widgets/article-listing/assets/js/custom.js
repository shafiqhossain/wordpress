// Execute when DOM content is loaded
document.addEventListener('DOMContentLoaded', function () {
	var $j = jQuery;
	var ajaxurl = window.location.origin + '/wp-admin/admin-ajax.php';
	var page = 1;

	// Event listener for input changes in search input field
	$j('#search-input').on('input', function () {
		var searchQuery = $j(this).val();
		var categoryId = $j('.cmma-categories-list > .active').data('category-id');

		// Data to be sent via AJAX
		var data = {
			action: 'cmma_elementor_widgets_landing_page_search',
			search_query: searchQuery,
			category_id: categoryId
		};

		// AJAX post request
		$j.post(ajaxurl, data, function (response) {
			$j('.cmma-post-list').html(response.html);
			checkLoadMoreVisibility(response.post_count)
		});
	});

	// Event listener for clicking on category
	$j('.cmma-categories-list > div').on('click', function () {
		$j('.loader').show();
		$j('#search-input').val('');
		$j('.cmma-categories-list > div').removeClass('active');
		$j(this).addClass('active');
		$j('.cmma-post-list').hide();
		$j('.load-more-posts').hide();

		// Reset page number
		page = 1;

		var categoryId = $j(this).data('category-id');
		var data = {
			'action': 'cmma_elementor_widgets_landing_page_filter_posts',
			'category_id': categoryId,
		};

		// AJAX post request with delay
		$j.post(ajaxurl, data, function (response) {
			$j('.loader').hide();
			$j('.cmma-post-list').show();
			$j('.cmma-post-list').html(response.html);
			checkLoadMoreVisibility(response.post_count)
		});
	});

	// Event listener for clicking on load more button
	$j('.load-more-posts').on('click', function () {
		$j('.load-more-loader').show();
		$j('.load-more-posts').hide();
		var categoryId = $j('.categories-list > .active').data('category-id');
		page++;

		var data = {
			page,
			'action': 'cmma_elementor_widgets_landing_page_load_posts',
			'category_id': categoryId
		};

		// AJAX post request with delay
		$j.post(ajaxurl, data, function (response) {
			if (response) {
				setTimeout(function () {
					$j('.load-more-loader').hide();
					$j('.cmma-post:last').after(response.html);
					checkLoadMoreVisibility(response.post_count)
				}, 1000);
			}
		});
	});

	// Function to check load more button visibility
	function checkLoadMoreVisibility(post_count) {
		if (post_count < 8) {
			$j('.load-more-posts').hide();
		} else {
			$j('.load-more-posts').show();
		}
	}
});
