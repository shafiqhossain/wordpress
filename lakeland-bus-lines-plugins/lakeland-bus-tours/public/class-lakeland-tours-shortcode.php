<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    lakeland_bus_tours
 * @subpackage lakeland_bus_tours/public
 * @author     Shafiq Hossain <md.shafiq.hossain@gmail.com>
 */
class LakeLand_Tours_Shortcode {


	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function lakeland_tours_listing_shortcode($atts, $content = null) {
	 global $wpdb;

	 $results = $wpdb->get_results( 'SELECT * FROM  wp_lakeland_tours where expiry >= now() order by from_date, month ', ARRAY_A );
	 $tour_list = '';

	 $pros = array();
	 foreach ($results as $result) {
	 	$year_date = date('Ym', strtotime($result['from_date']));
	 	$pros[$year_date][] = $result;
	 }
	 ksort($pros);

	$proccessed = array();
	$cnt = 0;

	if(isset($atts['title']) && $atts['title'] != '') {
		$tour_list .= '<div class="tour-row-head"><div class="tour-heading">' . stripslashes($atts['title'] ). '</div></div>';
	}
	$last_months = array();
	if(isset($atts['months'])) {
		for($i =0 ;$i < $atts['months'] ;$i++) {
			array_push($last_months, date('F', strtotime('+'. $i .' month')));
		}
	}
	foreach ($pros as $key => $pro):

		foreach ($pro as $result) {
		$month = date('F', strtotime($result['from_date']));
		if(!in_array($month, $proccessed) && (!isset($atts['months']) || ($cnt < $atts['months'] && in_array($month, $last_months)))) {
			$tour_list .= '<div class="tour-row"><div class="tour-month">' . $month . '</div></div>';
			array_push($proccessed, $month);
			$cnt++;
		}

if(in_array($month, $proccessed)) {
		$tour_list .= '<div class="tour-row">';
		if(isset($result['to_date']) && (strtotime($result['to_date']) > strtotime($result['from_date']) )) {
			$day = 'Multi Days';
			$date = date('m/d/y', strtotime($result['from_date'])) . ' - ' . date('m/d/y', strtotime($result['to_date']));
		} else {
			$day =  date('l', strtotime($result['from_date']));
			$date = date('m/d/y', strtotime($result['from_date']));
		}
		if($result['status']==1) {
		  $status = 'Limited Availability';
		}
		elseif($result['status']==2) {
		  $status = 'Sold Out';
		}
		else {
		  $status = '';
		}

		$tour_list .= '<div class="tour-title"><a href="#'.$result['id'].'" data-id="' .$result["id"] .'" >' . ucfirst(stripslashes($result['title'])) . '</a></div>';
		$tour_list .= '<div class="tour-day">' . $day . '</div>';
		$tour_list .= '<div class="tour-date">' . $date . '</div>';
		$tour_list .= '<div class="tour-status">' . $status . '</div>';
		$tour_list .= '</div>';
}
		}
	endforeach;

$tour_listing .= <<<HTML

	<div class="tour-list-container"><div class="loader display-none"><div class="loader-txt">Loading ...</div></div>$tour_list</div>

HTML;
 echo $tour_listing;

	}

	public function lakeland_tours_register_shortcode() {
		add_shortcode('lakeland_tours', array($this, 'lakeland_tours_listing_shortcode'));
	}

 	public static function fetch_posts($attr) {

 	}

}
