<?php
// Extract settings for better readability
$color_scheme   = $settings['color_scheme'] ?? '';
$headline       = $settings['headline'] ?? '';
$social_feed_id = str_replace('id_', '', $settings['social_media_feed_id'] ?? '');
$jump_navigation = isset($settings['enable_jump_navigation']) && $settings['enable_jump_navigation'] == 'yes' ? 'cmma-widget-jump-navigation' : '';
$jump_navigation_title = isset($settings['jump_navigation_title']) ? $settings['jump_navigation_title'] : '';

$this->add_inline_editing_attributes( 'headline', 'basic' );
?>

<section class="cmma-elementor-widget cmma-widget color-scheme-<?= $color_scheme ?> <?= $jump_navigation ?>" data-title="<?= $jump_navigation_title ?>">
    <div class="cmma-container social-panel">
        <div class="panel-wrapper">
            <div class="panel-wrapper-inner">
                <?php if ( ! empty( $headline )) : ?>
                    <div class="panel-content cmma-social-content">
                        <?php if ( ! empty( $headline ) ): ?>
                            <h2 <?php $this->add_render_attribute( 'headline', 'class', 'social-panel-content-title social-title' ); echo $this->get_render_attribute_string( 'headline' ); ?>><?= esc_html( $headline ) ?></h2>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Display social_feed_id if available -->
                <?php if ($social_feed_id): ?>
                    <div class="cmma-social-feed">
                        <?= do_shortcode( "[instagram-feed feed='$social_feed_id']" ); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
