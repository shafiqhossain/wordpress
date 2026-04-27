<?php if ( ! defined('ABSPATH') ) exit;
// $row and $edit_mode may be set by the parent page handler
$is_edit  = ! empty($edit_mode) && ! empty($row);
$nonce_action = $is_edit ? 'maxstamp_edit_subscriber' : 'maxstamp_add_subscriber';
$action_url   = $is_edit
    ? Helper::admin_url('maxstamp_subscriber_edit', ['id' => $row->id])
    : Helper::admin_url('maxstamp_subscriber_add');

$types    = Settings::type_keys();
$settings = Settings::get();
$statuses = Helper::subscription_statuses();

if ( ! empty($error) ) echo Helper::alert($error, 'danger');
?>
<a href="<?php echo Helper::admin_url('maxstamp_subscribers'); ?>" class="page-title-action">← All Subscribers</a>
<hr class="wp-header-end" style="margin-bottom:20px;">

<div class="maxstamp-form-wrap" style="max-width:720px;">
<form method="post" action="<?php echo esc_url($action_url); ?>">
    <?php Helper::nonce_field($nonce_action); ?>

    <div class="postbox">
        <h2 class="hndle" style="padding:10px 15px;">Subscriber Information</h2>
        <div class="inside" style="padding:15px;">

            <table class="form-table">
                <tr>
                    <th><label for="f_name">Full Name <span class="required">*</span></label></th>
                    <td><input type="text" id="f_name" name="name" class="regular-text"
                               value="<?php echo esc_attr($row->name ?? ''); ?>" required></td>
                </tr>
                <tr>
                    <th><label for="f_email">Email <span class="required">*</span></label></th>
                    <td>
                        <input type="email" id="f_email" name="email" class="regular-text"
                               value="<?php echo esc_attr($row->email ?? ''); ?>"
                               <?php echo $is_edit ? '' : 'required'; ?>>
                        <?php if ($is_edit) : ?>
                            <p class="description">To change email, edit the WordPress user directly.</p>
                        <?php else : ?>
                            <p class="description">A WP account will be created if this email doesn't exist.</p>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th><label for="f_phone">Phone</label></th>
                    <td><input type="tel" id="f_phone" name="phone" class="regular-text"
                               value="<?php echo esc_attr($row->phone ?? ''); ?>"></td>
                </tr>
                <tr>
                    <th><label for="f_extra">Notes</label></th>
                    <td><input type="text" id="f_extra" name="extra" class="large-text"
                               value="<?php echo esc_attr($row->extra ?? ''); ?>"></td>
                </tr>
            </table>
        </div>
    </div>

    <?php if ( ! $is_edit ) : /* Subscription section only when adding new */ ?>
    <div class="postbox" style="margin-top:15px;">
        <h2 class="hndle" style="padding:10px 15px;">Initial Subscription</h2>
        <div class="inside" style="padding:15px;">
            <table class="form-table">
                <tr>
                    <th><label for="f_type">Plan <span class="required">*</span></label></th>
                    <td>
                        <select id="f_type" name="subscription_type" onchange="maxstampUpdatePrice(this.value)">
                            <?php foreach ($types as $t) : ?>
                                <option value="<?php echo esc_attr($t); ?>"
                                        data-price="<?php echo esc_attr(Settings::price($t)); ?>">
                                    <?php echo esc_html($t); ?>
                                    — <?php echo Helper::format_price(Settings::price($t)); ?>/yr
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="f_price">Price Paid</label></th>
                    <td>
                        <input type="number" id="f_price" name="price_paid" step="0.01" min="0"
                               class="small-text"
                               value="<?php echo esc_attr(Settings::price($types[0])); ?>">
                        <?php echo esc_html(Settings::currency()); ?>
                    </td>
                </tr>
                <tr>
                    <th><label for="f_status">Status</label></th>
                    <td>
                        <select id="f_status" name="status">
                            <?php foreach ($statuses as $st) : ?>
                                <option value="<?php echo esc_attr($st); ?>"><?php echo esc_html($st); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="f_start">Start Date</label></th>
                    <td>
                        <input type="date" id="f_start" name="start_date"
                               value="<?php echo esc_attr(current_time('Y-m-d')); ?>">
                        <p class="description">Leave blank to use today. Expire date auto-set to +<?php echo $settings['subscription_duration']; ?> days.</p>
                    </td>
                </tr>
                <tr>
                    <th><label for="f_notes">Subscription Notes</label></th>
                    <td><input type="text" id="f_notes" name="notes" class="large-text"></td>
                </tr>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <p style="margin-top:15px;">
        <button type="submit" class="button button-primary button-large">
            <?php echo $is_edit ? 'Update Subscriber' : 'Add Subscriber'; ?>
        </button>
        <a href="<?php echo Helper::admin_url('maxstamp_subscribers'); ?>" class="button button-large">Cancel</a>
    </p>
</form>
</div>

<script>
function maxstampUpdatePrice(type) {
    var sel   = document.getElementById('f_type');
    var opt   = sel.options[sel.selectedIndex];
    var price = opt.dataset.price || '0';
    document.getElementById('f_price').value = parseFloat(price).toFixed(2);
}
</script>
