<?php
/**
 * Maxwellstamp RSS Feed – feed template.
 *
 * This template can be overridden by copying it to:
 *   {active-theme}/maxwellstamp-rss-feed/feed-template.php
 *
 * @package MaxwellstampRSSFeed
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── Send correct Content-Type header ─────────────────────────────────────────
header( 'Content-Type: ' . feed_content_type( 'rss-http' ) . '; charset=' . get_option( 'blog_charset' ), true );

// ── Emit the XML declaration (PHP short-echo workaround) ─────────────────────
echo '<?xml version="1.0" encoding="' . esc_attr( get_option( 'blog_charset' ) ) . '" ?>';
?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	<?php do_action( 'rss2_ns' ); ?>
>
<channel>

	<!-- ── Channel metadata ──────────────────────────────────────────────── -->
	<title><?php bloginfo_rss( 'name' ); wp_title_rss(); ?></title>
	<link><?php bloginfo_rss( 'url' ); ?></link>
	<description><?php bloginfo_rss( 'description' ); ?></description>
	<lastBuildDate><?php echo esc_html( mysql2date( 'D, d M Y H:i:s +0000', get_lastpostmodified( 'GMT' ), false ) ); ?></lastBuildDate>
	<language><?php bloginfo_rss( 'language' ); ?></language>
	<sy:updatePeriod><?php echo esc_html( maxwellstamp_rss_update_period() ); ?></sy:updatePeriod>
	<sy:updateFrequency><?php echo esc_html( maxwellstamp_rss_update_frequency() ); ?></sy:updateFrequency>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />

	<?php
	// ── Optional feed logo ────────────────────────────────────────────────
	$logo_url = maxwellstamp_rss_logo_url();
	if ( $logo_url ) : ?>
	<image>
		<url><?php echo esc_url( $logo_url ); ?></url>
		<title><?php bloginfo_rss( 'name' ); ?></title>
		<link><?php bloginfo_rss( 'url' ); ?></link>
	</image>
	<?php endif; ?>

	<?php do_action( 'rss2_head' ); ?>

	<!-- ── Feed items ────────────────────────────────────────────────────── -->
	<?php while ( have_posts() ) : the_post();

		$post_id    = get_the_ID();
		$post_image = maxwellstamp_rss_get_post_image( $post_id );
		$read_more  = maxwellstamp_rss_read_more_link();
		$related    = maxwellstamp_rss_get_related( $post_id );
	?>
	<item>
		<title><?php the_title_rss(); ?></title>
		<link><?php the_permalink_rss(); ?></link>
		<guid isPermaLink="false"><?php the_guid(); ?></guid>
		<dc:creator><?php the_author(); ?></dc:creator>
		<pubDate><?php echo esc_html( mysql2date( 'D, d M Y H:i:s +0000', get_post_time( 'Y-m-d H:i:s', true ), false ) ); ?></pubDate>

		<?php if ( $post_image ) : ?>
		<enclosure url="<?php echo esc_url( $post_image ); ?>" type="image/jpeg" />
		<?php endif; ?>

		<?php the_category_rss( 'rss2' ); ?>

		<content:encoded><![CDATA[
			<?php if ( $post_image ) : ?>
			<p><img src="<?php echo esc_url( $post_image ); ?>" alt="<?php the_title_attribute(); ?>" /></p>
			<?php endif; ?>
			<?php the_excerpt_rss(); ?>
			<?php echo $read_more; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- pre-escaped in helper ?>
			<?php echo $related;   // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- pre-escaped in helper ?>
		]]></content:encoded>

		<?php do_action( 'rss2_item' ); ?>
	</item>
	<?php endwhile; ?>

</channel>
</rss>
