document.addEventListener('DOMContentLoaded', function () {
    var $j = jQuery;
    var ajaxurl = window.location.origin + '/wp-admin/admin-ajax.php';

    var ajaxInProgress = false;

    $j('.cmma-work-categories-list.left > div').on('click', function () {
        handleCategoryClick($j(this), 'left');
    });

    $j('.cmma-work-categories-list.right > div').on('click', function () {
        handleCategoryClick($j(this), 'right');
    });

    function handleCategoryClick($element, list) {
        page = 1;
        $j('.cmma-work-list').empty();
        $j('.loader').show();
        $j('.cmma-work-categories-list.' + list + ' > div').removeClass('active');

        // if (list === 'right') {
        //     $j('.cmma-work-categories-list.left > div').removeClass('active');
        // }

        $element.addClass('active');
        $j('.cmma-work-list').hide();
        $j('#load-more-posts').hide();

        let marketId = $j('.cmma-work-categories-list.left>div.active').data('category-id');
        let postType = $j('.cmma-work-categories-list.right>div.active').data('category-id');
        var defaultPosts = $element.data('default-posts');
        var data = {
            market_id : marketId,
            post_type : postType,
            default_posts : defaultPosts,
			action: 'cmma_elementor_widgets_our_work_page_filter_posts',
        };
        $j.post(ajaxurl, data, function (response) {
            setTimeout(function () {
                $j('.cmma-work-list').html(response);
                $j('.loader').hide();
                $j('.cmma-work-list').show();
                ajaxInProgress = false;
                var postCount = $j('.cmma-work').length;
                if (postCount >= 24) {
                    $j('#load-more-posts').show();
                }
                $j('.cmma-work-categories-list > div').css('pointer-events', '');
            }, 1000);
        });
    }

    var page = 1;
    $j('#load-more-posts').on('click', function () {
        var $loader = $j(this).siblings('.load-more-loader');
        $loader.show();
        $j('#load-more-posts').hide();

		page++;

		var defaultPosts = $j(this).data('default-posts');
        let marketId = $j('.cmma-work-categories-list.left>div.active').data('category-id');
        let postType = $j('.cmma-work-categories-list.right>div.active').data('category-id');

        var data = {
            page: page,
            market_id : marketId,
            post_type : postType,
            default_posts: defaultPosts,
            action: 'cmma_elementor_widgets_content_listing_load_more_posts',
        };
        $j.post(ajaxurl, data, function (response) {
            if (response) {
                setTimeout(function () {
                    $loader.hide();
                    if (response !== 'no-posts') {
                        $j('.cmma-work:last').after(response);
                        $j('#load-more-posts').data('page', page);
                        checkLoadMoreVisibility();
                    } else {
                        $j('#load-more-posts').hide();
                    }
                }, 1000);
            }
        });
    });

    function checkLoadMoreVisibility() {
        var $loadMoreButton = $j('#load-more-posts');
        var $posts = $j('.cmma-work');
        if ($posts.length < 1) {
            $loadMoreButton.hide();
        } else {
            var fetchedPostsCount = $j('.cmma-work-list .cmma-work').length;
            var requestedPostsCount = parseInt($j('#load-more-posts').data('page')) * 24;
            if (fetchedPostsCount < requestedPostsCount) {
                $loadMoreButton.hide();
            } else {
                $loadMoreButton.show();
            }
        }
    }

	// Listen for click events on elements with the class '.cmma-work'
	$j(document).on('click','.cmma-work',function(){
		var keyId = $j(this).data('key-id');
		var mainElement = elementor.panel.$el[0].querySelector('main');

		let posts = mainElement.querySelectorAll(`.elementor-control-selected_post .elementor-repeater-fields .elementor-repeater-row-controls`);
		if (posts) {
			posts.forEach(element => {
				element.classList.remove('editable');
			});
		}

		try {
			mainElement.querySelector(`.elementor-control-selected_post .elementor-repeater-fields:nth-child(${keyId}) .elementor-repeater-row-controls`).classList.add('editable');
		} catch (error) {

		}
	});
});
