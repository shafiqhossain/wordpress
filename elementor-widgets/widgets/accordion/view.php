<?php
	$color_scheme  = $settings['color_scheme'] ?? '';
	$headline      = $settings['headline'] ?? '';
	$description   = $settings['description'] ?? '';
	$items         = $settings['items'] ?? '';
	$jump_navigation = isset($settings['enable_jump_navigation']) && $settings['enable_jump_navigation'] == 'yes' ? 'cmma-widget-jump-navigation' : '';
	$jump_navigation_title = isset($settings['jump_navigation_title']) ? $settings['jump_navigation_title'] : '';

	$this->add_inline_editing_attributes( 'headline', 'basic' );
	$this->add_inline_editing_attributes( 'description', 'basic' );
?>

<section class="cmma-elementor-widget color-scheme-<?= $color_scheme ?> <?= $jump_navigation ?>" data-title="<?= $jump_navigation_title ?>">
	<div class="cmma-container">
		<div class="panel-wrapper accordion-wrapper">
			<div class="panel-inner accordion-inner">
				<?php if ( ! empty( $headline ) || ! empty( $description ) ) : ?>
					<div class="panel-content accordion-content">
						<?php if ( ! empty( $headline ) ): ?>
							<h2 <?php $this->add_render_attribute( 'headline', 'class', 'panel-content-title accordion-title' ); echo $this->get_render_attribute_string( 'headline' ); ?>><?= esc_html( $headline ) ?></h2>
						<?php endif; ?>
						<?php if ( ! empty( $description ) ): ?>
							<div <?php $this->add_render_attribute( 'description', 'class', 'panel-content-description accordion-description' ); echo $this->get_render_attribute_string( 'description' ); ?>><?= $description ?></div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $items ) : ?>
				<div class="accordion-items">
					<?php foreach ( $items as $index => $item ) :
						$item_image = cmma_elementor_widgets_get_responsive_image_data( $item['item_image']['id'], 'full' );
						$item_title_setting_key = $this->get_repeater_setting_key( 'item_title', 'items', $index );
						$this->add_render_attribute( $item_title_setting_key, 'class', 'accordion-item-title' );
						$this->add_inline_editing_attributes( $item_title_setting_key, 'basic');

						$item_content_setting_key = $this->get_repeater_setting_key( 'item_content', 'items', $index );
						$this->add_render_attribute( $item_content_setting_key, 'class', 'accordion-item-left' );
						$this->add_inline_editing_attributes( $item_content_setting_key, 'basic');
						?>
						<div class="accordion-item">
							<div <?php $this->print_render_attribute_string( $item_title_setting_key ); ?>><?= $item['item_title']; ?><span class="accordion-icon"> <?= cmma_elementor_icons( 'minus' ); ?></span></div>
							<div class="accordion-item-content">
								<div <?= $this->get_render_attribute_string( $item_content_setting_key ); ?>><?= $item['item_content']; ?></div>
								<div class="accordion-item-right">
									<?php if ( isset( $item_image ) && ! empty( $item_image['srcset'] ) ): ?>
										<div class="accordion-item-image">
											<img srcset="<?= esc_attr( $item_image['srcset'] ) ?>"  src="<?= esc_url( $item_image['url'] ) ?>" loading="lazy" height="100%" width="100%" alt=""  />
										</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
