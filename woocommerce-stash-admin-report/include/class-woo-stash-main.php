<?php

// Block direct access to file
defined( 'ABSPATH' ) or die( 'Not Authorized!' );

class Woo_Stash_Main {

    public function __construct() {

        // Plugin uninstall hook
        register_uninstall_hook( WOS_FILE, array('Woo_Stash_Main', 'plugin_uninstall') );

        // Plugin activation/deactivation hooks
        register_activation_hook( WOS_FILE, array($this, 'plugin_activate') );
        register_deactivation_hook( WOS_FILE, array($this, 'plugin_deactivate') );

		// Load dependencies
        add_action( 'plugins_loaded', array($this, 'load_dependencies') );

        // Plugin Actions
        add_action( 'plugins_loaded', array($this, 'plugin_init') );
        add_action( 'wp_enqueue_scripts', array($this, 'plugin_enqueue_scripts') );
        add_action( 'admin_enqueue_scripts', array($this, 'plugin_enqueue_admin_scripts') );

		// Display stash stock fields
		add_action('woocommerce_product_options_inventory_product_data', array($this, 'woo_stash_product_stock_fields') );

		// Save stash stock fields
		add_action('woocommerce_process_product_meta', array($this, 'woo_stash_product_stock_fields_save') );

		// Add settings to woocommerce
		add_filter('woocommerce_general_settings', array($this, 'woo_stash_general_settings_for_stashes'));

		add_filter( 'woocommerce_get_sections_products' , array($this, 'woo_stash_zipcode_settings_tab') );
		add_filter( 'woocommerce_get_settings_products' , array($this, 'woo_stash_get_settings') , 10, 2 );

		// Postcode validation
		add_action( 'woocommerce_after_checkout_validation', array($this, 'woo_stash_after_checkout_validation'), 10, 2);
		add_action( 'woocommerce_checkout_update_order_meta', array($this, 'woo_stash_update_order_meta'), 10, 2);

		// Disable defaul woocommerce stock update
		add_filter( 'woocommerce_can_reduce_order_stock', array($this, '__return_false'));

		// Update stash stock manually based on postcode
		add_filter( 'manage_edit-product_columns', array($this, 'woo_stash_product_column'), 11);
		add_action( 'manage_product_posts_custom_column', array($this, 'woo_stash_product_list_column_content'), 10, 2 );
		add_filter( 'woocommerce_get_availability', array($this, 'woo_stash_get_availability'), 1, 2);
		add_filter( 'woocommerce_is_purchasable', array($this, 'woo_stash_is_product_purchasable'), 10, 2 );
		add_filter( 'woocommerce_short_description', array($this, 'woo_stash_display_out_of_stock_text'), 20, 1 );

		//add_action( 'woocommerce_order_status_pending', array( $this, 'woo_stash_reduce_stock_levels'));
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'woo_stash_reduce_stock_levels'), 10, 3 );
		add_action( 'woocommerce_order_status_cancelled', array( $this, 'woo_stash_increase_stock_levels'));
		add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'woo_stash_display_order_stash_location') );

		// line item
		add_filter( 'woocommerce_order_item_get_formatted_meta_data', array( $this, 'woo_stash_order_item_get_formatted_meta_data'), 10, 1 );

		// rest api
		add_action('rest_api_init', array( $this, 'woo_stash_rest_api_expose_product_meta_fields'));
		add_filter('woocommerce_rest_prepare_product_object', array( $this, 'woo_stash_change_data_to_product'), 10, 3 );
		add_action('rest_api_init', array( $this, 'woo_stash_rest_api_expose_order_meta_fields'));
		add_action( 'rest_api_init', array($this, 'woo_stash_rest_filter_add_filters_for_order') );

		// adds the warehouse filtering dropdown to the orders page
		add_action( 'restrict_manage_posts', array( $this, 'woo_stash_filter_orders_by_warehouse' ) );
		add_filter( 'posts_join',  array( $this, 'woo_stash_add_order_items_join' ) );
		add_filter( 'posts_where', array( $this, 'woo_stash_add_filterable_where' ) );

		// add warehouse column to orders page
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'woo_stash_add_order_warehouse_column_content') );
		add_filter( 'manage_edit-shop_order_columns', array( $this, 'woo_stash_add_order_warehouse_column_header'), 20 );

		// Reports
		add_action('admin_menu', array($this, 'woo_stash_register_low_stock_menu'), 20);
		add_action( 'admin_post_stash_export', array($this, 'woo_stash_generate_stock_csv') );

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
        load_plugin_textDomain( WOS_TEXT_DOMAIN, false, dirname(WOS_DIRECTORY_BASENAME) . '/languages' );
    }

	/**
	 * Load the required dependencies for this plugin.
	 */
	function load_dependencies() {

		/**
		 * The class responsible for warehouse low stock report
		 */
		require_once WOS_DIRECTORY . '/include/class-woo-stash-low-stock-products.php';
	}

    /**
     * Enqueue the main Plugin admin scripts and styles
     * @method plugin_enqueue_scripts
     */
    function plugin_enqueue_admin_scripts() {
        wp_register_style( 'wos-admin-style', WOS_DIRECTORY_URL . '/assets/dist/css/admin-style.css', array(), null );
        wp_register_script( 'wos-admin-script', WOS_DIRECTORY_URL . '/assets/dist/js/admin-script.js', array('jquery'), null, true );
        wp_enqueue_script('jquery');
        wp_enqueue_style('wos-admin-style');
        wp_enqueue_script('wos-admin-script');
    }

    /**
     * Enqueue the main Plugin user scripts and styles
     * @method plugin_enqueue_scripts
     */
    function plugin_enqueue_scripts() {
        wp_register_style( 'wos-user-style', WOS_DIRECTORY_URL . '/assets/dist/css/user-style.css', array(), null );
        wp_register_script( 'wos-user-script', WOS_DIRECTORY_URL . '/assets/dist/js/user-script.js', array('jquery'), null, true );
        wp_enqueue_script('jquery');
        wp_enqueue_style('wos-user-style');
        wp_enqueue_script('wos-user-script');
    }

    /**
     * Display stock fields to product page
     * @method woo_stash_product_stock_fields
     */
	public function woo_stash_product_stock_fields() {
        global $woocommerce, $post, $product_object;

        $stash_product_stock_2_value = $product_object->get_meta( 'stash_product_stock_2', true );
        $stash_product_stock_3_value = $product_object->get_meta( 'stash_product_stock_3', true );

        if ( empty($stash_product_stock_2_value) ) $stash_product_stock_2_value = 0;
        if ( empty($stash_product_stock_3_value) ) $stash_product_stock_3_value = 0;

		echo '<div class="product_custom_field">';
		// Stash 2 Stock
		woocommerce_wp_text_input(
			array(
				'id' => 'stash_product_stock_2',
				'placeholder' => 'Enter the stock for stash 2',
				'label' => __('Stash 2 Stock', 'woocommerce'),
				'type' => 'number',
				'desc_tip' => 'true',
				'custom_attributes' => array(
					'step' => 'any',
					'min' => '0'
				),
                'value' => $stash_product_stock_2_value
			)
		);

		// Stash 3 Stock
		woocommerce_wp_text_input(
			array(
				'id' => 'stash_product_stock_3',
				'placeholder' => 'Enter the stock for stash 3',
				'label' => __('Stash 3 Stock', 'woocommerce'),
				'type' => 'number',
				'desc_tip' => 'true',
				'custom_attributes' => array(
					'step' => 'any',
					'min' => '0'
				),
                'value' => $stash_product_stock_3_value
			)
		);

		echo '</div>';
	}

	// Save the product stock settings
	public function woo_stash_product_stock_fields_save($post_id) {
        // Custom Product Text Field
        if (array_key_exists('stash_product_stock_2', $_POST)) {
            update_post_meta($post_id, 'stash_product_stock_2', intval($_POST['stash_product_stock_2']));
        }
        if (array_key_exists('stash_product_stock_3', $_POST)) {
            update_post_meta($post_id, 'stash_product_stock_3', intval($_POST['stash_product_stock_3']));
        }
	}

	function woo_stash_general_settings_for_stashes($settings) {
		$key = 0;

		foreach( $settings as $values ){
			if($values['id'] == 'store_address' && $values['type'] == 'title'){
				$values['title'] = __( 'Stash #1 Store Address', 'woocommerce' );
				$values['desc'] = __( 'This is where Stash #1 business is located. Tax rates and shipping rates will use this address.', 'woocommerce' );
			}

			$new_settings[$key] = $values;
			$key++;

			if($values['id'] == 'woocommerce_store_postcode'){
				$new_settings[$key] = array(
					'title'    => __('Latitude'),
					'desc'     => __('Stash latitude'),
					'id'       => 'woocommerce_store_lat_stash_1',
					'default'  => '',
					'type'     => 'text',
					'desc_tip' => true,
				);
				$key++;

				$new_settings[$key] = array(
					'title'    => __('Longitude'),
					'desc'     => __('Stash longitude'),
					'id'       => 'woocommerce_store_lng_stash_1',
					'default'  => '',
					'type'     => 'text',
					'desc_tip' => true,
				);
				$key++;
			}

			if($values['id'] == 'store_address' && $values['type'] == 'sectionend'){
				$new_settings[$key] = array(
					'title' => __( 'Stash 2 Store Address', 'woocommerce' ),
					'type'  => 'title',
					'desc'  => __( 'This is where Stash #2 business is located.', 'woocommerce' ),
					'id'       => 'store_address_stash_2',
				);
				$key++;

				$new_settings[$key] = array(
					'title'    => __( 'Address line 1', 'woocommerce' ),
					'desc'     => __( 'The street address for Stash #2 business location.', 'woocommerce' ),
					'id'       => 'woocommerce_store_address_1_stash_2',
					'default'  => '',
					'type'     => 'text',
					'desc_tip' => true,
				);
				$key++;

				$new_settings[$key] = array(
					'title'    => __( 'Address line 2', 'woocommerce' ),
					'desc'     => __( 'An additional, optional address line for Stash #2 business location.', 'woocommerce' ),
					'id'       => 'woocommerce_store_address_2_stash_2',
					'default'  => '',
					'type'     => 'text',
					'desc_tip' => true,
				);
				$key++;

				$new_settings[$key] = array(
					'title'    => __( 'City', 'woocommerce' ),
					'desc'     => __( 'The city in which Stash #2 business is located.', 'woocommerce' ),
					'id'       => 'woocommerce_store_city_stash_2',
					'default'  => '',
					'type'     => 'text',
					'desc_tip' => true,
				);
				$key++;

				$new_settings[$key] = array(
					'title'    => __( 'Country / State', 'woocommerce' ),
					'desc'     => __( 'The country and state or province, if any, in which Stash #2 business is located.', 'woocommerce' ),
					'id'       => 'woocommerce_country_stash_2',
					'default'  => 'US:CA',
					'type'     => 'single_select_country',
					'desc_tip' => true,
				);
				$key++;

				$new_settings[$key] = array(
					'title'    => __( 'Postcode / ZIP', 'woocommerce' ),
					'desc'     => __( 'The postal code, if any, in which Stash #2 business is located.', 'woocommerce' ),
					'id'       => 'woocommerce_store_postcode_stash_2',
					'css'      => 'min-width:50px;',
					'default'  => '',
					'type'     => 'text',
					'desc_tip' => true,
				);
				$key++;

				$new_settings[$key] = array(
					'title'    => __('Latitude'),
					'desc'     => __('Stash latitude'),
					'id'       => 'woocommerce_store_lat_stash_2',
					'default'  => '',
					'type'     => 'text',
					'desc_tip' => true,
				);
				$key++;

				$new_settings[$key] = array(
					'title'    => __('Longitude'),
					'desc'     => __('Stash longitude'),
					'id'       => 'woocommerce_store_lng_stash_2',
					'default'  => '',
					'type'     => 'text',
					'desc_tip' => true,
				);
				$key++;

				$new_settings[$key] = array(
					'type' => 'sectionend',
					'id'   => 'store_address_stash_2',
				);
				$key++;


				$new_settings[$key] = array(
					'title' => __( 'Stash 3 Store Address', 'woocommerce' ),
					'type'  => 'title',
					'desc'  => __( 'This is where Stash #3 business is located.', 'woocommerce' ),
					'id'       => 'store_address_stash_3',
				);
				$key++;

				$new_settings[$key] = array(
					'title'    => __( 'Address line 1', 'woocommerce' ),
					'desc'     => __( 'The street address for Stash #3 business location.', 'woocommerce' ),
					'id'       => 'woocommerce_store_address_1_stash_3',
					'default'  => '',
					'type'     => 'text',
					'desc_tip' => true,
				);
				$key++;

				$new_settings[$key] = array(
					'title'    => __( 'Address line 2', 'woocommerce' ),
					'desc'     => __( 'An additional, optional address line for Stash #3 business location.', 'woocommerce' ),
					'id'       => 'woocommerce_store_address_2_stash_3',
					'default'  => '',
					'type'     => 'text',
					'desc_tip' => true,
				);
				$key++;

				$new_settings[$key] = array(
					'title'    => __( 'City', 'woocommerce' ),
					'desc'     => __( 'The city in which Stash #3 business is located.', 'woocommerce' ),
					'id'       => 'woocommerce_store_city_stash_3',
					'default'  => '',
					'type'     => 'text',
					'desc_tip' => true,
				);
				$key++;

				$new_settings[$key] = array(
					'title'    => __( 'Country / State', 'woocommerce' ),
					'desc'     => __( 'The country and state or province, if any, in which Stash #2 business is located.', 'woocommerce' ),
					'id'       => 'woocommerce_country_stash_3',
					'default'  => 'US:CA',
					'type'     => 'single_select_country',
					'desc_tip' => true,
				);
				$key++;

				$new_settings[$key] = array(
					'title'    => __( 'Postcode / ZIP', 'woocommerce' ),
					'desc'     => __( 'The postal code, if any, in which Stash #3 business is located.', 'woocommerce' ),
					'id'       => 'woocommerce_store_postcode_stash_3',
					'css'      => 'min-width:50px;',
					'default'  => '',
					'type'     => 'text',
					'desc_tip' => true,
				);
				$key++;

				$new_settings[$key] = array(
					'title'    => __('Latitude'),
					'desc'     => __('Stash latitude'),
					'id'       => 'woocommerce_store_lat_stash_3',
					'default'  => '',
					'type'     => 'text',
					'desc_tip' => true,
				);
				$key++;

				$new_settings[$key] = array(
					'title'    => __('Longitude'),
					'desc'     => __('Stash longitude'),
					'id'       => 'woocommerce_store_lng_stash_3',
					'default'  => '',
					'type'     => 'text',
					'desc_tip' => true,
				);
				$key++;

				$new_settings[$key] = array(
					'type' => 'sectionend',
					'id'   => 'store_address_stash_3',
				);
				$key++;
			}
		}
		return $new_settings;
	}

    /*
     * Add a new settings tab to the WooCommerce settings tabs array.
     *
     * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
     * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
     */
	public function woo_stash_stash_address_settings_tab( $settings_tab ){
		 $settings_tab['woo_stash_address'] = __( 'Stash Addresses' );
		 return $settings_tab;
	}

    /*
     * Add a new settings tab to the WooCommerce settings tabs array.
     *
     * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
     * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
     */
	public function woo_stash_zipcode_settings_tab( $settings_tab ){
		 $settings_tab['woo_stash_zipcodes'] = __( 'Stash Zipcodes' );
		 return $settings_tab;
	}

    /*
     * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
     *
     * @uses woocommerce_update_options()
     * @uses get_settings()
     */
	public function woo_stash_get_settings( $settings, $current_section ) {
		 $custom_settings = array();
		 if( 'woo_stash_zipcodes' == $current_section ) {

			$custom_settings =  array(
				'stash_zipcode_notice' => array(
					'name' => __( 'Stash Zipcodes' ),
					'type' => 'title',
					'desc' => __( 'Enter the zipcodes for each stash. For multiple zipcode enter with comma separated.' ),
					'id'   => 'stash_zipcode_notice'
				),
				'stash_1_zipcodes' => array(
					'name' => __( 'Stash 1 Zipcodes', 'woo-stash' ),
					'type' => 'textarea',
					'desc' => __( 'This is default stash. Please enter the zipcodes with comma separated.', 'woo-stash' ),
					'id'   => 'wc_settings_tab_demo_stash_1_zipcodes',
					'css'      => 'min-height:120px;',
				),
				'stash_2_zipcodes' => array(
					'name' => __( 'Stash 2 Zipcodes', 'woo-stash' ),
					'type' => 'textarea',
					'desc' => __( 'Please enter the zipcodes with comma separated.', 'woo-stash' ),
					'id'   => 'wc_settings_tab_demo_stash_2_zipcodes',
					'css'      => 'min-height:120px;',
				),
				'stash_3_zipcodes' => array(
					'name' => __( 'Stash 3 Zipcodes', 'woo-stash' ),
					'type' => 'textarea',
					'desc' => __( 'Please enter the zipcodes with comma separated.', 'woo-stash' ),
					'id'   => 'wc_settings_tab_demo_stash_3_zipcodes',
					'css'      => 'min-height:120px;',
				),
			 	'stash_zipcode_notice_end' => array( 'type' => 'sectionend', 'id' => 'stash_zipcode_notice' ),

			);

		   return $custom_settings;
	   }
	   else {
			return $settings;
	   }
	}

	/**
	 * Validate if postcode is selected before checkout
	 */
	public function woo_stash_after_checkout_validation( $fields, $errors ){
      global $woocommerce;

      if(empty($woocommerce->customer->get_shipping_postcode()) || empty($fields['shipping_postcode'])) {
	    $errors->add( 'validation', 'Delivery postcode is empty!' );
	    return;
      }

	  // Get the postcodes
	  $store_postcode_stash_1 = get_option('woocommerce_store_postcode');
	  $store_postcode_stash_2 = get_option('woocommerce_store_postcode_stash_2');
	  $store_postcode_stash_3 = get_option('woocommerce_store_postcode_stash_3');

	  $cart_content = WC()->cart->cart_contents;
	  $qty = 0;
	  if(is_array($cart_content)){
		foreach($cart_content as $cart_item){
			$product_id = $cart_item['product_id'];
			$qty = $cart_item['quantity'];
			$product = wc_get_product($product_id);

            $stash_product_stock_1_value = $product->get_stock_quantity();
        	if ( empty($stash_product_stock_1_value) ) $stash_product_stock_1_value = 0;

			$stash_product_stock_2_value = $product->get_meta( 'stash_product_stock_2', true );
        	if ( empty($stash_product_stock_2_value) ) $stash_product_stock_2_value = 0;

			$stash_product_stock_3_value = $product->get_meta( 'stash_product_stock_3', true );
        	if ( empty($stash_product_stock_3_value) ) $stash_product_stock_3_value = 0;

			if ($fields['shipping_postcode'] == $store_postcode_stash_1) {
				if($qty > $stash_product_stock_1_value) {
	    	  	  $errors->add( 'validation', 'Sorry! The product '.$product->get_name().' is not available in the nearest Stash.' );
				}
			}
			elseif ($fields['shipping_postcode'] == $store_postcode_stash_2) {
				if($qty > $stash_product_stock_2_value) {
	    	  	  $errors->add( 'validation', 'Sorry! The product '.$product->get_name().' is not available in the nearest Stash.' );
				}
			}
			elseif ($fields['shipping_postcode'] == $store_postcode_stash_3) {
				if($qty > $stash_product_stock_3_value) {
	    	  	  $errors->add( 'validation', 'Sorry! The product '.$product->get_name().' is not available in the nearest Stash.' );
				}
			}
			else {
	    	  $errors->add( 'validation', 'We can not deliver to the postcode you have selected.' );
			}
		}
	  }
	}

	/**
	 * Add postcode to order meta data
	 */
	function woo_stash_update_order_meta($order_id) {
      global $woocommerce;

      $location_postcode = $woocommerce->customer->get_shipping_postcode();

	  $stash_1_zipcodes = get_option( 'wc_settings_tab_demo_stash_1_zipcodes', true );
	  if(empty($stash_1_zipcodes)) {
		$stash_1_zipcodes_arr = array();
	  }
	  else {
		$stash_1_zipcodes_arr = array_map('trim', explode(',', $stash_1_zipcodes));
	  }

	  $stash_2_zipcodes = get_option( 'wc_settings_tab_demo_stash_2_zipcodes', true );
	  if(empty($stash_2_zipcodes)) {
		$stash_2_zipcodes_arr = array();
	  }
	  else {
		$stash_2_zipcodes_arr = array_map('trim', explode(',', $stash_2_zipcodes));
	  }

	  $stash_3_zipcodes = get_option( 'wc_settings_tab_demo_stash_3_zipcodes', true );
	  if(empty($stash_3_zipcodes)) {
		$stash_3_zipcodes_arr = array();
	  }
	  else {
		$stash_3_zipcodes_arr = array_map('trim', explode(',', $stash_3_zipcodes));
	  }

	  if (empty($location_postcode) || in_array($location_postcode, $stash_1_zipcodes_arr)) {
        update_post_meta( $order_id, '_stash_location', 1 );
	  }
	  elseif (in_array($location_postcode, $stash_2_zipcodes_arr)) {
        update_post_meta( $order_id, '_stash_location', 2 );
	  }
	  elseif (in_array($location_postcode, $stash_3_zipcodes_arr)) {
        update_post_meta( $order_id, '_stash_location', 3 );
	  }

      update_post_meta( $order_id, '_location_postcode', $woocommerce->customer->get_shipping_postcode() );
	}


	/**
	 * Reduce stash stock
	 */
	public function woo_stash_reduce_stock_levels( $order_id, $posted_data, $order ){
		$order = wc_get_order( $order_id );

        $location_postcode = $order->get_meta('_location_postcode');
        $stash_location = intval(get_post_meta( $order_id, '_stash_location', true));

        $items = $order->get_items();
		foreach ($order->get_items() as $item_id => $item) {
		  if( $item->get_quantity() > 0 ){
			$product_id = $item->get_product_id();
			$product = wc_get_product($product_id);
            $qty = $item->get_quantity();
        	$item_stock_status = intval(wc_get_order_item_meta( $item_id, '_item_stock_status', true ));
        	$item_stock_last_qty = intval(wc_get_order_item_meta( $item_id, '_item_stock_last_qty', true ));

			if ((empty($stash_location) || $stash_location == 1)) {
			  if($item_stock_status == 0 || $item_stock_status == '') {
                $new_stock = $product->reduce_stock( $qty );
                $order->add_order_note( sprintf( __( 'Item #%s stock reduced from %s to %s from Stash #1.', 'woocommerce' ), $product_id, $new_stock + $qty, $new_stock) );
	  		    wc_update_order_item_meta($item_id, '_item_stock_status', 1); // flag it
	  		    wc_update_order_item_meta($item_id, '_item_stock_last_qty', $qty); // flag the last qty
	  		  }
			  elseif ($item_stock_status == 1) {
			    // Adjust last stock deduction
        	    wc_update_product_stock( $product, $item_stock_last_qty, 'increase' );

                $new_stock = $product->reduce_stock( $qty );
                $order->add_order_note( sprintf( __( 'Item #%s stock reduced from %s to %s from Stash #1.', 'woocommerce' ), $product_id, $new_stock + $qty, $new_stock) );
	  		    wc_update_order_item_meta($item_id, '_item_stock_status', 1); // flag it
	  		    wc_update_order_item_meta($item_id, '_item_stock_last_qty', $qty); // flag the last qty
			  }
			}
			elseif ($stash_location == 2) {
			  if($item_stock_status == 0 || $item_stock_status == '') {
			    $qty_before = intval(get_post_meta($product->get_id(), 'stash_product_stock_2', true));
			    $qty_after = $qty_before - $qty;
			    update_post_meta($product->get_id(), 'stash_product_stock_2', $qty_after);

                $order->add_order_note( sprintf( __( 'Item #%s stock reduced from %s to %s from Stash #2.', 'woocommerce' ), $product_id, $qty_before, $qty_after) );
	  		    wc_update_order_item_meta($item_id, '_item_stock_status', 1); // flag it
	  		    wc_update_order_item_meta($item_id, '_item_stock_last_qty', $qty); // flag the last qty
	  		  }
			  elseif ($item_stock_status == 1) {
			    // Adjust previous qty
			    $qty_current = intval(get_post_meta($product->get_id(), 'stash_product_stock_2', true));
			    update_post_meta($product->get_id(), 'stash_product_stock_2', $qty_current + $item_stock_last_qty);

			    $qty_before = intval(get_post_meta($product->get_id(), 'stash_product_stock_2', true));
			    $qty_after = $qty_before - $qty;
			    update_post_meta($product->get_id(), 'stash_product_stock_2', $qty_after);

                $order->add_order_note( sprintf( __( 'Item #%s stock reduced from %s to %s from Stash #2.', 'woocommerce' ), $product_id, $qty_before, $qty_after) );
	  		    wc_update_order_item_meta($item_id, '_item_stock_status', 1); // flag it
	  		    wc_update_order_item_meta($item_id, '_item_stock_last_qty', $qty); // flag the last qty
			  }
			}
			elseif ($stash_location == 3) {
			  if($item_stock_status == 0 || $item_stock_status == '') {
			    $qty_before = intval(get_post_meta($product->get_id(), 'stash_product_stock_3', true));
			    $qty_after = $qty_before - $qty;
			    update_post_meta($product->get_id(), 'stash_product_stock_3', $qty_after);
                $order->add_order_note( sprintf( __( 'Item #%s stock reduced from %s to %s from Stash #3.', 'woocommerce' ), $product_id, $qty_before, $qty_after) );
	  		    wc_update_order_item_meta($item_id, '_item_stock_status', 1); // flag it
	  		    wc_update_order_item_meta($item_id, '_item_stock_last_qty', $qty); // flag the last qty
	  		  }
			  elseif ($item_stock_status == 1) {
			    // Adjust previous qty
			    $qty_current = intval(get_post_meta($product->get_id(), 'stash_product_stock_3', true));
			    update_post_meta($product->get_id(), 'stash_product_stock_3', $qty_current + $item_stock_last_qty);

			    $qty_before = intval(get_post_meta($product->get_id(), 'stash_product_stock_3', true));
			    $qty_after = $qty_before - $qty;
			    update_post_meta($product->get_id(), 'stash_product_stock_3', $qty_after);
                $order->add_order_note( sprintf( __( 'Item #%s stock reduced from %s to %s from Stash #3.', 'woocommerce' ), $product_id, $qty_before, $qty_after) );
	  		    wc_update_order_item_meta($item_id, '_item_stock_status', 1); // flag it
	  		    wc_update_order_item_meta($item_id, '_item_stock_last_qty', $qty); // flag the last qty
			  }
			}

		  }
		}
	}

	/**
	 * Adjust stash stock
	 */
	public function woo_stash_increase_stock_levels( $order_id ){
		$order = wc_get_order( $order_id );

        $location_postcode = $order->get_meta('_location_postcode');
        $stash_location = intval(get_post_meta( $order_id, '_stash_location', true));

        $items = $order->get_items();
		foreach ($order->get_items() as $item_id => $item) {
		  if( $item->get_quantity() > 0 ){
			$product_id = $item->get_product_id();
			$product = wc_get_product($product_id);
            $qty = $item->get_quantity();
        	$item_stock_status = wc_get_order_item_meta( $item_id, '_item_stock_status', true );

			if ((empty($stash_location) || $stash_location == 1) && $item_stock_status == 1) {
        	  wc_update_product_stock( $product, $qty, 'increase' );
              $new_stock = $product->get_stock_quantity();
              $order->add_order_note( sprintf( __( 'Item #%s stock adjusted from %s to %s from Stash #1.', 'woocommerce' ), $product_id, $new_stock - $qty, $new_stock) );
	  		  wc_update_order_item_meta($item_id, '_item_stock_status', 1); // flag it
	  		  wc_update_order_item_meta($item_id, '_item_stock_last_qty', 0); // flag the last qty
			}
			elseif ($stash_location == 2 && $item_stock_status == 1) {
			  $qty_before = intval(get_post_meta($product->get_id(), 'stash_product_stock_2', true));
			  $qty_after = $qty_before + $qty;
			  update_post_meta($product->get_id(), 'stash_product_stock_2', $qty_after);
              $order->add_order_note( sprintf( __( 'Item #%s stock adjusted from %s to %s from Stash #2.', 'woocommerce' ), $product_id, $qty_before, $qty_after) );
	  		  wc_update_order_item_meta($item_id, '_item_stock_status', 1); // flag it
	  		  wc_update_order_item_meta($item_id, '_item_stock_last_qty', 0); // flag the last qty
			}
			elseif ($stash_location == 3 && $item_stock_status == 1) {
			  $qty_before = intval(get_post_meta($product->get_id(), 'stash_product_stock_3', true));
			  $qty_after = $qty_before + $qty;
			  update_post_meta($product->get_id(), 'stash_product_stock_3', $qty_after);
              $order->add_order_note( sprintf( __( 'Item #%s stock adjusted from %s to %s from Stash #3.', 'woocommerce' ), $product_id, $qty_before, $qty_after) );
	  		  wc_update_order_item_meta($item_id, '_item_stock_status', 1); // flag it
	  		  wc_update_order_item_meta($item_id, '_item_stock_last_qty', 0); // flag the last qty
			}
		  }
		}
	}

	/**
	 * Remove default stock column and add new stock column
	 */
	function woo_stash_product_column($columns) {
	  unset($columns['is_in_stock']);
	  $columns['stash_stock'] = __( 'Stock', 'woo-stash');
	  return $columns;
	}

	/**
	 * Stock column adjustment with 3 warehouse stock
	 */
	function woo_stash_product_list_column_content( $column, $product_id ) {
	  global $post;

	  switch ( $column ) {
		case 'stash_stock' :
		  $product = wc_get_product($product_id);
		  $stock_str = '';

		  $stash_1_stock = intval($product->get_stock_quantity());
		  if($stash_1_stock <= 0) {
			$stock_str .= '<div class="stash-1-stock"><strong>Stash #1: </strong><mark class="outofstock">Out of stock</mark> ('.$stash_1_stock.')</div>';
		  }
		  else {
			$stock_str .= '<div class="stash-1-stock"><strong>Stash #1: </strong><mark class="instock">In stock</mark> ('.$stash_1_stock.')</div>';
		  }

		  $stash_2_stock = intval(get_post_meta($product_id, 'stash_product_stock_2', true));
		  if($stash_2_stock <= 0) {
			$stock_str .= '<div class="stash-2-stock"><strong>Stash #2: </strong><mark class="outofstock">Out of stock</mark> ('.$stash_2_stock.')</div>';
		  }
		  else {
			$stock_str .= '<div class="stash-2-stock"><strong>Stash #2: </strong><mark class="instock">In stock</mark> ('.$stash_2_stock.')</div>';
		  }

		  $stash_3_stock = intval(get_post_meta($product_id, 'stash_product_stock_3', true));
		  if($stash_3_stock <= 0) {
			$stock_str .= '<div class="stash-3-stock"><strong>Stash #3: </strong><mark class="outofstock">Out of stock</mark> ('.$stash_3_stock.')</div>';
		  }
		  else {
			$stock_str .= '<div class="stash-3-stock"><strong>Stash #3: </strong><mark class="instock">In stock</mark> ('.$stash_3_stock.')</div>';
		  }

		  echo $stock_str;
		  break;
	  }
	}


	/**
	 * Display "In stock" and "Out of stock" text based on location / zip code
	 */
	function woo_stash_get_availability( $availability, $product ) {
      global $woocommerce;

      if(!isset($woocommerce->customer) || empty($woocommerce->customer->get_shipping_postcode())) {
		$stash_1_stock = intval($product->get_stock_quantity());
		if($stash_1_stock <= 0) {
		  $availability['availability'] = __('Out of stock', 'woocommerce');
		}
		else {
		  $availability['availability'] = __('In stock', 'woocommerce');
		}

		return $availability;
      }


	  $stash_1_zipcodes = get_option( 'wc_settings_tab_demo_stash_1_zipcodes', true );
	  if(empty($stash_1_zipcodes)) {
		$stash_1_zipcodes_arr = array();
	  }
	  else {
		$stash_1_zipcodes_arr = array_map('trim', explode(',', $stash_1_zipcodes));
	  }

	  $stash_2_zipcodes = get_option( 'wc_settings_tab_demo_stash_2_zipcodes', true );
	  if(empty($stash_2_zipcodes)) {
		$stash_2_zipcodes_arr = array();
	  }
	  else {
		$stash_2_zipcodes_arr = array_map('trim', explode(',', $stash_2_zipcodes));
	  }

	  $stash_3_zipcodes = get_option( 'wc_settings_tab_demo_stash_3_zipcodes', true );
	  if(empty($stash_3_zipcodes)) {
		$stash_3_zipcodes_arr = array();
	  }
	  else {
		$stash_3_zipcodes_arr = array_map('trim', explode(',', $stash_3_zipcodes));
	  }

      $location_postcode = $woocommerce->customer->get_shipping_postcode();
	  if (in_array($location_postcode, $stash_1_zipcodes_arr)) {
		$stash_1_stock = intval($product->get_stock_quantity());
		if($stash_1_stock <= 0) {
		  $availability['availability'] = __('Out of stock', 'woocommerce');
		}
		else {
		  $availability['availability'] = __('In stock', 'woocommerce');
		}
	  }
	  elseif (in_array($location_postcode, $stash_2_zipcodes_arr)) {
		$stash_2_stock = intval(get_post_meta($product->get_id(), 'stash_product_stock_2', true));
		if($stash_2_stock <= 0) {
		  $availability['availability'] = __('Out of stock', 'woocommerce');
		}
		else {
		  $availability['availability'] = __('In stock', 'woocommerce');
		}
	  }
	  elseif (in_array($location_postcode, $stash_3_zipcodes_arr)) {
		$stash_3_stock = intval(get_post_meta($product->get_id(), 'stash_product_stock_3', true));
		if($stash_3_stock <= 0) {
		  $availability['availability'] = __('Out of stock', 'woocommerce');
		}
		else {
		  $availability['availability'] = __('In stock', 'woocommerce');
		}
	  }

	  return $availability;
	}

	/**
	 * Show / Hide add to cart button based on postcode and related stock availability
	 */
	function woo_stash_is_product_purchasable( $purchasable, $product ){
      global $woocommerce;

      if(!isset($woocommerce->customer) || empty($woocommerce->customer->get_shipping_postcode())) {
		$stash_1_stock = intval($product->get_stock_quantity());
		if($stash_1_stock <= 0) {
		  return false;
		}

		return true;
      }

	  $stash_1_zipcodes = get_option( 'wc_settings_tab_demo_stash_1_zipcodes', true );
	  if(empty($stash_1_zipcodes)) {
		$stash_1_zipcodes_arr = array();
	  }
	  else {
		$stash_1_zipcodes_arr = array_map('trim', explode(',', $stash_1_zipcodes));
	  }

	  $stash_2_zipcodes = get_option( 'wc_settings_tab_demo_stash_2_zipcodes', true );
	  if(empty($stash_2_zipcodes)) {
		$stash_2_zipcodes_arr = array();
	  }
	  else {
		$stash_2_zipcodes_arr = array_map('trim', explode(',', $stash_2_zipcodes));
	  }

	  $stash_3_zipcodes = get_option( 'wc_settings_tab_demo_stash_3_zipcodes', true );
	  if(empty($stash_3_zipcodes)) {
		$stash_3_zipcodes_arr = array();
	  }
	  else {
		$stash_3_zipcodes_arr = array_map('trim', explode(',', $stash_3_zipcodes));
	  }

      $location_postcode = $woocommerce->customer->get_shipping_postcode();
	  if (in_array($location_postcode, $stash_1_zipcodes_arr)) {
		$stash_1_stock = intval($product->get_stock_quantity());
		if($stash_1_stock <= 0) {
		  return false;
		}
	  }
	  elseif (in_array($location_postcode, $stash_2_zipcodes_arr)) {
		$stash_2_stock = intval(get_post_meta($product->get_id(), 'stash_product_stock_2', true));
		if($stash_2_stock <= 0) {
		  return false;
		}
	  }
	  elseif (in_array($location_postcode, $stash_3_zipcodes_arr)) {
		$stash_3_stock = intval(get_post_meta($product->get_id(), 'stash_product_stock_3', true));
		if($stash_3_stock <= 0) {
		  return false;
		}
	  }

		return true;
	}

	function woo_stash_display_out_of_stock_text( $post_excerpt ){
      global $woocommerce, $post;

	  if($post == false) return $post_excerpt;

	  $product = wc_get_product($post->ID);
	  $str = '';

	  if($product == false) return $post_excerpt;

      if(empty($woocommerce->customer->get_shipping_postcode())) {
		$stash_1_stock = intval($product->get_stock_quantity());
		if($stash_1_stock <= 0) {
	  	  $str = '<div class="stock-info"><mark class="outofstock">Out of stock</mark></div>';

		  $post_excerpt .= $str;
		  return $post_excerpt;
		}
      }

	  $stash_1_zipcodes = get_option( 'wc_settings_tab_demo_stash_1_zipcodes', true );
	  if(empty($stash_1_zipcodes)) {
		$stash_1_zipcodes_arr = array();
	  }
	  else {
		$stash_1_zipcodes_arr = array_map('trim', explode(',', $stash_1_zipcodes));
	  }

	  $stash_2_zipcodes = get_option( 'wc_settings_tab_demo_stash_2_zipcodes', true );
	  if(empty($stash_2_zipcodes)) {
		$stash_2_zipcodes_arr = array();
	  }
	  else {
		$stash_2_zipcodes_arr = array_map('trim', explode(',', $stash_2_zipcodes));
	  }

	  $stash_3_zipcodes = get_option( 'wc_settings_tab_demo_stash_3_zipcodes', true );
	  if(empty($stash_3_zipcodes)) {
		$stash_3_zipcodes_arr = array();
	  }
	  else {
		$stash_3_zipcodes_arr = array_map('trim', explode(',', $stash_3_zipcodes));
	  }

      $location_postcode = $woocommerce->customer->get_shipping_postcode();
	  if (in_array($location_postcode, $stash_1_zipcodes_arr)) {
		$stash_1_stock = intval($product->get_stock_quantity());
		if($stash_1_stock <= 0) {
	  	  $str = '<div class="stock-info"><mark class="outofstock">Out of stock</mark></div>';

		  $post_excerpt .= $str;
		  return $post_excerpt;
		}
	  }
	  elseif (in_array($location_postcode, $stash_2_zipcodes_arr)) {
		$stash_2_stock = intval(get_post_meta($product->get_id(), 'stash_product_stock_2', true));
		if($stash_2_stock <= 0) {
	  	  $str = '<div class="stock-info"><mark class="outofstock">Out of stock</mark></div>';

		  $post_excerpt .= $str;
		  return $post_excerpt;
		}
	  }
	  elseif (in_array($location_postcode, $stash_3_zipcodes_arr)) {
		$stash_3_stock = intval(get_post_meta($product->get_id(), 'stash_product_stock_3', true));
		if($stash_3_stock <= 0) {
	  	  $str = '<div class="stock-info"><mark class="outofstock">Out of stock</mark></div>';

		  $post_excerpt .= $str;
		  return $post_excerpt;
		}
	  }

	  return $post_excerpt;
	}


	/**
	 * Display stash location of the order
	 */
	function woo_stash_display_order_stash_location( $order ){
      echo '<p class="form-field form-field-wide wc-stash-location"><strong><h2>'.__('Stash Location').':</h2></strong> <h2>' . get_post_meta( $order->get_id(), '_stash_location', true ) . '</h2></p>';
	}


	/**
	 * Remove order item meta key
	 */
	function woo_stash_order_item_get_formatted_meta_data($formatted_meta){
		$temp_metas = [];
		foreach($formatted_meta as $key => $meta) {
			if ( isset( $meta->key ) && ! in_array( $meta->key, [
					'_item_stock_status',
					'_item_stock_last_qty',
				] ) ) {
				$temp_metas[ $key ] = $meta;
			}
		}
		return $temp_metas;
	}

	/**
	 * Expose product meta fields for rest api
	 * https://developer.wordpress.org/rest-api/extending-the-rest-api/modifying-responses/
	 */
	function woo_stash_rest_api_expose_product_meta_fields($current_vars) {
		register_rest_field( 'product', 'stash_product_stock_1', array(
			'get_callback' => function( $product_arr ) {
			  $product = wc_get_product($product_arr['id']);
			  return intval($product->get_stock_quantity());
			},
			'update_callback' => null,
			'schema' => array(
			  'description' => __( 'Stash #1 stock' ),
			  'type'        => 'integer'
			),
		));

		register_rest_field( 'product', 'stash_product_stock_2', array(
			'get_callback' => function( $product_arr ) {
			  $stock = intval(get_post_meta( $product_arr['id'], 'stash_product_stock_2', true));
			  return $stock;
			},
			'update_callback' => null,
			'schema' => array(
			  'description' => __( 'Stash #2 stock' ),
			  'type'        => 'integer'
			),
		));

		register_rest_field( 'product', 'stash_product_stock_3', array(
			'get_callback' => function( $product_arr ) {
			  $stock = intval(get_post_meta( $product_arr['id'], 'stash_product_stock_3', true));
			  return $stock;
			},
			'update_callback' => null,
			'schema' => array(
			  'description' => __( 'Stash #3 stock' ),
			  'type'        => 'integer'
			),
		));

		$meta_args = array(
			'type'         => 'integer',
			'description'  => 'Stash #2 stock',
			'single'       => true,
			'show_in_rest' => true,
		);
		register_meta( 'product', 'stash_product_stock_2', $meta_args );

		$meta_args = array(
			'type'         => 'integer',
			'description'  => 'Stash #3 stock',
			'single'       => true,
			'show_in_rest' => true,
		);
		register_meta( 'product', 'stash_product_stock_3', $meta_args );
	}


	/**
	 * Filter the product response based on parameter "warehouse_id"
	 */
	function woo_stash_change_data_to_product( $response, $product, $request ) {
      if($request->has_param('warehouse_id')) {
      	$warehouse_id = intval($request->get_param('warehouse_id'));

      	if($warehouse_id == 1) {
      	  //nothing to do
      	}
      	elseif($warehouse_id == 2) {
		  $stock = intval(get_post_meta( $product->get_id(), 'stash_product_stock_2', true));

	  	  $response->data['stock_quantity'] = $stock;
	  	  $response->data['stock_status'] = ($stock > 0 ? 'instock' : 'outofstock');
      	}
      	elseif($warehouse_id == 3) {
		  $stock = intval(get_post_meta( $product->get_id(), 'stash_product_stock_3', true));

	  	  $response->data['stock_quantity'] = $stock;
	  	  $response->data['stock_status'] = ($stock > 0 ? 'instock' : 'outofstock');
      	}
      	else {
	  	  $response->data['stock_quantity'] = 0;
	  	  $response->data['stock_status'] = 'outofstock';
      	}
      }

	  return $response;
	}


	/**
	 * Expose order meta fields for rest api
	 * https://developer.wordpress.org/rest-api/extending-the-rest-api/modifying-responses/
	 */
	function woo_stash_rest_api_expose_order_meta_fields($current_vars) {
		register_rest_field( 'shop_order', 'stash_location', array(
			'get_callback' => function( $order_arr ) {
			  $location_id = intval(get_post_meta( $order_arr['id'], '_stash_location', true));
			  return $location_id;
			},
			'update_callback' => null,
			'schema' => array(
			  'description' => __( 'Stash id' ),
			  'type'        => 'integer'
			),
		));

		$meta_args = array(
			'type'         => 'integer',
			'description'  => 'Stash id',
			'single'       => true,
			'show_in_rest' => true,
		);
		register_meta( 'shop_order', '_stash_location', $meta_args );
	}

	/**
	  * Add the filter to shop order post type
	  **/
	function woo_stash_rest_filter_add_filters_for_order() {
	  add_filter('woocommerce_rest_orders_prepare_object_query', array( $this, 'woo_stash_rest_orders_prepare_object_query'), 10, 2);
	}

	function woo_stash_rest_orders_prepare_object_query($args, $request) {
	  global $wpdb;

	  if($request->has_param('warehouse_id')) {
		$warehouse_id = $request->get_param('warehouse_id');
	  }
	  else {
		$warehouse_id = 0;
	  }

	   // Search by warehouse_id.
	  if ( ! empty( $warehouse_id ) ) {
			$order_ids = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT DISTINCT post_id
					FROM {$wpdb->prefix}postmeta a
					INNER JOIN {$wpdb->prefix}posts b ON a.post_id = b.ID
					WHERE a.meta_key = '_stash_location' AND a.meta_value = %d
					AND b.post_type = 'shop_order'",
					$warehouse_id
				)
			);

		  $order_ids = !empty($order_ids) ? $order_ids : array(0);
		  $args['post__in'] = $order_ids;
		}

		return $args;
	}


	/**
	 * Adds 'Warehouse' column content to 'Orders' page immediately after 'Status' column.
	 *
	 * @param string[] $column name of column being displayed
	 */
	function woo_stash_add_order_warehouse_column_content( $column ) {
		global $post;

		if ( 'order_warehouse' === $column ) {

			$order    = wc_get_order( $post->ID );
            $stash_location = $order->get_meta( '_stash_location', true);
            if(empty($stash_location)) {
              $stash_location = '#1';
            }
            else {
              $stash_location = '#'.$stash_location;
            }

			echo $stash_location;
		}
	}

	/**
	 * Adds 'Warehouse' column header to 'Orders' page immediately after 'Status' column.
	 *
	 * @param string[] $columns
	 * @return string[] $new_columns
	 */
	function woo_stash_add_order_warehouse_column_header( $columns ) {

		$new_columns = array();

		foreach ( $columns as $column_name => $column_info ) {

			$new_columns[ $column_name ] = $column_info;

			if ( 'order_status' === $column_name ) {
				$new_columns['order_warehouse'] = __( 'Warehouse', 'woo-stash' );
			}
		}

		return $new_columns;
	}


	/**
	 * Adds the warehouse filtering dropdown to the orders list
	 *
	 * @since 1.0.0
	 */
	public function woo_stash_filter_orders_by_warehouse() {
		global $typenow;

		if ( 'shop_order' === $typenow ) {
			?>

				<select name="_warehouse" id="dropdown_warehouse">
					<option value="">
						<?php esc_html_e( 'Filter by warehouse', 'woo-stash' ); ?>
					</option>
					<option value="1" <?php echo esc_attr( isset( $_GET['_warehouse'] ) ? selected( 1, $_GET['_warehouse'], false ) : '' ); ?>>
						<?php echo esc_html( 'Warehouse #1' ); ?>
					</option>
					<option value="2" <?php echo esc_attr( isset( $_GET['_warehouse'] ) ? selected( 2, $_GET['_warehouse'], false ) : '' ); ?>>
						<?php echo esc_html( 'Warehouse #2' ); ?>
					</option>
					<option value="3" <?php echo esc_attr( isset( $_GET['_warehouse'] ) ? selected( 3, $_GET['_warehouse'], false ) : '' ); ?>>
						<?php echo esc_html( 'Warehouse #3' ); ?>
					</option>
				</select>
			<?php
		}
	}


	/**
	 * Modify SQL JOIN for filtering the orders by any warehouse
	 *
	 * @param string $join JOIN part of the sql query
	 * @return string $join modified JOIN part of sql query
	 */
	public function woo_stash_add_order_items_join( $join ) {
		global $typenow, $wpdb;

		if ( 'shop_order' === $typenow && isset( $_GET['_warehouse'] ) && ! empty( $_GET['_warehouse'] ) ) {
			$join .= "LEFT JOIN {$wpdb->prefix}postmeta pm ON {$wpdb->posts}.ID = pm.post_id";
		}

		return $join;
	}


	/**
	 * Modify SQL WHERE for filtering the orders by any warehouse
	 *
	 * @param string $where WHERE part of the sql query
	 * @return string $where modified WHERE part of sql query
	 */
	public function woo_stash_add_filterable_where( $where ) {
		global $typenow, $wpdb;

		if ( 'shop_order' === $typenow && isset( $_GET['_warehouse'] ) && ! empty( $_GET['_warehouse'] ) ) {

			// Main WHERE query part
			$where .= $wpdb->prepare( " AND pm.meta_key='_stash_location' AND pm.meta_value='%s'", wc_clean( $_GET['_warehouse'] ) );
		}

		return $where;
	}


	/**
	 * Add low stcok report menu to Woocommerce
	 */
	function woo_stash_register_low_stock_menu() {
	  add_submenu_page( 'woocommerce', 'Low Stock Report', 'Low Stock Report', 'manage_options', 'woo-stash', array($this, 'woo_stash_warehouse_low_stock_report') );
	}

	/**
	 * Display the stock list
	 */
	function woo_stash_warehouse_low_stock_report() {
        $stockListTable = new Warehouse_Product_Stock_List_Table();
        $stockListTable->prepare_items();
        ?>
            <div class="wrap">
                <div id="icon-users" class="icon32"></div>
                <h2>Low Stock Report</h2>
                <?php $stockListTable->display(); ?>
            </div>
        <?php
	}



	/**
	 * Generate stock csv report
	 */
	function woo_stash_generate_stock_csv() {
	  header('Content-Type: text/csv; charset=utf-8');
	  header('Content-Disposition: attachment; filename=low-stock-report-' . date('Y-m-d') . '.csv');

	  // create a file pointer connected to the output stream
	  $output = fopen('php://output', 'w');

	  // set the column headers for the csv
	  $headings = array( 'ID', 'Name', 'SKU', 'Stock' );

	  // output the column headings
	  fputcsv($output, $headings );

	  $low_stock_amount = get_option('woocommerce_notify_low_stock_amount', 0);
	  $no_stock_amount = get_option('woocommerce_notify_no_stock_amount', 0);


		// get all simple products where stock is managed
	  $args = array(
		'post_type'			=> 'product',
		'post_status' 		=> 'publish',
		'posts_per_page' 		=> -1,
		'orderby'				=> 'title',
		'order'				=> 'ASC',
		'meta_query' 		=> array(
    	  'relation' => 'OR',
		),
	  );

	  $warehouse_id = '';
	  if( $_GET['warehouse-filter'] != '' ){
		$warehouse_id = $_GET['warehouse-filter'];
	  }

	  $stock_type = !empty($_GET['stock-type']) ? $_GET['stock-type'] : 'no-stock';
	  if( $stock_type != '' ){
	    if($warehouse_id == 1) {
			if ($stock_type == 'low-stock') {
			  $args['meta_query'][] = array(
				'key' 	=> '_stock',
				'value' => $low_stock_amount,
				'compare' => '<=',
				'type' => 'NUMERIC'
			  );
			  $args['meta_query'][] = array(
				'key' 	=> '_stock',
				'compare' => 'NOT EXISTS',
			  );
			  $args['meta_query'][] = array(
				'key' 	=> '_stock',
				'value' => '',
				'compare' => 'EXISTS',
			  );
			}
			elseif ($stock_type == 'no-stock') {
			  $args['meta_query'][] = array(
				'key' 	=> '_stock',
				'value' => $no_stock_amount,
				'compare' => '<=',
				'type' => 'NUMERIC'
			  );
			  $args['meta_query'][] = array(
				'key' 	=> '_stock',
				'compare' => 'NOT EXISTS',
			  );
			  $args['meta_query'][] = array(
				'key' 	=> '_stock',
				'value' => '',
				'compare' => 'EXISTS',
			  );
			}
			else {
			  $args['meta_query'][] = array(
				'key' 	=> '_stock',
				'value' => $no_stock_amount,
				'compare' => '<=',
				'type' => 'NUMERIC'
			  );
			  $args['meta_query'][] = array(
				'key' 	=> '_stock',
				'compare' => 'NOT EXISTS',
			  );
			  $args['meta_query'][] = array(
				'key' 	=> '_stock',
				'value' => '',
				'compare' => 'EXISTS',
			  );
			}
	    }
	    elseif($warehouse_id == 2) {
			if ($stock_type == 'low-stock') {
			  $args['meta_query'][] = array(
				'key' 	=> 'stash_product_stock_2',
				'value' => $low_stock_amount,
				'compare' => '<=',
				'type' => 'NUMERIC'
			  );
			  $args['meta_query'][] = array(
				'key' 	=> 'stash_product_stock_2',
				'compare' => 'NOT EXISTS',
			  );
			  $args['meta_query'][] = array(
				'key' 	=> 'stash_product_stock_2',
				'value' => '',
				'compare' => 'EXISTS',
			  );
			}
			elseif ($stock_type == 'no-stock') {
			  $args['meta_query'][] = array(
				'key' 	=> 'stash_product_stock_2',
				'value' => $no_stock_amount,
				'compare' => '<=',
				'type' => 'NUMERIC'
			  );
			  $args['meta_query'][] = array(
				'key' 	=> 'stash_product_stock_2',
				'compare' => 'NOT EXISTS',
			  );
			  $args['meta_query'][] = array(
				'key' 	=> 'stash_product_stock_2',
				'value' => '',
				'compare' => 'EXISTS',
			  );
			}
			else {
			  $args['meta_query'][] = array(
				'key' 	=> 'stash_product_stock_2',
				'value' => $no_stock_amount,
				'compare' => '<=',
				'type' => 'NUMERIC'
			  );
			  $args['meta_query'][] = array(
				'key' 	=> 'stash_product_stock_2',
				'compare' => 'NOT EXISTS',
			  );
			  $args['meta_query'][] = array(
				'key' 	=> 'stash_product_stock_2',
				'value' => '',
				'compare' => 'EXISTS',
			  );
			}
	    }
	    elseif($warehouse_id == 3) {
			if ($stock_type == 'low-stock') {
			  $args['meta_query'][] = array(
				'key' 	=> 'stash_product_stock_3',
				'value' => $low_stock_amount,
				'compare' => '<=',
				'type' => 'NUMERIC'
			  );
			  $args['meta_query'][] = array(
				'key' 	=> 'stash_product_stock_3',
				'compare' => 'NOT EXISTS',
			  );
			  $args['meta_query'][] = array(
				'key' 	=> 'stash_product_stock_3',
				'value' => '',
				'compare' => 'EXISTS',
			  );
			}
			elseif ($stock_type == 'no-stock') {
			  $args['meta_query'][] = array(
				'key' 	=> 'stash_product_stock_3',
				'value' => $no_stock_amount,
				'compare' => '<=',
				'type' => 'NUMERIC'
			  );
			  $args['meta_query'][] = array(
				'key' 	=> 'stash_product_stock_3',
				'compare' => 'NOT EXISTS',
			  );
			  $args['meta_query'][] = array(
				'key' 	=> 'stash_product_stock_3',
				'value' => '',
				'compare' => 'EXISTS',
			  );
			}
			else {
			  $args['meta_query'][] = array(
				'key' 	=> 'stash_product_stock_3',
				'value' => $no_stock_amount,
				'compare' => '<=',
				'type' => 'NUMERIC'
			  );
			  $args['meta_query'][] = array(
				'key' 	=> 'stash_product_stock_3',
				'compare' => 'NOT EXISTS',
			  );
			  $args['meta_query'][] = array(
				'key' 	=> 'stash_product_stock_3',
				'value' => '',
				'compare' => 'EXISTS',
			  );
			}
	    }
	    else {
			if ($stock_type == 'low-stock') {
			  $args['meta_query'][] = array(
				'key' 	=> '_stock',
				'value' => $low_stock_amount,
				'compare' => '<=',
				'type' => 'NUMERIC'
			  );
			  $args['meta_query'][] = array(
				'key' 	=> '_stock',
				'compare' => 'NOT EXISTS',
			  );
			  $args['meta_query'][] = array(
				'key' 	=> '_stock',
				'value' => '',
				'compare' => 'EXISTS',
			  );
			}
			elseif ($stock_type == 'no-stock') {
			  $args['meta_query'][] = array(
				'key' 	=> '_stock',
				'value' => $no_stock_amount,
				'compare' => '<=',
				'type' => 'NUMERIC'
			  );
			  $args['meta_query'][] = array(
				'key' 	=> '_stock',
				'compare' => 'NOT EXISTS',
			  );
			  $args['meta_query'][] = array(
				'key' 	=> '_stock',
				'value' => '',
				'compare' => 'EXISTS',
			  );
			}
			else {
			  $args['meta_query'][] = array(
				'key' 	=> '_stock',
				'value' => $no_stock_amount,
				'compare' => '<=',
				'type' => 'NUMERIC'
			  );
			  $args['meta_query'][] = array(
				'key' 	=> '_stock',
				'compare' => 'NOT EXISTS',
			  );
			  $args['meta_query'][] = array(
				'key' 	=> '_stock',
				'value' => '',
				'compare' => 'EXISTS',
			  );
			}
		}
	  }

	  $loop = new WP_Query( $args );

	  while ( $loop->have_posts() ) : $loop->the_post();
		global $product;

		$stock_1 = $product->get_stock_quantity();
		$stock_2 = intval(get_post_meta($product->get_id(), 'stash_product_stock_2', true));
		$stock_3 = intval(get_post_meta($product->get_id(), 'stash_product_stock_3', true));

		if( $_GET['warehouse-filter'] != '' ){
		  $warehouse_id = $_GET['warehouse-filter'];
		  if($warehouse_id == 1) {
			$stock = $stock_1;
		  }
		  elseif ($warehouse_id == 2) {
			$stock = $stock_2;
		  }
		  elseif ($warehouse_id == 3) {
			$stock = $stock_3;
		  }
		  else {
			$stock  = 'Stash 1: '.$stock_1.' # ';
			$stock .= 'Stash 2: '.$stock_2.'# ';
			$stock .= 'Stash 3: '.$stock_3;
		  }
		}
		else {
		  $stock  = 'Stash 1: '.$stock_1.' # ';
		  $stock .= 'Stash 2: '.$stock_2.'# ';
		  $stock .= 'Stash 3: '.$stock_3;
		}

		$row = array( $product->get_id(), $product->get_name(), $product->get_sku(), $stock );

		fputcsv($output, $row);

	  endwhile;
	}

}

new Woo_Stash_Main;
