<?php
// Extract settings for better readability
$headline                  	= $settings['headline'] ?? '';
$button_text               	= $settings['button_text'] ?? '';
$button_link               	= esc_url($settings['button_link']['url'] ?? '');
$color_scheme              	= $settings['color_scheme'] ?? '';
$markets					= $settings['toggle_show_markets'] ?? '';
$show_by_category			= $settings['show_by_category'] ?? '';
$post_category				= $settings['selected_post_category'] ?? '';
$category_post_limit		= $settings['selected_category_post_limit'] ?? '';
$selected_market			= $settings['selected_market'] ?? '';
$selected_posts				= $this->get_settings('selected_posts');
$jump_navigation			= isset($settings['enable_jump_navigation']) && $settings['enable_jump_navigation'] == 'yes' ? 'cmma-widget-jump-navigation' : '';
$jump_navigation_title		= isset($settings['jump_navigation_title']) ? $settings['jump_navigation_title'] : '';
$selected_post_orderby		= isset($settings['selected_post_orderby']) ? $settings['selected_post_orderby'] : '';
$selected_post_order		= isset($settings['selected_post_order']) ? $settings['selected_post_order'] : '';

if ($markets == 'yes') {
	$args_perspective = array(
		'post_type'      => array('project', 'perspective'), // Specify the post types
		'posts_per_page' => 8, // Limit the number of records to 8
		'orderby'        => 'rand',
		'meta_query'     => array(
			'relation' => 'OR',
			array(
				'key'     => 'select_market_post', // Replace with the actual name of the ACF field for the project
				'value'   => $selected_market,
				'compare' => 'LIKE',
			),
			array(
				'key'     => 'select_market_post', // Replace with the actual name of the ACF field for the perspective
				'value'   => $selected_market,
				'compare' => 'LIKE',
			),
		),
	);
} else {
	$args_common = array(
		'post_type'      => array('project', 'perspective'),
		'posts_per_page' => -1,
	);


	if ($show_by_category) {
		$args_perspective = array_merge($args_common, array(
			'category'       => $post_category,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'fields'         => 'ids',
		));
		$postIds = get_posts($args_perspective);
		$args_perspective = array_merge($args_common, array(
			'post__in'       => $postIds,
			'orderby'        => 'post__in',
		));
	} else {
		$selected_post_perspective = [];
		if (isset($selected_posts) && count($selected_posts)) {
			$selected_post_perspective = array_column($selected_posts,'post_id');
		}
		if ($selected_post_orderby == 'post__in' && $selected_post_order == "DESC") {
			$selected_post_perspective =  array_reverse($selected_post_perspective);
		}

		$args_perspective = array_merge($args_common, array(
			'orderby'	=> $selected_post_orderby,
			'order'		=> $selected_post_order,
			// 'orderby' => 'post__in',
			'ignore_custom_sort' => TRUE,
			'post__in'	=> $selected_post_perspective
		));
	}
}
// var_dump($args_perspective);
$query_perspective = new WP_Query($args_perspective);
$this->add_inline_editing_attributes('headline', 'basic');
$this->add_inline_editing_attributes('button_text', 'none');
?>

<section class="cmma-elementor-widget overflow-hidden color-scheme-<?= $color_scheme; ?> <?= $jump_navigation ?>" data-title="<?= $jump_navigation_title ?>">
	<div class="cmma-container">
		<div class="panel-wrapper panel-wrapper-wrap">
			<div class="project-panel-head">
				<?php if (!empty($headline)) : ?>
					<h2 <?php $this->add_render_attribute('headline', 'class', 'panel-content-title project-perspective-title'); echo $this->get_render_attribute_string('headline'); ?>><?= esc_html($headline); ?></h2>
				<?php endif; ?>
				<?php if (!empty($button_text)) : ?>
					<div class="panel-content-control project-control">
						<a href="<?= $button_link ?? 'javascript:void(0);'; ?>" class="cmma-button cmma-button-type-text">
							<span <?php $this->add_render_attribute('button_text', 'class', 'cmma-button-text'); echo $this->get_render_attribute_string('button_text'); ?>><?= $button_text; ?></span>
							<span class="cmma-button-icon"><?= cmma_elementor_icons('arrow', 'currentColor'); ?></span>
						</a>
					</div>
				<?php endif; ?>
			</div>

			<?php if (!empty($query_perspective)) : ?>
				<?php
				$slick_settings = [
					'slidesToShow'  => 2,
					'dots'          => false,
					'infinite'      => true,
					'autoplay'      => false,
					'autoplaySpeed' => 2500,
					'centerMode'    => false,
					'prevArrow'     => '<a href="javascript:void(0);" class="prev-btn">' . cmma_elementor_icons('arrow', 'currentColor') . '</button>',
					'nextArrow'     => '<a href="javascript:void(0);" class="next-btn">' . cmma_elementor_icons('arrow', 'currentColor') . '</button>',
					'responsive'    => [
						[
							'breakpoint' => 991,
							'settings'   => [
								'slidesToShow' => 1,
							],
						],
					],
				];
				?>

				<div class="project-panel-slider">
					<div class="project-panel-slider-inner" data-slick="<?= htmlspecialchars(json_encode($slick_settings)); ?>">
						<?php
							// Array of class names to shuffle
							$classes = ['red', 'yellow', 'green'];
							shuffle($classes);
							while ($query_perspective->have_posts()) :
								$query_perspective->the_post();
								$post_type = get_post_type();
								$image_id = get_post_thumbnail_id(get_the_ID());
								$image_info = cmma_elementor_widgets_get_responsive_image_data($image_id, 'full');
								$project_class = $classes[array_rand($classes)];
								$perspective_class = $classes[array_rand($classes)]; ?>
								<div class="project-panel-slider-item">
									<?php if ($post_type == 'project') : ?>
										<div class="panel-item-project <?= $project_class; ?>">
											<a href="<?= esc_url(get_permalink(get_the_ID())); ?>">
												<?php if (isset($image_info) && !empty($image_info['srcset'])) : ?>
													<div class="panel-item-project-img">
														<img srcset="<?= esc_attr($image_info['srcset']) ?>" src="<?= esc_url($image_info['url']) ?>" loading="lazy" height="100%" width="100%" alt="" />
													</div>
												<?php endif; ?>
												<p><?php the_title(); ?></p>
											</a>
										</div>
									<?php endif; ?>

									<?php if ($post_type == 'perspective') : ?>
										<div class="panel-item-perspective <?= $perspective_class; ?>">
											<p>Perspective</p>
											<a href="<?= esc_url(get_permalink(get_the_ID())); ?>">
												<h2><?php the_title(); ?></h2>
											</a>
										</div>
									<?php endif; ?>
								</div>
						<?php endwhile; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
