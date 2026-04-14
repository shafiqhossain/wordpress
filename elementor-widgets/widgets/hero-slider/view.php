<?php
$items  = $settings['images'] ?? [];
$height = $settings['height']['size'] . $settings['height']['unit'];
?>

<section class="cmma-elementor-widget">
	<div class="hero-slider-wrapper color-scheme-dark">
		<?php if ( count( $items ) ) : ?>
		<?php
			$slick_settings = [
			'slidesToShow'  => 1,
			'dots'          => false,
			'infinite'      => true,
			'autoplay'      => true,
			'autoplaySpeed' => 3000,
			'prevArrow'     => '<a href="javascript:void(0);" class="cmma-prev-btn">' . cmma_elementor_icons( 'arrow', 'currentColor' ) . '</button>',
			'nextArrow'     => '<a href="javascript:void(0);" class="cmma-next-btn">' . cmma_elementor_icons( 'arrow', 'currentColor' ) . '</button>' ,
			];
		?>
		<div class="cmma-hero-wrapper">
			<div class="cmma-hero-carousel" data-slick="<?= htmlspecialchars(json_encode($slick_settings)); ?>">
				<?php foreach ( $items as $index => $item ):
						$image_info = cmma_elementor_widgets_get_responsive_image_data( isset( $item['image']['id'] ) && ! empty( $item['image']['id'] ) ? $item['image']['id'] : '' , 'full' );
						$item_caption_setting_key = $this->get_repeater_setting_key( 'caption', 'images','caption-link', $index );
						?>
						<div class="cmma-slide">
							<div class="cmma-hero-inner">
								<?php if ( isset( $image_info ) && ! empty( $image_info['srcset'] ) ) : ?>
									<div class="cmma-hero-image" style="background-image: url('<?= esc_url( $image_info['url'] ) ?>'); height: <?php echo esc_attr($height); ?>">
										<div class="cmma-hero-caption">
											<?php if ( !empty( $item['caption'] ) ): ?>
												<?php if ( !empty( $item['caption-link']['url'] ) ): ?>
													<a href="<?= $item['caption-link']['url'] ?>">
												<?php endif; ?>
												<h5 <?php $this->print_render_attribute_string( $item_caption_setting_key ); ?>><?= $item['caption'] ?></h5>
												<?php if ( !empty( $item['caption-link']['url'] ) ): ?>
													</a>
												<?php endif; ?>
											<?php endif; ?>
										</div>
									</div>
								<?php endif; ?>
							</div>
						</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php endif; ?>
	</div>
</section>
