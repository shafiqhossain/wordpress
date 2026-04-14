<?php

function smmaCookieBannerShortCode() {
    // Check for the cookie on the server side
    if (!isset($_COOKIE['wp-settings-cookie-accepted'])) {
        ob_start();
        ?>
        <div id="custom-cookie-banner" class="custom-cookie-banner">
			<p>By using our site, you agree to our <a href="/privacy-policy">cookie policy</a> <button type="button" id="accept-cookies">Accept</button></p>
        </div>
        <?php
        return ob_get_clean();
    }
    return ''; // Return empty string if cookie is already set
}
add_shortcode('smma_cookie_banner', 'smmaCookieBannerShortCode');

// Automatically add the shortcode to the footer
function add_cookie_banner_to_footer() {
    echo do_shortcode('[smma_cookie_banner]');
}
add_action('wp_footer', 'add_cookie_banner_to_footer');