<?php
function _footerSharePost() {
    ob_start();
    ?>
	<div class="cmma-container">
    	<div class="cmma-share-block">
			<div class="cmma-share-block-inner">
				<p> Share: </p>
				<div class="cmma-shared-links">
					<a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode(get_permalink()); ?>" target="_blank" rel="noopener noreferrer"> LinkedIn</a> /
					<a href="mailto:?subject=<?= urlencode(get_the_title()); ?>&body=<?= urlencode(get_permalink()); ?>" target="_blank" rel="noopener noreferrer"> Email</a> /
					<div class="copy-link-wrapper">
						<a class="copy-link" data-clipboard-text="<?= esc_url(get_permalink()); ?>">Copy Link</a>
						<div class="copy-tooltip">Copied!</div>
					</div>
				</div>
			</div>
		</div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('cmma_share_post', '_footerSharePost');