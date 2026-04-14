<?php
function _heroShowMoreInfo() {
	$modal_id	= 'cmma-modal-' . rand();
	ob_start();
	$popup_color_scheme = get_field( 'more_information_popup_color_scheme' ); ?>
	<div class="cmma-show-more-info">
		<?php if( get_field('display_show_more_information') ) : ?>
			<div class="cmma-modal-button" data-modal-id="<?= $modal_id ?>">Show More Information &nbsp; <?= cmma_elementor_icons( 'plus', 'currentColor' ); ?></div>
		<?php endif ?>
		<div class="overlay"></div>
		<div id="<?= $modal_id ?>" class="modal widget-modal color-scheme-<?= $popup_color_scheme; ?>">
			<div class="cmma-modal-content color-scheme-dark">
				<span class="cmma-modal-close close" data-modal-id="<?= $modal_id ?>"><?= cmma_elementor_icons('minus', 'currentColor'); ?></span>
				<div class="modal-head">
					<h2><?= get_field( 'more_information_headline' ); ?></h2>
					<?= get_field( 'more_information_short_description' ); ?>
				</div>
				<div class="modal-columns">
					<?php
						$entries = get_field( 'more_information_entries' );
						if ( $entries && count( $entries ) ) :	?>
						<?php foreach ( $entries as $key => $entry ) : ?>
							<div class="modal-column">
								<h5><?= $entry['headline']; ?></h5>
								<?= $entry['short_description']; ?>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'cmma_show_more_info', '_heroShowMoreInfo' );