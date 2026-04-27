<?php if ( ! defined('ABSPATH') ) exit;
$settings = Settings::get();
if (!empty($saved)) echo Helper::alert('<strong>Settings saved!</strong>', 'success');
?>
<hr class="wp-header-end" style="margin-bottom:20px;">

<form method="post" action="<?php echo esc_url(Helper::admin_url('maxstamp_settings')); ?>">
    <?php Helper::nonce_field('maxstamp_save_settings'); ?>

    <!-- ── General ── -->
    <div class="postbox" style="max-width:800px;">
        <h2 class="hndle" style="padding:10px 15px;">General Settings</h2>
        <div class="inside">
            <table class="form-table">
                <tr>
                    <th><label for="currency_symbol">Currency Symbol</label></th>
                    <td>
                        <input type="text" id="currency_symbol" name="currency_symbol"
                               class="small-text" maxlength="5"
                               value="<?php echo esc_attr($settings['currency_symbol']); ?>">
                    </td>
                </tr>
                <tr>
                    <th><label for="currency_code">Currency Code</label></th>
                    <td>
                        <input type="text" id="currency_code" name="currency_code"
                               class="small-text" maxlength="10"
                               value="<?php echo esc_attr($settings['currency_code']); ?>">
                    </td>
                </tr>
                <tr>
                    <th><label for="subscription_duration">Default Duration (days)</label></th>
                    <td>
                        <input type="number" id="subscription_duration" name="subscription_duration"
                               class="small-text" min="1"
                               value="<?php echo (int)$settings['subscription_duration']; ?>">
                        <p class="description">Expire date = start date + this many days. Default: 365.</p>
                    </td>
                </tr>
                <tr>
                    <th>Public Subscriber List</th>
                    <td>
                        <label>
                            <input type="checkbox" name="public_list_enabled" value="1"
                                <?php checked($settings['public_list_enabled'], 1); ?>>
                            Enable public subscriber list at
                            <code><?php echo esc_url(home_url('/subscribers')); ?></code>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th>Admin Notifications</th>
                    <td>
                        <label>
                            <input type="checkbox" name="notify_admin_on_new" value="1"
                                <?php checked($settings['notify_admin_on_new'], 1); ?>>
                            Email admin when a new subscriber signs up
                        </label>
                    </td>
                </tr>
                <tr>
                    <th><label for="admin_notify_email">Notification Email</label></th>
                    <td>
                        <input type="email" id="admin_notify_email" name="admin_notify_email"
                               class="regular-text"
                               value="<?php echo esc_attr($settings['admin_notify_email']); ?>">
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- ── Subscription Type Pricing ── -->
    <div class="postbox" style="max-width:800px; margin-top:15px;">
        <h2 class="hndle" style="padding:10px 15px;">Subscription Plan Pricing</h2>
        <div class="inside">
            <p class="description" style="margin-bottom:15px;">
                Set the annual price for each subscription plan.
                These prices auto-fill the <em>Price Paid</em> field when creating subscriptions.
            </p>
            <table class="widefat fixed" style="max-width:680px;">
                <thead>
                    <tr>
                        <th style="width:120px;">Plan</th>
                        <th style="width:160px;">Annual Price (<?php echo esc_html($settings['currency_symbol']); ?>)</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($settings['subscription_types'] as $key => $meta) : ?>
                    <tr>
                        <td>
                            <span class="label label-<?php echo esc_attr($meta['color']); ?>">
                                <?php echo esc_html($meta['label']); ?>
                            </span>
                        </td>
                        <td>
                            <input type="number" name="type_price[<?php echo esc_attr($key); ?>]"
                                   step="0.01" min="0" class="small-text"
                                   value="<?php echo esc_attr(number_format((float)$meta['price'],2,'.','')); ?>">
                        </td>
                        <td>
                            <input type="text" name="type_desc[<?php echo esc_attr($key); ?>]"
                                   class="regular-text"
                                   value="<?php echo esc_attr($meta['description']); ?>">
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ── Public URLs ── -->
    <div class="postbox" style="max-width:800px; margin-top:15px;">
        <h2 class="hndle" style="padding:10px 15px;">Public URLs</h2>
        <div class="inside">
            <table class="form-table">
                <tr>
                    <th>Subscribe Page</th>
                    <td><a href="<?php echo esc_url(home_url('/subscribe')); ?>" target="_blank">
                        <?php echo esc_url(home_url('/subscribe')); ?>
                    </a></td>
                </tr>
                <tr>
                    <th>Subscriber List</th>
                    <td><a href="<?php echo esc_url(home_url('/subscribers')); ?>" target="_blank">
                        <?php echo esc_url(home_url('/subscribers')); ?>
                    </a></td>
                </tr>
                <tr>
                    <th>Success Page</th>
                    <td><a href="<?php echo esc_url(home_url('/subscribe/success')); ?>" target="_blank">
                        <?php echo esc_url(home_url('/subscribe/success')); ?>
                    </a></td>
                </tr>
            </table>
        </div>
    </div>

    <p style="margin-top:20px;">
        <button type="submit" class="button button-primary button-large">Save Settings</button>
    </p>
</form>
