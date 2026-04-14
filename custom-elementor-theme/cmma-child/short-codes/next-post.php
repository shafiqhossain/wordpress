<?php

function smma_get_next_post_by_market($post_id) {
	$select_market_post = get_field('select_market_post', $post_id);
	if ($select_market_post && count($select_market_post)) {
		$select_market_post = array_column($select_market_post,'ID');

        $args = array(
            'posts_per_page' => -1,
			'fields' => 'ids',
            'post_type'      => 'project',
            'meta_query'     => array(
				array(
					'key'     	=> 'select_market_post',
					'value'   	=> $select_market_post[0],
					'compare'	=> 'LIKE',
				),
			)
        );

		// Fetch posts
		$next_posts = get_posts($args);
		// Return the first post found
		if (!empty($next_posts)) {
			$nextId = getNextPostId($next_posts, $post_id);
			if ($nextId) {
				return get_post($nextId);
			}
		}
	}
    return null;
}

function getNextPostId($postIds, $currentId) {
    $count = count($postIds);
    for ($i = 0; $i < $count; $i++) {
        if ($postIds[$i] == $currentId) {
            // Check if the current ID is the last in the array
            if ($i == $count - 1) {
                return $postIds[0]; // Return the first element
            } else {
                return $postIds[$i + 1]; // Return the next element
            }
        }
    }
    return null; // Return null if currentId is not found
}

function _footerNextPost() {
    ob_start();

    $post_id   = get_queried_object_id();
    $post_type = get_post_type($post_id);

    $next_post = get_next_post();
    if ($post_type == 'project') {
        $next_post = smma_get_next_post_by_market($post_id);
    }

    if (empty($next_post)) :
        $args = array(
            'posts_per_page' => 1,
            'post_type'      => $post_type,
            'order'          => 'DESC',
            'post__not_in'   => array($post_id),
        );
        $first_post = get_posts($args);
        if (!empty($first_post)) :
            $next_post = reset($first_post);
        endif;
    endif;

    if ($next_post) :
        $next_post_id         = $next_post->ID;
        $next_post_title      = $next_post->post_title;
        $next_post_image_id   = get_post_thumbnail_id($next_post_id);
        $next_post_permalink  = get_permalink($next_post_id);
        $image_info           = smma_elementor_widgets_get_responsive_image_data($next_post_image_id, 'full');
        ?>

        <div class="smma-next-project">
            <div class="smma-container">
                <?php if (isset($image_info) && !empty($image_info['srcset'])) : ?>
                    <div class="smma-next-project-panel">
                <?php else : ?>
                    <div class="smma-next-project-panel smma-without-img">
                <?php endif; ?>
                    <div class="smma-next-project-panel">
                        <?php if (isset($image_info) && !empty($image_info['srcset'])) : ?>
                            <div class="smma-next-project-img">
                                <img srcset="<?= esc_attr($image_info['srcset']) ?>" src="<?= esc_url($image_info['url']) ?>" loading="lazy" height="100%" width="100%" alt="" />
                            </div>
                        <?php endif; ?>
                        <div class="smma-next-project-info">
                            <h6>Next <?= ucfirst($post_type); ?></h6>
                            <h2><?= esc_html($next_post_title); ?></h2>
                            <a href="<?= esc_url($next_post_permalink); ?>" class="smma-button smma-button-type-text smma_block_modal_toggle">
                                <span class="smma-button-text">Read More</span>
                                <span class="smma-button-icon"><?= smma_elementor_icons('arrow', 'currentColor'); ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif;
    return ob_get_clean();
}
add_shortcode('smma_footer_next_post', '_footerNextPost');