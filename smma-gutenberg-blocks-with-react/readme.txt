=== SMMA Gutenberg Blocks ===
Contributors: shafiqhossain
Tags: gutenberg, blocks, projects, testimonials, subscribe, callback
Requires at least: 5.9
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Custom Gutenberg blocks for SMMA: Recent Projects, Callback Action, Testimonials, Subscribe Now, Newsletter Subscription, and Dashboard Stats.

== Description ==

SMMA Gutenberg Blocks is a WordPress plugin that provides four custom Gutenberg editor blocks tailored for digital marketing and agency websites.

= Newsletter Subscription =
A front-end sign-up form that stores subscriber data in the `smma_newsletter_subscribers` custom database table. Fields: First Name, Last Name, Email. Submissions are validated and sent to the `smma/v1/newsletter-subscribe` REST endpoint. Duplicate emails are rejected with a clear message. The editor shows a live form preview; submission only works on the front end.

= Dashboard Stats =
A read-only summary block visible only to users with `edit_posts` capability. Displays three live stat cards:
* **Total Projects** — published `smma_project` post type count
* **Total Subscribers** — subscriber-role users with `smma_subscription_status = active` and a non-expired `smma_subscription_expiry` date
* **Total Products** — published WooCommerce products (shows "N/A" if WooCommerce is inactive)

Each card links to the relevant admin screen. The editor fetches live data from the `smma/v1/dashboard-stats` REST endpoint.

**Blocks Included (original):**

= Recent Projects =
Dynamically fetches and displays the 5 most recent projects from the custom `smma_project` post type. Each project card shows:
* Project title (linked to the project page)
* Short description
* Start date and end date

This block is display-only (no editor controls) and always reflects the latest published projects.

= Callback Action =
A call-to-action block featuring:
* Editable title
* Editable description
* Customizable callback button label and URL

Fully editable in the Gutenberg editor with live preview.

= Testimonials =
A testimonial carousel/display block. Each testimonial includes:
* Quote text
* Person image (upload via media library)
* Person name
* Designation
* Company name

Supports adding, editing, and saving multiple testimonials.

= Subscribe Now =
A subscription tier block with three plans:
* Free
* Business (highlighted as recommended)
* Corporate

Each plan has an editable title, description, and redirect URL. Users click the plan's "Select" button to be redirected to the appropriate subscription page. The block title is also editable.

== Installation ==

1. Upload the `smma-gutenberg-blocks` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Run `npm install` and `npm run build` inside the plugin directory to compile assets.
4. Add blocks via the Gutenberg editor block inserter under the **SMMA Blocks** category.

== Frequently Asked Questions ==

= Where do I add projects? =
Projects are managed under the **Projects** menu in the WordPress admin. Each project supports a title, excerpt (used as short description), and custom meta fields for start date, end date, and a short description.

= Can I customize the Subscribe Now redirect URLs? =
Yes. In the block editor, each subscription tier has an editable URL field that controls where the "Select" button redirects.

= Is the Recent Projects block editable? =
No. It is a dynamic block that automatically displays the 5 most recently published projects. There are no editor controls for it.

== Screenshots ==

1. Recent Projects block on the front end.
2. Callback Action block in the editor.
3. Testimonial block with multiple entries.
4. Subscribe Now block with Business tier highlighted.

== Changelog ==

= 1.1.0 =
* Added Newsletter Subscription block with database storage and REST API endpoint.
* Added Dashboard Stats block (Total Projects, Active Subscribers, WooCommerce Products).
* Added `smma_newsletter_subscribers` DB table (created on plugin activation).
* Added `smma/v1/newsletter-subscribe` REST endpoint (POST, public).
* Added `smma/v1/dashboard-stats` REST endpoint (GET, editor-only).

= 1.0.0 =
* Initial release.
* Added Recent Projects dynamic block.
* Added Callback Action block.
* Added Testimonials block.
* Added Subscribe Now block with three tiers.

== Upgrade Notice ==

= 1.0.0 =
Initial release.

== Developer Notes ==

**Author:** Shafiq Hossain
**Email:** md.shafiq.hossain@gmail.com
**Website:** https://www.isoftbd.com

Built with @wordpress/scripts and React (JSX). Source files are in the `blocks/` directory; compiled output goes to `build/`.

To develop locally:
1. Run `npm install`
2. Run `npm start` for watch mode
3. Run `npm run build` for production build
