<?php
$settings           = $this->get_settings_for_display();
$color_scheme       = $settings['color_scheme'] ?? '';
$content_length      = $settings['content_length'] ?? '';
$hide_quote_img		= $settings['hide_quote_img'] ?? '';
$modal_color_scheme	= $settings['modal_color_scheme'] ?? '';
$placement          = $settings['placement'] ?? '';

if ( ! empty( $settings['quote'] ) ) :
	$quote_id                  = str_replace('id_','',$settings['quote']);
	$quote_content             = get_post_field( 'post_content', $quote_id );
	$bio_link_text             = get_post_meta( $quote_id, 'bio_link_text', true );
	$show_author_in_modal      = get_post_meta( $quote_id, 'show_author_in_modal', true );
	$author_external_link      = get_post_meta( $quote_id, 'author_external_link', true );
	$author_id                 = get_post_meta( $quote_id, 'author', true ); 
	$url			                 = $author_external_link ? $author_external_link : get_the_permalink($author_id);

	if ( $show_author_in_modal && $author_id ) :
		$author  = get_post( $author_id );
		if ( $author ) :
			$first_name       	= get_post_meta( $author->ID, 'first_name', true );
			$last_name       	= get_post_meta( $author->ID, 'last_name', true );
			$author_name     	= trim( $first_name . ' ' . $last_name );
			$licensure			= get_post_meta( $author->ID, 'licensure_certificate', true );
			$quotes          	= get_post_meta( $author->ID, 'role_1', true );
			$corporate_title     = get_post_meta( $author->ID, 'role_2', true );
			$job_title          	= get_post_meta( $author->ID, 'role_3', true );
			$gallery         	= get_post_meta( $author->ID, 'gallery', true );
			$email           	= get_post_meta( $author->ID, 'email', true );
			$phone           	= get_post_meta( $author->ID, 'contact_number', true );
			$education       	= get_post_meta( $author->ID, 'education', true );
			$affiliation     	= get_post_meta( $author->ID, 'affiliation', true );
			$author_image    	= cmma_elementor_widgets_get_responsive_image_data( get_post_thumbnail_id( $author->ID ), 'full' );
			$author_content	 	= $author->post_content;
		endif;
	endif;
	else:
		return;
endif;
?>

<section class="cmma-elementor-widget color-scheme-<?= $color_scheme; ?>">
	<div class="cmma-container">
		<div class="panel-wrapper quote-wrapper content-placement-<?= $settings['placement']; ?>">
			<div class="panel-inner quote-panel-inner">
				<?php if ( isset( $author_image ) && ! empty( $author_image['srcset'] ) ) : ?>
					<div class="quote-panel-left quote-panel-img-<?= $hide_quote_img; ?>">
						<img srcset="<?php echo esc_attr( $author_image['srcset'] ); ?>" src="<?php echo esc_url( $author_image['url'] ); ?>" loading="lazy" height="100%" width="100%" alt="" />
					</div>
				<?php endif; ?>

				<div class="quote-panel-right">
					<div class="panel-content quote-panel-content wysiwyg-text content-length-<?= $content_length; ?>">
						<?= $quote_content ?>
						<?php if ( ! empty( $author_name ) ): ?>
							<div class="author-content-control">
								<?= $author_name ?><?php if ( ! empty( $quotes ) ) : ?>, <span><?= $quotes ?></span><?php endif ?>
							</div>
						<?php endif; ?>
					</div>
					<?php if ( ! empty( $bio_link_text ) && ( $show_author_in_modal || ! empty( $author_external_link ) ) ) : ?>
						<div class="panel-content-control">
							<a href="<?= $url; ?>" target="<?= $author_external_link ? '_blank' : ''; ?>" class="cmma-button cmma-button-type-text">
								<span <?php $this->add_render_attribute( 'button_text', 'class', 'cmma-button-text' ); echo $this->get_render_attribute_string( 'button_text' ); ?>><?= $bio_link_text; ?></span>
								<span class="cmma-button-icon"><?= cmma_elementor_icons( 'arrow', 'currentColor' ); ?></span>
							</a>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
