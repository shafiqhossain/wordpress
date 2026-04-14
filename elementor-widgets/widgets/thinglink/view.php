<?php
$color_scheme = $settings['color_scheme'] ?? '';
$embed_code   = $settings['embed_code'] ?? '';

$this->add_inline_editing_attributes( 'embed_code', 'basic' );
?>
<?php //Move the style into individual file if the styles are greater than several lines  ?>
<style type="text/css">
	.thinglink-panel {
		display: block;
	}
</style>
<section class="cmma-elementor-widget color-scheme-<?= $color_scheme ?>">
	<div class="cmma-container">
		<div class="panel-wrapper thinglink-panel">
			<div <?php echo $this->get_render_attribute_string( 'embed_code' ); ?>><?= $settings['embed_code'] ?></div>
			<script async charset="utf-8" src="//cdn.thinglink.me/jse/embed.js"></script>
		</div>
	</div>
</section>
