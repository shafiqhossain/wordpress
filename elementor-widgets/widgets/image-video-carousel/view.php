<?php
// Extract settings for better readability
$color_scheme = $settings['color_scheme'] ?? '';
$headline    = $settings['headline'] ?? '';
$description = $settings['description'] ?? '';
$button_text = $settings['button_text'] ?? '';
$button_link = esc_url( $settings['button_link']['url'] ?? '' );
$items       = $settings['videos'] ?? [];
$jump_navigation = isset($settings['enable_jump_navigation']) && $settings['enable_jump_navigation'] == 'yes' ? 'cmma-widget-jump-navigation' : '';
$jump_navigation_title = isset($settings['jump_navigation_title']) ? $settings['jump_navigation_title'] : '';
$mute_icon_display = $settings['mute_icon_display'] ?? '';

// Rendering Inline Editing
$this->add_inline_editing_attributes( 'headline', 'basic' );
$this->add_inline_editing_attributes( 'description', 'basic' );
$this->add_inline_editing_attributes( 'button_text', 'none' );
?>

<section class="cmma-elementor-widget color-scheme-<?php echo $color_scheme; ?> <?= $jump_navigation ?>" data-title="<?= $jump_navigation_title ?>">
	<div class="panel-wrapper image-video-wrapper">
		<?php if ( ! empty( $headline ) || ! empty( $description ) || ! empty( $button_text ) ) : ?>
			<div class="cmma-container">
				<div class="panel-inner image-video-inner">
					<div class="panel-content image-video-content">
						<?php if ( ! empty( $headline ) ) : ?>
							<h2 <?php $this->add_render_attribute( 'headline', 'class', 'panel-content-title cmma-title' ); echo $this->get_render_attribute_string( 'headline' ); ?>><?php echo esc_html( $headline ); ?></h2>
						<?php endif; ?>
						<?php if ( ! empty( $description ) ) : ?>
							<div <?php $this->add_render_attribute( 'description', 'class', 'panel-content-description cmma-description' ); echo $this->get_render_attribute_string( 'description' ); ?>><?php echo $description; ?></div>
						<?php endif; ?>
						<?php if ( ! empty( $button_text ) ) : ?>
							<div class="panel-content-control image-video-control">
								<a href="<?php echo $button_link ?? 'javascript:void(0);'; ?>" class="cmma-button cmma-button-type-text">
									<span <?php $this->add_render_attribute( 'button_text', 'class', 'cmma-button-text' ); echo $this->get_render_attribute_string( 'button_text' ); ?>><?php echo $button_text; ?></span>
									<span class="cmma-button-icon"><?php echo cmma_elementor_icons( 'arrow', 'currentColor' ); ?></span>
								</a>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<?php if ( count( $items ) ) : ?>
			<?php
			$slick_settings = [
				'slidesToShow'  => 2,
				'dots'          => false,
				'infinite'      => true,
				'autoplay'      => false,
				'autoplaySpeed' => 3000,
				'prevArrow'     => '<a href="javascript:void(0);" class="cmma-prev-btn">' . cmma_elementor_icons( 'arrow', 'currentColor' ) . '</button>',
				'nextArrow'     => '<a href="javascript:void(0);" class="cmma-next-btn">' . cmma_elementor_icons( 'arrow', 'currentColor' ) . '</button>',
				'responsive'    => [
					[
						'breakpoint' => 991,
						'settings'   => [
							'slidesToShow' => 1
						],
					],
				]
			];
			?>
			<div class="cmma-container">
				<div class="cmma-carousel-wrapper wow fadeIn" data-wow-delay=".25s" >
					<div class="cmma-carousel" data-slick="<?= htmlspecialchars( json_encode( $slick_settings ) ); ?>">
						<?php foreach ( $items as $index => $item ):
							$image_info = cmma_elementor_widgets_get_responsive_image_data( isset( $item['image']['id'] ) && ! empty( $item['image']['id'] ) ? $item['image']['id'] : '' , 'full' );
							$item_caption_setting_key = $this->get_repeater_setting_key( 'caption', 'videos', $index );
							$this->add_render_attribute( $item_caption_setting_key, 'class', 'cmma-slide-caption-title' );
							$this->add_inline_editing_attributes( $item_caption_setting_key, 'basic');
							?>
							<div class="cmma-slide">
								<div class="cmma-slide-inner">
									<?php if ( isset( $image_info ) && ! empty( $image_info['srcset'] ) ) : ?>
										<div class="cmma-slide-image">
											<img srcset="<?= esc_attr( $image_info['srcset'] ) ?>" src="<?= esc_url( $image_info['url'] ) ?>" loading="lazy" height="100%" width="100%" alt="" />
										</div>
									<?php elseif ( isset( $item['video_url'] ) && ! empty( $item['video_url'] ) ) : ?>
										<?php
											$video_type = preg_match( '/^((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube\.com|youtu.be))(\/(?:[\w\-]+\?v=|embed\/|v\/)?)([\w\-]+)(\S+)?$/', $item['video_url'] ) ? 'youtube' : '';
											if ( empty( $video_type ) ):
												$video_type = preg_match( '/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/', $item['video_url'] ) ? 'vimeo' : '';
											endif;
										?>
										<div class="cmma-slide-iframe <?= $video_type ?>-embed cmma-video-embed">
											<div id="cmma-video-replay-btn" class="cmma-video-replay-btn">
												<?= cmma_elementor_icons( 'replay', 'currentColor' ); ?>
											</div>
											<?= cmma_video_oembed_get( $item['video_url'], $video_type ); ?>
											<?php if ($mute_icon_display === 'yes' ):?>
												<div class="audio-btn-block">
													<span class="mute-btn " style="display: none">
														<?= cmma_elementor_icons( 'mute', 'currentColor' ); ?>
													</span>
													<span class="unmute-btn">
														<?= cmma_elementor_icons( 'unmute', 'currentColor' ); ?>
													</span>
												</div>
											<?php endif; ?>
										</div>
									<?php elseif ( ! empty( $item['image']['url'] ) ) : ?>
										<div class="cmma-slide-video cmma-video-embed video-embed">
											<video muted>
												<source src="<?= esc_url( $item['image']['url'] ) ?>" type="video/mp4">
											</video>
										</div>
									<?php endif; ?>
									<div class="cmma-slide-caption">
										<?php if ( !empty( $item['caption'] ) ): ?>
											<p <?php $this->print_render_attribute_string( $item_caption_setting_key ); ?>><?= $item['caption'] ?></p>
										<?php endif; ?>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>
