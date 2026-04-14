document.addEventListener('DOMContentLoaded', function () {

	var $j = jQuery;
	var ajaxurl = window.location.origin + '/wp-admin/admin-ajax.php';
	var page = 2;

	$j('#search-input').on('input', function () {
		$j('#load-more-posts').hide();
		$j('.cmma-member-no-results').hide();
		var searchQuery = $j(this).val();
		var categoryId = $j('.cmma-categories-list > .active').data('category-id');

		var data = {
			action: 'cmma_elementor_widgets_our_people_page_search',
			search_query: searchQuery,
			category_id: categoryId
		};

		$j.post(ajaxurl, data, function (response) {
			let postCount = $j(response).filter('div.cmma-post').length;
			console.log('postCountpostCount', postCount);
			if (postCount) {
				$j('.cmma-member-list').empty();
				$j('.cmma-member-list').append(response);
				$j('.cmma-member-list').show();
			} else {
				$j('.cmma-member-list').hide();
				$j('.cmma-member-no-results').show();
			}
		});
	});

	$j('.cmma-categories-list > div').on('click', function () {
		$j('.loader').show();
		$j('#search-input').val('');
		$j('.cmma-categories-list > div').removeClass('active');
		$j(this).addClass('active');
		$j('.cmma-member-list').hide();
		$j('#load-more-posts').hide();

		page = 1;

		var categoryId = $j(this).data('category-id');
		if (categoryId == 'all') {
			categoryId = 'view-all'
		}
		var videos = $j('.cmma-member-list').data('videos');
		var data = {
			'action': 'cmma_elementor_widgets_our_people_posts',
			'category_id': categoryId,
			'videos': videos,
			'page': page,
		};
		$j.post(ajaxurl, data, function (response) {
			page++;
			setTimeout(function () {
				$j('.cmma-post').remove();
				$j('.cmma-member-list').empty();
				$j('.cmma-member-list').append(response);
				$j('.loader').hide();
				$j('.cmma-member-list').show();
				var postCount = $j('.cmma-post').length;
				if (postCount >= 24 && categoryId != 'all') {
					$j('#load-more-posts').show();
				}
			}, 1000);
		});
	});

	$j('#load-more-posts').on('click', function () {
		$j('.load-more-loader').show();
		$j('#load-more-posts').hide();
		var categoryId = $j('.cmma-categories-list > .active').data('category-id');

		var data = {
			page,
			'action': 'cmma_elementor_widgets_our_people_posts',
			'category_id': categoryId
		};

		$j.post(ajaxurl, data, function (response) {
			if (response) {
				setTimeout(function () {
					$j('.load-more-loader').hide();
					$j('.cmma-post:last').after(response);
					page++;
					let postCount = $j(response).filter('div.cmma-post').length;
					if (postCount >= 24) {
						$j('#load-more-posts').show();
					}
				}, 1000);
			}
		});
	});

	var $carousel = $j('.cmma-people-slider-wrapper');
	if ($carousel.length) {
		$carousel.slick();
	}
});
