<?php
$post_link  = get_permalink();
$post_type  = get_post_type();
$post_title = get_the_title();
$post_type_obj = get_post_type_object( $post_type );
$post_image = get_post_thumbnail_id();
?>
<div class="single-item">
	<div class="single-item-inner">
		<?php if ( $post_type === 'project' ) : ?>
			<?php if ( $post_image ) : ?>
				<?php
				if ( function_exists( 'smma_elementor_widgets_get_responsive_image_data' ) ) :
					$image_info = smma_elementor_widgets_get_responsive_image_data( $post_image, 'full' );
				endif;
				?>
				<div class="single-item-image">
					<a href="<?= $post_link ?>">
						<?php if ( isset( $image_info ) && isset( $image_info['srcset'] ) ) : ?>
							<img srcset="<?= esc_attr( $image_info['srcset'] ) ?>" src="<?= esc_url( $image_info['url'] ) ?>" loading="lazy" height="100%" width="100%" alt="" />
						<?php else : ?>
							<img src="<?= wp_get_attachment_url( $post_image ) ?>" alt="" />
						<?php endif; ?>
					</a>
				</div>
			<?php endif; ?>
			<div class="single-item-content">
				<h5 class="single-item-title">
					<a href="<?= $post_link ?>"><?= $post_title ?></a>
				</h5>
			</div>
		<?php else : ?>
			<div class="single-item-board <?= ucfirst( $post_type ) ?>">
				<label class="single-item-label"><?php echo $post_type_obj->labels->singular_name; ?> </label>
				<h4 class="single-item-title">
					<a href="<?= $post_link ?>"><?= $post_title ?></a>
				</h4>
			</div>
		<?php endif; ?>
	</div>
</div>
