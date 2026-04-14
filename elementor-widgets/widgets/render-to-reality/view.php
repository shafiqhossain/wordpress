<?php

$color_scheme = $settings['color_scheme'] ?? '';
$first_image  = $settings['first_image'] ?? '';
$final_image  = $settings['final_image'] ?? '';
$headline     = $settings['headline'] ?? '';
$description  = $settings['description'] ?? '';
$button_text  = $settings['button_text'] ?? '';
$button_link  = esc_url( $settings['button_link']['url'] ?? '' );
$jump_navigation = isset($settings['enable_jump_navigation']) && $settings['enable_jump_navigation'] == 'yes' ? 'cmma-widget-jump-navigation' : '';
$jump_navigation_title = isset($settings['jump_navigation_title']) ? $settings['jump_navigation_title'] : '';

if ( isset( $settings['first_image'] ) && ! empty( $settings['first_image']['id'] ) ) :
	$first_image_info = cmma_elementor_widgets_get_responsive_image_data( $settings['first_image']['id'], 'full' );
endif;

if ( isset( $settings['final_image'] ) && ! empty( $settings['final_image']['id'] ) ) :
	$final_image_info = cmma_elementor_widgets_get_responsive_image_data( $settings['final_image']['id'], 'full' );
endif;

$this->add_inline_editing_attributes( 'headline', 'basic' );
$this->add_inline_editing_attributes( 'description', 'basic' );
$this->add_inline_editing_attributes( 'button_text', 'none' );
?>

<section class="cmma-elementor-widget color-scheme-<?php echo $color_scheme; ?> <?= $jump_navigation ?>" data-title="<?= $jump_navigation_title ?>">
	<div class="cmma-container">
		<div class="panel-wrapper render-reality">
			<div class="panel-inner render-reality-inner">
				<div class="panel-asset render-reality-asset">
					<div class="panel-asset-comparison render-reality-asset-comparison wow fadeIn" data-wow-delay=".25s" >
						<?php if ( isset( $first_image_info ) && isset( $first_image_info['srcset'] ) ) : ?>
							<img srcset="<?php echo esc_attr( $first_image_info['srcset'] ); ?>" src="<?php echo esc_url( $first_image_info['url'] ); ?>" loading="lazy" height="100%" width="100%" alt="" />
						<?php endif; ?>
						<?php if ( isset( $final_image_info ) && isset( $final_image_info['srcset'] ) ) : ?>
							<div class="panel-asset-resize render-reality-asset-resize">
								<img srcset="<?php echo esc_attr( $final_image_info['srcset'] ); ?>" src="<?php echo esc_url( $final_image_info['url'] ); ?>" loading="lazy" height="100%" width="100%" alt="" />
							</div>
						<?php endif; ?>
						<div class="panel-asset-divider render-reality-asset-divider">
							<span class="panel-asset-arrows render-reality-asset-arrows">
								<?php echo cmma_elementor_icons( 'arrow' ); ?>
								<?php echo cmma_elementor_icons( 'arrow' ); ?>
							</span>
						</div>
					</div>
				</div>

				<?php if ( ! empty( $headline ) || ! empty( $description ) || ! empty( $button_text ) ): ?>
					<div class="panel-content two-column render-reality-content">
						<?php if ( ! empty( $headline ) ): ?>
                            <div class="panel-content-left">
							    <h2 <?php $this->add_render_attribute( 'headline', 'class', 'panel-content-title render-reality-title' ); echo $this->get_render_attribute_string( 'headline' ); ?>><?= esc_html( $headline ) ?></h2>
                            </div>
						<?php endif; ?>
                            <?php if ( ! empty( $description ) || ! empty( $button_text ) ): ?>
                            <div class="panel-content-right">
                                <?php if ( ! empty( $description ) ): ?>
                                    <div <?php $this->add_render_attribute( 'description', 'class', 'panel-content-description render-reality-description' ); echo $this->get_render_attribute_string( 'description' ); ?>><?= $description ?></div>
                                <?php endif; ?>
                                <?php if ( ! empty( $button_text ) ): ?>
                                    <div class="panel-content-control render-reality-control">
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
