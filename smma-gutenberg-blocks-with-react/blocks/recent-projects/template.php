<?php
/**
 * Template for the Recent Projects block front-end output.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 * @param array    $projects   Array of WP_Post objects.
 *
 * @package smma-gutenberg-blocks
 */
?>
<div <?php echo wp_kses_data( get_block_wrapper_attributes( array( 'class' => 'smma-recent-projects' ) ) ); ?>>
	<h2 class="smma-recent-projects__heading"><?php esc_html_e( 'Recent Projects', 'smma-gutenberg-blocks' ); ?></h2>
	<div class="smma-recent-projects__grid">
		<?php foreach ( $projects as $project ) : ?>
			<?php
			$start_date        = get_post_meta( $project->ID, 'smma_project_start_date', true );
			$end_date          = get_post_meta( $project->ID, 'smma_project_end_date', true );
			$short_description = get_post_meta( $project->ID, 'smma_project_short_description', true );

			if ( empty( $short_description ) ) {
				$short_description = get_the_excerpt( $project );
			}
			?>
			<article class="smma-recent-projects__card">
				<h3 class="smma-recent-projects__title">
					<a href="<?php echo esc_url( get_permalink( $project ) ); ?>">
						<?php echo esc_html( get_the_title( $project ) ); ?>
					</a>
				</h3>
				<?php if ( ! empty( $short_description ) ) : ?>
					<p class="smma-recent-projects__description">
						<?php echo esc_html( $short_description ); ?>
					</p>
				<?php endif; ?>
				<div class="smma-recent-projects__dates">
					<?php if ( ! empty( $start_date ) ) : ?>
						<span class="smma-recent-projects__date smma-recent-projects__date--start">
							<strong><?php esc_html_e( 'Start:', 'smma-gutenberg-blocks' ); ?></strong>
							<?php echo esc_html( $start_date ); ?>
						</span>
					<?php endif; ?>
					<?php if ( ! empty( $end_date ) ) : ?>
						<span class="smma-recent-projects__date smma-recent-projects__date--end">
							<strong><?php esc_html_e( 'End:', 'smma-gutenberg-blocks' ); ?></strong>
							<?php echo esc_html( $end_date ); ?>
						</span>
					<?php endif; ?>
				</div>
				<a class="smma-recent-projects__link" href="<?php echo esc_url( get_permalink( $project ) ); ?>">
					<?php esc_html_e( 'View Project →', 'smma-gutenberg-blocks' ); ?>
				</a>
			</article>
		<?php endforeach; ?>
	</div>
</div>
