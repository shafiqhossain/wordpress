<?php
function _heroShowMoreInfo() {
	$modal_id	= 'smma-modal-' . rand();
	ob_start();
	$popup_color_scheme = get_field( 'more_information_popup_color_scheme' ); ?>
	<div class="smma-show-more-info">
		<?php if( get_field('display_show_more_information') ) : ?>
			<div class="smma-modal-button" data-modal-id="<?= $modal_id ?>">Show More Information &nbsp; <?= smma_elementor_icons( 'plus', 'currentColor' ); ?></div>
		<?php endif ?>
		<div class="overlay"></div>
		<div id="<?= $modal_id ?>" class="modal widget-modal color-scheme-<?= $popup_color_scheme; ?>">
			<div class="smma-modal-content color-scheme-dark">
				<span class="smma-modal-close close" data-modal-id="<?= $modal_id ?>"><?= smma_elementor_icons('minus', 'currentColor'); ?></span>
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
add_shortcode( 'smma_show_more_info', '_heroShowMoreInfo' );