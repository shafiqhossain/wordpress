<?php
// Extract settings for better readability
$color_scheme	= $settings['color_scheme'] ?? '';
$placement		= $settings['placement'] ?? '';
?>

<section class="cmma-elementor-widget cmma-widget color-scheme-<?= $color_scheme ?>">
	<div class="cmma-container">
		<div class="panel-wrapper services-list content-placement-<?php echo $placement; ?>">
			<div class="cmma-services-mask"></div>
			<div class="services-list-left">
				<div class="services-list-left-inner">
					<?php
						$args = array(
							'post_type'      => 'service',
							'posts_per_page' => -1,
							'orderby'=>'title',
							'order'=>'ASC',
						);

						$query = new WP_Query( $args );
						if ( $query->have_posts() ) :
							while ( $query->have_posts() ) : $query->the_post();
								$page_title = get_the_title();
								$short_title = get_post_meta( get_the_ID(), 'short_title', true );
								if ($short_title) {
									$page_title = $short_title;
								} ?>
								<div class="service-item">
									<h3 class="service-item-title" data-target="service-item-<?= get_the_ID(); ?>">
										<a href="<?php the_permalink(); ?>">
											<?= $page_title; ?>
										</a>
									</h3>
								</div>
								<?php endwhile;
							wp_reset_postdata();
						endif;
					?>
				</div>
			</div>

			<div class="services-list-right">
				<a href="javascript:void(0);" class="close-btn">|</a>
				<?php
					if ( $query->have_posts() ) :
						while ( $query->have_posts() ) : $query->the_post(); ?>
							<div class="service-item-content" data-target="service-item-<?= get_the_ID(); ?>">
								<div class="service-item-inner">
									<?php
										the_field('preview_text');
									?>
									<div class="panel-content-control service-item-control">
										<a href="<?php the_permalink(); ?>" class="cmma-button cmma-button-type-text">
											<span class="cmma-button-text">Learn More</span>
											<span class="cmma-button-icon"><?= cmma_elementor_icons( 'arrow', 'currentColor' ); ?></path></svg></span>
										</a>
									</div>
								</div>
							</div>
						<?php
						endwhile;
						wp_reset_postdata();
					endif;
				?>
			</div>
		</div>
	</div>
</section>
