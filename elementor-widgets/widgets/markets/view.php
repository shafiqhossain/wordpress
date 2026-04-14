<?php
	$headline     = $settings['headline'] ?? '';
	$color_scheme = $settings['color_scheme'] ?? '';
	$jump_navigation = isset($settings['enable_jump_navigation']) && $settings['enable_jump_navigation'] == 'yes' ? 'cmma-widget-jump-navigation' : '';
	$jump_navigation_title = isset($settings['jump_navigation_title']) ? $settings['jump_navigation_title'] : '';

	$query        = new WP_Query( array(
		'post_type'      => 'market',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	));

	$this->add_inline_editing_attributes('headline', 'basic');
?>

<section class="cmma-elementor-widget color-scheme-<?= $color_scheme ?> <?= $jump_navigation ?>" data-title="<?= $jump_navigation_title ?>">
	<div class="cmma-container">
		<div class="panel-wrapper markets-wrapper">
			<div class="markets-list">
				<?php if ( !empty( $headline ) ) : ?>
					<div class="markets-list-left">
						<h5 <?php $this->add_render_attribute( 'headline', 'class', 'markets-list-title' ); echo $this->get_render_attribute_string( 'headline' ); ?>><?= esc_html( $headline ) ?></h5>
					</div>
				<?php endif; ?>

				<div class="markets-list-right">
					<?php
					if ( $query->have_posts() ) :
						while ( $query->have_posts() ) :
							$query->the_post();
							?>
							<div class="market-item">
								<div class="market-item-heading">
									<?php $short_title = get_field( 'short_title' ); ?>
									<h2 class="market-item-title">
										<a href="<?php the_permalink(); ?>">
											<?= htmlspecialchars($short_title ?: get_the_title(), ENT_QUOTES, 'UTF-8'); ?>
											<?= cmma_elementor_icons( 'minus', 'currentColor' ); ?>
										</a>
									</h2>
								</div>
								<div class="market-item-content" style="display: none">
									<div class="market-item-description">
										<?php the_field('preview_text'); ?>
									</div>
									<div class="market-item-control">
										<a href="<?php the_permalink(); ?>" class="cmma-button cmma-button-type-text">
											<span class="cmma-button-text">View Market</span>
											<span class="cmma-button-icon"><?= cmma_elementor_icons( 'arrow', 'currentColor' ); ?></span>
										</a>
									</div>
								</div>
							</div>
						<?php endwhile;
						wp_reset_postdata();
					endif;
					?>
				</div>
			</div>
		</div>
	</div>
</section>
