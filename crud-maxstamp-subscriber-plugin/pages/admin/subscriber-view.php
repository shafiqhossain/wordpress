<?php if ( ! defined('ABSPATH') ) exit;
$wp_user = $row->wp_user_id ? get_user_by('id', $row->wp_user_id) : null;
if ($flash) echo Helper::alert($flash['msg'], $flash['type']);
?>
<a href="<?php echo Helper::admin_url('maxstamp_subscribers'); ?>" class="page-title-action">← All Subscribers</a>
<a href="<?php echo Helper::admin_url('maxstamp_subscriber_edit', ['id' => $row->id]); ?>" class="page-title-action">Edit</a>
<a href="<?php echo Helper::admin_url('maxstamp_subscription_add', ['subscriber_id' => $row->id]); ?>" class="page-title-action">+ Add Subscription</a>
<hr class="wp-header-end" style="margin-bottom:20px;">

<div class="maxstamp-view-wrap" style="display:flex; gap:20px; flex-wrap:wrap;">

    <!-- Profile card -->
    <div style="flex:0 0 300px;">
        <div class="postbox">
            <h2 class="hndle" style="padding:10px 15px;">
                <span class="dashicons dashicons-admin-users" style="margin-top:3px;"></span>
                Profile
            </h2>
            <div class="inside">
                <dl>
                    <dt><strong>Name</strong></dt>   <dd><?php echo esc_html($row->name); ?></dd>
                    <dt><strong>Email</strong></dt>  <dd><?php echo esc_html($row->email); ?></dd>
                    <dt><strong>Phone</strong></dt>  <dd><?php echo esc_html($row->phone) ?: '—'; ?></dd>
                    <dt><strong>Notes</strong></dt>  <dd><?php echo esc_html($row->extra) ?: '—'; ?></dd>
                    <dt><strong>Registered</strong></dt>
                    <dd><?php echo Helper::format_date($row->registered_at); ?></dd>
                    <dt><strong>WP Account</strong></dt>
                    <dd>
                        <?php if ($wp_user) : ?>
                            <a href="<?php echo esc_url(get_edit_user_link($wp_user->ID)); ?>" target="_blank">
                                @<?php echo esc_html($wp_user->user_login); ?>
                            </a>
                        <?php else : ?>
                            <span class="description">Not linked</span>
                        <?php endif; ?>
                    </dd>
                </dl>
                <hr>
                <a href="<?php echo wp_nonce_url( admin_url('admin-post.php?action=maxstamp_delete_subscriber&id='.$row->id), 'maxstamp_delete_subscriber_'.$row->id ); ?>"
                   class="button maxstamp-danger"
                   onclick="return confirm('Delete subscriber and ALL subscriptions?')">
                   <span class="dashicons dashicons-trash"></span> Delete Subscriber
                </a>
            </div>
        </div>
    </div>

    <!-- Subscriptions -->
    <div style="flex:1; min-width:400px;">
        <div class="postbox">
            <h2 class="hndle" style="padding:10px 15px;">
                Subscriptions
                <a href="<?php echo Helper::admin_url('maxstamp_subscription_add', ['subscriber_id' => $row->id]); ?>"
                   class="page-title-action" style="float:right; margin-top:-2px;">+ Add</a>
            </h2>
            <div class="inside" style="padding:0;">
                <?php if ($subscriptions) : ?>
                <table class="widefat fixed">
                    <thead>
                        <tr>
                            <th>Plan</th><th>Status</th><th>Price</th>
                            <th>Start</th><th>Expires</th><th>Registered</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($subscriptions as $sub) : ?>
                        <tr>
                            <td><?php echo Helper::type_badge($sub->subscription_type); ?></td>
                            <td><?php echo Helper::status_badge($sub->status); ?></td>
                            <td><?php echo Helper::format_price((float)$sub->price_paid); ?></td>
                            <td><?php echo Helper::format_date($sub->start_date); ?></td>
                            <td><?php echo Helper::format_date($sub->expire_date); ?></td>
                            <td><?php echo Helper::format_date($sub->registered_at); ?></td>
                            <td>
                                <a href="<?php echo Helper::admin_url('maxstamp_subscription_edit', ['id' => $sub->id]); ?>"
                                   class="button button-small">Edit</a>
                                <!-- Quick status buttons -->
                                <?php foreach (Helper::subscription_statuses() as $st) :
                                    if ($st === $sub->status) continue; ?>
                                    <a href="<?php echo wp_nonce_url(
                                        admin_url('admin-post.php?action=maxstamp_status_subscription&id='.$sub->id.'&status='.$st),
                                        'maxstamp_status_'.$sub->id
                                    ); ?>" class="button button-small"
                                       title="Set <?php echo esc_attr($st); ?>">
                                       <?php echo esc_html($st); ?>
                                    </a>
                                <?php endforeach; ?>
                                <a href="<?php echo wp_nonce_url(
                                    admin_url('admin-post.php?action=maxstamp_delete_subscription&id='.$sub->id),
                                    'maxstamp_delete_subscription_'.$sub->id
                                ); ?>" class="button button-small maxstamp-danger"
                                   onclick="return confirm('Delete this subscription?')">Del</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else : ?>
                    <p style="padding:15px;" class="description">No subscriptions yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
