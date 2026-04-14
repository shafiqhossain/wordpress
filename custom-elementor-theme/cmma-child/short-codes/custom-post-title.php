<?php
function _cmmaCustomPostTitle() {
    ob_start();

	$title = get_the_title();
	$short_title = get_field('short_title');
	?>
		<div class="cmma-custom-post-title">
			<h2><?= $short_title ? $short_title : $title; ?></h2>
		</div>
	<?php
    return ob_get_clean();
}
add_shortcode('cmma_custom_post_title', '_cmmaCustomPostTitle');