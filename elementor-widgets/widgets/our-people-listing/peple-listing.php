<?php
	$args = array(
		'posts_per_page'	=> 24,
		'post_status'		=> 'publish',
		'post_type' 		=> 'member',
		'paged' 			=> 1,
		'meta_key' 			=> 'last_name',
		'orderby' 			=> 'meta_value',
		'order' 			=> 'ASC',
		'meta_query'   		=> array(
			array(
				'key'       => 'show_bio',
				'value'		=> '1',
				'compare'	=> '=='
			),
		),
	);

	$query = new WP_Query($args);
	if ($query->have_posts()) {
		$count = 0;
		while ($query->have_posts()) :
			$query->the_post();
			$count++;
			$postID = get_the_ID();
			$modal_id = 'cmma-modal-' . $postID;
			$image_id = get_post_thumbnail_id($postID);
			$image_info = cmma_elementor_widgets_get_responsive_image_data($image_id, 'full');
			$custom_fields = get_fields($postID);
			$classes = array('red', 'green', 'yellow');
        	$random_class = $classes[array_rand($classes)];
			$post_slug = get_post_field( 'post_name', $postID);
			?>

			<div class="cmma-collection panel-content cmma-post cmma-members-list-block <?= $random_class ?>" data-post-id="<?= $postID; ?>">
				<a href="<?= get_permalink($postID); ?>" id="<?= $post_slug; ?>">
					<?php if (isset($image_info) && !empty($image_info['srcset'])) : ?>
						<img srcset="<?= esc_attr($image_info['srcset']) ?>" src="<?= esc_url($image_info['url']) ?>" loading="lazy" height="100%" width="100%" alt="" />
					<?php else: ?>
						<img srcset="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/placeholder.jpg" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/placeholder.jpg" loading="lazy" height="100%" width="100%" alt="" />
					<?php endif;  ?>
					<p><?= $custom_fields['first_name']; ?> <?= $custom_fields['last_name']; ?></p>
				</a>
			</div>
			<?php
				foreach ($videos_and_images as $item) {
					if ($item['order'] == $count) { ?>
						<div class="cmma-post cmma-cultural-block">
							<?php $image_info = cmma_elementor_widgets_get_responsive_image_data(isset($item['image']['id']) && !empty($item['image']['id']) ? $item['image']['id'] : '', 'full'); ?>

							<?php if (isset($image_info) && !empty($image_info['srcset'])) : ?>
								<div class="cmma-slide-image">
									<img srcset="<?= esc_attr($image_info['srcset']) ?>" src="<?= esc_url($image_info['url']) ?>" loading="lazy" height="100%" width="100%" alt="" />
								</div>
							<?php elseif (isset($item['video_url']) && !empty($item['video_url'])) : ?>
								<?php
								$video_type = preg_match('/^((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube\.com|youtu.be))(\/(?:[\w\-]+\?v=|embed\/|v\/)?)([\w\-]+)(\S+)?$/', $item['video_url']) ? 'youtube' : '';
								if (empty($video_type)) :
									$video_type = preg_match('/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/', $item['video_url']) ? 'vimeo' : '';
								endif;
								?>
								<div class="cmma-slide-iframe <?= $video_type ?>-embed cmma-video-embed">
									<?= cmma_video_oembed_get($item['video_url'], $video_type); ?>
								</div>
							<?php elseif (!empty($item['image']['url'])) : ?>
								<div class="cmma-slide-video cmma-video-embed video-embed">
									<video muted autoplay loop>
										<source src="<?= esc_url($item['image']['url']) ?>" type="video/mp4">
									</video>
								</div>
							<?php endif; ?>
							<div class="cmma-slide-caption">
								<?php if (!empty($item['caption'])) : ?>
									<p><?= $item['caption'] ?></p>
								<?php endif; ?>
							</div>
						</div>
					<?php
					}
				}
			endwhile;
		wp_reset_postdata();
	}
?>