<?php if ( ! defined('ABSPATH') ) exit; ?>
<div class="container text-center" style="max-width:600px; padding:60px 15px;">
    <div style="font-size:72px; color:#5cb85c; margin-bottom:20px;">&#10003;</div>
    <h1>You're subscribed!</h1>
    <p class="lead">Thank you for subscribing. Check your inbox — we've sent your account details.</p>
    <hr>
    <a href="<?php echo esc_url( home_url('/') ); ?>" class="btn btn-primary">
        <span class="glyphicon glyphicon-home"></span> Go Home
    </a>
    <a href="<?php echo esc_url( home_url('/subscribers') ); ?>" class="btn btn-default">
        View All Subscribers
    </a>
</div>
