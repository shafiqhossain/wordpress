<?php
$color_scheme        = $settings['color_scheme'] ?? '';
$placement           = $settings['placement'] ?? '';
$image               = $settings['image'] ?? '';
$headline            = $settings['headline'] ?? '';
$tagline             = $settings['tagline'] ?? '';
$button_text         = $settings['button_text'] ?? '';
$button_link         = esc_url( $settings['button_link']['url'] ?? '' );
$image_info          = cmma_elementor_widgets_get_responsive_image_data( $settings['image']['id'], 'full' );
$jump_navigation	= isset($settings['enable_jump_navigation']) && $settings['enable_jump_navigation'] == 'yes' ? 'cmma-widget-jump-navigation' : '';
$jump_navigation_title = isset($settings['jump_navigation_title']) ? $settings['jump_navigation_title'] : '';

if ( isset( $settings['mobile_image'] ) && ! empty( $settings['mobile_image']['id'] ) ) :
	$mobile_image_info = cmma_elementor_widgets_get_responsive_image_data( $settings['mobile_image']['id'], 'full' );
endif;

// Rendering Inline Editing
$this->add_inline_editing_attributes( 'headline', 'basic' );
$this->add_inline_editing_attributes( 'tagline', 'basic' );
$this->add_inline_editing_attributes( 'button_text', 'basic' );
?>

<div class="cmma-elementor-widget cmma-widget-wrapper image-with-text <?= $jump_navigation ?>" data-title="<?= $jump_navigation_title ?>">
	<section class="cmma-widget">
		<div class="cmma-container">
			<div class="panel-wrapper content-placement-<?php echo esc_attr( $settings['placement'] ?? '' ); ?> ">
				<div class="panel-image">
					<?php if ( isset( $image_info ) && ! empty( $image_info['srcset'] ) ) : ?>
						<div class="panel-asset image-with-text-element">
							<?php if ( isset( $mobile_image_info ) && isset( $mobile_image_info['srcset'] ) ) : ?>
								<img data-wow-delay=".25s" srcset="<?php echo esc_attr( $mobile_image_info['srcset'] ); ?>" src="<?php echo esc_url( $mobile_image_info['url'] ); ?>" loading="lazy" height="100%" width="100%" alt="" class="panel-image-mobile wow fadeIn" />
							<?php endif; ?>
							<img data-wow-delay=".25s" srcset="<?php echo esc_attr( $image_info['srcset'] ); ?>" src="<?php echo esc_url( $image_info['url'] ); ?>" loading="lazy" height="100%" width="100%" alt="" class="panel-image-desktop wow fadeIn" />
						</div>
					<?php endif; ?>
				</div>

				<div class="panel-text">
					<?php if ( ! empty( $headline ) || ! empty( $tagline ) || ! empty( $button_text ) ) : ?>
						<div class="panel-content">
							<?php if ( ! empty( $headline ) ) : ?>
								<h5 <?php	$this->add_render_attribute( 'headline', 'class', 'panel-content-title image-with-text-title' );	echo $this->get_render_attribute_string( 'headline' ); ?>><?php echo esc_html( $headline ); ?></h5>
							<?php endif; ?>
							<?php if ( ! empty( $tagline ) ) : ?>
								<h2 <?php $this->add_render_attribute( 'tagline', 'class', 'panel-content-tagline image-with-text-tagline' );	echo $this->get_render_attribute_string( 'tagline' ); ?>> <?php echo $tagline; ?></h2>
							<?php endif; ?>
							<?php if ( ! empty( $button_text ) ) : ?>
								<div class="panel-content-control image-with-text-control">
									<a href="<?php echo $button_link ?? 'javascript:void(0);'; ?>" class="cmma-button cmma-button-type-text">
										<span <?php	$this->add_render_attribute( 'button_text', 'class', 'cmma-button-text' );echo $this->get_render_attribute_string( 'button_text' );	?>><?php echo $button_text; ?></span>
										<span class="cmma-button-icon"><?php echo cmma_elementor_icons( 'arrow', 'currentColor' ); ?></span>
									</a>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>
</div>
