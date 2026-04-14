<?php
// Extract settings for better readability
$color_scheme       = $settings['color_scheme'] ?? '';
$modal_color_scheme = $settings['modal_color_scheme'] ?? '';
$placement          = $settings['placement'] ?? '';
$headline           = $settings['headline'];
$button_text        = $settings['button_text'] ?? '';
$pdf_url            = $this->get_settings('pdf')['url'];
$pdf_attachment_id  = $this->get_settings('pdf')['id'];
$file_is_gated		= $settings['file_is_gated'] ?? '';
$gravity_form_id	= $settings['gravity_form_id'] ?? '';
$form_desc	        = get_field('form_description') ?? '';
$jump_navigation	= isset($settings['enable_jump_navigation']) && $settings['enable_jump_navigation'] == 'yes' ? 'cmma-widget-jump-navigation' : '';
$jump_navigation_title = isset($settings['jump_navigation_title']) ? $settings['jump_navigation_title'] : '';

$modal_id	= 'cmma-modal-' . rand();

$download_button = false;
if (!$pdf_url) {
	$pdfFile	= get_field('file_download_sheet_file');
	if ($pdfFile) {
		$pdf_url = $pdfFile['url'];
		$pdf_attachment_id = $pdfFile['id'];
	}
	$download_button = false;
	$download_button	= get_field('hide_download_button');
	$button_text	= get_field('file_download_button_text');
	$form_text	= get_field('file_download_button_text');
	$color_scheme	= get_field('color_scheme') ?? '';
	$file_is_gated	= get_field('file_is_gated') ?? '';
	$gravity_form_id	= get_field('gravity_form');
	$modal_color_scheme	= get_field('modal_color_scheme') ?? '';
}

// Store the PDF URL in a transient
set_transient('cmma_elementor_pdf_id', $pdf_attachment_id, DAY_IN_SECONDS);

$this->add_inline_editing_attributes('headline', 'basic');
$this->add_inline_editing_attributes('button_text', 'none');

if (!$download_button) {
?>
	<section class="cmma-elementor-widget color-scheme-<?= $color_scheme; ?> <?= $jump_navigation ?>" data-title="<?= $jump_navigation_title ?>">
		<div class="cmma-container">
			<div class="panel-wrapper content-placement-<?= $placement; ?>">
				<div class="download-panel download-content-panel">
					<?php if ($headline) : ?>
						<h2 <?= $this->get_render_attribute_string('headline'); ?>><?= esc_html($headline); ?></h2>
					<?php endif; ?>
					<div class="clearfix clearboth"></div>
						<?php if ($button_text) : ?>
							<?php if ($file_is_gated == '1' || $file_is_gated == 'true') : ?>
								<div class="cmma-btn cmma-modal-button" <?= $this->get_render_attribute_string('button_text'); ?> data-modal-id="<?= $modal_id ?>">
									<?= esc_html($button_text); ?> <?= cmma_elementor_icons('download', 'currentColor'); ?>
								</div>
							<?php else : ?>
								<a class="cmma-btn" target="_blank" href="<?= $pdf_url; ?>"><?= esc_html($button_text); ?> <?= cmma_elementor_icons('download', 'currentColor'); ?></a>
							<?php endif; ?>
						<?php endif; ?>
					<div class="overlay"></div>
					<div id="<?= $modal_id ?>" class="modal widget-modal">
						<div class="cmma-modal-content color-scheme-<?= $modal_color_scheme; ?>">
							<div class="cmma-modal-head">
								<h2 <?= $this->get_render_attribute_string('button_text'); ?>><?= esc_html($button_text); ?></h2>
								<?php if ($form_desc) : ?>
									<p><?php echo $form_desc; ?></p>
								<?php endif; ?>
								<span class="cmma-modal-close" data-modal-id="<?= $modal_id ?>"><?= cmma_elementor_icons('minus', 'currentColor'); ?></span>
							</div>
							<div class="cmma-modal-description">
								<?= do_shortcode('[gravityform id="'. $gravity_form_id .'" ajax="true"]'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php }?>