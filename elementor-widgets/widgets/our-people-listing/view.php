<section class="cmma-elementor-widget panel-wrapper">
	<div class="cmma-container">
		<div class="cmma-our-people-wrapper">
			<div class="cmma-categories-head">
				<div class="cmma-categories-list">
					<div data-category-id="all" class="active">All</div>
					<?php
						$videos_and_images = $settings['videos'] ?? [];
						$terms = get_terms('role');
						foreach ($terms as $term) {
							echo '<div data-category-id="' . esc_attr($term->term_id) . '">' . esc_html($term->name) . '</div>';
						}
					?>
				</div>
				<div class="cmma-search">
					<?php echo cmma_elementor_icons('search', 'currentColor'); ?>
					<input type="text" id="search-input" placeholder="search">
				</div>
			</div>
			<div class="loader" style="display: none;">
				<?php echo cmma_elementor_icons('loader', 'currentColor'); ?>
			</div>
			<div class="cmma-member-list" data-videos='<?= json_encode($videos_and_images); ?>'>
				<?php include(plugin_dir_path(__FILE__).'peple-listing.php'); ?>
			</div>
			<div class="cmma-member-no-results" style="display: none;">
				<p>No results found.</p>
			</div>
			<div class="load-more-container">
				<div class="load-more-loader" style="display: none;">
					<?php echo cmma_elementor_icons('loader', 'currentColor'); ?>
				</div>
				<button id="load-more-posts" class="load-more-posts" style="display: <?= cmma_elementor_widgets_our_people_posts_count() > 24 ? 'block' : 'none'; ?>;">Load More +</button>
			</div>
		</div>
	</div>
</section>

<?php add_action('wp_footer', function () { ?>
	<script type="text/javascript">
		document.addEventListener('DOMContentLoaded', function() {
			calculateLargeImageContentHeight('.cmma-collection');
		});

		window.addEventListener('resize', function() {
			calculateLargeImageContentHeight('.cmma-collection');
		});

		function calculateLargeImageContentHeight(widgetClass) {
			var $j = jQuery;
			var $widget = $j(widgetClass);
			var $content = $widget.find('.panel-collection .cmma-member-list');

			if ($content.length) {
				$widget.find('.cmma-collection').css({'--large-image-content-height': $content.height() + 'px'});
			}
		}
	</script>
<?php }); ?>
