<?php
	$postID = $member->ID;
	$image_id = get_post_thumbnail_id($postID);
	$custom_fields = get_fields($postID);
	$post_content = get_post_field('post_content', $postID);
	$image_info = cmma_elementor_widgets_get_responsive_image_data($image_id, 'full');
?>
<div class="overlay"></div>
<div id="cmma-modal-<?= $postID ?>" class="modal widget-modal cmma-modal-show single-perspective">
	<div class="cmma-author-modal cmma-modal-content color-scheme-dark">
		<div class="cmma-show-more-info">
			<span class="cmma-modal-close close" data-modal-id="cmma-modal-<?= $postID ?>"><?= cmma_elementor_icons('minus', 'currentColor'); ?></span>
		</div>
		<div class="modal-head">
			<h2><?= $custom_fields['first_name']; ?> <?= $custom_fields['last_name']; ?></h2>
			<?php if (isset($custom_fields['licensure_certificate']) && !empty($custom_fields['licensure_certificate'] ) ) : ?>
				<p><?= $custom_fields['licensure_certificate']; ?></p>
			<?php endif ?>
			<?php if ( !empty($custom_fields['role_2'] ) ) : ?>
				<p><?= $custom_fields['role_2']; ?></p>
			<?php endif ?>
			<?php if ( !empty($custom_fields['role_3'] ) ) : ?>
				<p><?= $custom_fields['role_3']; ?></p>
			<?php endif ?>
		</div>
		<div class="author-modal-columns">
			<div class="quotes-modal-author-info">
				<?php if ( isset( $image_info ) && ! empty( $image_info['srcset'] ) ) : ?>
					<div class="quotes-modal-author-info-left">
						<img srcset="<?= esc_attr( $image_info['srcset'] ) ?>" src="<?= esc_url( $image_info['url'] ) ?>" loading="lazy" height="100%" width="100%" alt="" />
					</div>
				<?php endif; ?>
				<div class="quotes-modal-author-info-right">
					<?php if ( $custom_fields['email'] ) : ?>
						<a href="mailto:<?= $custom_fields['email']; ?>">Email</a><br>
					<?php endif; ?>
					<?php if ( $custom_fields['contact_number'] ) : ?>
						<a href="tel:<?= $custom_fields['contact_number']; ?>"><?= $custom_fields['contact_number']; ?></a>
					<?php endif; ?>
					<?php if ( $custom_fields['education'] ) : ?>
						<div class="education"><p>Education </p><?= $custom_fields['education']; ?></div>
					<?php endif; ?>
					<?php if ( $custom_fields['affiliation'] ) : ?>
						<div class="affiliation"><p>Affiliation</p><?= $custom_fields['affiliation']; ?></div>
					<?php endif; ?>
				</div>
			</div>
			<?php if (!empty($post_content)):?>
				<div class="quotes-modal-main-content"><?= $post_content; ?></div>
			<?php endif; ?>

			<?php if ($custom_fields['slider_image'] && count($custom_fields['slider_image'])): ?>
				<div class="cmma-people-slider <?= esc_attr($custom_fields['hide_slider'] ? 'hide' : ''); ?>">
					<h6><?= esc_html($custom_fields['slider-title']); ?></h6>
					<?php
					$slick_settings = [
						'slidesToShow'  => 1,
						'dots'          => false,
						'infinite'      => true,
						'autoplay'      => true,
						'autoplaySpeed' => 3000,
						'prevArrow'     => '<a href="javascript:void(0);" class="cmma-prev-btn">' . cmma_elementor_icons('arrow', 'currentColor') . '</a>',
						'nextArrow'     => '<a href="javascript:void(0);" class="cmma-next-btn">' . cmma_elementor_icons('arrow', 'currentColor') . '</a>',
					];
					?>

					<div class="cmma-people-slider-wrapper" data-slick="<?= esc_attr(json_encode($slick_settings)); ?>">
						<?php foreach ($custom_fields['slider_image'] as $key => $image) { ?>
							<div class="slide-item">
								<a href="<?php the_sub_field('slider_link'); ?>" target="_blank">
									<img src="<?= esc_url($image['slide_image']); ?>" alt=""/>
									<p><?= $image['slide_caption']; ?></p>
								</a>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>