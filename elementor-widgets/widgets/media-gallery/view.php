<?php
    // Extract settings for better readability
    $color_scheme       = $settings['color_scheme'] ?? '';
    $image              = $settings['image'] ?? '';
    $headline           = $settings['headline'] ?? '';
    $description        = $settings['description'] ?? '';
    $button_text        = $settings['button_text'] ?? '';
    $button_link        = esc_url( $settings['button_link']['url'] ?? '' );
    $button_external    = $settings['button_link']['is_external'];
    $button_nofollow    = $settings['button_link']['nofollow'];
	$jump_navigation	= isset($settings['enable_jump_navigation']) && $settings['enable_jump_navigation'] == 'yes' ? 'cmma-widget-jump-navigation' : '';
    $jump_navigation_title = isset($settings['jump_navigation_title']) ? $settings['jump_navigation_title'] : '';

	$image_info         = cmma_elementor_widgets_get_responsive_image_data( $settings['image']['id'], 'full' );
    $modal_id           = 'cmma-modal-' . rand();
    $modal_color_scheme = $settings['modal_color_scheme'] ?? '';
    $show_modal         = ! empty( $settings['gallery_items'] );

    $attr = ($button_external === 'on') ? 'target="_blank"' : '';
    $rel = ($button_nofollow  === 'on') ? 'rel="nofollow"' : '';

    $this->add_inline_editing_attributes( 'headline', 'basic' );
    $this->add_inline_editing_attributes( 'description', 'basic' );
    $this->add_inline_editing_attributes( 'button_text', 'none' );

    $button_icon = (cmma_elementor_widgets_check_external_links($button_link)) ? 'arrow-up-right' : 'arrow';
?>

<section class="cmma-elementor-widget color-scheme-<?= $color_scheme ?> <?= $jump_navigation ?>" data-title="<?= $jump_navigation_title ?>">
    <div class="cmma-container">
        <div class="panel-wrapper panel-two-image-wrapper media-gallery">
            <div class="panel-large-two-image">
                <?php if ( isset( $image_info ) && ! empty( $image_info['srcset'] ) ) : ?>
                    <div class="panel-asset large-two-image-element">
                        <?php if ( $show_modal ) : ?>
                            <a href="javascript:void(0);" class="cmma-modal-button" data-modal-id="<?= $modal_id ?>">
                                <?= cmma_elementor_icons( 'gallery', 'currentColor' ); ?>
                            </a>
                        <?php endif; ?>
                        <img srcset="<?= esc_attr( $image_info['srcset'] ) ?>" src="<?= esc_url( $image_info['url'] ) ?>" loading="lazy" height="100%" width="100%" alt="" />
                    </div>
                <?php endif; ?>
            </div>
            <?php if ( ! empty( $headline ) || ! empty( $description ) || ! empty( $button_text ) ) : ?>
                <div class="panel-aside">
                    <div class="panel-content">
                        <?php if ( ! empty( $headline ) ) : ?>
                            <h5 <?php $this->add_render_attribute( 'headline', 'class', 'panel-content-title media-gallery-title' ); echo $this->get_render_attribute_string( 'headline' ); ?>><?php echo esc_html( $headline ); ?></h5>
                        <?php endif; ?>
                        <?php if ( ! empty( $description ) ) : ?>
                            <div <?php $this->add_render_attribute( 'description', 'class', 'panel-content-description media-gallery-description' ); echo $this->get_render_attribute_string( 'description' ); ?>> <?php echo $description; ?></div>
                        <?php endif; ?>
                        <?php if ( ! empty( $button_text ) ) : ?>
                            <div class="panel-content-control media-gallery-control">
                                <a href="<?= $button_link ?? 'javascript:void(0);'; ?>" class="cmma-button cmma-button-type-text <?= $show_modal ? 'cmma-modal-button' : '' ?>" <?= $show_modal ? ( ' data-modal-id="' . $modal_id . '"' ) : '' ?> <?= $attr ?> <?= $rel ?>>
                                    <span <?php $this->add_render_attribute( 'button_text', 'class', 'cmma-button-text' ); echo $this->get_render_attribute_string( 'button_text' ); ?>><?= $button_text; ?></span>
                                    <span class="cmma-button-icon"><?php echo cmma_elementor_icons( $button_icon, 'currentColor' ); ?></span>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if ( $show_modal ) : ?>
            <?php
                $slick_settings = [
                    'slidesToShow'   	=> 1,
                    'dots'           	=> false,
                    'infinite'       	=> true,
                    'autoplay'       	=> false,
                    'adaptiveHeight'	=> true,
                    'arrows'         	=> false,
                ];
            ?>
            <div id="<?= $modal_id ?>" class="widget-modal media-gallery-modal">
                <div class="cmma-modal-content color-scheme-<?= $modal_color_scheme ?>">
                    <div class="cmma-modal-head">
                        <a href="javascript:void(0);" class="cmma-modal-close" data-modal-id="<?= $modal_id ?>">|</a>
                    </div>
                    <div class="cmma-modal-body">
                        <div class="cmma-container">
                            <div class="gallery-slider" data-slick="<?= htmlspecialchars( json_encode( $slick_settings ) ); ?>">
                                <?php
									if ( ! empty( $settings['gallery_items'] ) ) :
                                        foreach (  $settings['gallery_items'] as $key => $item ) :
                                            $repeater_slider_title = $this->get_repeater_setting_key( 'slider_title', 'gallery_items', $key );
                                            $this->add_render_attribute( $repeater_slider_title, 'class', 'gallery-slide-title' );
                                            $this->add_inline_editing_attributes( $repeater_slider_title ,'basic');

                                            $repeater_slider_content = $this->get_repeater_setting_key( 'slider_content', 'gallery_items', $key );
                                            $this->add_render_attribute( $repeater_slider_content, 'class', 'gallery-slide-description' );
                                            $this->add_inline_editing_attributes( $repeater_slider_content ,'basic'); ?>

											<div class="gallery-slide">
												<div class="gallery-slide-item">
													<div class="gallery-slide-image">
														<img src="<?= $item['slider_image']['url']; ?>">
													</div>
													<div class="gallery-slide-content">
														<div class="gallery-slide-body">
															<div class="gallery-slide-body-inner">
																<?php if ( ! empty( $item['slider_title'] ) ): ?>
																	<h5 <?php $this->print_render_attribute_string( $repeater_slider_title ); ?>><?= $item['slider_title']; ?></h5>
																<?php endif; ?>
																<?php if ( ! empty( $item['slider_content'] ) ): ?>
																	<div <?php $this->print_render_attribute_string( $repeater_slider_content ); ?>>
																		<?= $item['slider_content'];  ?>
																	</div>
																<?php endif; ?>
															</div>
														</div>
														<div class="gallery-slider-controls">
															<div class="gallery-slider-arrows">
																<a href="javascript:void(0);" class="gallery-slider-arrow cmma-prev-btn"><?= cmma_elementor_icons( 'arrow', 'currentColor' ) ?></a>
																<a href="javascript:void(0);" class="gallery-slider-arrow cmma-next-btn"><?= cmma_elementor_icons( 'arrow', 'currentColor' ) ?></a>
															</div>
															<div class="gallery-slider-dots">
																<?= ( $key + 1 ) . '/' . count( $settings['gallery_items'] ) ?>
															</div>
														</div>
													</div>
												</div>
											</div>
										<?php endforeach;
									endif;
								?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
