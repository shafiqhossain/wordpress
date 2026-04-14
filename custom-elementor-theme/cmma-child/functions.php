<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
	function chld_thm_cfg_locale_css( $uri ){
		if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
			$uri = get_template_directory_uri() . '/rtl.css';
		return $uri;
	}
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

if ( !function_exists( 'child_theme_configurator_css' ) ):
	function child_theme_configurator_css() {
		wp_enqueue_style( 'chld_thm_cfg_child', trailingslashit( get_stylesheet_directory_uri() ) . 'style.css', array( 'hello-elementor','hello-elementor','hello-elementor-theme-style' ) );
		wp_enqueue_style( 'scroller-css', trailingslashit( get_stylesheet_directory_uri() ) . 'assets/css/scroller.css', array( 'hello-elementor','hello-elementor','hello-elementor-theme-style' ) );
		// wp_enqueue_style( 'animation', trailingslashit( get_stylesheet_directory_uri() ) . 'assets/css/animate.css', array( 'hello-elementor','hello-elementor','hello-elementor-theme-style' ) );
		wp_enqueue_style( 'animation', 'https://cdn.jsdelivr.net/npm/wowjs@1.1.3/css/libs/animate.min.css', array( 'hello-elementor','hello-elementor','hello-elementor-theme-style' ) );
	}
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 10 );

// END ENQUEUE PARENT ACTION
function cmma_child_theme_scripts() {
    // Get the current theme version
    $theme_version = wp_get_theme()->get('Version');

    // Enqueue your scripts with theme version
    wp_enqueue_script('clipboard-js', 'https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js', array(), '2.0.8', true);
    wp_enqueue_script('scroller-js', get_stylesheet_directory_uri() . '/assets/js/scroller.js', array('jquery'), $theme_version, true);
    wp_enqueue_script('child-theme-custom-script', get_stylesheet_directory_uri() . '/assets/js/custom.js', array('jquery'), $theme_version, true);
    wp_enqueue_script('animation-script', get_stylesheet_directory_uri() . '/assets/js/wow.js', array('jquery'), $theme_version, true);
}
// Hook the function to the wp_enqueue_scripts action
add_action('wp_enqueue_scripts', 'cmma_child_theme_scripts');
// ------ Custom Code

require get_stylesheet_directory() . '/short-codes/index.php';

function custom_wp_search_size( $query ) {
	if ( $query->is_search ) {
		$query->query_vars['posts_per_page'] = 8;
	}

	return $query;
}
add_filter( 'pre_get_posts', 'custom_wp_search_size' );

add_action( 'wp_ajax_nopriv_cmma_search_posts', 'cmma_search_posts' );
add_action( 'wp_ajax_cmma_search_posts', 'cmma_search_posts' );

function cmma_search_posts() {
	$paged     = sanitize_text_field( $_POST['page'] );
	$param     = sanitize_text_field( $_POST['s'] );
	$post_type = sanitize_text_field( $_POST['filter'] );
	$response  = [];
	$args      = [
		's'              => $param,
		'post_type'      => $post_type,
		'post_status'    => 'publish',
		'posts_per_page' => 8,
		'paged'          => $paged,
	];

	$query = new WP_Query( $args );

	if ( $query->have_posts() ) {
		ob_start();
		while ( $query->have_posts() ) {
			$query->the_post();

			include get_stylesheet_directory() . '/helpers/html/search.php';
		}

		$response = [
			'status'      => 'success',
			'html'        => ob_get_clean(),
			'page'        => $paged,
			'total_pages' => $query->max_num_pages,
		];

		wp_reset_postdata();
	} else {
		$response = [
			'status' => 'error',
			'html'  =>  'No posts found',
		];
	}

	wp_send_json_success( $response );
	wp_die();
}

function get_search_filters() {
	$search_param = get_query_var( 's' );

	if ( !empty( $search_param ) ) {
		global $wpdb;

		$query = $wpdb->prepare("SELECT DISTINCT wp_posts.post_type
			FROM wp_posts
			WHERE wp_posts.post_status = 'publish'
			AND ((wp_posts.post_title LIKE %s) OR (wp_posts.post_content LIKE %s))",
			['%'. $search_param . '%', '%'. $search_param . '%']
		);

		$result = $wpdb->get_results($query);

		return $result;
	}
}

/**
 * Disable the "Add Form" option in Gravity Forms.
 * This filter removes the option to add a new form when displaying the Gravity Forms button.
*/
add_filter( 'gform_display_add_form_button', '__return_false' );

/**
 * Disabled default styles of Gravity Forms
 */
add_filter( 'gform_disable_css', '__return_true' );
add_filter( 'gform_required_legend', '__return_empty_string' );

/**
* Filters the next, previous and submit buttons.
* Replaces the form's <input> buttons with <button> while maintaining attributes from original <input>.
*
* @param string $button Contains the <input> tag to be filtered.
* @param object $form Contains all the properties of the current form.
*
* @return string The filtered button.
*/
add_filter( 'gform_next_button', 'input_to_button', 10, 2 );
add_filter( 'gform_previous_button', 'input_to_button', 10, 2 );
add_filter( 'gform_submit_button', 'input_to_button', 10, 2 );
function input_to_button( $button, $form ) {
	$dom = new DOMDocument();
	$dom->loadHTML( '<?xml encoding="utf-8" ?>' . $button );
	$input = $dom->getElementsByTagName( 'input' )->item(0);
	$new_button = $dom->createElement( 'button' );
	$new_button->appendChild( $dom->createTextNode( $input->getAttribute( 'value' ) ) );
	$input->removeAttribute( 'value' );
	foreach( $input->attributes as $attribute ) {
		$new_button->setAttribute( $attribute->name, $attribute->value );
	}
	$input->parentNode->replaceChild( $new_button, $input );

	return $dom->saveHtml( $new_button );
}


function add_custom_rewrite_rules() {
    add_rewrite_rule(
        '^jobs-demo-detail-page/([^/]+)?$',
        'index.php?pagename=jobs-demo-detail-page&jobs-demo-detail-page=$matches[1]',
        'top'
    );
}
add_action('init', 'add_custom_rewrite_rules');

function add_custom_query_vars($vars) {
    $vars[] = 'jobs-demo-detail-page';
    return $vars;
}
add_filter('query_vars', 'add_custom_query_vars');

add_action( 'wp_ajax_nopriv_cmma_create_member_modal', 'cmma_create_member_modal' );
add_action( 'wp_ajax_cmma_create_member_modal', 'cmma_create_member_modal' );
function cmma_create_member_modal() {
	$response = [
		'success'	=> true,
	];

	$url = sanitize_text_field($_POST['url']);
	$url = rtrim($url, '/');

	if ($url) {
		$url_parts = explode('/', $url);
		$last_part = end($url_parts);

		$member = get_page_by_path($last_part, OBJECT, 'member' );
		if ($member) {
			ob_start();
			include(get_stylesheet_directory().'/helpers/html/member-modal.php');
			$response['modal_id'] = $member->ID;
			$response['html'] = ob_get_clean();
			$response['modal_title'] = get_the_title($member->ID);
		} else {
			$response['success'] = false;
			$response['msg'] = "Member Not Found.";
		}
	}

	wp_send_json_success( $response );
	wp_die();
}




function cmma_wpseo_metadesc($description) {
	$post_id = get_the_ID();
	if (is_singular('member')){
		$description = get_the_content($post_id);
		if ($description) {
			return cmma_get_first_paragraph_from_html($description);
		}
	}

    if(is_singular(['post','perspective'])){
		return fetch_elementor_description('', $description);
	}

    // Handle different post types and fetch appropriate descriptions.
    if (is_singular(['project', 'service', 'market'])) {
        return fetch_elementor_description('description_columns', $description);
    } elseif (is_singular(['collection', 'page'])) {
        return fetch_elementor_description('short_description', $description);
	}
    // Return the default description if none of the conditions match.
    return $description;
}
add_filter('wpseo_metadesc', 'cmma_wpseo_metadesc');

function fetch_elementor_description($fieldType, $yoastDescription) {
    $post_id = get_the_ID();

	if(!empty($fieldType)){
		// Attempt to get the description from a custom field.
		$description = get_field($fieldType, $post_id);
		if ($description) {
			return cmma_get_first_paragraph_from_html($description);
		}
	}

    // If no custom field description, check the Elementor data.
    $elementor_data = get_post_meta($post_id, '_elementor_data', true);
    $elementor_data = json_decode($elementor_data, true);

    if (is_array($elementor_data) && count($elementor_data)) {
        foreach ($elementor_data as $element) {
            if (isset($element['elements']) && is_array($element['elements'])) {
                foreach ($element['elements'] as $element_item) {
                    $description = cmma_fetch_elementor_widget_description($element_item);
                    if ($description) {
                        return cmma_get_first_paragraph_from_html($description);
                    }
                }
            }
        }
    }

    return $yoastDescription;
}
function cmma_get_first_paragraph_from_html($html) {
    $doc = new DOMDocument();
    @$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

    $xpath = new DOMXPath($doc);
    $paragraphs = $xpath->query('//p');

    // Return the first paragraph's text or fallback to sanitized HTML.
    return $paragraphs->length > 0 ? trim($paragraphs->item(0)->textContent) : sanitize_text_field($html);
}

function cmma_fetch_elementor_widget_description($element) {
    $widgets = [
		'wysiwyg',
		'cmma-accordion',
        'cmma-two-image',
		'image-with-story',
        'cmma-large-image',
        'cmma-single-image',
		'cmma-single-video',
		'cmma-media-gallery',
		'cmma-featured-text',
        'cmma-image-carousel',
		'image-with-deep-dive',
		'cmma-render-to-reality',
    ];

    if (isset($element['widgetType']) && in_array($element['widgetType'], $widgets, true)) {
		// Handle wysiwyg field differently if needed.
		if ($element['widgetType'] === 'wysiwyg' && isset($element['settings']['wysiwyg'])) {
			return $element['settings']['wysiwyg'];
		} elseif (isset($element['settings']['description'])) {
			return $element['settings']['description'];
		}
	}

    return null; // Return null if no description is found.
}

// Force AJAX on all forms from GF Settings
add_filter( 'gform_form_args', 'setup_form_args' );
function setup_form_args( $form_args ) {
    $form_args['ajax'] = true;

    return $form_args;
}

remove_filter('the_title', 'wptexturize');


add_action('template_redirect', 'custom_redirect_people_to_our_people_dynamic');
function custom_redirect_people_to_our_people_dynamic() {
	if (is_user_logged_in()) {
		return;
	}

    $request_uri = $_SERVER['REQUEST_URI'];

	// Redirect /people?* to /people
	if (strpos($request_uri, '/people?') === 0) {
		wp_redirect(home_url('/people'), 301);
		exit;
	}

	// Redirect /?* to /
	if (
		strpos($request_uri, '/?') === 0
		&& strpos($request_uri, 's=') === false
		&& !preg_match('/utm_(medium|content|source)=/', $request_uri)
		|| strpos($request_uri, '/author/') === 0
	) {
		wp_redirect(home_url('/'), 301);
		exit;
	}

	// Redirect /work?* to /work
	if (strpos($request_uri, '/work?') === 0) {
		wp_redirect(home_url('/work'), 301);
		exit;
	}

    // Check if the URL matches the pattern /people/*
    if (preg_match('/^\/people\/(.*)/', $request_uri, $matches)) {
        $dynamic_part = rtrim($matches[1], '/');
        $dynamic_part = explode('/',$dynamic_part)[0];
        $dynamic_part = explode('?',$dynamic_part)[0];
        $redirect_url = site_url().'/our-people/#' . $dynamic_part;
        wp_redirect($redirect_url, 301);
        exit;
    }
}


function _cmma_theme_setup() {
  add_theme_support( 'site-icon' );
}
add_action( 'after_setup_theme', '_cmma_theme_setup' );

// Include Disable Comments class
require_once get_stylesheet_directory() . '/inc/disable-comments.php';
require_once get_stylesheet_directory() . '/inc/security.php';

add_action( 'init', 'set_autosave_interval' );
function set_autosave_interval() {
    if ( ! defined( 'AUTOSAVE_INTERVAL' ) ) {
        define( 'AUTOSAVE_INTERVAL', 86400 );
    }
}


add_filter( 'gform_disable_post_creation', function( $is_disabled, $form, $entry ) {
	return true;
}, 10, 3 );



/*exclude post type from search */
add_action( 'pre_get_posts', function( $query ) {
  if ( $query->is_search() && $query->is_main_query() && ! is_admin() ) {
      $exclude = [ 'quote', 'collection' ];
      $post_types = get_post_types( [ 'exclude_from_search' => false ] ); // all searchable post types
      $allowed = array_diff( $post_types, $exclude );
      $query->set( 'post_type', $allowed );
  }
});

add_action('wp_head', function () {
  ?>
  <!-- Biscred Tag -->
  <script>
    (function(w,d,s,i){
      w.bcpf=w.bcpf||[];
      var b=new Date(),
          f=d.getElementsByTagName(s)[0],
          j=d.createElement(s);
      j.async=true;
      w.bcpf.push({'init':b.getTime(),'account':i});
      j.src='https://pf.biscred.com/'+i+'/bcpf.js?'+b.getDate()+b.getMonth();
      f.parentNode.insertBefore(j,f);
    })(window,document,'script','97ce2ce946');
  </script>
  <!-- End Biscred Tag -->
  <?php
});

add_action('init', function () {

    if (!isset($_GET['gf_force_pdf']) || $_GET['gf_force_pdf'] !== '1') {
        return;
    }

    if (!isset($_GET['post_id'])) {
        wp_die('Invalid request.');
    }

    $post_id = intval($_GET['post_id']);

    // Get ACF file field
    $file = get_field('file_download_sheet_file', $post_id);

    if (!$file) {
        wp_die('Error: File not assigned.');
    }

    // If ACF returns array
    if (is_array($file) && isset($file['ID'])) {
        $file_path = get_attached_file($file['ID']);
    } else {
        // If returns URL
        $file_path = str_replace(home_url('/'), ABSPATH, $file);
    }

    if (!file_exists($file_path)) {
        wp_die('Error: File not found on server.');
    }

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_path));

    if (ob_get_level()) { ob_end_clean(); }

    readfile($file_path);
    exit;
});


add_filter('gform_confirmation_1', 'gf_auto_download_after_submit', 10, 4);
function gf_auto_download_after_submit($confirmation, $form, $entry, $ajax) {
    if (!is_string($confirmation)) {
        return $confirmation;
    }

	$post_id = get_queried_object_id();

	$download_url = add_query_arg(
		array(
			'gf_force_pdf' => '1',
			'post_id'      => $post_id
		),
		home_url('/')
	);

    $confirmation .= "
    <script>
    (function($){
      $(document).on('gform_confirmation_loaded', function(event, formId){
        if(formId != 1) return;

        var storageKey = 'gf_download_lock_" . esc_js($entry['id']) . "';

        // AUTO PDF DOWNLOAD
        if (!sessionStorage.getItem(storageKey)) {
          sessionStorage.setItem(storageKey, 'true');

          setTimeout(function() {
            window.location.assign('?gf_force_pdf=1&post_id=" . $post_id . "');
          }, 1000);
        }

        // AUTO CLOSE MODAL
        var observer = new MutationObserver(function () {
          var closeBtn = document.querySelector('.cmma-modal-close');

          if (closeBtn) {
            setTimeout(function () {
              closeBtn.click();
            }, 3000); // close after 3 seconds

            observer.disconnect();
          }
        });

        observer.observe(document.body, {
          childList: true,
          subtree: true
        });

      });
    })(jQuery);
    </script>
    ";

    return $confirmation;
}
