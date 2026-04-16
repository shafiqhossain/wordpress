<?php
/*
  Plugin Name: WeShare Calculator Shortcode
  Plugin URI:
  Description: A plugin to display WeShare premium savings
  Version: 1.0
  Author: Md. Shafiq Hossain
  License: GPL2
*/

function hotel_cost_calculator_display($atts) {
	extract(shortcode_atts(array(
      'title' => '',
      'top_desc' => '',
      'bottom_desc' => '',
      'class' => 'hotel-cost-calculator',
      'pfactor' => '0.18',
      'currency' => '$',
      'cindex' => '1',
      'outer_border' => '0',
      'outer_divider' => '0',
      'inner_border' => '0',
      'inner_divider' => '0',
	), $atts));

	if(empty($pfactor)) {
	  $premium_factor = 0.18;
	}
	else {
	  $premium_factor = floatval($pfactor);
	}
	$premium_factor_percntage = $premium_factor * 100;

	if(empty($cindex)) {
	  $cindex = '1';
	}
	if(empty($currency)) {
	  $currency = '$';
	}

	$title = __($title,'weshare');
	$text_days = __('How many days in the next 12 months in hotels?','weshare');
	$text_average = __("What's your average per night?",'weshare');
	$text_expenditure = __('Your total expenditure:','weshare');
	$text_savings = __('WeShare Premium average savings','weshare');
	$text_net = __('Your new WeShare Premium net:','weshare');

	$output = '<div class="hotel-cost-calculator-wrapper '.($outer_border ? 'hotel-outer-border' : '').'">';
	$output .= '<form action="" class="hotel-cost-calculator-form" id="hotel-cost-calculator-form-'.$cindex.'">';
	if(!empty($title)) {
	  $output .= '<h2 class="calculator-title">'.$title.'</h2>';
	}
	if(!empty($top_desc)) {
	  $output .= '<p class="top-description">'.$top_desc.'</p>';
	}
	$output .= '<table class="hotel-cost-calculator-list '.($inner_border ? 'hotel-inner-border' : '').'">';
	$output .= '  <tbody>';
	$output .= '    <tr class="row-header">';
	$output .= '      <td colspan="2" class="row-image row-image-challenge"><img src="'.get_template_directory_uri().'/images/upgrade/take_the_challenge.png" alt="Take The Challenge" /></td>';
	$output .= '    </tr>';
	$output .= '    <tr class="row-days">';
	$output .= '      <th class="row-label row-label-days">'.$text_days.'</th>';
	$output .= '      <td class="row-value row-value-days"><span class="input"><input name="hotel_days_'.$cindex.'" id="hotel_days_'.$cindex.'" type="text" value="" class="hotel-days" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" maxlength="3" /></span></td>';
	$output .= '    </tr>';
	$output .= '    <tr class="row-average">';
	$output .= '      <th class="row-label row-label-average">'.$text_average.'</th>';
	$output .= '      <td class="row-value row-value-average"><span class="currency">'.$currency.'</span><span class="input"><input name="hotel_average_'.$cindex.'" id="hotel_average_'.$cindex.'" type="text" value="" class="hotel-average-night" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" maxlength="5" /></span></td>';
	$output .= '    </tr>';
	$output .= '    <tr class="row-expenditure">';
	$output .= '      <th class="row-label row-label-expenditure">'.$text_expenditure.'</th>';
	$output .= '      <td class="row-value row-value-expenditure"><span class="currency">'.$currency.'</span><span class="input"><input name="hotel_total_expenditure_'.$cindex.'" id="hotel_total_expenditure_'.$cindex.'" type="text" value="" readonly class="hotel-total-expenditure" tabindex="-1" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" /></span></td>';
	$output .= '    </tr>';
	$output .= '    <tr class="row-savings">';
	$output .= '      <th class="row-label row-label-savings">'.$text_savings.' '.$premium_factor_percntage.'%:</th>';
	$output .= '      <td class="row-value row-value-savings"><span class="currency">'.$currency.'</span><span class="input"><input name="hotel_premium_average_savings_'.$cindex.'" id="hotel_premium_average_savings_'.$cindex.'" type="text" value="" readonly class="hotel-average-savings" tabindex="-1" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" /></span></td>';
	$output .= '    </tr>';
	$output .= '    <tr class="row-net">';
	$output .= '      <th class="row-label row-label-net">'.$text_net.'</th>';
	$output .= '      <td class="row-value row-value-net"><span class="currency">'.$currency.'</span><span class="input"><input name="hotel_premium_net_'.$cindex.'" id="hotel_premium_net_'.$cindex.'" type="text" value="" readonly class="hotel-premium-net" tabindex="-1" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" /></span></td>';
	$output .= '    </tr>';
	$output .= '    <tr class="row-header">';
	$output .= '      <td colspan="2" class="row-image row-image-upgrade"><img src="'.get_template_directory_uri().'/images/upgrade/upgrade_now.png" alt="Upgrade Now" /></td>';
	$output .= '    </tr>';
	$output .= '  </tbody>';
	$output .= '</table>';
	$output .= '<input name="hotel_index_'.$cindex.'" id="hotel_index_'.$cindex.'" type="hidden" value="'.$cindex.'" />';
	$output .= '<input name="hotel_factor_'.$cindex.'" id="hotel_factor_'.$cindex.'" type="hidden" value="'.$premium_factor.'" />';
	$output .= '</form>';

	if(!empty($bottom_desc)) {
	  $output .= '<p class="bottom-description">'.$bottom_desc.'</p>';
	}
	if($inner_divider) {
	  $output .= '<hr class="hotel-inner-divider">';
	}

	$output .= '</div>';
	if($outer_divider) {
	  $output .= '<hr class="hotel-outer-divider">';
	}

	return $output;
}
add_shortcode('hotel-cost-calculator', 'hotel_cost_calculator_display');


function hotel_cost_calculator_load_plugin_libraries() {
  $plugin_url = plugin_dir_url( __FILE__ );

  wp_enqueue_style('calculator-style', $plugin_url . 'css/calculator.css' );
  wp_enqueue_script('calculator-script', $plugin_url . '/js/calculator.js', array ( 'jquery' ), 1.1, true);
  wp_localize_script('calculator-ajax-script', 'calculator_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' )) );
}
add_action( 'wp_enqueue_scripts', 'hotel_cost_calculator_load_plugin_libraries' );


function hotel_cost_calculator_variables() { ?>
  <script type="text/javascript">
        var ajax_url = '<?php echo admin_url( "admin-ajax.php" ); ?>';
        var ajax_nonce = '<?php echo wp_create_nonce( "secure_nonce_name" ); ?>';
  </script><?php
}
add_action ( 'wp_head', 'hotel_cost_calculator_variables' );

/* ajax calculation */
function hotel_cost_calculator_ajax_calculation(){
  // This is a secure process to validate if this request comes from a valid source.
  //check_ajax_referer('secure-nonce-name', 'security');

  $days = intval($_POST['days']);
  $average = floatval($_POST['average']);
  $factor = floatval($_POST['factor']);

  $expenditure = $days * $average;
  $savings = $expenditure * $factor;
  $net = $expenditure - $savings;

  echo json_encode(array("expenditure" => number_format($expenditure,0), "savings" => number_format($savings,0), "net" => number_format($net,0)));
  die;
}
add_action('wp_ajax_nopriv_hotel_cost_calculator_ajax_calculation', 'hotel_cost_calculator_ajax_calculation');
add_action('wp_ajax_hotel_cost_calculator_ajax_calculation', 'hotel_cost_calculator_ajax_calculation');

?>
