<?php if ( ! defined('ABSPATH') ) exit;

$is_edit      = ! empty($edit_mode) && ! empty($row);
$nonce_action = $is_edit ? 'maxstamp_edit_subscription' : 'maxstamp_add_subscription';
$action_url   = $is_edit
    ? Helper::admin_url('maxstamp_subscription_edit', ['id' => $row->id])
    : Helper::admin_url('maxstamp_subscription_add');

$subscriber_model = new Subscriber();
$all_subscribers  = $subscriber_model->all();
$types            = Settings::type_keys();
$statuses         = Helper::subscription_statuses();
$settings         = Settings::get();

// Pre-fill subscriber_id from URL (when coming from subscriber view)
$pre_subscriber   = (int) ($subscriber_id ?? $row->subscriber_id ?? 0);

if ( ! empty($error) ) echo Helper::alert($error, 'danger');
?>
<a href="<?php echo Helper::admin_url('maxstamp_subscriptions'); ?>" class="page-title-action">← All Subscriptions</a>
<hr class="wp-header-end" style="margin-bottom:20px;">

<div class="maxstamp-form-wrap" style="max-width:720px;">
<form method="post" action="<?php echo esc_url($action_url); ?>">
    <?php Helper::nonce_field($nonce_action); ?>

    <div class="postbox">
        <h2 class="hndle" style="padding:10px 15px;">Subscription Details</h2>
        <div class="inside" style="padding:15px;">
            <table class="form-table">

                <?php if ( ! $is_edit ) : ?>
                <tr>
                    <th><label for="f_subscriber">Subscriber <span class="required">*</span></label></th>
                    <td>
                        <select id="f_subscriber" name="subscriber_id" required>
                            <option value="">— Select subscriber —</option>
                            <?php foreach ($all_subscribers as $sbr) : ?>
                                <option value="<?php echo (int)$sbr->id; ?>"
                                    <?php selected($pre_subscriber, (int)$sbr->id); ?>>
                                    <?php echo esc_html($sbr->name . ' <' . $sbr->email . '>'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <?php else : ?>
                    <input type="hidden" name="subscriber_id" value="<?php echo (int)$row->subscriber_id; ?>">
                <?php endif; ?>

                <tr>
                    <th><label for="f_type">Plan <span class="required">*</span></label></th>
                    <td>
                        <select id="f_type" name="subscription_type" onchange="maxstampUpdatePrice(this.value)">
                            <?php foreach ($types as $t) :
                                $price = Settings::price($t);
                            ?>
                                <option value="<?php echo esc_attr($t); ?>"
                                        data-price="<?php echo esc_attr($price); ?>"
                                        <?php selected(($row->subscription_type ?? ''), $t); ?>>
                                    <?php echo esc_html($t); ?> — <?php echo Helper::format_price($price); ?>/yr
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
                               value="<?php echo esc_attr($row->price_paid ?? Settings::price($types[0])); ?>">
                        <?php echo esc_html(Settings::currency()); ?>
                    </td>
                </tr>

                <tr>
                    <th><label for="f_status">Status</label></th>
                    <td>
                        <select id="f_status" name="status">
                            <?php foreach ($statuses as $st) : ?>
                                <option value="<?php echo esc_attr($st); ?>"
                                    <?php selected(($row->status ?? 'Active'), $st); ?>>
                                    <?php echo esc_html($st); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th><label for="f_start">Start Date <span class="required">*</span></label></th>
                    <td>
                        <input type="date" id="f_start" name="start_date"
                               value="<?php echo esc_attr($row->start_date ?? current_time('Y-m-d')); ?>"
                               required>
                    </td>
                </tr>

                <tr>
                    <th><label for="f_expire">Expire Date</label></th>
                    <td>
                        <input type="date" id="f_expire" name="expire_date"
                               value="<?php echo esc_attr($row->expire_date ?? date('Y-m-d', strtotime('+' . $settings['subscription_duration'] . ' days'))); ?>">
                        <p class="description">Auto-set to start + <?php echo $settings['subscription_duration']; ?> days if left blank.</p>
                    </td>
                </tr>

                <tr>
                    <th><label for="f_notes">Notes</label></th>
                    <td><input type="text" id="f_notes" name="notes" class="large-text"
                               value="<?php echo esc_attr($row->notes ?? ''); ?>"></td>
                </tr>
            </table>
        </div>
    </div>

    <p style="margin-top:15px;">
        <button type="submit" class="button button-primary button-large">
            <?php echo $is_edit ? 'Update Subscription' : 'Create Subscription'; ?>
        </button>
        <a href="<?php echo Helper::admin_url('maxstamp_subscriptions'); ?>" class="button button-large">Cancel</a>
    </p>
</form>
</div>

<script>
// Auto-update expire date when start changes
document.getElementById('f_start').addEventListener('change', function() {
    var d = new Date(this.value);
    d.setDate(d.getDate() + <?php echo (int)$settings['subscription_duration']; ?>);
    var yyyy = d.getFullYear();
    var mm   = String(d.getMonth()+1).padStart(2,'0');
    var dd   = String(d.getDate()).padStart(2,'0');
    document.getElementById('f_expire').value = yyyy+'-'+mm+'-'+dd;
});

function maxstampUpdatePrice(type) {
    var sel   = document.getElementById('f_type');
    var opt   = sel.options[sel.selectedIndex];
    var price = opt.dataset.price || '0';
    document.getElementById('f_price').value = parseFloat(price).toFixed(2);
}
</script>
