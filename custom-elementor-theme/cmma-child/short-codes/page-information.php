<?php
function __cmma_page_information() {
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

	<div class="cmma-page-information-container">
		<div class="cmma-page-spacer<?= $slides && count( $slides ) ? ' has-slideshow' : '' ?>"></div>
		<?= do_shortcode( '[cmma_slideshow]' ); ?>
		<div class="cmma-page-head">
			<div class="cmma-page-title">
				<?php
					if (isset($heading_size) && $heading_size === 'large') {
						echo '<h1>' . get_the_title() . '</h1>';
					} else {
						echo '<h2>' . get_the_title() . '</h2>';
					}
				?>
			</div>
			<div class="cmma-page-information">
				<div class="cmma-page-left">
					<?php if ( $menu_items ) : ?>
						<ul class="cmma-page-menu">
							<?php
								foreach( $menu_items as $key => $menu_item ) :
									$page = $menu_item['page'];
									if ($page) : ?>
										<li class="cmma-page-menu-item"><a href="<?= get_permalink( $page->ID ) ?>"><?= $page->post_title ?></a></li>
									<?php endif;
								endforeach;
							?>
						</ul>
					<?php endif; ?>
					<?php if ( $file_enable_download && $file_download_button ) : ?>
						<div class="cmma-page-download">
							<a href="<?= $file_download_button['url'] ?>" class="cmma-button cmma-button-type-text without-animation">
								<span class="cmma-button-text"><?= esc_html( $file_download_button['title'] ); ?></span>
								<span class="cmma-button-icon">
									<?php if ( function_exists( 'cmma_elementor_icons' ) ) : ?>
										<?= cmma_elementor_icons( 'download', 'currentColor' ); ?>
									<?php endif; ?>
								</span>
							</a>
						</div>
					<?php endif; ?>
				</div>
				<div class="cmma-page-content">
					<?php if ( ! empty( $optional_overline ) ) : ?>
						<h5><?= $optional_overline ?></h5>
					<?php endif; ?>

					<?php if ( ! empty( $short_description ) ) : ?>
						<?= $short_description ?>
					<?php endif; ?>

					<?php if ( ! empty( $optional_link ) ) : ?>
						<div class="cmma-page-link">
							<a href="<?= $optional_link['url'] ?>" class="cmma-button cmma-button-type-text without-animation">
								<span class="cmma-button-text"><?= esc_html( $optional_link['title'] ); ?> </span>
								<span class="cmma-button-icon"><?php echo cmma_elementor_icons( 'arrow', 'currentColor' ); ?></span>
							</a>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $gravity_form_id ) ) : ?>
						<div class="cmma-page-link">
							<a href="#" class="cmma-button cmma-button-type-text without-animation cmma-gravity-form-button">
								<span class="cmma-button-text">Sign up for updates</span>
								<span class="cmma-button-icon"><?php echo cmma_elementor_icons( 'arrow', 'currentColor' ); ?></span>
							</a>
							<div class="cmma-gravity-form" style="display: none;">
								<?= do_shortcode( '[gravityform id="' . $gravity_form_id . '"]' ); ?>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $content_columns ) ) : ?>
						<div class="cmma-page-content-columns">
							<?php foreach ( $content_columns as $content_column  ) : ?>
								<?php if ( ! empty( $content_column['content'] ) ) : ?>
									<div class="cmma-page-content-column">
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

add_shortcode( 'cmma_page_information', '__cmma_page_information' );
