<?php
// Extract settings for better readability
$color_scheme = $settings['color_scheme'] ?? '';

$image = $settings['image'] ?? '';
$headline = $settings['headline'] ?? '';
$description = $settings['description'] ?? '';
$button_text = $settings['button_text'] ?? '';
$button_link = esc_url( $settings['button_link']['url'] ?? '' );

$overlay_headline = $settings['overlay_headline'] ?? '';
$overlay_overline = $settings['overlay_overline'] ?? '';
$overlay_button_text = $settings['overlay_button_text'] ?? '';
$overlay_button_link = esc_url( $settings['overlay_button_link']['url'] ?? '' );
$jump_navigation = isset($settings['enable_jump_navigation']) && $settings['enable_jump_navigation'] == 'yes' ? 'cmma-widget-jump-navigation' : '';
$jump_navigation_title = isset($settings['jump_navigation_title']) ? $settings['jump_navigation_title'] : '';

$image_id = $settings['image']['id'];
$large_image_image_id = $settings['image']['id'];
$large_image_image_size = 'full'; // You can change this to your desired image size
// Get image data
$image_info = cmma_elementor_widgets_get_responsive_image_data( $large_image_image_id, $large_image_image_size );

if ( isset( $settings['mobile_image'] ) && ! empty( $settings['mobile_image']['id'] ) ):
	$mobile_image_id = $settings['mobile_image']['id'];
	$mobile_image_info = cmma_elementor_widgets_get_responsive_image_data( $mobile_image_id, 'full' );
endif;

$this->add_inline_editing_attributes( 'overlay_headline', 'basic' );
$this->add_inline_editing_attributes( 'overlay_overline', 'basic' );
$this->add_inline_editing_attributes( 'overlay_button_text', 'none' );

$this->add_inline_editing_attributes( 'headline', 'basic' );
$this->add_inline_editing_attributes( 'description', 'basic' );
$this->add_inline_editing_attributes( 'button_text', 'none' );
?>

<section class="cmma-elementor-widget color-scheme-<?= $color_scheme ?> <?= $jump_navigation ?>" data-title="<?= $jump_navigation_title ?>">
	<div class="cmma-container">
		<div class="panel-wrapper large-image">
			<div class="panel-inner large-image-inner">
				<?php if ( isset( $image_info ) && ! empty( $image_info['srcset'] ) ): ?>
					<?php if ( ! empty( $overlay_headline ) || ! empty( $overlay_button_text ) || ! empty( $overlay_overline ) ) : ?>
						<div class="panel-asset large-image-element">
					<?php else : ?>
						<div class="panel-asset large-image-element no-hover-effect">
					<?php endif; ?>
						<?php if ( ! empty( $overlay_headline ) || ! empty( $overlay_button_text ) || ! empty( $overlay_overline ) ) : ?>
							<div class="panel-head large-image-hover">
								<?php if ( ! empty( $overlay_overline ) ): ?>
									<div <?php $this->add_render_attribute( 'overlay_overline', 'class', 'panel-head-left large-image-hover-left' ); echo $this->get_render_attribute_string( 'overlay_overline' ); ?>><?= $overlay_overline ?></div>
								<?php endif; ?>
								<?php if ( ! empty( $overlay_headline ) || ! empty( $overlay_button_text ) ): ?>
									<div class="panel-head-right large-image-hover-right">
										<p <?= $this->get_render_attribute_string( 'overlay_headline' ); ?>><?= $overlay_headline ?></p>
										<?php if ( ! empty( $overlay_button_text ) ): ?>
											<a href="<?= $overlay_button_link ?? 'javascript:void(0);' ?>" class="cmma-button cmma-button-type-text without-animation">
												<span <?php $this->add_render_attribute( 'overlay_button_text', 'class', 'cmma-button-text' ); echo $this->get_render_attribute_string( 'overlay_button_text' ); ?>><?= esc_html( $overlay_button_text ); ?></span>
												<span class="cmma-button-icon"><?= cmma_elementor_icons( 'arrow', 'currentColor' ); ?></span>
											</a>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>
						<?php if ( isset( $mobile_image_info ) && isset( $mobile_image_info['srcset'] ) ): ?>
							<img  data-wow-delay=".25s" srcset="<?= esc_attr( $mobile_image_info['srcset'] ) ?>" src="<?= esc_url( $mobile_image_info['url'] ) ?>" loading="lazy" height="100%" width="100%" alt="" class="wow fadeIn panel-image-mobile" />
						<?php endif; ?>
						<img  data-wow-delay=".25s" srcset="<?= esc_attr( $image_info['srcset'] ) ?>" src="<?= esc_url( $image_info['url'] ) ?>" loading="lazy" height="100%" width="100%" alt="" class="panel-image-desktop wow fadeIn" />
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $headline ) || ! empty( $description ) || ! empty( $button_text ) ): ?>
					<div class="panel-content two-column large-image-content">
						<?php if ( ! empty( $headline ) ): ?>
							<div class="panel-content-left">
								<h2 <?php $this->add_render_attribute( 'headline', 'class', 'panel-content-title large-image-title' ); echo $this->get_render_attribute_string( 'headline' ); ?>><?= esc_html( $headline ) ?></h2>
							</div>
						<?php endif; ?>
							<?php if ( ! empty( $description ) || ! empty( $button_text ) ): ?>
							<div class="panel-content-right">
								<?php if ( ! empty( $description ) ): ?>
									<div <?php $this->add_render_attribute( 'description', 'class', 'panel-content-description large-image-description' ); echo $this->get_render_attribute_string( 'description' ); ?>><?= $description ?></div>
								<?php endif; ?>
								<?php if ( ! empty( $button_text ) ): ?>
									<div class="panel-content-control large-image-control">
										<a href="<?= $button_link ? $button_link : 'javascript:void(0);' ?>" class="cmma-button cmma-button-type-text">
											<span <?php $this->add_render_attribute( 'button_text', 'class', 'cmma-button-text' ); echo $this->get_render_attribute_string( 'button_text' ); ?>><?= $button_text ?></span>
											<span class="cmma-button-icon"><?= cmma_elementor_icons( 'arrow', 'currentColor' ); ?></span>
										</a>
									</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
