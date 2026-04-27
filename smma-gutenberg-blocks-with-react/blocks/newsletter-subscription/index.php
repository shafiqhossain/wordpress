<?php
/**
 * Newsletter Subscription Block registration.
 *
 * @package smma-gutenberg-blocks
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers the Newsletter Subscription block.
 */
function smma_newsletter_subscription_block_init() {
	register_block_type(
		__DIR__,
		array(
			'render_callback' => 'smma_newsletter_subscription_render_callback',
		)
	);
}
add_action( 'init', 'smma_newsletter_subscription_block_init' );

/**
 * Enqueue the front-end form handler script.
 */
function smma_newsletter_enqueue_frontend_script() {
	if ( ! is_admin() && has_block( 'smma/newsletter-subscription' ) ) {
		wp_enqueue_script(
			'smma-newsletter-frontend',
			plugins_url( 'frontend.js', __FILE__ ),
			array(),
			'1.0.0',
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'smma_newsletter_enqueue_frontend_script' );

/**
 * Renders the Newsletter Subscription block on the front end.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Inner block content.
 * @param WP_Block $block      Block instance.
 * @return string  Rendered HTML output.
 */
function smma_newsletter_subscription_render_callback( $attributes, $content, $block ) {
	$block_title     = isset( $attributes['blockTitle'] ) ? $attributes['blockTitle'] : __( 'Subscribe to Our Newsletter', 'smma-gutenberg-blocks' );
	$subheading      = isset( $attributes['subheading'] ) ? $attributes['subheading'] : '';
	$button_label    = isset( $attributes['buttonLabel'] ) ? $attributes['buttonLabel'] : __( 'Subscribe Now', 'smma-gutenberg-blocks' );
	$success_message = isset( $attributes['successMessage'] ) ? $attributes['successMessage'] : __( 'Thank you for subscribing!', 'smma-gutenberg-blocks' );
	$nonce           = wp_create_nonce( 'wp_rest' );
	$api_url         = esc_url( rest_url( 'smma/v1/newsletter-subscribe' ) );

	ob_start();
	?>
	<div <?php echo wp_kses_data( get_block_wrapper_attributes( array( 'class' => 'smma-newsletter' ) ) ); ?>>
		<div class="smma-newsletter__inner">
			<div class="smma-newsletter__header">
				<h2 class="smma-newsletter__title"><?php echo esc_html( $block_title ); ?></h2>
				<?php if ( ! empty( $subheading ) ) : ?>
					<p class="smma-newsletter__subheading"><?php echo esc_html( $subheading ); ?></p>
				<?php endif; ?>
			</div>

			<form
				class="smma-newsletter__form"
				data-api-url="<?php echo esc_attr( $api_url ); ?>"
				data-nonce="<?php echo esc_attr( $nonce ); ?>"
				data-success="<?php echo esc_attr( $success_message ); ?>"
				novalidate
			>
				<div class="smma-newsletter__row">
					<div class="smma-newsletter__field">
						<label for="smma-nl-first-name-<?php echo esc_attr( uniqid() ); ?>" class="smma-newsletter__label">
							<?php esc_html_e( 'First Name', 'smma-gutenberg-blocks' ); ?>
						</label>
						<input
							type="text"
							id="smma-nl-first-name-<?php echo esc_attr( uniqid() ); ?>"
							name="first_name"
							class="smma-newsletter__input"
							placeholder="<?php esc_attr_e( 'Jane', 'smma-gutenberg-blocks' ); ?>"
							required
						/>
					</div>
					<div class="smma-newsletter__field">
						<label for="smma-nl-last-name-<?php echo esc_attr( uniqid() ); ?>" class="smma-newsletter__label">
							<?php esc_html_e( 'Last Name', 'smma-gutenberg-blocks' ); ?>
						</label>
						<input
							type="text"
							id="smma-nl-last-name-<?php echo esc_attr( uniqid() ); ?>"
							name="last_name"
							class="smma-newsletter__input"
							placeholder="<?php esc_attr_e( 'Smith', 'smma-gutenberg-blocks' ); ?>"
							required
						/>
					</div>
				</div>

				<div class="smma-newsletter__field smma-newsletter__field--email">
					<label for="smma-nl-email-<?php echo esc_attr( uniqid() ); ?>" class="smma-newsletter__label">
						<?php esc_html_e( 'Email Address', 'smma-gutenberg-blocks' ); ?>
					</label>
					<input
						type="email"
						id="smma-nl-email-<?php echo esc_attr( uniqid() ); ?>"
						name="email"
						class="smma-newsletter__input"
						placeholder="<?php esc_attr_e( 'jane@example.com', 'smma-gutenberg-blocks' ); ?>"
						required
					/>
				</div>

				<div class="smma-newsletter__message" aria-live="polite"></div>

				<button type="submit" class="smma-newsletter__button">
					<span class="smma-newsletter__button-label"><?php echo esc_html( $button_label ); ?></span>
					<span class="smma-newsletter__button-spinner" aria-hidden="true"></span>
				</button>
			</form>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
