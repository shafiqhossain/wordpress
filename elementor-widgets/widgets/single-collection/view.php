<?php
// Extract settings for better readability
$selected_posts = get_field('posts') ?? '';
$post_types = array(
	'post_type'      	=> array('project', 'perspective','member'),
	'post_status'   	=> 'publish',
	'posts_per_page'	=> -1,
	'post__in'      	=> $selected_posts,
);
$other_posts = get_posts($post_types);

$args = array(
	'post_status'		=> 'publish',
	'posts_per_page'	=> -1,
	'post__in'			=> $selected_posts
);
$posts = get_posts($args);
$all_posts = array_merge($other_posts, $posts);
?>

<section class="cmma-elementor-widget">
	<div class="cmma-container">
		<div class="cmma-collection-wrapper">
			<?php if (count($all_posts)) : ?>
				<div class="cmma-collection-list">
					<?php foreach ($all_posts as $post) :
						$post_type = $post->post_type;
						$postID = $post->ID;
						$post_title = $post->post_title;
						$image_id = get_post_thumbnail_id($postID);
						$image_info = cmma_elementor_widgets_get_responsive_image_data($image_id, 'full');
						$card_type = get_field('select_card', $postID);
						$sub_title = get_field('sub_title', $postID);
						$modal_id = 'cmma-modal-' . $postID; // Unique modal ID for each post
						$widget_id = 'cmma-collection-' . $postID; // Unique widget ID for each post
						if(!$card_type) {
							$card_type = 'small';
						}
						?>
						<div class="cmma-collection <?= $card_type; ?>" data-post-id="<?= $postID; ?>">
							<?php if ($post_type === 'project' || $post_type === 'perspective') : ?>
								<?php if (isset($image_info) && !empty($image_info['srcset'])) : ?>
									<div class="cmma-collection-img">
										<a href="<?= esc_url(get_permalink($post)); ?>">
											<img srcset="<?= esc_attr($image_info['srcset']) ?>" src="<?= esc_url($image_info['url']) ?>" loading="lazy" height="100%" width="100%" alt="" />
										</a>
									</div>
									<p><?= $post_title ?></p>
								<?php else : ?>
									<div class="panel-item-perspective">
										<a href="<?= esc_url(get_permalink($post)); ?>"></a>
										<p><?= ucfirst($post_type) ?></p>
										<h4><?= $post_title ?></h4>
									</div>
								<?php endif; ?>

							<?php elseif ($post_type === 'member') : ?>
								<?php if (isset($image_info) && !empty($image_info['srcset'])) : ?>
									<div class="cmma-collection-img">
										<a href="<?= get_permalink($postID); ?>">
											<img srcset="<?= esc_attr($image_info['srcset']) ?>" src="<?= esc_url($image_info['url']) ?>" loading="lazy" height="100%" width="100%" alt="" />
										</a>
									</div>
									<p><?= $post_title ?><?php if ($sub_title) echo ', ' . $sub_title; ?></p>
								<?php else : ?>
									<div class="panel-item-member">
										<a href="<?= get_permalink($postID); ?>" class="cmma-button cmma-button-type-text"></a>
										<p><?= ucfirst($post_type) ?></p>
										<h4><?= $post_title ?><?php if ($sub_title) echo ', ' . $sub_title; ?></h4>
									</div>
								<?php endif; ?>

							<?php else : ?>
								<?php if (isset($image_info) && !empty($image_info['srcset'])) : ?>
									<div class="cmma-collection-img">
										<a href="<?= esc_url(get_permalink($post)); ?>">
											<img srcset="<?= esc_attr($image_info['srcset']) ?>" src="<?= esc_url($image_info['url']) ?>" loading="lazy" height="100%" width="100%" alt="" />
										</a>
									</div>
									<p><?= $post_title ?></p>
								<?php else : ?>
									<div class="panel-item-news">
										<a href="<?= esc_url(get_permalink($post)); ?>"></a>
										<p>News</p>
										<h4><?= $post_title ?></h4>
									</div>
								<?php endif;
							 endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php add_action('wp_footer',function () { ?>
	<script type="text/javascript">
		document.addEventListener('DOMContentLoaded', function() {
			calculateLargeImageContentHeight('.cmma-collection');
		});

		window.addEventListener('resize', function() {
			calculateLargeImageContentHeight('.cmma-collection');
		});

		function calculateLargeImageContentHeight(widgetClass) {
			var $j = jQuery;
			var $widget = $j(widgetClass);
			var $content = $widget.find('.panel-collection .cmma-collection-list');

			if ($content.length) {
				$widget.find('.cmma-collection').css({'--large-image-content-height': $content.height() + 'px'});
			}
		}
	</script>
<?php }); ?>
