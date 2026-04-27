<?php if ( ! defined('ABSPATH') ) exit;

$filter_type   = sanitize_text_field( $_GET['filter_type']   ?? '' );
$filter_status = sanitize_text_field( $_GET['filter_status'] ?? '' );
$search        = sanitize_text_field( $_GET['s']             ?? '' );

$subscribers = $subscriber_model->all([
    'subscription_type' => $filter_type,
    'status'            => $filter_status,
    'search'            => $search,
]);

$types    = Settings::type_keys();
$statuses = Helper::subscription_statuses();

$flash = Helper::flash_get('subscriber_del') ?? Helper::flash_get('subscriber_add');
if ($flash) echo Helper::alert($flash['msg'], $flash['type']);
?>
<a href="<?php echo Helper::admin_url('maxstamp_subscriber_add'); ?>" class="page-title-action">Add New</a>
<hr class="wp-header-end">

<!-- Filter bar -->
<form method="get" action="" style="margin:15px 0;">
    <input type="hidden" name="page" value="maxstamp_subscribers">
    <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
        <input type="search" name="s" class="regular-text" placeholder="Search name / email…" value="<?php echo esc_attr($search); ?>">
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
        <a href="<?php echo Helper::admin_url('maxstamp_subscribers'); ?>" class="button">Reset</a>
    </div>
</form>

<p class="description">Showing <strong><?php echo count($subscribers); ?></strong> subscribers.</p>

<table class="wp-list-table widefat fixed striped">
    <thead>
        <tr>
            <th>ID</th><th>Name</th><th>Email</th><th>Phone</th>
            <th>Subscriptions</th><th>WP User</th><th>Registered</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($subscribers) : foreach ($subscribers as $s) :
        $subs    = $subscription_model->for_subscriber((int)$s->id);
        $wp_user = $s->wp_user_id ? get_user_by('id', $s->wp_user_id) : null;
    ?>
        <tr>
            <td><?php echo (int)$s->id; ?></td>
            <td>
                <strong>
                    <a href="<?php echo Helper::admin_url('maxstamp_subscriber_view', ['id' => $s->id]); ?>">
                        <?php echo esc_html($s->name); ?>
                    </a>
                </strong>
            </td>
            <td><?php echo esc_html($s->email); ?></td>
            <td><?php echo esc_html($s->phone); ?></td>
            <td>
                <?php foreach ($subs as $sub) : ?>
                    <?php echo Helper::type_badge($sub->subscription_type); ?>
                    <?php echo Helper::status_badge($sub->status); ?>&nbsp;
                <?php endforeach; ?>
                <?php if (empty($subs)) echo '<span class="description">—</span>'; ?>
            </td>
            <td>
                <?php if ($wp_user) : ?>
                    <a href="<?php echo esc_url(get_edit_user_link($wp_user->ID)); ?>" target="_blank">
                        <?php echo esc_html($wp_user->user_login); ?>
                    </a>
                <?php else : ?>
                    <span class="description">—</span>
                <?php endif; ?>
            </td>
            <td><?php echo Helper::format_date($s->registered_at); ?></td>
            <td>
                <a href="<?php echo Helper::admin_url('maxstamp_subscriber_view',  ['id' => $s->id]); ?>" class="button button-small">View</a>
                <a href="<?php echo Helper::admin_url('maxstamp_subscriber_edit',  ['id' => $s->id]); ?>" class="button button-small">Edit</a>
                <a href="<?php echo Helper::admin_url('maxstamp_subscription_add', ['subscriber_id' => $s->id]); ?>" class="button button-small">+ Sub</a>
                <a href="<?php echo wp_nonce_url( admin_url('admin-post.php?action=maxstamp_delete_subscriber&id='.$s->id), 'maxstamp_delete_subscriber_'.$s->id ); ?>"
                   class="button button-small maxstamp-danger"
                   onclick="return confirm('Delete subscriber and ALL their subscriptions?')">Delete</a>
            </td>
        </tr>
    <?php endforeach; else : ?>
        <tr><td colspan="8" style="text-align:center; padding:20px;">No subscribers found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
