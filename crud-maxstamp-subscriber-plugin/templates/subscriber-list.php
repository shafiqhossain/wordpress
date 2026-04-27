<?php
if ( ! defined('ABSPATH') ) exit;

$settings = Settings::get();
if ( ! $settings['public_list_enabled'] ) {
    echo '<div class="container"><p>This page is not available.</p></div>';
    return;
}

Helper::auto_expire();
$subscriber_model   = new Subscriber();
$subscription_model = new Subscription();
$subscribers        = $subscriber_model->all();
?>
<div class="container maxstamp-public-list" style="padding:40px 15px;">
    <h1>Subscribers</h1>
    <p class="text-muted"><?php echo count($subscribers); ?> registered subscribers</p>

    <?php if ( $subscribers ) : ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr class="info">
                    <th>#</th>
                    <th>Name</th>
                    <th>Active Subscriptions</th>
                    <th>Member Since</th>
                </tr>
            </thead>
            <tbody>
            <?php $i=1; foreach ( $subscribers as $s ) :
                $subs = $subscription_model->for_subscriber( (int)$s->id );
                $active = array_filter($subs, fn($x) => $x->status === 'Active');
            ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td>
                        <a href="<?php echo esc_url( home_url('/subscribers/' . $s->id) ); ?>">
                            <?php echo esc_html($s->name); ?>
                        </a>
                    </td>
                    <td>
                        <?php foreach ($active as $a) echo Helper::type_badge($a->subscription_type) . ' '; ?>
                        <?php if ( empty($active) ) echo '<span class="text-muted">—</span>'; ?>
                    </td>
                    <td><?php echo Helper::format_date($s->registered_at); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else : ?>
        <p class="text-muted">No subscribers yet. <a href="<?php echo esc_url(home_url('/subscribe')); ?>">Be the first!</a></p>
    <?php endif; ?>

    <a href="<?php echo esc_url(home_url('/subscribe')); ?>" class="btn btn-primary">
        <span class="glyphicon glyphicon-plus"></span> Subscribe
    </a>
</div>
