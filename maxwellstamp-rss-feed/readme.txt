=== Maxwellstamp RSS Feed ===
Contributors:      maxwellstamp
Tags:              rss, feed, related posts, custom feed
Requires at least: 5.0
Tested up to:      6.5
Requires PHP:      7.4
Stable tag:        1.0.0
License:           GPLv2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

Custom RSS feed with related posts and featured image support.

== Description ==

Maxwellstamp RSS Feed registers a custom RSS 2.0 feed endpoint that includes:

* Featured image (as `<enclosure>` and inline in `<content:encoded>`)
* Post excerpt
* "Read more" link back to your site
* Related posts (matched by shared tags)

**Feed URL**
* Pretty permalinks: `https://example.com/feed/maxwellstamp-rss/`
* Query string:      `https://example.com/?feed=maxwellstamp-rss`

== Template Overriding ==

Copy `templates/feed-template.php` from the plugin into your active theme at:

    {theme}/maxwellstamp-rss-feed/feed-template.php

The plugin will automatically use your theme copy, letting you customise the
feed markup without modifying plugin files.

== Filters ==

`maxwellstamp_rss_default_image` – URL of the fallback image when a post has no featured image.
`maxwellstamp_rss_update_frequency` – Integer update frequency (default `1`).
`maxwellstamp_rss_update_period` – Update period string (default `'hourly'`).
`maxwellstamp_rss_logo_url` – URL for the channel `<image>` logo. Return empty string to omit.
`maxwellstamp_rss_read_more_label` – Text of the "read more" link appended to each item.

== Installation ==

1. Upload the `maxwellstamp-rss-feed` folder to `/wp-content/plugins/`.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Visit `/?feed=maxwellstamp-rss` to confirm the feed is working.

== Changelog ==

= 1.0.0 =
* Initial release.

== File Structure ==

    maxwellstamp-rss-feed/
    ├── maxwellstamp-rss-feed.php   ← Main plugin file
    ├── readme.txt
    ├── assets/
    │   └── images/
    │       └── default.jpg         ← Fallback image (replace with your own)
    └── templates/
        └── feed-template.php       ← RSS feed template (overridable in theme)
