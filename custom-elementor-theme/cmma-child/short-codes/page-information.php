<?php
function __smma_page_information() {
	ob_start();
	$menu_items                = get_field( 'menu_items' );
	$short_description         = get_field( 'short_description' );
	$optional_overline         = get_field( 'optional_overline' );
	$optional_link             = get_field( 'optional_link' );
	$file_enable_download      = get_field( 'file_enable_download' );
	$file_button_text          = get_field( 'file_download_button_text' );
	$file_download_button      = get_field( 'file_download_button_link' );
	$enable_callout            = get_field( 'enable_callout' );
	$callout_overline          = get_field( 'callout_overline' );
	$callout_short_description = get_field( 'callout_short_description' );
	$callout_button_link       = get_field( 'callout_button_link' );
	$slides                    = get_field( 'slides' );
	$content_columns           = get_field( 'content_columns' );
	$heading_size							 = get_field( 'select_page_heading_size' );
	$gravity_form_id					 = get_field( 'choose_gravity_form' );
	?>

	<div class="smma-page-information-container">
		<div class="smma-page-spacer<?= $slides && count( $slides ) ? ' has-slideshow' : '' ?>"></div>
		<?= do_shortcode( '[smma_slideshow]' ); ?>
		<div class="smma-page-head">
			<div class="smma-page-title">
				<?php
					if (isset($heading_size) && $heading_size === 'large') {
						echo '<h1>' . get_the_title() . '</h1>';
					} else {
						echo '<h2>' . get_the_title() . '</h2>';
					}
				?>
			</div>
			<div class="smma-page-information">
				<div class="smma-page-left">
					<?php if ( $menu_items ) : ?>
						<ul class="smma-page-menu">
							<?php
								foreach( $menu_items as $key => $menu_item ) :
									$page = $menu_item['page'];
									if ($page) : ?>
										<li class="smma-page-menu-item"><a href="<?= get_permalink( $page->ID ) ?>"><?= $page->post_title ?></a></li>
									<?php endif;
								endforeach;
							?>
						</ul>
					<?php endif; ?>
					<?php if ( $file_enable_download && $file_download_button ) : ?>
						<div class="smma-page-download">
							<a href="<?= $file_download_button['url'] ?>" class="smma-button smma-button-type-text without-animation">
								<span class="smma-button-text"><?= esc_html( $file_download_button['title'] ); ?></span>
								<span class="smma-button-icon">
									<?php if ( function_exists( 'smma_elementor_icons' ) ) : ?>
										<?= smma_elementor_icons( 'download', 'currentColor' ); ?>
									<?php endif; ?>
								</span>
							</a>
						</div>
					<?php endif; ?>
				</div>
				<div class="smma-page-content">
					<?php if ( ! empty( $optional_overline ) ) : ?>
						<h5><?= $optional_overline ?></h5>
					<?php endif; ?>

					<?php if ( ! empty( $short_description ) ) : ?>
						<?= $short_description ?>
					<?php endif; ?>

					<?php if ( ! empty( $optional_link ) ) : ?>
						<div class="smma-page-link">
							<a href="<?= $optional_link['url'] ?>" class="smma-button smma-button-type-text without-animation">
								<span class="smma-button-text"><?= esc_html( $optional_link['title'] ); ?> </span>
								<span class="smma-button-icon"><?php echo smma_elementor_icons( 'arrow', 'currentColor' ); ?></span>
							</a>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $gravity_form_id ) ) : ?>
						<div class="smma-page-link">
							<a href="#" class="smma-button smma-button-type-text without-animation smma-gravity-form-button">
								<span class="smma-button-text">Sign up for updates</span>
								<span class="smma-button-icon"><?php echo smma_elementor_icons( 'arrow', 'currentColor' ); ?></span>
							</a>
							<div class="smma-gravity-form" style="display: none;">
								<?= do_shortcode( '[gravityform id="' . $gravity_form_id . '"]' ); ?>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $content_columns ) ) : ?>
						<div class="smma-page-content-columns">
							<?php foreach ( $content_columns as $content_column  ) : ?>
								<?php if ( ! empty( $content_column['content'] ) ) : ?>
									<div class="smma-page-content-column">
										<?= $content_column['content'] ?>
									</div>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

add_shortcode( 'smma_page_information', '__smma_page_information' );
