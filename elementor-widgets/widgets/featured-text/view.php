<?php
// Extract settings for better readability
$color_scheme = $settings['color_scheme'] ?? '';

$image       = $settings['image'] ?? '';
$headline    = $settings['headline'] ?? '';
$description = $settings['description'] ?? '';
$button_text = $settings['button_text'] ?? '';
$button_link = esc_url( $settings['button_link']['url'] ?? '' );
$jump_navigation = isset($settings['enable_jump_navigation']) && $settings['enable_jump_navigation'] == 'yes' ? 'cmma-widget-jump-navigation' : '';
$jump_navigation_title = isset($settings['jump_navigation_title']) ? $settings['jump_navigation_title'] : '';

$this->add_inline_editing_attributes( 'headline', 'basic' );
$this->add_inline_editing_attributes( 'description', 'basic' );
$this->add_inline_editing_attributes( 'button_text', 'none' );

?>

<section class="cmma-elementor-widget color-scheme-<?= $color_scheme ?> <?= $jump_navigation ?> <?= $jump_navigation ?>" data-title="<?= $jump_navigation_title ?>">
	<div class="cmma-container">
		<div class="panel-wrapper featured-text">
			<div class="panel-inner featured-text-inner">
				<?php if ( ! empty( $headline ) || ! empty( $description ) || ! empty( $button_text ) ) : ?>
					<div class="panel-content featured-text-content">
						<?php if ( ! empty( $headline ) ): ?>
							<div class="featured-text-left">
								<h2 <?php $this->add_render_attribute( 'headline', 'class', 'panel-content-title featured-text-title' ); echo $this->get_render_attribute_string( 'headline' ); ?>><?= esc_html( $headline ) ?></h2>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $description ) || ! empty( $button_text ) ): ?>
							<div class="featured-text-right">
								<?php if ( ! empty( $description ) ): ?>
									<div <?php $this->add_render_attribute( 'description', 'class', 'panel-content-description featured-text-description' ); echo $this->get_render_attribute_string( 'description' ); ?>><?= $description ?></div>
								<?php endif; ?>
								<?php if ( ! empty( $button_text ) ): ?>
									<div class="panel-content-control featured-text-control">
										<a href="<?= $button_link ?? 'javascript:void(0);' ?>" class="cmma-button cmma-button-type-text">
											<span <?php $this->add_render_attribute( 'button_text', 'class', 'cmma-button-text' ); echo $this->get_render_attribute_string( 'button_text' ); ?>><?= $button_text ?></span>
											<span class="cmma-button-icon"><?= cmma_elementor_icons( 'arrow', 'currentColor' ); ?></span>
										</a>
									</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
