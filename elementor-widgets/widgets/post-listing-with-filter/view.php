<section class="cmma-elementor-widget panel-wrapper">
	<div class="cmma-container">
		<div class="cmma-work-categories-wrapper">
			<div class="cmma-work-categories-head">
				<div class="cmma-work-categories-list left">
					<?php
						global $wpdb;
						$query = $wpdb->prepare("SELECT DISTINCT wp_posts.ID, wp_posts.post_title
							FROM wp_posts
							JOIN wp_postmeta ON wp_posts.ID = SUBSTRING_INDEX(SUBSTRING_INDEX(wp_postmeta.meta_value, ':\"', -1), '\"', 1) 
							WHERE wp_postmeta.meta_key = 'select_market_post'
							AND wp_posts.post_status = 'publish'
							AND wp_posts.post_type = 'market'
							AND wp_postmeta.meta_value <> '' ORDER BY wp_posts.post_title ASC");

						$markets = $wpdb->get_results($query);
						if ($markets) :
							foreach ($markets as $market) :	?>
								<div data-category-id="<?= $market->ID; ?>" data-marketid="<?= $market->ID; ?>">
									<?php
									$short_title = get_field('short_title', $market->ID);
									if ($short_title) {
										echo esc_html($short_title);
									} else {
										echo esc_html($market->post_title);
									}
									?>
								</div>
							<?php
							endforeach;
						endif;
						$sel_posts = '';
						$selected_posts = $settings['selected_post'] ?? '';
						if ($selected_posts && count($selected_posts)) {
							$selected_posts = array_column($selected_posts,'post_select');
							$sel_posts = implode(',', $selected_posts);
						}
					?>
				</div>
				<div class="cmma-work-categories-list right">
					<div data-category-id="all" data-default-posts="<?= $sel_posts;?>" class="active">
						<a href="<?= get_permalink(); ?>">All</a>
					</div>
					<div data-category-id="perspective">Perspectives</div>
					<div data-category-id="project">Projects</div>
				</div>
			</div>

			<?php

			$post_count = 24;
			$sticky_posts = [];

			$selected_posts = array_filter($selected_posts);
			if (count($selected_posts)) {
				$sticky_args = array(
					'post_type'      	=> array('project', 'perspective'),
					'post_status'    	=> 'publish',
					'posts_per_page'	=> -1,
					'orderby'        	=> 'post__in',
					'include'        	=> implode(',', $selected_posts),
				);
				$sticky_posts = get_posts($sticky_args);
				$post_count -= count($sticky_posts);
			}

			$args = array(
				'post_type'             => array('project', 'perspective'),
				'post_status'           => 'publish',
				'posts_per_page'        => $post_count,
				'post__not_in'   		=> $selected_posts,
			);
			$posts = get_posts($args);
			$all_posts = array_merge($sticky_posts, $posts);

			if (count($all_posts)) : ?>
				<div class="loader" style="display: none;"><?php echo cmma_elementor_icons('loader', 'currentColor'); ?></div>
				<div class="cmma-work-list">
					<?php
					foreach ($all_posts as $key => $post) :
						$post_type = $post->post_type;
						$postID = $post->ID;
						$post_title = $post->post_title;
						$image_id = get_post_thumbnail_id($postID);
						$image_info = cmma_elementor_widgets_get_responsive_image_data($image_id, 'full');
						$card_type = get_field('select_card', $postID); ?>
						<?= generate_work_html($post_type, $card_type, $image_info, $post,$post_title, ($key+1)); ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="load-more-container">
			<div class="load-more-loader" style="display: none;"><?php echo cmma_elementor_icons('loader', 'currentColor'); ?></div>
			<button id="load-more-posts" <?php if (count($all_posts) < 24): ?> style="display: none;" <?php endif;?>data-default-posts="<?= $sel_posts;?>" class="load-more-posts">Load More +</button>
		</div>
	</div>
</section>
