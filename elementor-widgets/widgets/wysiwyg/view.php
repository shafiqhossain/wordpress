<?php
// Extract settings for better readability
$color_scheme = $settings['color_scheme'] ?? '';
$placement    = $settings['placement'] ?? '';
$description  = $settings['wysiwyg'] ?? '';
$jump_navigation = isset($settings['enable_jump_navigation']) && $settings['enable_jump_navigation'] == 'yes' ? 'cmma-widget-jump-navigation' : '';
$jump_navigation_title = isset($settings['jump_navigation_title']) ? $settings['jump_navigation_title'] : '';

// Rendering Inline Editing
$this->add_inline_editing_attributes( 'wysiwyg', 'basic' );
?>

<section class="cmma-elementor-widget color-scheme-<?= $color_scheme ?> <?= $jump_navigation ?>" data-title="<?= $jump_navigation_title ?>">
	<div class="cmma-container">
		<div class="panel-wrapper wysiwyg-wrapper content-placement-<?= $placement ?>">
			<div class="panel-inner wysiwyg-inner">
				<?php if ( ! empty( $description ) ) : ?>
					<div class="panel-content wysiwyg-content wysiwyg-text">
						<div <?php $this->add_render_attribute( 'wysiwyg', 'class', 'panel-content-description cmma-description' ); echo $this->get_render_attribute_string( 'wysiwyg' ); ?>><?= $description ?></div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
