<?php
function __smma_slideshow( $atts ) {
	ob_start();

	$slider = get_field( 'slides' );
	if ($slider && count($slider)) :
		$slick_settings = [
			'slidesToShow'  => 1,
			'dots'          => false,
			'infinite'      => false,
			'autoplay'      => false,
			'autoplaySpeed' => 3000,
			'adaptiveHeight'=> true,
			'prevArrow'     => '<a href="javascript:void(0);" class="smma-prev-btn">' . smma_elementor_icons( 'arrow', 'currentColor' ) . '</button>',
			'nextArrow'     => '<a href="javascript:void(0);" class="smma-next-btn">' . smma_elementor_icons( 'arrow', 'currentColor' ) . '</button>'
		];
		$count = sizeof($slider);
		?>
		<div class="smma-page-media">
			<div class="<?php if ($count > 1) : ?> smma-page-slideshow <?php endif; ?>" <?php if ($count > 1) : ?> data-slick="<?= htmlspecialchars(json_encode			($slick_settings)); ?>" <?php endif; ?>>

				<?php foreach ($slider as $key => $slide) :
                    $image_id         	= $slide['image'];
                    $image_info       	= smma_elementor_widgets_get_responsive_image_data($image_id, 'full');
                    $mobile_image_id  	= $slide['mobile_image'];
                    $mobile_image_info	= smma_elementor_widgets_get_responsive_image_data($mobile_image_id, 'full');
                    $caption			= $slide['caption'];
                    $link				= isset($slide['link']) ? $slide['link'] : null;
					?>

					<a href="<?= isset($link['url']) ? $link['url'] : 'javascript:void(0);'?>" target="<?= isset($link['target']) ? $link['target'] : '_self' ?>">
                        <div class="<?php if ($count > 1) : ?> slide-content <?php endif; ?> ">
                        	<?php if ($slide['is_video']) {
                                if (isset($slide['embed']) && !empty($slide['embed'])) :
                                    $video_type = preg_match('/^((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube\.com|youtu.be))(\/(?:[\w\-]+\?v=|embed\/|v\/)?)([\w\-]+)(\S+)?$/', $slide['embed']) ? 'youtube' : '';
                                    if (empty($video_type)) :
                                        $video_type = preg_match('/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/', $slide['embed']) ? 'vimeo' : '';
                                    endif;
                                    ?>
                                    <div class="smma-slide-iframe <?= $video_type ?>-embed smma-video-embed">
                                        <?= smma_video_oembed_get($slide['embed'], $video_type); ?>
                                    </div>
                                <?php elseif (!empty($slide['video_file'])) : ?>
                                    <div class="smma-slide-video smma-video-embed video-embed">
                                        <video muted autoplay loop>
                                            <source src="<?= esc_url($slide['video_file']) ?>" type="video/mp4">
                                        </video>
                                    </div>
                                <?php endif;
                            } else {
                                if (isset($mobile_image_info) && !empty($mobile_image_info['srcset'])) : ?>
                                    <div class="smma-slide-img mobile-image">
                                        <img srcset="<?= esc_attr($mobile_image_info['srcset']) ?>" src="<?= esc_url($mobile_image_info['url']) ?>" loading="lazy" height="100%" width="100%" alt="" />
                                    </div>
                                <?php endif; ?>
                                <div class="smma-slide-img desktop-img">
                                    <img srcset="<?= esc_attr($image_info['srcset']) ?>" src="<?= esc_url($image_info['url']) ?>" loading="lazy" height="100%" width="100%" alt="" />
                                </div>
                            <?php } ?>
                            <span><?= $caption; ?></span>
                        </div>
					</a>

                <?php endforeach; ?>
			</div>
		</div>
	<?php endif;
	return ob_get_clean();
}
add_shortcode( 'smma_slideshow', '__smma_slideshow' );
