<section class="cmma-elementor-widget panel-wrapper">
	<div class="cmma-container">
		<div class="cmma-categories-wrapper">
			<div class="cmma-categories-head">
				<div class="cmma-categories-list">
					<div data-category-id="all" class="active">All</div>
					<?php foreach (get_categories() as $category) : ?>
						<div data-category-id="<?= esc_attr($category->term_id); ?>"><?= esc_html($category->name); ?></div>
					<?php endforeach; ?>
				</div>
				<div class="cmma-search">
					<?php echo cmma_elementor_icons( 'search', 'currentColor' ); ?>
					<input type="text" id="search-input" placeholder="search">
				</div>
			</div>
			<div class="loader" style="display: none;"><?php echo cmma_elementor_icons( 'loader', 'currentColor' ); ?></div>
			<div class="cmma-post-list">
				<?php
					$args = [
						'posts_per_page' => 8,
						'post_status'    => 'publish',
						'paged'          => 1
					];
					$query = new WP_Query($args);
					if ($query->have_posts()):
						while ($query->have_posts()) :
							$query->the_post();
							echo cmma_elementor_widgets_generate_post_html( $query->post );
						endwhile;
						wp_reset_postdata();
					else:
						echo '<div class="cmma-post">No posts found.</div>';
					endif;
				?>
			</div>
			<div class="load-more-container">
				<div class="load-more-loader" style="display: none;"><?php echo cmma_elementor_icons( 'loader', 'currentColor' ); ?></div>
				<button id="load-more-posts" class="load-more-posts">Load More +</button>
			</div>
		</div>
	</div>
</section>
