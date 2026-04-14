<?php
function _cmmaPagePopup()
{
	$page_id = get_queried_object_id();

	$show_modal = get_field('show_modal',$page_id);
	$video_url = get_field('video_url',$page_id);
	$video_file = get_field('video_file',$page_id);

	if (!isset($_COOKIE['wp-settings-page-popup-'.$page_id]) && $show_modal) {
		ob_start();
		?>
			<div class="cmma-video-popup-container">
				<div class="overlay"></div>
				<div id="cmma-video-modal-<?= $page_id; ?>" class="widget-modal cmma-modal-show" data-type="page-popup" data-id="<?= $page_id; ?>">
					<div class="cmma-video-modal">
					<a href="/" class="site-logo ">
						<svg xmlns="http://www.w3.org/2000/svg" width="83" height="17" viewBox="0 0 83 17" fill="none">
							<g clip-path="url(#clip0_5023_7993)">
							<path d="M12.8508 8.32818C12.0757 7.6409 11.2802 7.47919 10.2399 7.35791L3.85525 6.36742C3.67166 6.32699 3.61047 6.16528 3.61047 5.86207V3.78002C3.61047 3.51724 3.67166 3.29489 4.18162 3.29489H13.5852V0H3.38609C2.42738 0 1.63185 0.30321 0.97911 0.889417C0.32637 1.47562 0 2.22354 0 3.07253V6.22592C0 7.17598 0.387565 8.00476 1.1219 8.65161C1.83583 9.29845 2.71295 9.43995 3.63087 9.62188L10.1379 10.6326C10.3826 10.6932 10.3826 10.8347 10.3826 11.1379V13.22C10.3826 13.4828 10.3215 13.7051 9.8115 13.7051H0.407963V17H10.607C11.5453 17 12.3409 16.6968 12.9936 16.1106C13.6463 15.5244 13.9727 14.7765 13.9727 13.9275V10.7539C13.9727 9.80381 13.5852 8.99524 12.8508 8.32818Z" fill="currentColor"></path>
							<path d="M38.4301 0.889417C37.7773 0.30321 36.9818 0 36.0435 0H17.6852V17H21.2753V3.29489H26.1912C26.6808 3.29489 26.7624 3.53746 26.7624 3.78002V16.9798H30.3524V3.29489H35.2684C35.7579 3.29489 35.8395 3.53746 35.8395 3.78002V16.9798H39.4296V3.07253C39.4296 2.22354 39.0828 1.47562 38.4301 0.889417Z" fill="currentColor"></path>
							<path d="M73.168 13.7051C72.6785 13.7051 72.5969 13.4625 72.5969 13.22V11.1379C72.5969 10.8347 72.5969 10.6932 72.8417 10.6326L79.2467 9.64209L79.3487 9.62188H79.3895V13.7051H73.168ZM82.9795 3.07253C82.9795 2.22354 82.6532 1.47562 82.0004 0.889417C81.3477 0.30321 80.5522 0 79.6139 0H69.4148V3.29489H78.8183C79.3079 3.29489 79.3895 3.53746 79.3895 3.78002V5.86207C79.3895 6.16528 79.3283 6.30678 79.1447 6.36742L72.7601 7.35791C71.7198 7.49941 70.9242 7.6409 70.1491 8.32818C69.3944 8.99524 69.0272 9.8038 69.0272 10.7539V13.9073C69.0272 14.7562 69.3536 15.5042 70.0063 16.0904C70.6591 16.6766 71.4546 16.9798 72.3929 16.9798H82.9999L82.9795 3.07253Z" fill="currentColor"></path>
							<path d="M44.5495 0.889417C43.8968 1.47562 43.5704 2.22354 43.5704 3.07253V17H47.1605V3.78002C47.1605 3.51724 47.2217 3.29489 47.7317 3.29489H52.6272V17H56.2173V3.78002C56.2173 3.51724 56.2785 3.29489 56.7884 3.29489H61.7044V17H65.274V0H46.9157C45.9978 0 45.1819 0.30321 44.5495 0.889417Z" fill="currentColor"></path>
							</g>
							<defs>
							<clipPath id="clip0_5023_7993">
								<rect width="83" height="17" fill="currentColor"></rect>
							</clipPath>
							</defs>
						</svg>
						</a>
						<a href="javascript:void(0);" class="cmma-modal-close" data-modal-id="cmma-video-modal-<?= $page_id; ?>">
							<svg fill="currentColor" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
								viewBox="0 0 490 490" xml:space="preserve">
							<polygon points="456.851,0 245,212.564 33.149,0 0.708,32.337 212.669,245.004 0.708,457.678 33.149,490 245,277.443 456.851,490
								489.292,457.678 277.331,245.004 489.292,32.337 "/>
							</svg>
						</a>
						<div class="cmma-video-modal-body">
							<?php
								if ($video_url && !empty($video_url)) :
									$video_type = preg_match('/^((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube\.com|youtu.be))(\/(?:[\w\-]+\?v=|embed\/|v\/)?)([\w\-]+)(\S+)?$/', $video_url) ? 'youtube' : '';
									if (empty($video_type)) :
										$video_type = preg_match('/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/', $video_url) ? 'vimeo' : '';
									endif;
								?>
								<div class="cmma-iframe-body <?= $video_type ?>-embed cmma-video-embed">
									<?= cmma_video_oembed_get($video_url, $video_type); ?>
								</div>
							<?php elseif ($video_file && !empty($video_file)) : ?>
								<div class="cmma-video-body cmma-video-embed video-embed">
									<video muted autoplay loop>
										<source src="<?= esc_url($video_file) ?>" type="video/mp4">
									</video>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		<?php

		return  ob_get_clean();
	}
}
add_shortcode('cmma_page_popup', '_cmmaPagePopup');



// Function to display the shortcode in the footer
function cmma_page_popup_add_to_footer() {
    if (function_exists('do_shortcode')) {
        echo do_shortcode('[cmma_page_popup]');
    }
}

// Hook the function to the wp_footer action
add_action('wp_footer', 'cmma_page_popup_add_to_footer');
