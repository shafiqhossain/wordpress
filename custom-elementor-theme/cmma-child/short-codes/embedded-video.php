<?php
function _cmmaEmbeddedVideo($atts) {
    ob_start();

    // Extract the 'url' attribute from the shortcode
    $atts = shortcode_atts( array(
        'url' => '', // Default value for 'url'
		'mute_icon_display' => 'yes'
    ), $atts );

    // Store the video URL
    $video_link = esc_url($atts['url']);
	$mute_icon_display = $atts['mute_icon_display'];
    // Begin output with wrapper div
    ?>
    <div class="cmma-embedded-video">
		<?php

			if (empty($video_link)) {
				echo '<p class="error-message">URL MISSING</p>';
			} else {
				// Check if the video is from YouTube or Vimeo
				$video_type = preg_match('/^((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube\.com|youtu.be))(\/(?:[\w\-]+\?v=|embed\/|v\/)?)([\w\-]+)(\S+)?$/', $video_link) ? 'youtube' : '';
				if (empty($video_type)) {
					$video_type = preg_match('/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/', $video_link) ? 'vimeo' : '';
				}
				?>
					<div class="<?= $video_type ?>-embed">
						<div id="cmma-video-replay-btn" class="cmma-video-replay-btn">
							<?= cmma_elementor_icons( 'replay', 'currentColor' ); ?>
						</div>
						<?= cmma_video_oembed_get($video_link, $video_type); ?>
						<?php if ($mute_icon_display === 'yes' ):?>
							<div id="cmma-video-embed-audio" class="cmma-video-embed-audio muted">
								<span class="mute-btn " style="display: none">
									<?= cmma_elementor_icons( 'unmute', 'currentColor' ); ?>
								</span>
								<span class="unmute-btn">
									<?= cmma_elementor_icons( 'mute', 'currentColor' ); ?>
								</span>
							</div>
						<?php endif; ?>

					</div>
				<?php
			}
		?>
    </div>
    <?php

    return ob_get_clean();
}
add_shortcode('cmma_embedded_video', '_cmmaEmbeddedVideo');
