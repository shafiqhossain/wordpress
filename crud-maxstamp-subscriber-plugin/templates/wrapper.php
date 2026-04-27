<?php
if ( ! defined('ABSPATH') ) exit;

get_header();

$tpl = MAXSTAMP_CURRENT_TPL;

echo '<div class="maxstamp-page-wrapper">';

switch ( $tpl ) {
    case 'subscribe':
        include MAXSTAMP_TEMPLATES . 'subscribe.php';
        break;
    case 'subscribe_success':
        include MAXSTAMP_TEMPLATES . 'subscribe-success.php';
        break;
    case 'subscriber_list':
        include MAXSTAMP_TEMPLATES . 'subscriber-list.php';
        break;
    case 'subscriber_view':
        include MAXSTAMP_TEMPLATES . 'subscriber-view.php';
        break;
    default:
        echo '<p>Page not found.</p>';
}

echo '</div>';

get_footer();
