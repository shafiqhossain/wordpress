<?php
if ( ! defined('ABSPATH') ) exit;

$settings = Settings::get();
$types    = $settings['subscription_types'];
$error_key = md5( $_POST['email'] ?? '' );
$pub_error = get_transient( 'maxstamp_public_error_' . $error_key );
if ( $pub_error ) delete_transient( 'maxstamp_public_error_' . $error_key );

// Pre-select type from URL ?plan=Gold
$preselect = sanitize_text_field( $_GET['plan'] ?? 'Free' );
if ( ! array_key_exists($preselect, $types) ) $preselect = 'Free';
?>
<div class="container maxstamp-subscribe-page" style="max-width:860px; padding:40px 15px;">

    <div class="text-center" style="margin-bottom:30px;">
        <h1>Choose Your Subscription</h1>
        <p class="lead text-muted">Pick a plan that works for you. You can change it any time.</p>
    </div>

    <?php if ( $pub_error ) echo Helper::alert( esc_html($pub_error), 'danger' ); ?>

    <!-- ── Pricing cards ── -->
    <div class="row" style="margin-bottom:35px;">
        <?php foreach ( $types as $key => $meta ) :
            $active = ($key === $preselect) ? 'panel-primary' : 'panel-default';
        ?>
        <div class="col-xs-12 col-sm-6 col-md-4" style="margin-bottom:20px;">
            <div class="panel <?php echo $active; ?> maxstamp-plan-card"
                 style="cursor:pointer; transition:.2s;"
                 data-plan="<?php echo esc_attr($key); ?>">
                <div class="panel-heading text-center">
                    <h3 class="panel-title" style="font-size:18px;"><?php echo esc_html($meta['label']); ?></h3>
                </div>
                <div class="panel-body text-center">
                    <div style="font-size:34px; font-weight:700; margin:10px 0;">
                        <?php echo esc_html( $settings['currency_symbol'] ); ?><?php echo number_format( (float)$meta['price'], 2 ); ?>
                        <small style="font-size:14px; color:#999;">/year</small>
                    </div>
                    <p class="text-muted" style="min-height:36px;"><?php echo esc_html($meta['description']); ?></p>
                    <button type="button"
                            class="btn btn-<?php echo esc_attr($meta['color']); ?> maxstamp-select-plan"
                            data-plan="<?php echo esc_attr($key); ?>">
                        Select <?php echo esc_html($meta['label']); ?>
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- ── Subscription form ── -->
    <div class="panel panel-default" id="maxstamp-form-panel">
        <div class="panel-heading">
            <h3 class="panel-title">
                <span class="glyphicon glyphicon-user"></span>
                Your Details
                &mdash; Selected plan:
                <strong id="maxstamp-selected-label"><?php echo esc_html($preselect); ?></strong>
            </h3>
        </div>
        <div class="panel-body">
            <form method="post" action="<?php echo esc_url( home_url('/subscribe') ); ?>" class="form-horizontal">
                <?php Helper::nonce_field('maxstamp_public_subscribe'); ?>
                <input type="hidden" name="maxstamp_public_subscribe" value="1">
                <input type="hidden" name="subscription_type" id="maxstamp_type_input" value="<?php echo esc_attr($preselect); ?>">
                <input type="hidden" name="status" value="Active">

                <div class="form-group">
                    <label class="col-sm-3 control-label">Full Name <span class="text-danger">*</span></label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control" name="name"
                               value="<?php echo esc_attr( wp_get_current_user()->display_name ?? '' ); ?>"
                               placeholder="Your full name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">Email <span class="text-danger">*</span></label>
                    <div class="col-sm-7">
                        <input type="email" class="form-control" name="email"
                               value="<?php echo esc_attr( wp_get_current_user()->user_email ?? '' ); ?>"
                               placeholder="your@email.com" required>
                        <span class="help-block">A WordPress account will be created if you don't have one.</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">Phone</label>
                    <div class="col-sm-7">
                        <input type="tel" class="form-control" name="phone" placeholder="+1 234 567 8900">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">Notes</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control" name="extra" placeholder="Optional notes">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-7 col-sm-offset-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <span class="glyphicon glyphicon-ok"></span>
                            Subscribe Now
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div><!-- /.panel -->
</div><!-- /.container -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    var cards  = document.querySelectorAll('.maxstamp-plan-card');
    var btns   = document.querySelectorAll('.maxstamp-select-plan');
    var input  = document.getElementById('maxstamp_type_input');
    var label  = document.getElementById('maxstamp-selected-label');

    function selectPlan(plan) {
        input.value = plan;
        label.textContent = plan;
        cards.forEach(function(c) {
            c.classList.remove('panel-primary');
            c.classList.add('panel-default');
            if (c.dataset.plan === plan) {
                c.classList.remove('panel-default');
                c.classList.add('panel-primary');
            }
        });
        document.getElementById('maxstamp-form-panel').scrollIntoView({ behavior: 'smooth' });
    }

    btns.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            selectPlan(this.dataset.plan);
        });
    });
    cards.forEach(function(c) {
        c.addEventListener('click', function() { selectPlan(this.dataset.plan); });
    });
});
</script>
