<?php
function _heroAuthors() {
	ob_start();
	$authors = get_field( 'perspective_author' );
	if ( ! empty( $authors ) ) :
		$countAuthors = count( $authors );
		?>
		<div class="smma-author-block <?php if ( $countAuthors > 1 ) : ?> multiple-authors<?php endif; ?>">
			<div class="smma-author-block-outer">
				<?php if ( $countAuthors > 1 ) : ?> <h5> Meet the Authors: </h5> <?php else : ?> <h5> Meet the Author: </h5><?php endif; ?>
				<div class="smma-author-block-inner ">
					<?php foreach ( $authors as $author ) : ?>
						<div class="smma-meet_authors">
							<?php
								$post_id              = $author->ID;
								$post                 = get_post( $post_id );
								$next_post_image_id   = get_post_thumbnail_id( $post_id );
								$next_post_image_url  = wp_get_attachment_image_url( $next_post_image_id, 'full' );
								$next_post_permalink  = get_permalink( $post_id );
								$custom_fields        = get_fields( $post_id );
								$repeater_field_value = get_field( 'our_people_gallery', $post_id );
								$image_info           = smma_elementor_widgets_get_responsive_image_data( $next_post_image_id, 'full' );
								$modal_id	= 'smma-modal-' . rand();

								$author_slider = get_field( 'slider_image', $post_id );
								$author_slider_title = get_field( 'slider-title', $post_id );
								$author_hide_slider = get_field( 'hide_slider', $post_id );

							?>
							<div class="smma-show-more-info ">
								<?= $post->post_title; ?><?php if (!empty($custom_fields['role_1'])): ?>, <?= $custom_fields['role_1']; ?><?php endif; ?>
								<div>
									<?php if ( isset($custom_fields['show_bio']) && $custom_fields['show_bio'] == 1	) { ?>
										<a href="<?= get_the_permalink($post_id);?>" class="smma-button smma-modal-button">
											<span class="smma-button-text">View Bio</span>
											<span class="smma-button-icon">
												<?= smma_elementor_icons( 'arrow', 'currentColor' ); ?>
											</span>
										</a>
									<?php } ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	<?php
	endif;
	return ob_get_clean();
}
add_shortcode( 'smma_post_author', '_heroAuthors' );