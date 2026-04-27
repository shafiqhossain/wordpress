<?php if ( ! defined('ABSPATH') ) exit;
$total_subscribers  = $sub_r->count();
$by_type            = $sub_n->count_by_type();
$by_status          = $sub_n->count_by_status();
$settings           = Settings::get();

$flash = Helper::flash_get('subscriber_del');
if ($flash) echo Helper::alert($flash['msg'], $flash['type']);
?>
<hr class="wp-header-end">

<!-- KPI row -->
<div class="row" style="margin-top:20px;">
    <div class="col-md-3 col-sm-6">
        <div class="panel panel-primary text-center">
            <div class="panel-body">
                <div style="font-size:42px; font-weight:700;"><?php echo $total_subscribers; ?></div>
                <div>Total Subscribers</div>
            </div>
            <div class="panel-footer">
                <a href="<?php echo Helper::admin_url('maxstamp_subscribers'); ?>">View All &rarr;</a>
            </div>
        </div>
    </div>
    <?php foreach ($by_status as $row) :
        $cls = ['Active'=>'success','Suspended'=>'warning','Cancelled'=>'danger','Expired'=>'default'][$row->status] ?? 'default';
    ?>
    <div class="col-md-3 col-sm-6">
        <div class="panel panel-<?php echo $cls; ?> text-center">
            <div class="panel-body">
                <div style="font-size:42px; font-weight:700;"><?php echo $row->total; ?></div>
                <div><?php echo esc_html($row->status); ?></div>
            </div>
            <div class="panel-footer">
                <a href="<?php echo Helper::admin_url('maxstamp_subscriptions', ['filter_status' => $row->status]); ?>">View &rarr;</a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- By-type breakdown -->
<div class="row" style="margin-top:10px;">
    <?php foreach ($by_type as $row) :
        $meta = $settings['subscription_types'][$row->subscription_type] ?? [];
        $cls  = $meta['color'] ?? 'default';
    ?>
    <div class="col-md-2 col-sm-4">
        <div class="panel panel-<?php echo $cls; ?> text-center">
            <div class="panel-heading"><strong><?php echo esc_html($row->subscription_type); ?></strong></div>
            <div class="panel-body" style="font-size:28px; font-weight:700;"><?php echo $row->total; ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<p style="margin-top:20px;">
    <a href="<?php echo Helper::admin_url('maxstamp_subscriber_add'); ?>" class="btn btn-primary">
        <span class="glyphicon glyphicon-plus"></span> Add Subscriber
    </a>
    <a href="<?php echo esc_url(home_url('/subscribe')); ?>" class="btn btn-default" target="_blank">
        <span class="glyphicon glyphicon-eye-open"></span> View Public Page
    </a>
</p>
