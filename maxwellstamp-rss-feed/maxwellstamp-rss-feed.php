<?php
/**
 * Plugin Name:       Maxwellstamp RSS Feed
 * Plugin URI:        https://example.com/maxwellstamp-rss-feed
 * Description:       Custom RSS feed with related posts and featured image support.
 * Version:           1.0.0
 * Requires at least: 5.0
 * Requires PHP:      7.4
 * Author:            Maxwellstamp
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       maxwellstamp-rss-feed
 *
 * @package MaxwellstampRSSFeed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'MAXWELLSTAMP_RSS_VERSION',     '1.0.0' );
define( 'MAXWELLSTAMP_RSS_PLUGIN_DIR',  plugin_dir_path( __FILE__ ) );
define( 'MAXWELLSTAMP_RSS_PLUGIN_URL',  plugin_dir_url( __FILE__ ) );
define( 'MAXWELLSTAMP_RSS_SLUG',        'maxwellstamp-rss' ); // Feed URL slug: /?feed=maxwellstamp-rss

/**
 * Register the custom RSS feed endpoint.
 *
 * Feed is available at: /?feed=maxwellstamp-rss
 * Or with pretty permalinks: /feed/maxwellstamp-rss/
 */
function maxwellstamp_rss_register_feed() {
	add_feed( MAXWELLSTAMP_RSS_SLUG, 'maxwellstamp_rss_render_feed' );
}
add_action( 'init', 'maxwellstamp_rss_register_feed' );


/**
 * Callback that loads the RSS feed template.
 *
 * Looks for the template in this order:
 *   1. Active theme:        {theme}/maxwellstamp-rss-feed/feed-template.php
 *   2. Plugin templates:    {plugin}/templates/feed-template.php
 */
function maxwellstamp_rss_render_feed() {
	$theme_template  = get_stylesheet_directory() . '/maxwellstamp-rss-feed/feed-template.php';
	$plugin_template = MAXWELLSTAMP_RSS_PLUGIN_DIR . 'templates/feed-template.php';

	if ( file_exists( $theme_template ) ) {
		include $theme_template;
	} elseif ( file_exists( $plugin_template ) ) {
		include $plugin_template;
	} else {
		wp_die(
			esc_html__( 'Maxwellstamp RSS Feed: feed template not found.', 'maxwellstamp-rss-feed' ),
			esc_html__( 'Feed Template Missing', 'maxwellstamp-rss-feed' ),
			array( 'response' => 500 )
		);
	}
}


/**
 * Flush rewrite rules on activation so the new feed endpoint works immediately.
 */
function maxwellstamp_rss_activate() {
	maxwellstamp_rss_register_feed();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'maxwellstamp_rss_activate' );


/**
 * Flush rewrite rules on deactivation to clean up.
 */
function maxwellstamp_rss_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'maxwellstamp_rss_deactivate' );


// -------------------------------------------------------------------------
// Template helper functions (used inside feed-template.php)
// -------------------------------------------------------------------------

/**
 * Return HTML for related posts based on shared tags.
 *
 * @param  int $post_id   The current post ID.
 * @param  int $max_posts Maximum number of related posts to show. Default 3.
 * @return string         HTML string with related post links, or empty string.
 */
function maxwellstamp_rss_get_related( $post_id, $max_posts = 3 ) {
	$tags    = wp_get_post_tags( $post_id );
	$tag_ids = wp_list_pluck( $tags, 'term_id' );

	if ( empty( $tag_ids ) ) {
		return '';
	}

	$related_query = new WP_Query( array(
		'tag__in'        => $tag_ids,
		'post__not_in'   => array( $post_id ),
		'posts_per_page' => absint( $max_posts ),
		'no_found_rows'  => true, // Performance: skip counting rows.
	) );

	if ( ! $related_query->have_posts() ) {
		return '';
	}

	$output = 'Related:<br />';

	while ( $related_query->have_posts() ) {
		$related_query->the_post();
		$output .= '<a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a><br />';
	}

	wp_reset_postdata();

	return $output;
}


/**
 * Return the featured image URL for a post, falling back to a filterable default.
 *
 * @param  int    $post_id    The post ID.
 * @param  string $image_size Image size slug. Default 'large'.
 * @return string             Absolute URL to the image.
 */
function maxwellstamp_rss_get_post_image( $post_id, $image_size = 'large' ) {
	$thumbnail_id = get_post_thumbnail_id( $post_id );
	$image_src    = wp_get_attachment_image_src( $thumbnail_id, $image_size );

	if ( $image_src ) {
		return $image_src[0];
	}

	/**
	 * Filter the fallback image URL used when a post has no featured image.
	 *
	 * @param string $default_url Absolute URL to the fallback image.
	 */
	return apply_filters(
		'maxwellstamp_rss_default_image',
		MAXWELLSTAMP_RSS_PLUGIN_URL . 'assets/images/default.jpg'
	);
}


/**
 * Return the "read more" link appended to each feed item's content.
 *
 * @param  string $label Optional link text. Default reads from filter.
 * @return string        HTML anchor tag wrapped in line breaks.
 */
function maxwellstamp_rss_read_more_link( $label = '' ) {
	if ( ! $label ) {
		/** Translators: %s is the site name. */
		$label = sprintf(
			/* translators: %s: site name */
			__( 'See the rest of the story at %s', 'maxwellstamp-rss-feed' ),
			get_bloginfo( 'name' )
		);
	}

	/**
	 * Filter the "read more" label shown in the feed.
	 *
	 * @param string $label Link text.
	 */
	$label = apply_filters( 'maxwellstamp_rss_read_more_label', $label );

	return '<br /><a href="' . esc_url( get_permalink() ) . '">' . esc_html( $label ) . '</a><br /><br />';
}


/**
 * Return the sy:updateFrequency value (filterable).
 *
 * @return int
 */
function maxwellstamp_rss_update_frequency() {
	/**
	 * Filter the RSS update frequency (integer, default 1).
	 *
	 * @param int $frequency
	 */
	return (int) apply_filters( 'maxwellstamp_rss_update_frequency', 1 );
}


/**
 * Return the sy:updatePeriod value (filterable).
 *
 * @return string  One of 'hourly', 'daily', 'weekly', 'monthly', 'yearly'.
 */
function maxwellstamp_rss_update_period() {
	$allowed = array( 'hourly', 'daily', 'weekly', 'monthly', 'yearly' );

	/**
	 * Filter the RSS update period. Default 'hourly'.
	 *
	 * @param string $period
	 */
	$period = apply_filters( 'maxwellstamp_rss_update_period', 'hourly' );

	return in_array( $period, $allowed, true ) ? $period : 'hourly';
}


/**
 * Return the feed logo URL (filterable).
 *
 * @return string Absolute URL, or empty string to omit the <image> block.
 */
function maxwellstamp_rss_logo_url() {
	/**
	 * Filter the feed logo URL.
	 * Return an empty string to suppress the <image> element entirely.
	 *
	 * @param string $url
	 */
	return apply_filters( 'maxwellstamp_rss_logo_url', '' );
}
