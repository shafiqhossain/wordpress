<?php
// Extract settings for better readability
$images       = $settings['images'];
$color_scheme = $settings['color_scheme'] ?? '';
?>

<section class="cmma-elementor-widget color-scheme-<?= $color_scheme ?>">
	<div class="cmma-container">
		<div class="panel-wrapper grid-wrapper multiple-images">
			<?php if ( count( $images ) ) : ?>
				<div class="grid-layout grid-layout-<?= count( $images ) ?>">
					<?php foreach ( $images as $image ) : ?>
						<div class="grid-column">
							<div class="grid-image">
								<?php $image_info = cmma_elementor_widgets_get_responsive_image_data( $image['id'], 'full' ) ?>
								<img srcset="<?= esc_attr( $image_info['srcset'] ) ?>" src="<?= esc_url( $image_info['url'] ) ?>" loading="lazy" height="100%" width="100%" alt="" />
							</div>
							<div class="panel-content grid-content">
								<div class="panel-content-description grid-content-description">
									<?= wp_get_attachment_caption( $image['id'] ); ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
