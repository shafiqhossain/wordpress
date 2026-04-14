<?php
$color_scheme      = $settings['color_scheme'] ?? '';
$image             = $settings['image'] ?? '';
$headline          = $settings['heading'] ?? '';
$description       = $settings['description'] ?? '';
$short_description = $settings['short_description'] ?? '';

$this->add_inline_editing_attributes( 'headline', 'basic' );
$this->add_inline_editing_attributes( 'description', 'basic' );
$this->add_inline_editing_attributes( 'short_description', 'basic' );
?>

<section class="cmma-elementor-widget color-scheme-<?php echo $color_scheme; ?>">
	<div class="cmma-container">
		<div class="stats-panel">
			<div class="stats-panel-inner">
				<?php
				if ( $settings['stats'] ) :
					$slick_settings = array(
						'slidesToShow'  => 5,
						'dots'          => false,
						'infinite'      => true,
						'autoplay'      => false,
						'autoplaySpeed'	=> 5000,
						'prevArrow'     => '<a href="javascript:void(0);" class="cmma-prev-btn">' . cmma_elementor_icons( 'arrow', 'currentColor' ) . '</button>',
						'nextArrow'     => '<a href="javascript:void(0);" class="cmma-next-btn">' . cmma_elementor_icons( 'arrow', 'currentColor' ) . '</button>',
						'responsive'    => array(
							array(
								'breakpoint'	=> 991,
								'settings'		=> array(
									'slidesToShow'	=> 3,
								),
							),
							array(
								'breakpoint'	=> 767,
								'settings'   	=> array(
									'slidesToShow'    	=> 4,
									'vertical'        	=> true,
									'verticalSwiping'	=> true,
								),
							),
						),
					);
					?>
					<div class="stats-list <?php echo count( $settings['stats'] ) > 4 ? 'stats-slider' : ''; ?>" data-slick="<?php echo htmlspecialchars( json_encode( $slick_settings ) ); ?>">
						<?php
							foreach ( $settings['stats'] as $index => $item ) :
								$item_heading_setting_key = $this->get_repeater_setting_key( 'heading', 'stats', $index );
								$this->add_inline_editing_attributes( $item_heading_setting_key, 'basic');

								$item_description_setting_key = $this->get_repeater_setting_key( 'description', 'stats', $index );
								$this->add_inline_editing_attributes( $item_description_setting_key, 'basic');

								$item_short_description_setting_key = $this->get_repeater_setting_key( 'short_description', 'stats', $index );
								$this->add_inline_editing_attributes( $item_short_description_setting_key, 'basic');
							?>
							<div class="stats-list-item">
								<div class="stats-item-top">
									<h2 <?php $this->print_render_attribute_string( $item_heading_setting_key ); ?>><?php echo $item['heading']; ?></h2>
									<p <?php $this->print_render_attribute_string( $item_description_setting_key ); ?>><?php echo $item['description']; ?></p>
								</div>
								<div class="stats-item-bottom">
									<p <?php $this->print_render_attribute_string( $item_short_description_setting_key ); ?>><?php echo $item['short_description']; ?></p>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
