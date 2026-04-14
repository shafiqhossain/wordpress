<?php
function _heroSliderShortCode($atts) {
    ob_start();

    $slider = get_field('mediamedia_slideshow');
    $autoplay_option = get_field('autoplayoption');
    $autoplay_speed = get_field('auto_play_speed');
    if ($slider && count($slider)) {
        $slick_settings = [
            'slidesToShow'  => 1,
            'dots'          => false,
            'infinite'      => true,
			'pauseOnHover'  => false,
            'prevArrow'     => '<a href="javascript:void(0);" class="prev-btn">' . cmma_elementor_icons('arrow', 'currentColor') . '</button>',
            'nextArrow'     => '<a href="javascript:void(0);" class="next-btn">' . cmma_elementor_icons('arrow', 'currentColor') . '</button>',
        ];
		if ($autoplay_option) {
			$slick_settings['autoplay'] = true;
			$slick_settings['autoplaySpeed'] = $autoplay_speed;
		}
        $count = sizeof($slider);
        ?>

        <div class="single-hero-slider">
            <div class="<?php if ($count > 1) : ?> cmma-page-slideshow <?php endif; ?>" <?php if ($count > 1) : ?> data-slick="<?= htmlspecialchars(json_encode($slick_settings)); ?>" <?php endif; ?>>
                <?php foreach ($slider as $key => $slide) :
                    $image_id         	= $slide['image'];
                    $image_info       	= cmma_elementor_widgets_get_responsive_image_data($image_id, 'full');
                    $mobile_image_id  	= $slide['mobile_image'];
                    $mobile_image_info	= cmma_elementor_widgets_get_responsive_image_data($mobile_image_id, 'full');
                    $caption			= $slide['caption'];
                    $link				= isset($slide['link']) ? $slide['link'] : null;

					?>
                    <a href="<?= isset($link['url']) ? $link['url'] : 'javascript:void(0);'?>" target="<?= isset($link['target']) ? $link['target'] : '_self' ?>">
                        <div class="<?php if ($count > 1) : ?> slide-content <?php endif; ?> ">
                        	<?php if ($slide['is_video']) {
                                if (isset($slide['video_link']) && !empty($slide['video_link'])) :
                                    $video_type = preg_match('/^((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube\.com|youtu.be))(\/(?:[\w\-]+\?v=|embed\/|v\/)?)([\w\-]+)(\S+)?$/', $slide['video_link']) ? 'youtube' : '';
                                    if (empty($video_type)) :
                                        $video_type = preg_match('/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/', $slide['video_link']) ? 'vimeo' : '';
                                    endif;
                                    ?>
                                    <div class="cmma-slide-iframe <?= $video_type ?>-embed cmma-video-embed">
										<div id="cmma-video-replay-btn" class="cmma-video-replay-btn">
											<?= cmma_elementor_icons( 'replay', 'currentColor' ); ?>
										</div>
                                        <?= cmma_video_oembed_get($slide['video_link'], $video_type); ?>
                                    </div>
                                <?php elseif (!empty($slide['upload_video'])) : ?>
                                    <div class="cmma-slide-video cmma-video-embed video-embed">
                                        <video muted autoplay loop>
                                            <source src="<?= esc_url($slide['upload_video']) ?>" type="video/mp4">
                                        </video>
                                    </div>
                                <?php endif;
                            } else {
                                if (isset($mobile_image_info) && !empty($mobile_image_info['srcset'])) : ?>
                                    <div class="cmma-slide-img mobile-image">
                                        <img srcset="<?= esc_attr($mobile_image_info['srcset']) ?>" src="<?= esc_url($mobile_image_info['url']) ?>" loading="lazy" height="100%" width="100%" alt="" />
                                    </div>
                                <?php endif; ?>
                                <div class="cmma-slide-img desktop-img">
                                    <img srcset="<?= esc_attr($image_info['srcset']) ?>" src="<?= esc_url($image_info['url']) ?>" loading="lazy" height="100%" width="100%" alt="" />
                                </div>
                            <?php } ?>
                            <span><?= $caption; ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php }
    return ob_get_clean();
}
add_shortcode('cmma_hero_slider', '_heroSliderShortCode');