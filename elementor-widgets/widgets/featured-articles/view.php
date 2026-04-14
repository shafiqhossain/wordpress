<?php
// Extract settings for better readability
$selected_posts = $settings['selected_posts'] ?? '';
$args_posts = array(
  'posts_per_page' => -1,
  'post__in'       => $selected_posts,
);
$query_posts = new WP_Query($args_posts);
$this->add_inline_editing_attributes('content', 'basic');
?>

<section class="cmma-elementor-widget">
	<div class="cmma-container">
		<div class="panel-wrapper">
			<?php if (!empty($query_posts)) : ?>
				<div class="cmma-featured-article">
					<?php
						while ($query_posts->have_posts()) :
							$query_posts->the_post();
							$post_type = get_post_type();
							$image_id = get_post_thumbnail_id(get_the_ID());
							$image_info = cmma_elementor_widgets_get_responsive_image_data($image_id, 'full');
							$content_post = get_post(get_the_ID());
							$content = $content_post->post_content;
							$next_post_permalink = get_permalink(get_the_ID());
						?>
						<div class="panel-content cmma-featured-article-inner">
							<?php if (isset($image_info) && !empty($image_info['srcset'])) : ?>
								<img srcset="<?= esc_attr($image_info['srcset']) ?>" src="<?= esc_url($image_info['url']) ?>" loading="lazy" height="100%" width="100%" alt="" />
							<?php endif; ?>
							<p><?php the_title(); ?></p>
							<div class="panel-content-control large-image-control">
								<a href="<?php echo $next_post_permalink ? $next_post_permalink : 'javascript:void(0);'; ?>" class="cmma-button cmma-button-type-text">
									<span <?php	$this->add_render_attribute( 'button_text', 'class', 'cmma-button-text' );echo $this->get_render_attribute_string( 'button_text' );	?>>Read More</span>
									<span class="cmma-button-icon"><?php echo cmma_elementor_icons( 'arrow', 'currentColor' ); ?></span>
								</a>
							</div>
						</div>
					<?php endwhile; ?>
				</div>
      		<?php endif; ?>
    	</div>
  	</div>
</section>
