<?php
/**
 * The template for displaying search results.
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $wp_query;

$total_pages = $wp_query->max_num_pages;
?>
<main id="content" class="search-page">
	<div class="cmma-container">
		<div class="search-wrapper">
			<div class="search-top">
				<div class="search-icon">
					<?php
					if ( function_exists( 'cmma_elementor_icons' ) ) :
						echo cmma_elementor_icons( 'search' );
					endif;
					?>
				</div>
				<div class="search-form">
					<?= get_search_form(); ?>
				</div>
			</div>
			<?php if ( have_posts() ) : ?>
				<div class="search-result">
					<div class="search-filters">
						<?php
							$post_types         = get_search_filters();
							$allowed_post_types = ['project', 'perspective', 'member', 'post'];
							$filters            = [];
              $exclude_post_types = ['collection','quote'];
							if ( ! empty( $post_types ) ) :
                foreach ( $post_types as $post_type ) :
                  if ( in_array( $post_type->post_type, $allowed_post_types ) && ! in_array( $post_type->post_type, $exclude_post_types ) ) :
                    $post_type_object                 = get_post_type_object( $post_type->post_type );
                    $filters[$post_type_object->name] = $post_type_object->labels->name;
                  endif;
                endforeach;
              endif;
						?>
						<ul>
							<li><a href="javascript:void(0);" class="active" data-key="any"><?= __( 'All' ) ?></a></li>
							<?php
							foreach ( $filters as $key => $label ) :
								?>
								<li><a href="javascript:void(0);" data-key="<?= $key ?>"><?= $label ?></a></li>
								<?php
							endforeach;
							?>
						</ul>
					</div>
					<div id="search-items-grid" class="items-grid">
						<?php
						while ( have_posts() ) :
							the_post();

							include get_stylesheet_directory() . '/helpers/html/search.php';
						endwhile;
						?>
					</div>
					<div class="load-more-container">
						<div class="load-more-loader" style="display: none;"><?php echo cmma_elementor_icons( 'loader', 'currentColor' ); ?></div>
						<button id="load-more-button" class="load-more-button" data-current-page="1" data-total-pages="<?= $total_pages ?>" <?= $total_pages == 1 ? 'style="display: none;"' : '' ?>><?= __(' Load More + ') ?></button>
					</div>
				</div>
			<?php else : ?>
				<div class="search-empty">
					<p><?php echo esc_html__( 'It seems we can\'t find what you\'re looking for.', 'hello-elementor' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</div>
</main>
<?php add_action( 'wp_footer', function () { ?>
	<script type="text/javascript">
		document.addEventListener('DOMContentLoaded', function() {
			window.$j = window.$j || jQuery;
			var $loadMoreButton = $j('.search-result #load-more-button'),
				$loadMoreLoader = $j('.search-result .load-more-loader'),
				$filterButton = $j('.search-filters ul li a');

			$filterButton.click(function(e) {
				if ($j(this).hasClass('active')) {
					return;
				}

				$filterButton.removeClass('active');
				$j(this).addClass('active');

				$j('#search-items-grid').html('');

				$loadMoreLoader.show();
				$loadMoreButton.hide();
				$loadMoreButton.attr('data-current-page', 1);

				getSearchResult(1);

				return false;
			});

			$loadMoreButton.click(function(e) {
				var page = parseInt($j(this).attr('data-current-page')) + 1,
					totalPages = parseInt($j(this).attr('data-total-pages'));

				$loadMoreLoader.show();
				$loadMoreButton.hide();

				if (page > totalPages) {
					return false;
				}

				getSearchResult(page, totalPages);

				return false;
			});

			function getSearchResult(page, totalPages) {
				$j.ajax({
					url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
					type: 'POST',
					data: {
						s: '<?= get_query_var( 's' ) ?>',
						filter: $j('.search-filters a.active').attr('data-key'),
						page: page,
						action: 'cmma_search_posts'
					},
					success: function(response) {
						$loadMoreButton.attr('data-current-page', page);
						$loadMoreLoader.hide();

						if (response && response.data) {
							if (response.data.status === 'success') {
								$j('#search-items-grid').append(response.data.html);
								$loadMoreButton.show();
								$loadMoreButton.attr('data-total-pages', response.data.total_pages);

								if ((totalPages && page === totalPages) || response.data.total_pages === 1) {
									$loadMoreButton.hide();
								}
							} else if (response.data.status === 'error') {
								$loadMoreButton.hide();
							}
						}
					}
				});
			}
		});
	</script>
<?php }); ?>
