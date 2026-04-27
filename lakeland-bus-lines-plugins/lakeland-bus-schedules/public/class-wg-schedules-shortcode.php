<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    lakeland_bus_schedules
 * @subpackage lakeland_bus_schedules/public
 * @author     Shafiq Hossain <md.shafiq.hossain@gmail.com>
 */
class LakeLand_Schedules_Shortcode {


	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function lakeland_schedules_listing_shortcode($atts, $content = null) {
	 global $wpdb;

	 $limit = '';
	 if(isset($atts['count']) && $atts['count']) {
	 	$limit = ' limit 10';
	 }
	 $schedule_table = $wpdb->prefix . 'schedules';

	 $results = $wpdb->get_results( 'SELECT * FROM  '. $schedule_table . ' order by day ' . $limit, ARRAY_A );

	 $day_list = array('', 'Monday To Friday', 'Weekend', 'Holidays');
	 $days = array();
	 foreach ($results as $result) {
	 	$days[$result['day']][$result['zone']][] = $result;
	 }

	$proccessed = array();
	$schedule_list  = '';
	$schedule_col = '';
	foreach ($days as $key => $zones) {

		$schedule_col = '';
		$schedule_list .= '<div class="day-row-head"><div class="day-heading">' . ucwords($day_list[$key]) . '</div></div>';

		$cols = 0;
		foreach ($zones as $zone => $days) {

			$schedule_col .= '<div class="zone-col-50pr"><div class="zone-row">' . ucwords($zone) . '</div>';
			foreach ($days as  $route) {

				$schedule_col .= '<div class="zone-row-1"><div class="zone-col-50pr zone-cols"><a href="javascript:void(0)" data-id="' . $route['id'] .'" class="get-route-detail"> ' . $route['title_3'] . '</a></div>';
				 if($route['file']) {
					$schedule_col .= '<div class="zone-col-50pr zone-cols"><a target="new" href="'. $route['file'] .'" data-id="' . $route['id'] . '" >' . $route['zone_info'] . '</a></div></div>';
				} else {
					$schedule_col .= '<div class="zone-col-50pr zone-cols"><a href="javascript:void(0)" data-id="' . $route['id'] . '" class="get-route-pdf">' . $route['zone_info'] . '</a></div></div>';
				}
			  }

			 $schedule_col .= '</div>';

			$cols++;

			if($cols%2 == 0 || count($zones) == $cols)  {
				$schedule_list .= '<div class="route-row">' . $schedule_col .'</div>';
				$schedule_col = '';
			}

		}
	}

$tour_listing .= <<<HTML

	<div class="schedule-list-container"><div class="loader display-none"><div class="loader-txt">Loading ...</div></div>$schedule_list</div>

HTML;
 echo $tour_listing;

	}

	public function lakeland_schedules_register_shortcode() {
		add_shortcode('lakeland_schedules', array($this, 'lakeland_schedules_listing_shortcode'));
	}

 	public static function fetch_posts($attr) {

 	}

}
