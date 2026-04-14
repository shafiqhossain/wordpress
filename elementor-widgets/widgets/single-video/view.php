<?php
// Extract settings for better readability
$color_scheme = $settings['color_scheme'] ?? '';
$autoplay = isset($settings['autoplay']) ? $settings['autoplay'] : '';
$placement = $settings['placement'] ?? '';
$headline = $settings['headline'] ?? '';
$description = $settings['description'] ?? '';
$button_text = $settings['button_text'] ?? '';
$button_link = esc_url( $settings['button_link']['url'] ?? '' );
$video_url = $settings['video_url'] ?? '';
$jump_navigation = isset($settings['enable_jump_navigation']) && $settings['enable_jump_navigation'] == 'yes' ? 'cmma-widget-jump-navigation' : '';
$jump_navigation_title = isset($settings['jump_navigation_title']) ? $settings['jump_navigation_title'] : '';
$mute_icon_display = $settings['mute_icon_display'] ?? '';

// Rendering Inline Editing
$this->add_inline_editing_attributes( 'headline', 'basic' );
$this->add_inline_editing_attributes( 'description', 'basic' );
?>

<section class="cmma-elementor-widget color-scheme-<?= $color_scheme ?> <?= $jump_navigation ?>" data-title="<?= $jump_navigation_title ?>">
	<div class="cmma-container">
		<div class="panel-wrapper single-video content-placement-<?= $placement ?>">
			<div class="panel-inner single-video-inner">
				<?php if ( ! empty( $video_url ) ):
					$video_type = preg_match('/^((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube\.com|youtu.be))(\/(?:[\w\-]+\?v=|embed\/|v\/)?)([\w\-]+)(\S+)?$/', $video_url) ? 'youtube' : '';
					if (empty($video_type)) :
						$video_type = preg_match('/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/', $video_url) ? 'vimeo' : '';
					endif; ?>
					<div class="panel-asset single-video-element animated wow fadeIn single-hero-slider" data-wow-delay=".25s"  style="visibility :hidden">
						<?php if ($autoplay == 'no') { ?>
							<div class="youtube-thumb">
								<img src="https://img.youtube.com/vi/<?= _cmma_get_youtube_video_id($video_url); ?>/maxresdefault.jpg" />
								<div class="control-icon">
									<div class="play"><span></span></div>
									<div class="pause"><span></span><span></span></div>
								</div>
							</div>
						<?php }?>
						<div id="cmma-video-replay-btn" class="cmma-video-replay-btn" style="display:<?= $autoplay == 'no' ? 'none' : 'block'; ?>">
							<?= cmma_elementor_icons( 'replay', 'currentColor' ); ?>
						</div>
						<div class="panel-video-embed cmma-video-embed" style="display:<?= $autoplay == 'no' ? 'none' : 'block'; ?>">
							<?= cmma_video_oembed_get($video_url, $video_type, $autoplay == 'no' ? 0 : 1); ?>
						</div>
						<?php if ($mute_icon_display == 'yes' ):?>
							<div id="cmma-video-embed-audio" class="cmma-video-embed-audio <?= $autoplay == 'no' ? '' : 'muted'; ?>" style="display:<?= $autoplay == 'no' ? 'none' : 'block'; ?>">
								<span class="mute-btn " style="display: none">
									<?= cmma_elementor_icons( 'mute', 'currentColor' ); ?>
								</span>
								<span class="unmute-btn">
									<?= cmma_elementor_icons( 'unmute', 'currentColor' ); ?>
								</span>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $headline ) || ! empty( $description ) || ! empty( $button_text ) ): ?>
					<div class="panel-content single-video-content">
						<div class="content-heading">
							<?php if ( ! empty( $headline ) ): ?>
								<h2 <?php $this->add_render_attribute( 'headline', 'class', 'panel-content-title cmma-title' ); echo $this->get_render_attribute_string( 'headline' ); ?>><?= esc_html( $headline ) ?></h2>
							<?php endif; ?>
						</div>
						<div class="content-block">
							<?php if ( ! empty( $description ) ): ?>
								<div <?php $this->add_render_attribute( 'description', 'class', 'panel-content-description cmma-description' ); echo $this->get_render_attribute_string( 'description' ); ?>><?= $description ?></div>
							<?php endif; ?>
							<?php if ( ! empty( $button_text ) ): ?>
								<div class="single-video-control">
									<a href="<?= $button_link ?? 'javascript:void(0);' ?>" class="cmma-button cmma-button-type-text">
										<span <?php $this->add_render_attribute( 'button_text', 'class', 'cmma-button-text' ); echo $this->get_render_attribute_string( 'button_text' ); ?>><?= $button_text ?></span>
										<span class="cmma-button-icon"><?= cmma_elementor_icons( 'arrow', 'currentColor' ); ?></span>
									</a>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>