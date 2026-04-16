<?php

// Block direct access to file
defined( 'ABSPATH' ) or die( 'Not Authorized!' );

class FourTheLocalSchools {

    public function __construct() {

        // Plugin uninstall hook
        register_uninstall_hook( WPS_FILE, array('WordPress_Plugin_Starter', 'plugin_uninstall') );

        // Plugin activation/deactivation hooks
        register_activation_hook( WPS_FILE, array($this, 'plugin_activate') );
        register_deactivation_hook( WPS_FILE, array($this, 'plugin_deactivate') );

        // Plugin Actions
        add_action( 'plugins_loaded', array($this, 'plugin_init') );
        add_action( 'wp_enqueue_scripts', array($this, 'plugin_enqueue_scripts') );
        add_action( 'admin_enqueue_scripts', array($this, 'plugin_enqueue_admin_scripts') );

		//start the session
		add_action('init', array($this, 'forthelocal_start_session'), 1);

		//make post type private
        //add_filter('wp_insert_post_data', array( $this, 'make_booster_selection_type_private'));

		//short code form
        //add_shortcode('subscription-boosters', array( $this, 'subscription_boosters_form') );

	    // Populate select field using filter
	    add_filter('acf/load_field/name=county_name', array($this, 'acf_load_county_field_values'));
	    add_filter('acf/load_field/name=school', array($this, 'acf_load_schools_field_values'));
	    add_filter('acf/load_field/name=booster', array($this, 'acf_load_boosters_field_values'));
	    add_filter('acf/load_field/name=students', array($this, 'acf_load_students_field_values'));

		add_filter('acf/prepare_field/name=user_id', array($this, 'acf_load_user_id_field_value'));  //user_id
		add_filter('acf/prepare_field/name=product_id', array($this, 'acf_load_product_id_field_value'));  //product_id

		//woocommerce
		add_filter('acfe/form/load/post_id/form=members-boosters-signup', array($this, 'forthelocal_validate_cart_and_redirect'), 10, 3);
		add_action('acfe/form/submit/post/form=members-boosters-signup', array($this, 'forthelocal_save_member_boosters_post_into_session'), 10, 5);
		add_action( 'woocommerce_after_checkout_validation', array($this, 'forthelocal_validate_members_booster_post'), 10, 2);
		//add_action('woocommerce_checkout_create_order', array($this, 'before_checkout_create_order'), 20, 2);
		add_action('woocommerce_thankyou', array($this, 'update_members_boosters_post_by_order'), 9999, 1);

	    //ajax callbacks
        add_action('wp_ajax_get_counties_by_state_by_ajax', array( $this, 'get_counties_by_state_by_ajax_callback'));
        add_action('wp_ajax_nopriv_get_counties_by_state_by_ajax', array( $this, 'get_counties_by_state_by_ajax_callback'));

        add_action('wp_ajax_get_schools_by_county_by_ajax', array( $this, 'get_schools_by_county_by_ajax_callback'));
        add_action('wp_ajax_nopriv_get_schools_by_county_by_ajax', array( $this, 'get_schools_by_county_by_ajax_callback'));

        add_action('wp_ajax_get_boosters_by_schools_by_ajax', array( $this, 'get_boosters_by_schools_by_ajax_callback'));
        add_action('wp_ajax_nopriv_get_boosters_by_schools_by_ajax', array( $this, 'get_boosters_by_schools_by_ajax_callback'));

        add_action('wp_ajax_get_students_by_boosters_by_ajax', array( $this, 'get_students_by_boosters_by_ajax_callback'));
        add_action('wp_ajax_nopriv_get_students_by_boosters_by_ajax', array( $this, 'get_students_by_boosters_by_ajax_callback'));
    }

    public static function plugin_uninstall() { }

    /**
     * Plugin activation function
     * called when the plugin is activated
     * @method plugin_activate
     */
    public function plugin_activate() { }

    /**
     * Plugin deactivate function
     * is called during plugin deactivation
     * @method plugin_deactivate
     */
    public function plugin_deactivate() { }

    /**
     * Plugin init function
     * init the polugin textDomain
     * @method plugin_init
     */
    function plugin_init() {
        // before all load plugin text domain
        load_plugin_textDomain( WPS_TEXT_DOMAIN, false, dirname(WPS_DIRECTORY_BASENAME) . '/languages' );
    }


    /**
     * Enqueue the main Plugin admin scripts and styles
     * @method plugin_enqueue_scripts
     */
    function plugin_enqueue_admin_scripts() {
        wp_register_style( 'wps-admin-style', WPS_DIRECTORY_URL . '/assets/dist/css/admin-style.css', array(), null );
        wp_register_script( 'wps-admin-script', WPS_DIRECTORY_URL . '/assets/dist/js/admin-script.js', array(), null, true );
        wp_register_script( 'wps-schools-script', WPS_DIRECTORY_URL . '/assets/dist/js/schools.js', array(), null, true );
        wp_register_script( 'wps-business-script', WPS_DIRECTORY_URL . '/assets/dist/js/business.js', array(), null, true );
        wp_register_script( 'wps-subscription-script', WPS_DIRECTORY_URL . '/assets/dist/js/subscription.js', array(), null, true );

        wp_enqueue_script('jquery');
        wp_enqueue_style('wps-admin-style');
        wp_enqueue_script('wps-admin-script');

		// Localize the script with data for school
		$script_schools_data = array(
		  'ajaxurl' => admin_url( 'admin-ajax.php' ),
		  'security' => wp_create_nonce( '4thelocal_nonce' ),
		);
		wp_localize_script( 'wps-schools-script', 'school_counties', $script_schools_data );

		// Localize the script with data for business
		$script_business_data = array(
		  'ajaxurl' => admin_url( 'admin-ajax.php' ),
		  'security' => wp_create_nonce( '4thelocal_nonce' ),
		);
		wp_localize_script( 'wps-business-script', 'business_counties', $script_business_data );

		// Localize the script with data for subscription
		$script_subscription_counties_data = array(
		  'ajaxurl' => admin_url( 'admin-ajax.php' ),
		  'security' => wp_create_nonce( '4thelocal_nonce' ),
		);
		wp_localize_script( 'wps-subscription-script', 'subscription_counties', $script_subscription_counties_data );

		$script_subscription_schools_data = array(
		  'ajaxurl' => admin_url( 'admin-ajax.php' ),
		  'security' => wp_create_nonce( '4thelocal_nonce' ),
		);
		wp_localize_script( 'wps-subscription-script', 'subscription_schools', $script_subscription_schools_data );

		$script_subscription_boosters_data = array(
		  'ajaxurl' => admin_url( 'admin-ajax.php' ),
		  'security' => wp_create_nonce( '4thelocal_nonce' ),
		);
		wp_localize_script( 'wps-subscription-script', 'subscription_boosters', $script_subscription_boosters_data );

		$script_subscription_students_data = array(
		  'ajaxurl' => admin_url( 'admin-ajax.php' ),
		  'security' => wp_create_nonce( '4thelocal_nonce' ),
		);
		wp_localize_script( 'wps-subscription-script', 'subscription_students', $script_subscription_students_data );

        wp_enqueue_script('wps-schools-script');
        wp_enqueue_script('wps-business-script');
        wp_enqueue_script('wps-subscription-script');
    }

    /**
     * Enqueue the main Plugin user scripts and styles
     * @method plugin_enqueue_scripts
     */
    function plugin_enqueue_scripts() {
        wp_register_style( 'wps-user-style', WPS_DIRECTORY_URL . '/assets/dist/css/user-style.css', array(), null );
        wp_register_script( 'wps-user-script', WPS_DIRECTORY_URL . '/assets/dist/js/user-script.js', array(), null, true );
        wp_register_script( 'wps-schools-script', WPS_DIRECTORY_URL . '/assets/dist/js/schools.js', array(), null, true );
        wp_register_script( 'wps-business-script', WPS_DIRECTORY_URL . '/assets/dist/js/business.js', array(), null, true );
        wp_register_script( 'wps-subscription-script', WPS_DIRECTORY_URL . '/assets/dist/js/subscription.js', array(), null, true );

        wp_enqueue_script('jquery');
        wp_enqueue_style('wps-user-style');
        wp_enqueue_script('wps-user-script');

		// Localize the script with data for school
		$script_schools_data = array(
		  'ajaxurl' => admin_url( 'admin-ajax.php' ),
		  'security' => wp_create_nonce( '4thelocal_nonce' ),
		);
		wp_localize_script( 'wps-schools-script', 'school_counties', $script_schools_data );

		// Localize the script with data for business
		$script_business_data = array(
		  'ajaxurl' => admin_url( 'admin-ajax.php' ),
		  'security' => wp_create_nonce( '4thelocal_nonce' ),
		);
		wp_localize_script( 'wps-business-script', 'business_counties', $script_business_data );

		// Localize the script with data for subscription
		$script_subscription_counties_data = array(
		  'ajaxurl' => admin_url( 'admin-ajax.php' ),
		  'security' => wp_create_nonce( '4thelocal_nonce' ),
		);
		wp_localize_script( 'wps-subscription-script', 'subscription_counties', $script_subscription_counties_data );

		$script_subscription_schools_data = array(
		  'ajaxurl' => admin_url( 'admin-ajax.php' ),
		  'security' => wp_create_nonce( '4thelocal_nonce' ),
		);
		wp_localize_script( 'wps-subscription-script', 'subscription_schools', $script_subscription_schools_data );

		$script_subscription_boosters_data = array(
		  'ajaxurl' => admin_url( 'admin-ajax.php' ),
		  'security' => wp_create_nonce( '4thelocal_nonce' ),
		);
		wp_localize_script( 'wps-subscription-script', 'subscription_boosters', $script_subscription_boosters_data );

		$script_subscription_students_data = array(
		  'ajaxurl' => admin_url( 'admin-ajax.php' ),
		  'security' => wp_create_nonce( '4thelocal_nonce' ),
		);
		wp_localize_script( 'wps-subscription-script', 'subscription_students', $script_subscription_students_data );

        wp_enqueue_script('wps-schools-script');
        wp_enqueue_script('wps-business-script');
        wp_enqueue_script('wps-subscription-script');
    }

	/**
	 * Start the session
	*/
	function forthelocal_start_session() {
	  if(!session_id()) {
		session_start();
	  }
	}


    /**
     * Make booster_selection post type private
     * @method plugin_settings_page
     */
    function make_booster_selection_type_private($post) {
      if ($post['post_type'] == 'members_boosters') {
        $post['post_status'] = 'private';
      }
      return $post;
    }


    /**
     * Load the counties based on state in the School and Businesses
     * and Members Boosters post type
    */
	function acf_load_county_field_values( $field ) {
	  $post_type = get_post_type();
	  if(!in_array($post_type, array('schools', 'businesses', 'members_boosters'))) {
		return $field;
	  }

	  // reset choices
	  $field['choices'] = array();

	  $selected_state = get_field('state');

	  // Get field from options page
	  $states_and_counties = get_field('field_6070b5e09b95e', 'options');

	  // Simplify array to look like: state => counties
	  $counties = array();
	  foreach ($states_and_counties as $key => $value) {
		$counties[$value['state_code']] = $value['country_names'];
	  }

	  // Returns counties by State selected
	  $choices = array();
	  if (array_key_exists( $selected_state, $counties)) {
		$choices = $counties[$selected_state];
		$choices = explode(',', $choices);
	  }

	  // remove any unwanted white space
	  $choices = array_map('trim', $choices);

	  // loop through array and add to field 'choices'
	  if( is_array($choices) ) {
		foreach( $choices as $choice ) {
		  $field['choices'][ $choice ] = $choice;
		}
	  }

	  // return the field
	  return $field;
	}

    /**
     * Load the schools based on post id in the Members Boosters post type
    */
	function acf_load_schools_field_values( $field ) {
	  $post_type = get_post_type();
	  if(!in_array($post_type, array('members_boosters'))) {
		return $field;
	  }

	  // reset choices
	  $field['choices'] = array();

	  $post_id = get_the_ID();
	  $school_name = get_post_meta($post_id, 'school', true );

	  $schools = array();
	  $schools[] = $school_name;

	  // remove any unwanted white space
	  $choices = array_map('trim', $schools);

	  // loop through array and add to field 'choices'
	  if( is_array($choices) ) {
		foreach( $choices as $choice ) {
		  $field['choices'][ $choice ] = $choice;
		}
	  }

	  // return the field
	  return $field;
	}


    /**
     * Load the boosters based on post id in the Members Boosters post type
    */
	function acf_load_boosters_field_values( $field ) {
	  $post_type = get_post_type();
	  if(!in_array($post_type, array('members_boosters'))) {
		return $field;
	  }

	  // reset choices
	  $field['choices'] = array();

	  $post_id = get_the_ID();
	  $booster_names = get_post_meta($post_id, 'booster', false );
	  if(empty($booster_names)) {
	    $booster_names = array();
	  }
	  else {
	    $booster_names = $booster_names[0];
	  }
	  $booster_names = array_filter($booster_names);

	  // loop through array and add to field 'choices'
	  if( is_array($booster_names) ) {
		foreach( $booster_names as $choice ) {
		  if(!empty($choice)) {
		    $field['choices'][ $choice ] = $choice;
		  }
		}
	  }

	  // return the field
	  return $field;
	}


    /**
     * Load the students based on post id in the Members Boosters post type
    */
	function acf_load_students_field_values( $field ) {
	  $post_type = get_post_type();
	  if(!in_array($post_type, array('members_boosters'))) {
		return $field;
	  }

	  // reset choices
	  $field['choices'] = array();

	  $post_id = get_the_ID();
	  $student_names = get_post_meta($post_id, 'students', false );
	  if(empty($student_names)) {
	    $student_names = array();
	  }
	  else {
	    $student_names = $student_names[0];
	  }
	  $student_names = array_filter($student_names);

	  // loop through array and add to field 'choices'
	  if( is_array($student_names) ) {
		foreach( $student_names as $choice ) {
		  $field['choices'][ $choice ] = $choice;
		}
	  }

	  // return the field
	  return $field;
	}

	/**
	 * Load user id in member's booster selection form
	*/
	function acf_load_user_id_field_value( $field ) {
	  $post_type = get_post_type();

	  if(!in_array($post_type, array('page'))) {
		return $field;
	  }

	  $field['value'] = get_current_user_id();

	  return $field;
	}

	/**
	 * Load product id in member's booster selection form
	*/
	function acf_load_product_id_field_value( $field ) {
	  $post_type = get_post_type();

	  if(!in_array($post_type, array('page'))) {
		return $field;
	  }

      global $woocommerce;

      $cart_items = $woocommerce->cart->get_cart();
      //print_r($cart_items);
      $product_ids = '';
      foreach($cart_items as $cart) {
        if(!empty($product_ids)) $product_ids .= ',';
        $product_ids .= $cart['product_id'];
      }
	  $field['value'] = $product_ids;

	  return $field;
	}


	// Return counties by state
	function get_counties_by_state_by_ajax_callback( ) {
	  // Verify nonce
	  if( !isset( $_REQUEST['security'] ) || !wp_verify_nonce( $_REQUEST['security'], '4thelocal_nonce' ) )
		die('Permission denied');

	  // Get state var
	  $selected_state = $_REQUEST['state'];

	  // Get field from options page
	  $states_and_counties = get_field('field_6070b5e09b95e', 'options');

	  // Simplify array to look like: state => counties
	  $counties = array();
	  foreach ($states_and_counties as $key => $value) {
		$counties[$value['state_code']] = $value['country_names'];
	  }

	  // Returns counties by State selected
	  if (array_key_exists( $selected_state, $counties)) {
		$arr_data = $counties[$selected_state];
		return wp_send_json_success($arr_data);
	  }
	  else {

		$arr_data = array();
		return wp_send_json_success($arr_data);
	  }
	  die();
	}

	// Return schools by state and county
	function get_schools_by_county_by_ajax_callback( ) {
	  // Verify nonce
	  if( !isset( $_REQUEST['security'] ) || !wp_verify_nonce( $_REQUEST['security'], '4thelocal_nonce' ) )
		die('Permission denied');

	  // Get state var
	  $selected_state = $_REQUEST['state'];
	  $selected_county = $_REQUEST['county'];


	  $args = array(
		'numberposts'	=> -1,
		'post_type'		=> 'schools',
    	'post_status' => array('publish'),
		'meta_query'	=> array(
			'relation'		=> 'AND',
			array(
				'key'		=> 'state',
				'compare'	=> '=',
				'value'		=> $selected_state,
			),
			array(
				'key'		=> 'county_name',
				'compare'	=> '=',
				'value'		=> $selected_county,
			)
		)
	  );

	  // query
	  $the_query = new WP_Query( $args );

	  $schools = array();
      while ($the_query->have_posts()) : $the_query->the_post();
    	$school_name =  get_the_title();
	    $schools[] = $school_name;
	  endwhile;

	  //make it unique
	  $schools = array_unique($schools);

	  // Returns Area by Country selected if selected country exists in array
	  if (!empty($schools)) {

		// Convert schools to array
		return wp_send_json_success($schools);
	  }
	  else {
		$arr_data = array();
		return wp_send_json_success($arr_data);
	  }
	  die();
	}


	// Return boosters, by school, state and county
	function get_boosters_by_schools_by_ajax_callback( ) {
	  // Verify nonce
	  if( !isset( $_REQUEST['security'] ) || !wp_verify_nonce( $_REQUEST['security'], '4thelocal_nonce' ) )
		die('Permission denied');

	  // Get state var
	  $selected_state = $_REQUEST['state'];
	  $selected_county = $_REQUEST['county'];
	  $selected_school = $_REQUEST['school'];

	  $args = array(
		'numberposts'	=> -1,
		'post_type'		=> 'schools',
    	'post_status' => array('publish'),
		's'		=> $selected_school,
		'meta_query'	=> array(
			'relation'		=> 'AND',
			array(
				'key'		=> 'state',
				'compare'	=> '=',
				'value'		=> $selected_state,
			),
			array(
				'key'		=> 'county_name',
				'compare'	=> '=',
				'value'		=> $selected_county,
			)
		)
	  );

	  // query
	  $the_query = new WP_Query( $args );

	  $boosters = array();
      while ($the_query->have_posts()) : $the_query->the_post();
		$booster_name = get_field( 'booster' );
	    $boosters[] = $booster_name;
	  endwhile;

	  // Returns boosters
	  if (!empty($boosters)) {
		return wp_send_json_success($boosters);
	  }
	  else {
		$arr_data = array();
		return wp_send_json_success($arr_data);
	  }
	  die();
	}


	/**
	 * Return students, by boosters, school, state and county
	 */
	function get_students_by_boosters_by_ajax_callback( ) {
	  // Verify nonce
	  if( !isset( $_REQUEST['security'] ) || !wp_verify_nonce( $_REQUEST['security'], '4thelocal_nonce' ) )
		die('Permission denied');

	  // Get state var
	  $selected_state = $_REQUEST['state'];
	  $selected_county = $_REQUEST['county'];
	  $selected_school = $_REQUEST['school'];
	  $selected_boosters = $_REQUEST['boosters'];
	  $boosters_arr = explode('||',$selected_boosters);

	  $args = array(
		'numberposts'	=> -1,
		'post_type'		=> 'schools',
    	'post_status' => array('publish'),
		's'		=> $selected_school,
		'meta_query'	=> array(
			'relation'		=> 'AND',
			array(
				'key'		=> 'state',
				'compare'	=> '=',
				'value'		=> $selected_state,
			),
			array(
				'key'		=> 'county_name',
				'compare'	=> '=',
				'value'		=> $selected_county,
			),
			array(
				'key'		=> 'booster',
				'compare'	=> 'IN',
				'value'		=> $boosters_arr,
			)
		)
	  );

	  // query
	  $the_query = new WP_Query( $args );

	  $students = array();
      while ($the_query->have_posts()) : $the_query->the_post();

		$rows = get_field('students');
		if( $rows ) {
		  foreach( $rows as $row ) {
			$first_name = $row['first_name'];
			$last_name = $row['last_name'];
			$student_name = $first_name.' '.$last_name;
	        $students[] = $student_name;
		  }
		}

	  endwhile;

	  // Returns students
	  if (!empty($students)) {
		return wp_send_json_success($students);
	  }
	  else {
		$arr_data = array();
		return wp_send_json_success($arr_data);
	  }
	  die();
	}

	/**
	 * WooCommerce Max 1 Product @ Cart
	 */
	function forthelocal_only_one_in_cart( $passed, $added_product_id ) {
	  wc_empty_cart();
	  return $passed;
	}

	/**
	 * WooCommerce Max 1 Qty Product @ Cart
	 */
	function forthelocal_woocom_add_to_cart( $cart_item_data ) {
	  global $woocommerce;
	  $woocommerce->cart->empty_cart();

	  // Do nothing with the data and return
	  return $cart_item_data;
	}


	/*
	 * @int     $post_id  Post ID used as source
	 * @array   $form     The form settings
	 * @string  $action   The action alias name
	 */
	function forthelocal_validate_cart_and_redirect($post_id, $form, $action){
	  //validate if product is added in cart, if not redirect to front page
	  if(!isset($_SESSION['member_boosters_post']) || (isset($_SESSION['member_boosters_post']) && empty($_SESSION['member_boosters_post'])) ) {
	    wp_redirect( home_url(), 301);
	  }

	  return $post_id;
	}


	/*
	 * @int     $post_id  The targeted post ID
	 * @string  $type     Action type: 'insert_post' or 'update_post'
	 * @array   $args     The generated post arguments
	 * @array   $form     The form settings
	 * @string  $action   The action alias name
	 *
	 * Note: At this point the post & meta fields are already saved in the database
	 */
	function forthelocal_save_member_boosters_post_into_session($post_id, $type, $args, $form, $action){
	  //save the post id into session
	  $_SESSION['member_boosters_post'] = $post_id;
	}


	/**
	 * Validate if any booster is selected before checkout
	 */
	function forthelocal_validate_members_booster_post( $fields, $errors ){
	  if(!isset($_SESSION['member_boosters_post']) || (isset($_SESSION['member_boosters_post']) && empty($_SESSION['member_boosters_post'])) ) {
		$errors->add('validation', 'No boosters is selected.' );
	  }
	}

	/**
	 * Add the members_boosters post id to order meta data
	*/
	function before_checkout_create_order( $order, $data ) {
	  if(isset($_SESSION['member_boosters_post']) && !empty($_SESSION['member_boosters_post'])) {
	    $order->update_meta_data( 'member_boosters_post', $_SESSION['member_boosters_post'] );
	    unset($_SESSION['member_boosters_post']);
	  }
	}

	/**
	 * After payment successful, update the members_boosters post with the
	 * woocommerce order id
	*/
	function update_members_boosters_post_by_order($order_id) {
	  if ( ! $order_id ) return;

	  // Allow code execution only once
	  if( !get_post_meta( $order_id, '_thankyou_action_done', true ) ) {
	    $order = new WC_Order( $order_id );
	    $user_id = (int)$order->user_id;
        $order_number = $order->get_order_number();

	    $product_ids = '';
		foreach ( $order->get_items() as $item_id => $item ) {
		  $product = $item->get_product();
		  if(!empty($product_ids)) $product_ids .= ',';
		  $product_ids .= $product->get_id();
		}

	    //get the meta data which is added after saving members_boosters front end form
	    $members_booster_post_id = isset($_SESSION['member_boosters_post']) && !empty($_SESSION['member_boosters_post']) ? $_SESSION['member_boosters_post'] : 0;

	    //now update the post by order id and number

	    //fieldset : Admin Order Info
	    update_field( 'field_60744c3b18471', $order_id, $members_booster_post_id); //order_id
	    update_field( 'field_60775abff06f0', $order_number, $members_booster_post_id); //order_number
	    update_field( 'field_607733d50d3f5', $user_id, $members_booster_post_id); //user_id
	    update_field( 'field_60773248ae63b', $product_ids, $members_booster_post_id); //product_ids
	    update_field( 'field_60744c4c18472', 1, $members_booster_post_id); //paid

	    //fieldset : Members Boosters | hidden fields
	    update_field( 'field_60744de3070ca', $order_id, $members_booster_post_id); //order_id
	    update_field( 'field_60775b0ea1322', $order_number, $members_booster_post_id); //order_number
	    update_field( 'field_60744e0f070cd', 1, $members_booster_post_id); //paid

	    //remove session variable
	    unset($_SESSION['member_boosters_post']);

	    // Flag the action as done to avoid repetitions on reload
	    $order->update_meta_data( '_thankyou_action_done', true );
	    $order->save();
	  }
	}

}

new FourTheLocalSchools;
