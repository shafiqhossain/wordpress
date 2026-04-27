<?php if ( ! defined('ABSPATH') ) exit;

$filter_type   = sanitize_text_field( $_GET['filter_type']   ?? '' );
$filter_status = sanitize_text_field( $_GET['filter_status'] ?? '' );

$subscriptions = $subscription_model->all([
    'subscription_type' => $filter_type,
    'status'            => $filter_status,
]);

$types    = Settings::type_keys();
$statuses = Helper::subscription_statuses();
$settings = Settings::get();
?>
<a href="<?php echo Helper::admin_url('maxstamp_subscription_add'); ?>" class="page-title-action">Add New</a>
<hr class="wp-header-end">

<form method="get" action="" style="margin:15px 0;">
    <input type="hidden" name="page" value="maxstamp_subscriptions">
    <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
        <select name="filter_type" class="postform">
            <option value="">— All Plans —</option>
            <?php foreach ($types as $t) : ?>
                <option value="<?php echo esc_attr($t); ?>" <?php selected($filter_type,$t); ?>><?php echo esc_html($t); ?></option>
            <?php endforeach; ?>
        </select>
        <select name="filter_status" class="postform">
            <option value="">— All Statuses —</option>
            <?php foreach ($statuses as $st) : ?>
                <option value="<?php echo esc_attr($st); ?>" <?php selected($filter_status,$st); ?>><?php echo esc_html($st); ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="button">Filter</button>
        <a href="<?php echo Helper::admin_url('maxstamp_subscriptions'); ?>" class="button">Reset</a>
    </div>
</form>

<p class="description">Showing <strong><?php echo count($subscriptions); ?></strong> subscriptions.</p>

<table class="wp-list-table widefat fixed striped">
    <thead>
        <tr>
            <th>ID</th><th>Subscriber</th><th>Plan</th><th>Status</th>
            <th>Price Paid</th><th>Registered</th><th>Start</th><th>Expires</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($subscriptions) : foreach ($subscriptions as $sub) : ?>
        <tr>
            <td><?php echo (int)$sub->id; ?></td>
            <td>
                <a href="<?php echo Helper::admin_url('maxstamp_subscriber_view', ['id' => $sub->subscriber_id]); ?>">
                    <?php echo esc_html($sub->subscriber_name); ?>
                </a>
            </td>
            <td><?php echo Helper::type_badge($sub->subscription_type); ?></td>
            <td><?php echo Helper::status_badge($sub->status); ?></td>
            <td><?php echo Helper::format_price((float)$sub->price_paid); ?></td>
            <td><?php echo Helper::format_date($sub->registered_at); ?></td>
            <td><?php echo Helper::format_date($sub->start_date); ?></td>
            <td><?php
                $exp   = $sub->expire_date;
                $today = current_time('Y-m-d');
                $warn  = ($exp < $today && $sub->status === 'Active') ? ' style="color:#c9302c; font-weight:bold;"' : '';
                echo '<span'.$warn.'>'.Helper::format_date($exp).'</span>';
            ?></td>
            <td>
                <a href="<?php echo Helper::admin_url('maxstamp_subscription_edit', ['id' => $sub->id]); ?>"
                   class="button button-small">Edit</a>
                <!-- Quick status -->
                <?php foreach ($statuses as $st) :
                    if ($st === $sub->status) continue; ?>
                    <a href="<?php echo wp_nonce_url(
                        admin_url('admin-post.php?action=maxstamp_status_subscription&id='.$sub->id.'&status='.$st),
                        'maxstamp_status_'.$sub->id
                    ); ?>" class="button button-small" title="Set to <?php echo esc_attr($st); ?>">
                        <?php echo esc_html($st); ?>
                    </a>
                <?php endforeach; ?>
                <a href="<?php echo wp_nonce_url(
                    admin_url('admin-post.php?action=maxstamp_delete_subscription&id='.$sub->id),
                    'maxstamp_delete_subscription_'.$sub->id
                ); ?>" class="button button-small maxstamp-danger"
                   onclick="return confirm('Delete this subscription?')">Delete</a>
            </td>
        </tr>
    <?php endforeach; else : ?>
        <tr><td colspan="9" style="text-align:center; padding:20px;">No subscriptions found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
