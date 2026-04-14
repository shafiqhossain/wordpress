<?php
function collectionShortCode() {?>
	<div class="panel-wrapper-wrap">
		<?php
		ob_start();
		$args = array(
			'post_type'      => 'collection',
			'posts_per_page' => -1,
			'order'          => 'DESC',
		);
		$query = new WP_Query($args);
		?>
		<div class="cmma-collection-list">
			<?php
			$jump_navigation = [];
			$members_modal_data = [];
			if ($query->have_posts()) :
				while ($query->have_posts()) :
					$query->the_post();
					$post_id = get_the_ID();
					$jump_navigation[] =[
						'id' => $post_id,
						'title' => get_the_title()
					];
					?>
					<div class="cmma-collection" >
						<div id="post-<?php echo $post_id; ?>" class="collection-space"></div>
						<h2><?php the_title(); ?></h2>
						<div class="cmma-collection-description">
							<?php echo get_field('short_description'); ?>
						</div>
						<a href="<?= esc_url(get_permalink($post_id)); ?>" class="cmma-button cmma-button-type-text">
							<span class="cmma-button-text">View Collection</span>
							<span class="cmma-button-icon"><?php echo cmma_elementor_icons('arrow', 'currentColor'); ?></span>
						</a>
					</div>
					<?php
					$selected_posts = get_field('posts');
					$post_count = count($selected_posts);
					if (!empty($selected_posts)) { ?>
						<div class="collection-panel-slider">
							<div class="collection-panel-slider-inner <?php if($post_count<=2):?> collection-noslide <?php endif;?>" >
								<?php
									$selected_posts = array_slice($selected_posts, 0, 6);
									foreach ($selected_posts as $index => $post_id) {
										$post = get_post($post_id);
										if (!$post || $post->post_status !== 'publish') {
											continue;
										}
										$post_type = $post->post_type;
										$postID = $post->ID;
										$post_title = $post->post_title;
										$sub_title = get_field('sub_title', $postID);
										$image_id = get_post_thumbnail_id($postID);
										$image_info = cmma_elementor_widgets_get_responsive_image_data($image_id, 'full');
										$modal_id = 'cmma-modal-' . $postID;
										$href = esc_url(get_permalink($post));
										$buton_class = '';
										$data_modal_id = '';
										if($post_type === 'member'):
											$href ='javascript:void(0)';
											$buton_class =   'cmma-modal-button';
											$data_modal_id = 'data-modal-id=' . $modal_id;
											$post_title = $post_title.', '.$sub_title;
											$members_modal_data[$post->ID] = $post;
											$member_landscape_image = get_field('member_landscape_image');
											$horizontal_thumbnail = get_field('horizontal-thumbnail', $post->ID);
										endif;?>
											<div class="collection-panel-slider-item" data-post-id="<?= $postID; ?>">
												<?php if ((isset($image_info) && !empty($image_info['srcset'])) || !empty($horizontal_thumbnail)) : ?>
													<?php if ($post_type == 'project') : ?>
														<div class="cmma-collection-img project">
															<a href="<?= $href; ?>" <?= $data_modal_id ?> class="<?= $buton_class; ?>" >
																<div class="cmma-collection-img-inner">
																	<img srcset="<?= esc_attr($image_info['srcset']) ?>" src="<?= esc_url($image_info['url']) ?>" loading="lazy" height="100%" width="100%" alt="" />
																</div>
															</a>
														</div>
														<p><?= $post_title ?></p>
													<?php endif; ?>

													<?php if ($post_type == 'perspective') : ?>
														<div class="cmma-collection-img perspective">
															<a href="<?= $href; ?>" <?= $data_modal_id ?> class="<?= $buton_class; ?>" >
																<div class="cmma-collection-img-inner">
																	<img srcset="<?= esc_attr($image_info['srcset']) ?>" src="<?= esc_url($image_info['url']) ?>" loading="lazy" height="100%" width="100%" alt="" />
																</div>
															</a>
														</div>
														<p><?= $post_title ?></p>
													<?php endif; ?>

													<?php if ($post_type == 'post') : ?>
														<div class="cmma-collection-img post">
															<a href="<?= $href; ?>" <?= $data_modal_id ?> class="<?= $buton_class; ?>" >
																<div class="cmma-collection-img-inner">
																	<img srcset="<?= esc_attr($image_info['srcset']) ?>" src="<?= esc_url($image_info['url']) ?>" loading="lazy" height="100%" width="100%" alt="" />
																</div>
															</a>
														</div>
														<p><?= $post_title ?></p>
													<?php endif; ?>

													<?php if ($post_type == 'member') : ?>
														<div class="cmma-collection-img member">
															<a href="<?= $href ?>" <?= $data_modal_id ?> class="<?= $buton_class; ?>">
																<div class="cmma-collection-img-inner">
																	<?php
																		$landscape_image = get_field('horizontal-thumbnail', $post->ID);
																	?>
																	<?php if ($landscape_image): ?>
																		<img src="<?php echo esc_url($landscape_image); ?>" />
																	<?php else: ?>
																		<img srcset="<?= esc_attr($image_info['srcset']) ?>" src="<?= esc_url($image_info['url']) ?>" loading="lazy" height="100%" width="100%" alt="" />
																	<?php endif; ?>
																</div>
															</a>
														</div>
														<p><?= $post_title ?></p>
													<?php endif; ?>
												<?php else : ?>
													<div class="panel-item-perspective">
														<a href="<?= $href ?>" <?= $data_modal_id ?> class="<?= $buton_class; ?>" >
															<p><?= ucfirst($post_type) ?></p>
															<h4><?= $post_title ?></h4>
														</a>
													</div>
												<?php endif; ?>
											</div>
						
										<?php
									}
								?>
							</div>
						</div>
						<?php
					}
					
					$post_types = array(
						'post_type'			=> array('member'),
						'post_status'    	=> 'publish',
						'posts_per_page'	=> -1,
						'post__in'      	=> $selected_posts,
					);
					$all_posts = get_posts($post_types);
					?>
					<div class="overlay"></div>
					<?php foreach ($all_posts as $post) :
						$post_type = $post->post_type;
						if($post_type === 'member'):
							$postID = $post->ID;
							$image_id = get_post_thumbnail_id($postID);
							$custom_fields = get_fields($postID);
							$post_content = get_post_field('post_content', $postID);
							$image_info = cmma_elementor_widgets_get_responsive_image_data($image_id, 'full');
						?>
						<div id="cmma-modal-<?= $postID ?>" class="modal widget-modal single-perspective">
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
						<?php endif; ?>					
					<?php endforeach; ?>
					<?php wp_reset_postdata(); ?>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			<?php endif; ?>
		</div>

		<div class="cmma-footer-jump-navigation">
			<div class="cmma-container">
				<div class="cmma-footer-jump-navigation-wrapper">
					<div class="cmma-footer-jump-navigation-heading">Jump to</div>
					<ul>
						<?php foreach ($jump_navigation as $navigationKey => $navigation) { ?>
							<li data-id="<?= $navigationKey; ?>"><a href="#post-<?= $navigation['id']; ?>"><?= $navigation['title']; ?></a></li>
						<?php } ?>
					</ul>
				</div>
			</div>
		</div>

		<?php add_action('wp_footer',function () { ?>
			<script type="text/javascript">

				document.addEventListener('DOMContentLoaded', function () {
					var $j = jQuery;

					// Smooth scrolling for anchor links
					$j('a[href^="#"]').on('click', function (event) {
						event.preventDefault();
						var target = $j(this).attr('href');
						$j('html, body').animate({
							scrollTop: $j(target).offset().top
						}, 100);
						$j('li').removeClass('scrolled');
						$j(this).closest('li').addClass('scrolled');
					});

					// Navigation menu behavior on mouse leave
					$j('.cmma-footer-jump-navigation').on('mouseleave', function () {
						var liHeight = $j(this).find('li').outerHeight();
						var activeLi = $j('li.scrolled').attr('data-id');
						$j('.cmma-footer-jump-navigation ul').animate({
							scrollTop: (parseInt(activeLi) * liHeight)
						}, 0);
					});

					$j('.collection-panel-slider-inner').each(function() {
						var $carousel = $j(this);
						var slidesLength = $carousel.find('.collection-panel-slider-item').length;
						var slickSettings = {
							slidesToShow: (slidesLength < 2) ? slidesLength :2,
							dots: false,
							centerMode: false,
							infinite: true,
							centerPadding: '160px',
							margin:32,
							autoplay: false,
							loop:true,
							autoplaySpeed: 7000,
							prevArrow: '<button class="cmma-prev-btn"><svg width="8" height="24" viewBox="0 0 8 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 18.0072L6.18055 13.484L0 8.99255V7L8 12.9147V14.085L0 20V18.0072Z" fill="currentcolor" /></svg></button>',
							nextArrow: '<button class="cmma-next-btn"><svg width="8" height="24" viewBox="0 0 8 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 18.0072L6.18055 13.484L0 8.99255V7L8 12.9147V14.085L0 20V18.0072Z" fill="currentcolor" /></svg></button>',
							responsive: [
								{
									breakpoint: 1440,
									settings: {
										slidesToShow: 2,
									}
								},
								{
									breakpoint: 991,
									settings: {
										slidesToShow: 1,
									}
								},
								{
									breakpoint: 767,
									margin:6,
									settings: {
										slidesToShow: 1,
										centerPadding: '30px',
									}
								}
							]
						};
						$carousel.slick(slickSettings);
					});

					updateProgressBar();
					calculateLargeImageContentHeight('.collection-panel-slider-item');
				});

				window.addEventListener('resize', function() {
					calculateLargeImageContentHeight('.collection-panel-slider-item');
				});

				function calculateLargeImageContentHeight(widgetClass) {
					var $j = jQuery;
					var $widget = $j(widgetClass);
					var $content = $widget.find('.panel-collection .cmma-collection-list');

					if ($content.length) {
						$widget.find('.collection-panel-slider-item').css({'--large-image-content-height': $content.height() + 'px'});
					}
				}

				function updateProgressBar() {
					const footerFixedLinks = document.querySelector('.cmma-footer-jump-navigation');
					const totalHeight = document.body.scrollHeight - window.innerHeight;
					const progress = (window.pageYOffset / totalHeight) * 100;
					footerFixedLinks.style.setProperty('--progress-bar-width', progress + '%');

					window.onload = function() {
						const footerWrapper = document.querySelector('.cmma-footer-jump-navigation-wrapper');
						const footerLinks = footerWrapper.querySelector('ul');
						const items = footerLinks.querySelectorAll('li');
						if (items.length > 4) {
							footerWrapper.classList.add('overlay-item');
						}
					};

				}

				window.addEventListener('scroll', updateProgressBar);
				window.addEventListener('resize', updateProgressBar);
			</script>
		<?php }); ?>
		<?php return ob_get_clean();?>
	</div>
<?php }
add_shortcode('cmma_collection', 'collectionShortCode');