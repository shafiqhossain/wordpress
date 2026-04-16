<?php
  /*
  Plugin Name: Custom Rivet Fields Shortcode
  Plugin URI:
  Description: a plugin to to display custom rivet fields into woocommerce product tab
  Version: 1.0
  Author: Md. Shafiq Hossain
  License: GPL2
  */

  function custom_rivet_fields_display($atts) {
	extract(shortcode_atts(array(
      'field' => '_part_items',
      'class' => 'custom-parts',
      'id' => 'custom-parts-display',
      'post_id' => '',
      'label' => 'no',
	), $atts));

	if(empty($post_id)) {
	  $post_id = get_the_ID();
	}

	//print 'post_id: '.$post_id.'<br />';
	//print 'field: '.$field.'<br />';
	$items = get_post_meta($post_id, $field, true);
	//print_r($items);

	$output = '';

	if(count($items)>0) {
		$output .='
			<script>
			jQuery.expr[\':\'].Contains = function(a, i, m) {
			  return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
			};
			</script>

			<div id="search-parts-number" class="parts-search">
			  <span class="parts-search-label">Search: </span><input type="text" name="search-parts-number" class="parts-search-text">
			</div>

			<script>
			(function($){
			  $(\'input[name="search-parts-number"]\').keyup(function(){
				var searchby = $(\'input[name="search-parts-number"]\').val();
				$(\'.parts-custom-fields tbody tr\').css(\'display\',\'none\');
				$(\'.parts-custom-fields tbody tr:Contains("\'+searchby+\'")\').css(\'display\',\'table-row\');
			  });
			})(jQuery);
			</script>
			<!-- <script src="'.plugins_url().'/custom-fields-display/sorttable.js"></script>-->
		';

		$layout_type = get_post_meta($post_id, '_display_layout_type', true);

		if($layout_type == 2) {
			$output .= '<table id="'.$id.'" cellpadding="5" cellspacing="0" class="parts-custom-fields parts-items-list sortable1 '.$class.'">';

			if($label=='yes') {
			  $output .= '<thead>';
			  $output .= '	<tr>';
			  $output .= '		<th>Parts Number</th>';
			  $output .= '		<th>Diameter</th>';
			  $output .= '		<th>Grip Code</th>';
			  $output .= '		<th>Grip Range</th>';
			  $output .= '		<th>Head Type</th>';
			  $output .= '		<th>Head Diameter</th>';
			  $output .= '		<th>Head Height</th>';
			  $output .= '		<th>Material</th>';
			  $output .= '		<th>Length</th>';
			  $output .= '		<th>Notch Length</th>';
			  $output .= '		<th>Catalog Page</th>';
			  $output .= '		<th>Drawing</th>';
			  $output .= '		<th>Tools</th>';
			  //$output .= '		<th>Category</th>';
			  //$output .= '		<th>Sub-Cat</th>';
			  //$output .= '		<th>Series</th>';
			  $output .= '	</tr>';
			  $output .= '</thead>';
			}

			$output .= '<tbody>';
			foreach($items as $key => $row) {
			  $catalogpage_link = '';
			  $drawing_link = '';

			  $catalogpage = (isset($row['catalogpage']) ? $row['catalogpage'] : '');
			  if(!empty($catalogpage)) {
				$catalogpage_link = '<a target="_blank" href="'.$catalogpage.'"><img src="'.get_stylesheet_directory_uri().'/images/catalog-page.png" alt="Catalog Page" width="32px" height="32px" /></a>';
			  }
			  else {
				$catalogpage_link = '<a target="_blank" href="#"><img src="'.get_stylesheet_directory_uri().'/images/catalog-page.png" alt="Catalog Page" width="32px" height="32px" /></a>';
			  }

			  $drawing = (isset($row['drawing']) ? $row['drawing'] : '');
			  if(!empty($drawing)) {
				$drawing_link = '<a target="_blank" href="'.$drawing.'"><img src="'.get_stylesheet_directory_uri().'/images/drawing-page.png" alt="Catalog Page" width="32px" height="32px" /></a>';
			  }
			  else {
				$drawing_link = '<a target="_blank" href="#"><img src="'.get_stylesheet_directory_uri().'/images/drawing-page.png" alt="Catalog Page" width="32px" height="32px" /></a>';
			  }

			  $bodymaterial = '';
			  if(is_array($row['bodymaterial'])) {
				$bodymaterial = implode(", ", $row['bodymaterial']);
			  }
			  else {
				$bodymaterial = $row['bodymaterial'];
			  }

			  $output .= '	<tr>';
			  $output .= '		<td>'.(isset($row['item_id']) ? $row['item_id'] : '').'</td>';
			  $output .= '		<td>'.(isset($row['diameter']) ? $row['diameter'] : '').'</td>';
			  $output .= '		<td>'.(isset($row['grip_code']) ? $row['grip_code'] : '').'</td>';
			  $output .= '		<td>'.(isset($row['grip']) ? $row['grip'] : '').'</td>';
			  $output .= '		<td>'.(isset($row['head_type']) ? $row['head_type'] : '').'</td>';
			  $output .= '		<td>'.(isset($row['head_diameter']) ? $row['head_diameter'] : '').'</td>';
			  $output .= '		<td>'.(isset($row['head_height']) ? $row['head_height'] : '').'</td>';
			  $output .= '		<td>'.$bodymaterial.'</td>';
			  $output .= '		<td>'.(isset($row['length']) ? $row['length'] : '').'</td>';
			  $output .= '		<td>'.(isset($row['notch_length']) ? $row['notch_length'] : '').'</td>';
			  $output .= '		<td>'.$catalogpage_link.'</td>';
			  $output .= '		<td>'.$drawing_link.'</td>';
			  $output .= '		<td>'.(isset($row['tools']) ? $row['tools'] : '').'</td>';
			  //$output .= '		<td>'.(isset($row['category']) ? $row['category'] : '').'</td>';
			  //$output .= '		<td>'.(isset($row['subcategory']) ? $row['subcategory'] : '').'</td>';
			  //$output .= '		<td>'.(isset($row['series']) ? $row['series'] : '').'</td>';
			  $output .= '	</tr>';
			}  //for
			$output .= '</tbody>';
			$output .= '</table>';
		}
		else if($layout_type == 3) {
			$output .= '<table id="'.$id.'" cellpadding="5" cellspacing="0" class="parts-custom-fields parts-items-list sortable '.$class.'">';

			if($label=='yes') {
			  $output .= '<thead>';
			  $output .= '	<tr>';
			  $output .= '		<th>Parts Number</th>';
			  $output .= '		<th>Diameter</th>';
			  $output .= '		<th>Grip Code</th>';
			  $output .= '		<th>Grip Range</th>';
			  $output .= '		<th>Head Type</th>';
			  $output .= '		<th>Head Diameter</th>';
			  $output .= '		<th>Head Height</th>';
			  $output .= '		<th>Material</th>';
			  $output .= '		<th>Length</th>';
			  //$output .= '		<th>Notch Length</th>';
			  $output .= '		<th>Catalog Page</th>';
			  $output .= '		<th>Drawing</th>';
			  $output .= '		<th>Tools</th>';
			  //$output .= '		<th>Category</th>';
			  //$output .= '		<th>Sub-Cat</th>';
			  //$output .= '		<th>Series</th>';
			  $output .= '	</tr>';
			  $output .= '</thead>';
			}

			$output .= '<tbody>';
			foreach($items as $key => $row) {
			  $catalogpage_link = '';
			  $drawing_link = '';

			  $catalogpage = (isset($row['catalogpage']) ? $row['catalogpage'] : '');
			  if(!empty($catalogpage)) {
				$catalogpage_link = '<a target="_blank" href="'.$catalogpage.'"><img src="'.get_stylesheet_directory_uri().'/images/catalog-page.png" alt="Catalog Page" width="32px" height="32px" /></a>';
			  }
			  else {
				$catalogpage_link = '<a target="_blank" href="#"><img src="'.get_stylesheet_directory_uri().'/images/catalog-page.png" alt="Catalog Page" width="32px" height="32px" /></a>';
			  }

			  $drawing = (isset($row['drawing']) ? $row['drawing'] : '');
			  if(!empty($drawing)) {
				$drawing_link = '<a target="_blank" href="'.$drawing.'"><img src="'.get_stylesheet_directory_uri().'/images/drawing-page.png" alt="Catalog Page" width="32px" height="32px" /></a>';
			  }
			  else {
				$drawing_link = '<a target="_blank" href="#"><img src="'.get_stylesheet_directory_uri().'/images/drawing-page.png" alt="Catalog Page" width="32px" height="32px" /></a>';
			  }

			  $bodymaterial = '';
			  if(is_array($row['bodymaterial'])) {
				$bodymaterial = implode(", ", $row['bodymaterial']);
			  }
			  else {
				$bodymaterial = $row['bodymaterial'];
			  }

			  $output .= '	<tr>';
			  $output .= '		<td>'.(isset($row['item_id']) ? $row['item_id'] : '').'</td>';
			  $output .= '		<td>'.(isset($row['diameter']) ? $row['diameter'] : '').'</td>';
			  $output .= '		<td>'.(isset($row['grip_code']) ? $row['grip_code'] : '').'</td>';
			  $output .= '		<td>'.(isset($row['grip']) ? $row['grip'] : '').'</td>';
			  $output .= '		<td>'.(isset($row['head_type']) ? $row['head_type'] : '').'</td>';
			  $output .= '		<td>'.(isset($row['head_diameter']) ? $row['head_diameter'] : '').'</td>';
			  $output .= '		<td>'.(isset($row['head_height']) ? $row['head_height'] : '').'</td>';
			  $output .= '		<td>'.$bodymaterial.'</td>';
			  $output .= '		<td>'.(isset($row['length']) ? $row['length'] : '').'</td>';
			  //$output .= '		<td>'.(isset($row['notch_length']) ? $row['notch_length'] : '').'</td>';
			  $output .= '		<td>'.$catalogpage_link.'</td>';
			  $output .= '		<td>'.$drawing_link.'</td>';
			  $output .= '		<td>'.(isset($row['tools']) ? $row['tools'] : '').'</td>';
			  //$output .= '		<td>'.(isset($row['category']) ? $row['category'] : '').'</td>';
			  //$output .= '		<td>'.(isset($row['subcategory']) ? $row['subcategory'] : '').'</td>';
			  //$output .= '		<td>'.(isset($row['series']) ? $row['series'] : '').'</td>';
			  $output .= '	</tr>';
			}  //for
			$output .= '</tbody>';
			$output .= '</table>';
		}
		else if($layout_type == 4) {
			$output .= '<table id="'.$id.'" cellpadding="5" cellspacing="0" class="parts-custom-fields parts-items-list sortable '.$class.'">';

			if($label=='yes') {
			  $output .= '<thead>';
			  $output .= '	<tr>';
			  $output .= '		<th>Parts #</th>';
			  $output .= '		<th>Dia.</th>';
			  $output .= '		<th>Grip Code</th>';
			  $output .= '		<th>Grip Range</th>';
			  $output .= '		<th>Head Dia.</th>';
			  $output .= '		<th>Head Ht.</th>';
			  $output .= '		<th>Matr. (M Comp)</th>';
			  $output .= '		<th>Matr. (F Comp)</th>';
			  $output .= '		<th>Length (M)</th>';
			  $output .= '		<th>Length (F)</th>';
			  $output .= '		<th>Catalog</th>';
			  $output .= '		<th>Drawing</th>';
			  $output .= '		<th>Tools</th>';
			  $output .= '	</tr>';
			  $output .= '</thead>';
			}

			$output .= '<tbody>';
			foreach($items as $key => $row) {
			  $catalogpage_link = '';
			  $drawing_link = '';
			  $solidmodel_link = '';

			  $catalogpage = (isset($row['catalogpage']) ? $row['catalogpage'] : '');
			  if(!empty($catalogpage)) {
				$catalogpage_link = '<a target="_blank" href="'.$catalogpage.'"><img src="'.get_stylesheet_directory_uri().'/images/catalog-page.png" alt="Catalog Page" width="32px" height="32px" /></a>';
			  }
			  else {
				$catalogpage_link = '<a target="_blank" href="#"><img src="'.get_stylesheet_directory_uri().'/images/catalog-page.png" alt="Catalog Page" width="32px" height="32px" /></a>';
			  }

			  $drawing = (isset($row['drawing']) ? $row['drawing'] : '');
			  if(!empty($drawing)) {
				$drawing_link = '<a target="_blank" href="'.$drawing.'"><img src="'.get_stylesheet_directory_uri().'/images/drawing-page.png" alt="Catalog Page" width="32px" height="32px" /></a>';
			  }
			  else {
				$drawing_link = '<a target="_blank" href="#"><img src="'.get_stylesheet_directory_uri().'/images/drawing-page.png" alt="Catalog Page" width="32px" height="32px" /></a>';
			  }

			  $solidmodel = (isset($row['solidmodel']) ? $row['solidmodel'] : '');
			  if(!empty($solidmodel)) {
				$solidmodel_link = '<a target="_blank" href="'.$solidmodel.'"><img src="'.get_stylesheet_directory_uri().'/images/model-page.png" alt="Catalog Page" width="32px" height="32px" /></a>';
			  }
			  else {
				$solidmodel_link = '<a target="_blank" href="#"><img src="'.get_stylesheet_directory_uri().'/images/model-page.png" alt="Catalog Page" width="32px" height="32px" /></a>';
			  }

			  $bodymaterial = '';
			  if(is_array($row['bodymaterial'])) {
				$bodymaterial = implode(",", $row['bodymaterial']);
			  }
			  else {
				$bodymaterial = $row['bodymaterial'];
			  }


			  $mandrelmaterial = '';
			  if(is_array($row['mandrelmaterial'])) {
				$mandrelmaterial = implode(",", $row['mandrelmaterial']);
			  }
			  else {
				$mandrelmaterial = $row['mandrelmaterial'];
			  }

			  $output .= '	<tr>';
			  $output .= '		<td>'.(isset($row['item_id']) ? $row['item_id'] : '').'</td>';
			  $output .= '		<td>'.(isset($row['diameter']) ? $row['diameter'] : '').'</td>';
			  $output .= '		<td>'.(isset($row['grip_code']) ? $row['grip_code'] : '').'</td>';
			  $output .= '		<td>'.(isset($row['grip']) ? $row['grip'] : '').'</td>';
			  $output .= '		<td>'.(isset($row['head_diameter']) ? $row['head_diameter'] : '').'</td>';
			  $output .= '		<td>'.(isset($row['head_height']) ? $row['head_height'] : '').'</td>';
			  $output .= '		<td>'.$bodymaterial.'</td>';
			  $output .= '		<td>'.$mandrelmaterial.'</td>';
			  $output .= '		<td>'.(isset($row['length']) ? $row['length'] : '').'</td>';
			  $output .= '		<td>'.(isset($row['notch_length']) ? $row['notch_length'] : '').'</td>';
			  $output .= '		<td>'.$catalogpage_link.'</td>';
			  $output .= '		<td>'.$drawing_link.'</td>';
			  $output .= '		<td>'.(isset($row['tools']) ? $row['tools'] : '').'</td>';
			  $output .= '	</tr>';
			}  //for
			$output .= '</tbody>';
			$output .= '</table>';
		}
		else {
			$output .= '<table id="'.$id.'" cellpadding="5" cellspacing="0" class="parts-custom-fields parts-items-list sortable '.$class.'">';

			if($label=='yes') {
			  $output .= '<thead>';
			  $output .= '	<tr>';
			  $output .= '		<th>Parts Number</th>';
			  $output .= '		<th>Diameter</th>';
			  $output .= '		<th>Grip</th>';
			  $output .= '		<th>Head Type</th>';
			  $output .= '		<th>Body Material</th>';
			  $output .= '		<th>Mandrel Material</th>';
			  $output .= '		<th>Category</th>';
			  $output .= '		<th>Sub-Cat</th>';
			  $output .= '		<th>Catalog Page</th>';
			  $output .= '		<th>Drawing</th>';
			  $output .= '		<th>Solid Model</th>';
			  $output .= '		<th>Tools</th>';
			  $output .= '	</tr>';
			  $output .= '</thead>';
			}


			$output .= '<tbody>';
			foreach($items as $key => $row) {
			  $catalogpage_link = '';
			  $drawing_link = '';
			  $solidmodel_link = '';

			  $catalogpage = (isset($row['catalogpage']) ? $row['catalogpage'] : '');
			  if(!empty($catalogpage)) {
				$catalogpage_link = '<a target="_blank" href="'.$catalogpage.'"><img src="'.get_stylesheet_directory_uri().'/images/catalog-page.png" alt="Catalog Page" width="32px" height="32px" /></a>';
			  }
			  else {
				$catalogpage_link = '<a target="_blank" href="#"><img src="'.get_stylesheet_directory_uri().'/images/catalog-page.png" alt="Catalog Page" width="32px" height="32px" /></a>';
			  }

			  $drawing = (isset($row['drawing']) ? $row['drawing'] : '');
			  if(!empty($drawing)) {
				$drawing_link = '<a target="_blank" href="'.$drawing.'"><img src="'.get_stylesheet_directory_uri().'/images/drawing-page.png" alt="Catalog Page" width="32px" height="32px" /></a>';
			  }
			  else {
				$drawing_link = '<a target="_blank" href="#"><img src="'.get_stylesheet_directory_uri().'/images/drawing-page.png" alt="Catalog Page" width="32px" height="32px" /></a>';
			  }

			  $solidmodel = (isset($row['solidmodel']) ? $row['solidmodel'] : '');
			  if(!empty($solidmodel)) {
				$solidmodel_link = '<a target="_blank" href="'.$solidmodel.'"><img src="'.get_stylesheet_directory_uri().'/images/model-page.png" alt="Catalog Page" width="32px" height="32px" /></a>';
			  }
			  else {
				$solidmodel_link = '<a target="_blank" href="#"><img src="'.get_stylesheet_directory_uri().'/images/model-page.png" alt="Catalog Page" width="32px" height="32px" /></a>';
			  }

			  $bodymaterial = '';
			  if(is_array($row['bodymaterial'])) {
				$bodymaterial = implode(", ", $row['bodymaterial']);
			  }
			  else {
				$bodymaterial = $row['bodymaterial'];
			  }


			  $mandrelmaterial = '';
			  if(is_array($row['mandrelmaterial'])) {
				$mandrelmaterial = implode(",", $row['mandrelmaterial']);
			  }
			  else {
				$mandrelmaterial = $row['mandrelmaterial'];
			  }

			  $output .= '	<tr>';
			  $output .= '		<td>'.(isset($row['item_id']) ? $row['item_id'] : '').'</td>';
			  $output .= '		<td>'.(isset($row['diameter']) ? $row['diameter'] : '').'</td>';
			  $output .= '		<td>'.(isset($row['grip']) ? $row['grip'] : '').'</td>';
			  $output .= '		<td>'.(isset($row['head_type']) ? $row['head_type'] : '').'</td>';
			  $output .= '		<td>'.$bodymaterial.'</td>';
			  $output .= '		<td>'.$mandrelmaterial.'</td>';
			  $output .= '		<td>'.(isset($row['category']) ? $row['category'] : '').'</td>';
			  $output .= '		<td>'.(isset($row['subcategory']) ? $row['subcategory'] : '').'</td>';
			  $output .= '		<td>'.$catalogpage_link.'</td>';
			  $output .= '		<td>'.$drawing_link.'</td>';
			  $output .= '		<td>'.$solidmodel_link.'</td>';
			  $output .= '		<td>'.(isset($row['tools']) ? $row['tools'] : '').'</td>';
			  $output .= '	</tr>';
			}  //for
			$output .= '</tbody>';
			$output .= '</table>';
		}//if-layout

	}//if-count

	return $output;
  }

  //https://bloke.org/wordpress/conditional-woocommerce-product-tabs/

  function custom_rivet_fields_design_consideration($atts) {
	extract(shortcode_atts(array(
      'field' => '_design_consideration',
      'class' => 'custom-design-consideration',
      'id' => 'custom-design-consideration',
      'post_id' => '',
	), $atts));

	if(empty($post_id)) {
	  $post_id = get_the_ID();
	}

	$meta_value = get_post_meta($post_id, $field, true);

	$output = '';
	if($meta_value) {
	  $post = get_post($meta_value);
	  $output .= '<a id="'.$id.'" class="'.$class.'" href="'.$post->guid.'">Design Consideration</a>';
	}

	return $output;
  }

  function custom_rivet_fields_complete_catalog($atts) {
	extract(shortcode_atts(array(
      'field' => '_complete_catalog',
      'class' => 'custom-complete-catalog',
      'id' => 'custom-complete-catalog',
      'post_id' => '',
	), $atts));

	if(empty($post_id)) {
	  $post_id = get_the_ID();
	}

	$meta_value = get_post_meta($post_id, $field, true);

	$output = '';
	if($meta_value) {
	  $post = get_post($meta_value);
	  $output .= '<a id="'.$id.'" class="'.$class.'" href="'.$post->guid.'">Complete Catalog</a>';
	}

	return $output;
  }

  function custom_rivet_fields_tool_selection($atts) {
	extract(shortcode_atts(array(
      'field' => '_tool_selection',
      'class' => 'custom-tool-selection',
      'id' => 'custom-tool-selection',
      'post_id' => '',
	), $atts));

	if(empty($post_id)) {
	  $post_id = get_the_ID();
	}

	$meta_value = get_post_meta($post_id, $field, true);

	$output = '';
	if($meta_value) {
	  $post = get_post($meta_value);
	  $output .= '<a id="'.$id.'" class="'.$class.'" href="'.$post->guid.'">Tool Selection</a>';
	}

	return $output;
  }

  function custom_rivet_fields_video_animation($atts) {
	extract(shortcode_atts(array(
      'field' => '_video_animation',
      'class' => 'custom-video-animation',
      'id' => 'custom-video-animation',
      'post_id' => '',
	), $atts));

	if(empty($post_id)) {
	  $post_id = get_the_ID();
	}

	$meta_value = get_post_meta($post_id, $field, true);

	$output = '';
	if($meta_value) {
	  $post = get_post($meta_value);
	  $output .= '<a id="'.$id.'" class="'.$class.'" href="'.$post->guid.'">Video | Animation</a>';
	}

	return $output;
  }

  function custom_rivet_fields_attached_files() {
	$post_id = get_the_ID();

    $output = '';

	//design consideration
    $field = '_design_consideration';
	$meta_value = get_post_meta($post_id, $field, true);
	if($meta_value) {
	  $post = get_post($meta_value);
	  $output .= '<a target="_blank" id="custom-design-consideration" class="custom-design-consideration" href="'.$post->guid.'">Design Consideration</a>';
	}

	//Complete Catalog
    $field = '_complete_catalog';
	$meta_value = get_post_meta($post_id, $field, true);
	if($meta_value) {
	  $post = get_post($meta_value);
	  $output .= '<a target="_blank" id="custom-complete-catalog" class="custom-complete-catalog" href="'.$post->guid.'">Complete Catalog</a>';
	}

	//Tools Selection
    $field = '_tool_selection';
	$meta_value = get_post_meta($post_id, $field, true);
	if($meta_value) {
	  $post = get_post($meta_value);
	  $output .= '<a target="_blank" id="custom-tool-selection" class="custom-tool-selection" href="'.$post->guid.'">Tool Selection</a>';
	}

	//Video, Animation
    $field = '_videotype';
	$video_type = get_post_meta($post_id, $field, true);
    $field = '_video_id';
	$video_id = get_post_meta($post_id, $field, true);
	if(!empty($video_type) && !empty($video_id)) {
	  if($video_type == 'YouTube') {
		$video_shortcode = '[video_lightbox_youtube video_id="'.$video_id.'" width="640" height="480" anchor="Video | Animation"]';
	  }
	  else if($video_type == 'Vimeo') {
		$video_shortcode = '[video_lightbox_vimeo5 video_id="'.$video_id.'" width="640" height="480" anchor="Video | Animation"]';
	  }

	  $output .= do_shortcode($video_shortcode);
	  //$output .= '<a target="_blank" id="custom-video-animation" class="custom-video-animation" href="'.$meta_value.'">Video | Animation</a>';
	}

	if(!empty($output)) {
	  $output = '<div class="product-attached-files">'.$output.'</div>';
	}

	print $output;
  }


  add_shortcode('jsf-parts', 'custom_rivet_fields_display');
  add_shortcode('jsf-design-consideration', 'custom_rivet_fields_design_consideration');
  add_shortcode('jsf-complete-catalog', 'custom_rivet_fields_complete_catalog');
  add_shortcode('jsf-tool-selection', 'custom_rivet_fields_tool_selection');
  add_shortcode('jsf-video-animation', 'custom_rivet_fields_video_animation');

  add_action('scalia_woocommerce_single_product_right', 'custom_rivet_fields_attached_files',40);
?>
