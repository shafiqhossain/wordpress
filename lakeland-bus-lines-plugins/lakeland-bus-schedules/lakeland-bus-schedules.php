<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.isoftbd.com
 * @since             1.0.0
 * @package           lakeland_bus_schedules
 *
 * @wordpress-plugin
 * Plugin Name:       Bus Schedule
 * Plugin URI:        http://www.isoftbd.com
 * Description:       To manage bas schedules
 * Version:           1.0.0
 * Author:            Shafiq Hossain
 * Author URI:
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       lakeland-bus-schedules
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-lakeland-contacts-activator.php
 */
function activate_lakeland_schedules() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-lakeland-schedules-activator.php';
	LakeLand_Schedules_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_lakeland_schedules() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-lakeland-schedules-deactivator.php';
	LakeLand_Schedules_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_lakeland_schedules' );
register_deactivation_hook( __FILE__, 'deactivate_lakeland_schedules' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-lakeland-schedules.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_lakeland_schedules() {

	$plugin = new LakeLand_Schedules();
	$plugin->run();

}
run_lakeland_schedules();

$stop_word = array('*', '-', '(D)', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', ' ', '', '202', '203', '204', '205', '206', '207', '208', '209', '210', '211', '212', '213', '214', '215', '216', '217', '218', '220', '221', '222', '223', '224', '225', '226', '227', '228', '229', '230', '231', '232', '233', '234', '235', '236', '237', '238', '239', '240', '241', '400', '401', '402', '403', '404', '405', '406', '407', '408', '409', '410', '411', '412', '413', 'SAT', 'BLK', 'New');
function schedule() {
	global $stop_word;
	global $wpdb;
    $schedule_attributes_table = $wpdb->prefix . 'schedule_attributes';
	$schedule_detail_table = $wpdb->prefix . 'schedule_detail';
	$schedule_table = $wpdb->prefix . 'schedules';

	$schedule = $wpdb->get_results( 'SELECT * FROM  '. $schedule_table .' where id=' . $_REQUEST['id'], ARRAY_A );
	$schedule = $schedule[0];
	$day_list = array('', 'Monday To Friday', 'Weekend', 'Holidays');
	?>
	<div class="schedule-detail-popup">
	  <a href="#" class="back-to-schedule">Back</a>
	  <h1 class="schedule-detail-head" align="center"><?php echo ucfirst($schedule['title']); ?> <?php echo ucfirst($day_list[$schedule['day']]); ?></h1>
	  <h3 class="schedule-detail-head" align="center"><?php echo ucfirst($schedule['title_3']); ?></h3>

	  <?php if(isset($schedule['pm_title']) && $schedule['pm_title'] != ''): ?>
	    <h3 class="schedule-detail-head" align="center"><?php echo ucfirst($schedule['pm_title']); ?></h3>
	  <?php endif; ?>

	  <?php if(isset($schedule['red_title']) && $schedule['red_title'] != ''): ?>
	    <h3 class="schedule-detail-head" align="center"><?php echo ucfirst($schedule['red_title']); ?></h3>
	  <?php endif; ?>

	  <div  style="overflow:auto;margin-top:20px"  class="stop-schedule-container" >
        <table cellspacing="0" cellpadding="0"   style="padding:0px;margin:0px;position:relative">
        <?php $attributes = $wpdb->get_row(
                $wpdb->prepare("SELECT * FROM " . $schedule_attributes_table . " WHERE id=%d AND name=%s", $_REQUEST['id'], '-highlight-'),
                ARRAY_A
            );
        ?>
        <?php $highlight_values = json_decode($attributes['values']); ?>
        <?php $results = $wpdb->get_results( 'SELECT * FROM  ' . $schedule_detail_table .  ' WHERE schedule_id=' . $_REQUEST['id'] , ARRAY_A ); ?>
	    <?php $cnt = 0; ?>

	    <?php foreach ($results as $result) { ?>
	      <?php $schedule_time = json_decode($result['schedules']); ?>
          <?php if($result['stop_name'] != '-lbl-') { ?>
          <tr class="stop-schedule">
            <td style="padding: 0;margin: 0px;position:relative;left:0;z-index: 9999"  class="lakeland-col-name ">
              <div class="s-name">
                <?php
                  if(strtolower($result['stop_name']) == 'sat=saturday only') {
                    echo '<span style="font-weight:600;color:#900">SAT ';
                    echo '</span>= Saturday Only';
                   }
                  else {
                     echo $result['stop_name'];
                   }
                ?>
              </div>
            </td>
	        <?php for($i = 0; $i< 42; $i++): ?>
		      <?php if($schedule_time[$i] != '00:00' && $schedule_time[$i] != ''): ?>
                <td  style="padding:0px;margin:0px" class="<?php echo ($i==0 ? 'first' : '' );?> ">
                    <?php
                        if(date('H', strtotime($schedule_time[$i])) >= 12) {
                          $pm_class= 'pm';
                        }
                        else {
                          $pm_class= '';
                        }
                        if (isset($highlight_values[$i]) && !empty($highlight_values[$i])) {
                          $highlight_class = 'font-weight:600;color:#ba1200;';
                        }
                        else {
                          $highlight_class = '';
                        }
                    ?>
                    <div   class="s-time <?php echo $pm_class; ?>"  style="width: 50px"  maxlength="5" >
                        <?php
                        if(in_array($schedule_time[$i], $stop_word)) {
                            if(strtolower($schedule_time[$i]) == 'sat') {
                                echo '<span style="font-weight:600;color:#900">';
                                echo $schedule_time[$i];
                                echo '</span>';
                            }
                            elseif (strtolower($schedule_time[$i]) == 'blk') {
                                echo '<span style="color:#ccc">';
                                echo '&nbsp;';
                                echo '</span>';
                            }
                            else {
                              echo '<span style="'.$highlight_class.'">';
                              echo $schedule_time[$i];
                              echo '</span>';
                            }
                        }
                        else {
                            echo '<span style="'.$highlight_class.'">';
                            echo date('g:i', strtotime($schedule_time[$i]));
                            echo '</span>';
                        } ?>
                    </div>
                </td>
		<?php
			  endif;
			endfor;
		?>

	</tr>
	<?php } else { ?>
	<tr class="stop-label-schedule stop-schedule">
	     <td style="text-align:left;position: relative;left:0;z-index: 9999;background: #F1F1F1;padding:7px 0 0 0px;margin:0" class="lakeland-col-name " colspan="42"><div class="s-name" ><?php echo $schedule_time[0] ?></div></td>
	</tr>

<?php
	 }
	$cnt++;
 }
    ?>

</table>
<a href="javascript:void(0)" class="show-all-route display-none">Show All</a>
</div>
<br/>
<table width="100%" cellspacing="1" cellpadding="1" class="schedule-footer">
<?php
 for ($footer_cnt = 1; $footer_cnt <= 8; $footer_cnt++) {
	if(isset($schedule['footer_' . $footer_cnt]) && $schedule['footer_' . $footer_cnt]) {
 ?>

	<tr class="stop-schedule">
		<td >
			<?php echo $schedule['footer_' . $footer_cnt]; ?>
		</td>
	</tr>
<?php
	}
} ?>
</table>
</div>
<?php } ?>

<?php
add_action('wp_ajax_schedule', 'schedule');
add_action('wp_ajax_nopriv_schedule', 'schedule');

function schedule_pdf() {
	global $stop_word;
	global $wpdb;

    $schedule_attributes_table = $wpdb->prefix . 'schedule_attributes';
	$schedule_detail_table = $wpdb->prefix . 'schedule_detail';
	$schedule_table = $wpdb->prefix . 'schedules';
	$day_list = array('', 'Monday To Fridays', 'Weekend', 'Holidays');
	$schedule = $wpdb->get_results( 'SELECT * FROM  '. $schedule_table .' where id=' . $_REQUEST['id'], ARRAY_A );
	$schedule = $schedule[0];
    $attributes = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM " . $schedule_attributes_table . " WHERE id=%d AND name=%s", $_REQUEST['id'], '-highlight-'),
        ARRAY_A
    );
    $highlight_values = json_decode($attributes['values']);
?>
    <?php ob_start(); ?>

	<div class="schedule-detail-popup">
	  <h1 class="schedule-detail-head" align="center"><?php echo ucfirst($schedule['title']); ?> </h1>
	  <h2 class="schedule-detail-head" align="center"><?php echo ucfirst($day_list[$schedule['day']]); ?></h2>
	  <h3 class="schedule-detail-head" align="center"><?php echo ucfirst($schedule['title_3']); ?></h3>

      <div  style="overflow:auto;"  class="stop-schedule-container" >
        <table cellspacing="0" cellpadding="0"   style="border:1px solid #ccc;padding:0px;margin:0px;position:relative">
          <?php $results = $wpdb->get_results( 'SELECT * FROM  ' . $schedule_detail_table .  ' WHERE schedule_id=' . $_REQUEST['id'] , ARRAY_A ); ?>
          <?php $cnt = 0; ?>
          <?php foreach ($results as $result) { ?>
            <?php $schedule_time = json_decode($result['schedules']); ?>
            <?php if($result['stop_name'] != '-lbl-'): ?>
              <tr class="stop-schedule" style="">
                <td style="padding: 4px;margin: 2px;position:relative;left:0;z-index: 9999;white-space: nowrap;font-size:32px;border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;height: 80px"  class="lakeland-col-name ">
                    <div class="s-name" ><strong><?php echo $result['stop_name'] ?></strong></div>
                </td>
                <?php for($i = 0; $i< 42; $i++): ?>
                  <?php if($schedule_time[$i] != '00:00' && $schedule_time[$i] != ''): ?>
                    <?php
                    if(date('H', strtotime($schedule_time[$i])) >= 12) {
                      $pm_class= 'background: #ccc';
                    }
                    else {
                      $pm_class= '';
                    }
                    if (isset($highlight_values[$i]) && !empty($highlight_values[$i])) {
                        $highlight_class = 'font-weight:600;color:#ba1200;';
                    }
                    else {
                        $highlight_class = '';
                    }
                    ?>
                    <td style="height: 80px;padding:4px;margin:2px;border-bottom: 1px solid #ccc;border-right: 1px solid #ccc; <?php  echo $pm_class; ?>; height: 80px;font-size:32px"  class="<?php echo ($i==0 ? 'first' : '' );?>  ">
                      <div class="s-time "  >
                        <?php
                            if(in_array($schedule_time[$i], $stop_word)) {
                                if(strtolower($schedule_time[$i]) == 'sat') {
                                    echo '<span style="font-weight:600;color:#900">';
                                    echo $schedule_time[$i];
                                    echo '</span>';
                                }
                                elseif (strtolower($schedule_time[$i]) == 'blk') {
                                    echo '<span style="color:#ccc">';
                                    echo '&nbsp;';
                                    echo '</span>';
                                }
                                else {
                                    echo '<span style="'.$highlight_class.'">';
                                    echo $schedule_time[$i];
                                    echo '</span>';
                                }
                            }
                            else {
                              echo '<span style="'.$highlight_class.'">';
                              echo date('h:i', strtotime($schedule_time[$i])) ;
                              echo '</span>';
                            }
                        ?>
                      </div>
                    </td>
                  <?php endif; ?>
                <?php endfor;?>
              </tr>
	        <?php else: ?>
              <tr class="stop-label-schedule stop-schedule">
                <td style="text-align:left;position: relative;left:0;z-index: 9999;background: #F1F1F1;padding:7px 0 0 0px;margin:0" class="lakeland-col-name " colspan="42">
                  <div class="s-name" ><?php echo $schedule_time[0] ?></div>
                </td>
              </tr>
            <?php endif; ?>
            <?php $cnt++; ?>
          <?php } ?>
        </table>
      </div>
      <br/>
      <table width="100%" cellspacing="1" cellpadding="1" class="schedule-footer">
        <?php for ($footer_cnt = 1; $footer_cnt <= 8; $footer_cnt++) { ?>
	      <?php if(isset($schedule['footer_' . $footer_cnt]) && $schedule['footer_' . $footer_cnt]) { ?>
            <tr class="stop-schedule">
                <td >
                    <?php echo $schedule['footer_' . $footer_cnt]; ?>
                </td>
            </tr>
          <?php } ?>
        <?php } ?>
      </table>
    </div>
    <?php
      $page = ob_get_contents();
      ob_end_clean();
    ?>

    <?php
    include  ABSPATH . '/wp-content/plugins/lakeland-schedules/lib/mpdf/mpdf.php';
    $default_font = 0 == $pdfprnt_options_array['additional_fonts'] ? 'dejavusansmono' : '';
    $mpdf         = new mPDF( '+aCJK','', 0,  $default_font, 1, 1, 15, 1, 9, 9, 'L' );

    $msg = 	 $page;
    $file_name = preg_replace('/[^A-Za-z0-9\-]/', '_', $schedule['title']);
    $mpdf->allow_charset_conversion = true;
      $mpdf->charset_in  = get_bloginfo( 'charset' );
      $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont   = true;
        $mpdf->AddPage('L');
        $mpdf->baseScript       = 1;
        $mpdf->autoVietnamese   = true;
        $mpdf->autoArabic       = true;
        $data = '<html>
        <head></head>
       <body style="padding:0 50px">&nbsp;<div style="width:90%;margin:0 auto">' .
         '<div style="padding: 4px;font-family: arial">' . $msg . '</div></div>'
        .
        '</body>
        </html>';
    $mpdf->WriteHTML($data);
    $mpdf->Output( '../wp-content/uploads/pdf/' . $file_name  .'.pdf');
    echo get_site_url() .'/wp-content/uploads/pdf/'. $file_name  .'.pdf';
    die;
}
add_action('wp_ajax_schedule_pdf', 'schedule_pdf');
add_action('wp_ajax_nopriv_schedule_pdf', 'schedule_pdf');
