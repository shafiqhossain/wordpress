<?php
/**
 * Dashboard Stats Block registration.
 *
 * @package smma-gutenberg-blocks
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers the Dashboard Stats block.
 */
function smma_dashboard_stats_block_init() {
	register_block_type(
		__DIR__,
		array(
			'render_callback' => 'smma_dashboard_stats_render_callback',
		)
	);
}
add_action( 'init', 'smma_dashboard_stats_block_init' );

/**
 * Renders the Dashboard Stats block on the front end.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Inner block content.
 * @param WP_Block $block      Block instance.
 * @return string  Rendered HTML.
 */
function smma_dashboard_stats_render_callback( $attributes, $content, $block ) {
	// Only render for users who can edit posts.
	if ( ! current_user_can( 'edit_posts' ) ) {
		return '';
	}

	global $wpdb;

	$block_title = isset( $attributes['blockTitle'] ) ? $attributes['blockTitle'] : __( 'Overview', 'smma-gutenberg-blocks' );

	// ── Total Projects ──────────────────────────────────────────────────────
	$total_projects = (int) wp_count_posts( 'smma_project' )->publish;

	// ── Active Subscribers (subscriber-role users, active status, not expired) ──
	$today = current_time( 'Y-m-d' );
	$active_subscribers = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(DISTINCT u.ID)
			 FROM {$wpdb->users} u
			 INNER JOIN {$wpdb->usermeta} um_role
			         ON u.ID = um_role.user_id
			        AND um_role.meta_key = '{$wpdb->prefix}capabilities'
			        AND um_role.meta_value LIKE %s
			 INNER JOIN {$wpdb->usermeta} um_status
			         ON u.ID = um_status.user_id
			        AND um_status.meta_key = 'smma_subscription_status'
			        AND um_status.meta_value = 'active'
			  LEFT JOIN {$wpdb->usermeta} um_expiry
			         ON u.ID = um_expiry.user_id
			        AND um_expiry.meta_key = 'smma_subscription_expiry'
			 WHERE ( um_expiry.meta_value IS NULL
			         OR um_expiry.meta_value = ''
			         OR um_expiry.meta_value >= %s )",
			'%subscriber%',
			$today
		)
	);

	// ── WooCommerce Products ─────────────────────────────────────────────────
	$woocommerce_active = post_type_exists( 'product' );
	$total_products     = $woocommerce_active ? (int) wp_count_posts( 'product' )->publish : null;

	$stats = array(
		array(
			'label'   => __( 'Total Projects', 'smma-gutenberg-blocks' ),
			'value'   => number_format_i18n( $total_projects ),
			'icon'    => 'portfolio',
			'color'   => 'blue',
			'link'    => admin_url( 'edit.php?post_type=smma_project' ),
		),
		array(
			'label'   => __( 'Total Subscribers', 'smma-gutenberg-blocks' ),
			'value'   => number_format_i18n( $active_subscribers ),
			'icon'    => 'groups',
			'color'   => 'green',
			'link'    => admin_url( 'users.php?role=subscriber' ),
		),
		array(
			'label'   => __( 'Total Products', 'smma-gutenberg-blocks' ),
			'value'   => null === $total_products
				? __( 'N/A', 'smma-gutenberg-blocks' )
				: number_format_i18n( $total_products ),
			'icon'    => 'cart',
			'color'   => 'purple',
			'link'    => $woocommerce_active ? admin_url( 'edit.php?post_type=product' ) : '',
			'note'    => $woocommerce_active ? '' : __( 'WooCommerce not active', 'smma-gutenberg-blocks' ),
		),
	);

	ob_start();
	?>
	<div <?php echo wp_kses_data( get_block_wrapper_attributes( array( 'class' => 'smma-dashboard-stats' ) ) ); ?>>
		<h2 class="smma-dashboard-stats__heading"><?php echo esc_html( $block_title ); ?></h2>
		<div class="smma-dashboard-stats__grid">
			<?php foreach ( $stats as $stat ) : ?>
				<div class="smma-dashboard-stats__card smma-dashboard-stats__card--<?php echo esc_attr( $stat['color'] ); ?>">
					<span class="smma-dashboard-stats__icon dashicons dashicons-<?php echo esc_attr( $stat['icon'] ); ?>"></span>
					<span class="smma-dashboard-stats__value"><?php echo esc_html( $stat['value'] ); ?></span>
					<span class="smma-dashboard-stats__label"><?php echo esc_html( $stat['label'] ); ?></span>
					<?php if ( ! empty( $stat['note'] ) ) : ?>
						<span class="smma-dashboard-stats__note"><?php echo esc_html( $stat['note'] ); ?></span>
					<?php endif; ?>
					<?php if ( ! empty( $stat['link'] ) ) : ?>
						<a class="smma-dashboard-stats__link" href="<?php echo esc_url( $stat['link'] ); ?>">
							<?php esc_html_e( 'View all →', 'smma-gutenberg-blocks' ); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
