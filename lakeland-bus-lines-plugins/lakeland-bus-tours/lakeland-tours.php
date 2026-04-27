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
 * @package           lakeland_bus_tours
 *
 * @wordpress-plugin
 * Plugin Name:       Tour Management
 * Plugin URI:        http://www.isoftbd.com
 * Description:       Tour Management is used to manage tour listing and details
 * Version:           1.0.0
 * Author:            Shafiq Hossain
 * Author URI:
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       lakeland-bus-tours
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
function activate_lakeland_tours() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-lakeland-tours-activator.php';
	LakeLand_Tours_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_lakeland_contacts() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-lakeland-tours-deactivator.php';
	LakeLand_Tours_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_lakeland_tours' );
register_deactivation_hook( __FILE__, 'deactivate_lakeland_tours' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-lakeland-tours.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_lakeland_tours() {

	$plugin = new LakeLand_Tours();
	$plugin->run();

}
run_lakeland_tours();

function loadMore() {

	global $wpdb;
	 $results = $wpdb->get_results( 'SELECT * FROM  wp_lakeland_tours  where id=' . $_REQUEST['id'] . ' order by from_date, month ', ARRAY_A );
	 $results = $results[0];
	 $tour_detail = $wpdb->get_results( 'SELECT * FROM  wp_tour_details where tour_id=' . $_REQUEST['id'], ARRAY_A );
	 $tour_detail = $tour_detail[0];
	 $tour_imgs = $wpdb->get_results( 'SELECT * FROM  wp_tour_images where tour_id='. $tour_detail['id'], ARRAY_A );
	 $tour_dept = $wpdb->get_results( 'SELECT * FROM  wp_tour_depts where tour_id='. $tour_detail['id'], ARRAY_A );
	 $upload_dir = wp_upload_dir();

     $img_url = $upload_dir['baseurl'] . '/tour_img/';
	 $tour_trips = $wpdb->get_results( 'SELECT * FROM  wp_tour_trips where tour_id='. $tour_detail['id'] . ' order by date, trip_hh, trip_hh_to', ARRAY_A );
	 ?>
	 <div class="tour-detail-popup">
	  <a href="#" class="back-to-listing">Back</a>
	 <h1 class="trip-d-header"><?php echo ucfirst(stripslashes($results['title'])); ?>

	 </h1>
	 <h2  class="trip-d-header"><?php echo date('F d, Y', strtotime($results['from_date'])); ?></h2>

	 <div class="tour-img-container">
	 <?php foreach ($tour_imgs  as $tour_img ): ?>
	 	 <div class="tour-img"><img src="<?php echo $img_url . $tour_img['name']; ?>" /></div>
	 <?php endforeach; ?>
	 </div>
	 <div class="tour-detail-row">
	 	<div class="tour-detail-head">
	 		SEATS/TICKETS: <?php echo ucfirst($tour_detail['no_seat']); ?> (Please call for current availablity)
	 	</div>
	 </div>
	 <div class="tour-detail-row">
	 	<div class="tour-detail-col-50pr border-tl">
	 		Departure Time
	 	</div>
	 	<div class="tour-detail-col-50pr border-tl">
	 		Return Time
	 	</div>
	 </div>
	 <?php
	 $cnt = 0;
	 foreach ($tour_dept as $tour_departure) :
	 	$class = $cnt == 0 ? 'first' : '';
	 ?>

	 <div class="tour-detail-row <?php echo $class; ?>">
	 	<div class="tour-detail-col-50pr">
	 		<div class="tour-detail-row">
	 			<div class="tour-detail-col-75pr"><?php echo $tour_departure['dept_location']; ?></div>
	 			<div class="tour-detail-col-25pr"><?php echo date('h:i A', strtotime($tour_departure['dept_hh_mm'])); ?></div>
	 		</div>
	 	</div>
	 	<div class="tour-detail-col-50pr">
	 		<div class="tour-detail-row">
	 			<div class="tour-detail-col-75pr"><?php echo $tour_departure['return_location']; ?></div>
	 			<div class="tour-detail-col-25pr"><?php echo date('h:i A', strtotime($tour_departure['return_hh_mm'])); ?></div>
	 		</div>
	 	</div>
	 </div>
	<?php
	$cnt++;
	endforeach; ?>

	<br/>
	<div class="tour-detail-note">* Please note that we need a minimum of 10 passengers for a Sparta pick-up. When booking, if you are interested in picking up in Sparta, let our reservationist know. If we get 10 or more, we'll contact you.</div>
	<br/>
	<div class="tour-detail-row ">
		<div class="tour-detail-col-100pr  no-border"><span class="tour-label">Price :</span> <?php echo $tour_detail['price']; ?></div>
	</div>

	<br/>
	<div class="tour-detail-row ">
		<div class="tour-detail-col-100pr trip-header">TRIP ITINERARY</div>
	</div>
	<?php foreach ($tour_trips as $tour_trip) :  ?>
	<div class="tour-trip-detail-row ">
		<div class="tour-trip-col-25pr"><?php echo date('h:i A', strtotime($tour_trip['trip_hh'])) . ' - ' . date('h:i A', strtotime($tour_trip['trip_hh_to'])); ?></div>
		<div class="tour-trip-col-75pr">
			<div class="tour-trip-detail"><?php echo stripslashes($tour_trip['title']); ?></div>
			<?php echo stripslashes($tour_trip['description']); ?>
		</div>
	</div>
	<?php endforeach;  ?>
<br/>
<p>Please contact a tour representative for more information and/or reservations at (973) 366-0600 ext. 602.</p>
</div>
<?php
}
add_action('wp_ajax_loadMore', 'loadMore');
add_action('wp_ajax_nopriv_loadMore', 'loadMore');


function searchContactAddress() {

	if(isset($_REQUEST['phone_1']) && $_REQUEST['phone_1']) {
		global  $wpdb;

		$phones = $_REQUEST;
		unset($phones['action']);
		$phones = implode(',', $phones);

		$sub_query  = ' where phone in (' .  $phones . ')';
		$table = $wpdb->prefix . 'lakeland_bus_contacts';
	    $results = $wpdb->get_results( 'SELECT * FROM  ' . $table .   $sub_query, ARRAY_A );
	    echo json_encode($results);
	    die;
	}
}
add_action('wp_ajax_searchContactAddress', 'searchContactAddress');
add_action('wp_ajax_nopriv_searchContactAddress', 'searchContactAddress');
