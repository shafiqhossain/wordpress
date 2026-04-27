<?php
if ( ! defined('ABSPATH') ) exit;

$id                 = (int) get_query_var('maxstamp_id');
$subscriber_model   = new Subscriber();
$subscription_model = new Subscription();
$s                  = $subscriber_model->find( $id );

if ( ! $s ) {
    echo '<div class="container"><div class="alert alert-warning">Subscriber not found.</div></div>';
    return;
}

Helper::auto_expire();
$subscriptions = $subscription_model->for_subscriber( $id );
?>
<div class="container maxstamp-subscriber-detail" style="max-width:760px; padding:40px 15px;">
    <nav>
        <ol class="breadcrumb">
            <li><a href="<?php echo esc_url(home_url('/subscribers')); ?>">Subscribers</a></li>
            <li class="active"><?php echo esc_html($s->name); ?></li>
        </ol>
    </nav>

    <div class="panel panel-default">
        <div class="panel-heading"><h3 class="panel-title">Profile</h3></div>
        <div class="panel-body">
            <dl class="dl-horizontal">
                <dt>Name</dt>   <dd><?php echo esc_html($s->name); ?></dd>
                <dt>Member Since</dt> <dd><?php echo Helper::format_date($s->registered_at); ?></dd>
            </dl>
        </div>
    </div>

    <h3>Subscriptions</h3>
    <?php if ( $subscriptions ) : ?>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr class="info">
                    <th>Plan</th><th>Status</th><th>Start</th><th>Expires</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($subscriptions as $sub) : ?>
                <tr>
                    <td><?php echo Helper::type_badge($sub->subscription_type); ?></td>
                    <td><?php echo Helper::status_badge($sub->status); ?></td>
                    <td><?php echo Helper::format_date($sub->start_date); ?></td>
                    <td><?php echo Helper::format_date($sub->expire_date); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else : ?>
        <p class="text-muted">No subscriptions yet.</p>
    <?php endif; ?>

    <a href="<?php echo esc_url(home_url('/subscribe')); ?>" class="btn btn-primary">Add Subscription</a>
    <a href="<?php echo esc_url(home_url('/subscribers')); ?>" class="btn btn-default">Back</a>
</div>
