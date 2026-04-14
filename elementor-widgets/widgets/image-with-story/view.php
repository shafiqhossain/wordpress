<?php
$modal_id            = 'modal-image-with-story-' . rand();
$color_scheme        = $settings['color_scheme'] ?? '';
$placement           = $settings['placement'] ?? '';
$has_videos          = false;
$image               = $settings['image'] ?? '';
$headline            = $settings['headline'] ?? '';
$description         = $settings['description'] ?? '';
$button_text         = $settings['button_text'] ?? '';
$button_link         = esc_url( $settings['button_link']['url'] ?? '' );
$short_description   = $settings['short_description'] ?? '';

$overlay_headline    = $settings['overlay_headline'] ?? '';
$overlay_overline    = $settings['overlay_overline'] ?? '';
$overlay_button_text = $settings['overlay_button_text'] ?? '';
$overlay_button_link = esc_url( $settings['overlay_button_link']['url'] ?? '' );

$jump_navigation	 = isset($settings['enable_jump_navigation']) && $settings['enable_jump_navigation'] == 'yes' ? 'cmma-widget-jump-navigation' : '';
$jump_navigation_title = isset($settings['jump_navigation_title']) ? $settings['jump_navigation_title'] : '';

$image_info          = cmma_elementor_widgets_get_responsive_image_data( $settings['image']['id'], 'full' );
$image_info_small    = cmma_elementor_widgets_get_responsive_image_data( $settings['story_image']['id'], 'full' );

$mute_icon_display = $settings['mute_icon_display'] ?? '';

if ( isset( $settings['mobile_image'] ) && ! empty( $settings['mobile_image']['id'] ) ) :
	$mobile_image_info = cmma_elementor_widgets_get_responsive_image_data( $settings['mobile_image']['id'], 'full' );
endif;

if ( ! empty( $settings['videos'] ) ) :
	foreach ( $settings['videos'] as $video ) :
		if ( ! empty( $video['video']['url'] ) ):
			$has_videos = true;
			break;
		endif;
	endforeach;
endif;

// Rendering Inline Editing
$this->add_inline_editing_attributes( 'headline', 'basic' );
$this->add_inline_editing_attributes( 'description', 'basic' );
$this->add_inline_editing_attributes( 'short_description', 'basic' );
?>

<!-- Section with background color based on settings -->
<section class="cmma-elementor-widget cmma-widget color-scheme-<?php echo esc_attr( $settings['color_scheme'] ?? '' ); ?> <?= $jump_navigation ?>" data-title="<?= $jump_navigation_title ?>">
	<div class="cmma-container">
		<div class="panel-wrapper panel-two-image-wrapper image-with-story content-placement-<?php echo esc_attr( $settings['placement'] ?? '' ); ?> ">
			<div class="panel-large-two-image">
				<?php if ( isset( $image_info ) && ! empty( $image_info['srcset'] ) ) : ?>
					<?php if ( ! empty( $overlay_headline ) || ! empty( $overlay_button_text ) || ! empty( $overlay_overline )) : ?>
						<div class="panel-asset large-two-image-element">
					<?php else : ?>
						<div class="panel-asset large-two-image-element no-hover-effect">
					<?php endif; ?>
						<?php if ( ! empty( $overlay_headline ) || ! empty( $overlay_button_text ) || ! empty( $overlay_overline )) : ?>
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
							<img  style="visibility :hidden" data-wow-delay=".25s" srcset="<?php echo esc_attr( $mobile_image_info['srcset'] ); ?>" src="<?php echo esc_url( $mobile_image_info['url'] ); ?>" loading="lazy" height="100%" width="100%" alt="" class="panel-image-mobile animated wow fadeIn" />
						<?php endif; ?>
						<img  style="visibility :hidden" data-wow-delay=".25s" srcset="<?php echo esc_attr( $image_info['srcset'] ); ?>" src="<?php echo esc_url( $image_info['url'] ); ?>" loading="lazy" height="100%" width="100%" alt="" class="panel-image-desktop animated wow fadeIn" />
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $headline ) || ! empty( $description ) || ! empty( $button_text ) ) : ?>
					<div class="panel-content">
						<?php if ( ! empty( $headline ) ) : ?>
							<h2 <?php $this->add_render_attribute( 'headline', 'class', 'panel-content-title large-two-image-title' );	echo $this->get_render_attribute_string( 'headline' ); ?>><?php echo esc_html( $headline ); ?></h2>
						<?php endif; ?>
						<?php if ( ! empty( $description ) ) : ?>
							<div <?php $this->add_render_attribute( 'description', 'class', 'panel-content-description large-two-image-description' );	echo $this->get_render_attribute_string( 'description' ); ?>> <?php echo $description; ?></div>
						<?php endif; ?>
						<?php if ( ! empty( $button_text ) ) : ?>
							<div class="panel-content-control large-image-control">
								<a href="<?php echo $button_link ? $button_link : 'javascript:void(0);'; ?>" class="cmma-button cmma-button-type-text">
									<span <?php	$this->add_render_attribute( 'button_text', 'class', 'cmma-button-text' );echo $this->get_render_attribute_string( 'button_text' );	?>><?php echo $button_text; ?></span>
									<span class="cmma-button-icon"><?php echo cmma_elementor_icons( 'arrow', 'currentColor' ); ?></span>
								</a>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="panel-small-two-image">
				<div class="small-two-image-wrapper">
					<?php if ( ! empty( $image_info_small['url'] ) ) : ?>
						<div class="small-two-image-element">
							<a href="javascript:void(0);" <?= $has_videos ? 'class="image-with-story-status cmma-modal-button cmma-modal-scroll"' : '' ?> data-modal-id="<?= $modal_id ?>">
								<img srcset="<?php echo esc_attr( $image_info_small['srcset'] ); ?>" src="<?php echo esc_url( $image_info_small['url'] ); ?>" loading="lazy" height="100%" width="100%" alt="" />
							</a>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $short_description ) ) : ?>
						<div <?php $this->add_render_attribute( 'short_description', 'class', 'panel-content-description image-with-story-description' ); echo $this->get_render_attribute_string( 'short_description' ); ?>>
							<p><?php echo $short_description; ?></p>
						</div>
					<?php endif; ?>

				</div>
			</div>
		</div>

		<?php if ( $has_videos ) : ?>
			<div id="<?= $modal_id ?>" class="widget-modal story-modal color-scheme-dark">
				<div class="story-modal-content">
					<div class="story-container">
						<div class="story-progressbars">
							<?php foreach ( $settings['videos'] as $index => $video ) : ?>
								<?php if ( ! empty( $video['video']['url'] ) ) : ?>
									<div class="story-progressbar">
										<span data-slick-index="<?php echo $index; ?>" class="story-progressbar-line" style="--progress-line: 0%;"></span>
									</div>
								<?php endif;
							endforeach; ?>
						</div>
						<div class="story-controls">
							<?php if ($mute_icon_display === 'yes' ):?>
								<a href="javascript:void(0);" class="sound-btn" state="unmute">
									<?php echo cmma_elementor_icons( 'mute', 'currentColor' ); ?>
									<?php echo cmma_elementor_icons( 'unmute', 'currentColor' ); ?>
								</a>
							<?php endif; ?>

							<a href="javascript:void(0);" class="process-btn" state="play">
								<?php echo cmma_elementor_icons( 'pause', 'currentColor' ); ?>
								<?php echo cmma_elementor_icons( 'play', 'currentColor' ); ?>
							</a>
							<a href="javascript:void(0);" class="close-btn cmma-modal-close" data-modal-id="<?= $modal_id ?>">
								<?php echo cmma_elementor_icons( 'close', 'currentColor' ); ?>
							</a>
						</div>
						<div class="story-slider">
							<?php foreach ( $settings['videos'] as $key => $video ) : ?>
								<?php if ( ! empty( $video['video']['url'] ) ) : ?>
									<div class="story-slide">
										<div class="video-iframe" >
											<video width="640" height="565" loop="false" controls="false">
												<source src="<?= esc_url($video['video']['url']) ?>" type="video/mp4">
											</video>
										</div>
									</div>
								<?php endif;
							endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>
