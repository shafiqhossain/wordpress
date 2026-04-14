<?php
function _deepDiveShortCode($atts) {
	ob_start();

	$short_description = get_field('short_description');
	$link = get_field('link');
	$deep_dive = get_field('deep_dive');
	$modal_id = 'smma-modal-' . rand();
	$widget_id = 'image-with-deep-dive-' . rand();
	$modal_content = get_field('deep_dive_modal_content');
	?>
	<div class="smma-container">
		<div id="<?= $widget_id ?>" class="panel-wrapper">
			<div class="panel-deep-dive">
				<?php if (!empty($short_description)) : ?>
					<div class="panel-content">
						<?php if (!empty($short_description)) : ?>
							<div><?php echo $short_description; ?></div>
						<?php endif; ?>
						<?php if ($deep_dive == 1) : ?>
							<div class="panel-content-control large-image-control">
								<a href="javascript:void(0);" class="smma-button smma-button-type-text smma-modal-button" data-modal-id="<?= $modal_id ?>">
									<span class="smma-btn-text">Deep Dive</span>
									<span class="smma-button-icon"><?php echo smma_elementor_icons('arrow', 'currentColor'); ?></span>
								</a>
							</div>
						<?php else : ?>
							<?php if ($link) : ?>
								<a href="<?= $link ?>" class="smma-button">
									<span>Deep Dive</span>
									<span class="smma-button-icon"><?php echo smma_elementor_icons('arrow', 'currentColor'); ?></span>
								</a>
							<?php endif; ?>
						<?php endif; ?>

						<div id="<?= $modal_id ?>" class="widget-modal">
							<div class="smma-modal-content color-scheme-dark">
								<div class="smma-modal-head">
									<h3>Deep Dive</h3>
									<a href="javascript:void(0);" class="smma-modal-close" data-modal-id="<?= $modal_id ?>">|</a>
								</div>
								<div class="smma-modal-body wysiwyg-text">
									<?php if (!empty($modal_content)) : ?>
										<div class="smma-modal-description">
											<?php echo $modal_content; ?>
										</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		document.addEventListener('DOMContentLoaded', function() {
			calculateLargeImageContentHeight('#<?= $widget_id ?>');
		});

		window.addEventListener('resize', function() {
			calculateLargeImageContentHeight('#<?= $widget_id ?>');
		});

		function calculateLargeImageContentHeight(widgetId) {
			var $j = jQuery;
			var $widget = $j(widgetId);
			var $content = $widget.find('.panel-deep-dive .panel-content');

			if ($content.length) {
				$widget.find('.panel-content-control').css({ '--large-image-content-height': $content.height() + 'px' });
			}
		}
	</script>

	<?php
	return ob_get_clean();
}
add_shortcode('smma_deep_dive', '_deepDiveShortCode');