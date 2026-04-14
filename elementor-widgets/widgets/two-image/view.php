<?php
// Extract settings for better readability
$color_scheme            = $settings['color_scheme'] ?? '';
$placement               = $settings['placement'] ?? '';
$headline                = $settings['headline'] ?? '';
$description             = $settings['description'] ?? '';
$button_text             = $settings['button_text'] ?? '';
$button_link             = esc_url( $settings['button_link']['url'] ?? '' );
$button_link_is_external = ! empty( $settings['button_link']['is_external'] );
$button_link_nofollow   = ! empty( $settings['button_link']['nofollow'] );
$small_image_button_text = $settings['small_image_button_text'] ?? '';
$small_image_button_link = esc_url( $settings['small_image_button_link']['url'] ?? '' );
$small_image_button_is_external = ! empty( $settings['small_image_button_link']['is_external'] );
$small_image_button_nofollow   = ! empty( $settings['small_image_button_link']['nofollow'] );
$short_description       = $settings['short_description'] ?? '';
$jump_navigation		 = isset($settings['enable_jump_navigation']) && $settings['enable_jump_navigation'] == 'yes' ? 'cmma-widget-jump-navigation' : '';
$jump_navigation_title = isset($settings['jump_navigation_title']) ? $settings['jump_navigation_title'] : '';

$overlay_headline        = $settings['overlay_headline'] ?? '';
$overlay_overline        = $settings['overlay_overline'] ?? '';
$overlay_button_text     = $settings['overlay_button_text'] ?? '';
$overlay_button_link     = esc_url( $settings['overlay_button_link']['url'] ?? '' );
$position                = $settings['position'] ?? '';

$image_info              = cmma_elementor_widgets_get_responsive_image_data( $settings['large_image']['id'], 'full' );
$image_info_small        = cmma_elementor_widgets_get_responsive_image_data( $settings['small_image']['id'], 'full' );

if ( isset( $settings['mobile_image'] ) && ! empty( $settings['mobile_image']['id'] ) ) :
	$mobile_image_info = cmma_elementor_widgets_get_responsive_image_data( $settings['mobile_image']['id'], 'full' );
endif;

// Rendering Inline Editing
$this->add_inline_editing_attributes( 'headline', 'basic' );
$this->add_inline_editing_attributes( 'description', 'basic' );
$this->add_inline_editing_attributes( 'short_description', 'basic' );
$this->add_inline_editing_attributes( 'button_text', 'none' );
$this->add_inline_editing_attributes( 'small_image_button_text', 'none' );

$this->add_inline_editing_attributes( 'overlay_headline', 'basic' );
$this->add_inline_editing_attributes( 'overlay_overline', 'basic' );
$this->add_inline_editing_attributes( 'overlay_button_text', 'none' );
?>
<section class="cmma-elementor-widget color-scheme-<?php echo $color_scheme; ?> <?= $jump_navigation ?>" data-title="<?= $jump_navigation_title ?>">
	<div class="cmma-container">
		<div class="panel-wrapper panel-two-image-wrapper content-placement-<?php echo $placement; ?> content-position-<?php echo $position;?>">
			<div class="panel-large-two-image">
				<?php if ( isset( $image_info ) && ! empty( $image_info['srcset'] ) ) : ?>
					<?php if ( ! empty( $overlay_headline ) || ! empty( $overlay_button_text ) || ! empty( $overlay_overline ) ) : ?>
						<div class="panel-asset large-two-image-element ">
					<?php else : ?>
						<div class="panel-asset large-two-image-element no-hover-effect">
					<?php endif; ?>
						<?php if ( ! empty( $overlay_headline ) || ! empty( $overlay_button_text ) || ! empty( $overlay_overline ) ) : ?>
							<div class="panel-head large-two-image-hover">
								<?php if ( ! empty( $overlay_overline ) ) : ?>
									<div <?php $this->add_render_attribute( 'overlay_overline', 'class', 'panel-head-left large-image-hover-left' ); echo $this->get_render_attribute_string( 'overlay_overline' );?>><?php echo $overlay_overline; ?></div>
								<?php endif; ?>

								<?php if ( ! empty( $overlay_headline ) || ! empty( $overlay_button_text ) ) : ?>
									<div class="panel-head-right large-two-image-hover-right">
										<p <?php echo $this->get_render_attribute_string( 'overlay_headline' ); ?>><?php echo $overlay_headline; ?></p>
										<?php if ( ! empty( $overlay_button_text ) ) : ?>
											<a href="<?php echo $overlay_button_link ?? 'javascript:void(0);'; ?>" class="cmma-button cmma-button-type-text without-animation">
												<span <?php $this->add_render_attribute( 'overlay_button_text', 'class', 'cmma-button-text' );echo $this->get_render_attribute_string( 'overlay_button_text' );?>><?php echo esc_html( $overlay_button_text ); ?></span>
												<span class="cmma-button-icon"><?php echo cmma_elementor_icons( 'arrow', 'currentColor' ); ?></span>
											</a>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<?php if ( isset( $mobile_image_info ) && isset( $mobile_image_info['srcset'] ) ) : ?>
							<img  data-wow-delay=".25s" srcset="<?php echo esc_attr( $mobile_image_info['srcset'] ); ?>" src="<?php echo esc_url( $mobile_image_info['url'] ); ?>" loading="lazy" height="100%" width="100%" alt="" class="panel-image-mobile animated wow fadeIn"  style="visibility :hidden"/>
						<?php endif; ?>
						<img  data-wow-delay=".25s" srcset="<?php echo esc_attr( $image_info['srcset'] ); ?>" src="<?php echo esc_url( $image_info['url'] ); ?>" loading="lazy" height="100%" width="100%" alt="" class="panel-image-desktop animated wow fadeIn" style="visibility :hidden" />
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $headline ) || ! empty( $description ) || ! empty( $button_text ) ) : ?>
					<div class="panel-content">
						<?php if ( ! empty( $headline ) ) : ?>
							<h2 <?php	$this->add_render_attribute( 'headline', 'class', 'panel-content-title large-two-image-title' );	echo $this->get_render_attribute_string( 'headline' ); ?>><?php echo esc_html( $headline ); ?></h2>
						<?php endif; ?>
						<?php if ( ! empty( $description ) ) : ?>
							<div <?php $this->add_render_attribute( 'description', 'class', 'panel-content-description large-two-image-description' );	echo $this->get_render_attribute_string( 'description' ); ?>> <?php echo $description; ?></div>
						<?php endif; ?>
						<?php if ( ! empty( $button_text ) ) : ?>
							<div class="panel-content-control large-image-control">
								<a href="<?php echo $button_link ? $button_link : 'javascript:void(0);'; ?>" <?php echo $button_link_is_external ? 'target="_blank"' : ''; ?> <?php echo $button_link_nofollow ? 'rel="nofollow"' : ''; ?> class="cmma-button cmma-button-type-text">
									<span <?php	$this->add_render_attribute( 'button_text', 'class', 'cmma-button-text' );echo $this->get_render_attribute_string( 'button_text' );	?>><?php echo $button_text; ?></span>
									<span class="cmma-button-icon"><?php echo cmma_elementor_icons( 'arrow', 'currentColor' ); ?></span>
								</a>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="panel-small-two-image">
				<div class="small-two-image-element">
					<?php if ( ! empty( $image_info_small['url'] ) ) : ?>
						<img class="animated wow fadeIn" srcset="<?php echo esc_attr( $image_info_small['srcset'] ); ?>" src="<?php echo esc_url( $image_info_small['url'] ); ?>" loading="lazy" height="100%" width="100%" alt=""  data-wow-delay=".25s"  style="visibility :hidden"/>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $short_description ) ) : ?>
					<div <?php $this->add_render_attribute( 'short_description', 'class', 'panel-content-description small-two-image-description' ); echo $this->get_render_attribute_string( 'short_description' );?>><?php echo $short_description; ?></div>
				<?php endif; ?>

				<?php if ( ! empty( $small_image_button_text ) ) : ?>
					<div class="panel-content-control large-image-control">
						<a href="<?php echo $small_image_button_link ? $small_image_button_link : 'javascript:void(0);'; ?>" <?php echo $small_image_button_is_external ? 'target="_blank"' : ''; ?> <?php echo $small_image_button_nofollow ? 'rel="nofollow"' : ''; ?> class="cmma-button cmma-button-type-text">
							<span <?php	$this->add_render_attribute( 'small_image_button_text', 'class', 'cmma-button-text' );echo $this->get_render_attribute_string( 'small_image_button_text' );	?>><?php echo $small_image_button_text; ?></span>
							<span class="cmma-button-icon"><?php echo cmma_elementor_icons( 'arrow', 'currentColor' ); ?></span>
						</a>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
