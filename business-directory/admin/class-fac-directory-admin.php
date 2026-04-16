<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/shafiqhossain
 * @since      1.0.0
 *
 * @package    Fac_Directory
 * @subpackage Fac_Directory/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Fac_Directory
 * @subpackage Fac_Directory/admin
 * @author     Shafiq Hossain <md.shafiq.hossain@gmail.com>
 */
class Fac_Directory_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		// Lets add an action to setup the admin menu in the left nav
		add_action( 'admin_menu', array($this, 'add_fac_directory_menu') );

		// Add actions to setup the settings we want on the wp admin page
		add_action('admin_init', array($this, 'fac_directory_setup_sections'));
		add_action('admin_init', array($this, 'fac_directory_setup_fields'));

		// Define custom cron schedule
        add_filter( 'cron_schedules', array($this, 'fac_directory_cron_schedule') );
        add_action( 'wp_batch_processing_init', array($this, 'fac_directory_batch_processing_init'), 15, 1 );
    }

    /**
     * Add cron schedule
     */
    public function fac_directory_cron_schedule( $schedules ) {
        $schedules['every_six_hours'] = array(
            'interval' => 21600, // Every 6 hours
            'display'  => __( 'Every 6 hours' ),
        );
        return $schedules;
    }

	/**
	 * Add the menu items to the admin menu
	 *
	 * @since    1.0.0
	 */
	public function add_fac_directory_menu() {

		// Main Menu Item
	  	add_menu_page(
			'FAC Directory',
			'FAC Directory',
			'manage_options',
			'fac-directory',
			array($this, 'fac_directory_display_profiles_list'),
			'dashicons-store',
			1);

		// Sub Menu: FAC Directory Profiles
		add_submenu_page(
            'fac-directory',
			'FAC Profiles',
			'FAC Profiles',
			'manage_options',
            'fac-directory/profiles',
            array($this, 'fac_directory_display_profiles_list')
		);

        // Sub Menu: FAC Directory Groups
        add_submenu_page(
            'fac-directory',
            'FAC Profiles',
            'FAC Groups',
            'manage_options',
            'fac-directory/groups',
            array($this, 'fac_directory_display_groups_list')
        );

        // Sub Menu: FAC Directory Settings
        add_submenu_page(
            'fac-directory',
            'FAC Settings',
            'FAC Settings',
            'manage_options',
            'fac-directory/settings',
            array($this, 'fac_directory_display_admin_settings_page')
        );

        // Sub Menu: FAC Data Fetching
        add_submenu_page(
            'fac-directory',
            'FAC Data Fetching',
            'FAC Data Fetching',
            'manage_options',
            'fac-directory/data-fetching',
            array($this, 'fac_directory_display_data_fetching_page')
        );

    }

	/**
	 * Callback function for displaying the admin settings page.
	 *
	 * @since    1.0.0
	 */
	public function fac_directory_display_admin_settings_page(){
	  require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/fac-directory-display-admin-settings.php';
	}

	/**
	 * Callback function for displaying the existing FAC profiles.
	 *
	 * @since    1.0.0
	 */
	public function fac_directory_display_profiles_list(){
        /**
         * The class responsible for profile list in backend
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fac-directory-profiles.php';

        $profileListTable = new Fac_Directory_Profile_List_Table();
        $profileListTable->prepare_items();
        ?>
        <div class="wrap">
            <div id="icon-users" class="icon32"></div>
            <h2>FAC Profiles</h2>
            <?php $profileListTable->display(); ?>
        </div>
        <?php
	}

    /**
     * Callback function for displaying the existing FAC groups.
     *
     * @since    1.0.0
     */
    public function fac_directory_display_groups_list(){
        /**
         * The class responsible for groups list in backend
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fac-directory-groups.php';

        $groupListTable = new Fac_Directory_Groups_List_Table();
        $groupListTable->prepare_items();
        ?>
        <div class="wrap">
            <div id="icon-users" class="icon32"></div>
            <h2>FAC Groups</h2>
            <?php $groupListTable->display(); ?>
        </div>
        <?php
    }

    /**
     * Callback function for displaying the data fetching page.
     *
     * @since    1.0.0
     */
    public function fac_directory_display_data_fetching_page(){
        /**
         * The class responsible for fetch data in backend
         */
        include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fac-directory-fetch-form.php';
    }

	/**
	 * Setup sections in the settings
	 *
	 * @since    1.0.0
	 */
	public function fac_directory_setup_sections() {
		add_settings_section( 'section_ym_settings', '<h2>YM Settings</h2>', array($this, 'fac_directory_section_callback'), 'fac-directory-options' );
		add_settings_section( 'section_page_settings', '<h2>Page Settings</h2>', array($this, 'fac_directory_section_callback'), 'fac-directory-options' );
	}

	/**
	 * Callback for each section
	 *
	 * @since    1.0.0
	 */
	public function fac_directory_section_callback( $arguments ) {
		switch( $arguments['id'] ){
			case 'section_ym_settings':
				echo '<p>YM API Information.</p>';
				break;
			case 'section_page_settings':
				echo '<p>Directory page settings.</p>';
				break;
		}
	}

	/**
	 * Field Configuration, each item in this array is one field/setting we want to capture
	 *
	 * @since    1.0.0
	 */
	public function fac_directory_setup_fields() {
		$args = array(
			'sort_order' => 'asc',
			'sort_column' => 'post_title',
			'hierarchical' => 1,
			'exclude' => '',
			'include' => '',
			'meta_key' => '',
			'meta_value' => '',
			'authors' => '',
			'child_of' => 0,
			'parent' => -1,
			'exclude_tree' => '',
			'number' => '',
			'offset' => 0,
			'post_type' => 'page',
			'post_status' => 'publish'
		);
		$pages = get_pages($args); // Get all pages based on supplied args

		$page_list = array();
		foreach ($pages as $page) { // $pages is array of object
		   $page_list[$page->ID] = $page->post_title;
		}

        $batch_list = array();
        $batch_list[50] = '50';
        $batch_list[100] = '100';
        $batch_list[200] = '200';
        $batch_list[500] = '500';
        $batch_list[1000] = '1,000';
        $batch_list[2000] = '2,000';
        $batch_list[5000] = '5,000';
        $batch_list[10000] = '10,000';

        $options = [];
        $options[1] = 'Yes';
        $options[0] = 'No';

        $fields = array(
			array(
				'uid' => 'fac_directory_client_id',
				'label' => 'Client ID',
				'section' => 'section_ym_settings',
				'type' => 'text',
				'placeholder' => '',
				'helper' => '<p>Please enter the client id which will passed through API call.</p>',
				'supplemental' => '',
				'default' => '',
                'width' => '50%',
			),
			array(
				'uid' => 'fac_directory_api_key',
				'label' => 'API Key',
				'section' => 'section_ym_settings',
				'type' => 'text',
				'placeholder' => '',
				'helper' => '<p>Please enter the API Key which will passed through API call.</p>',
				'supplemental' => '',
				'default' => '',
                'width' => '50%',
			),
			array(
				'uid' => 'fac_directory_api_password',
				'label' => 'API Password',
				'section' => 'section_ym_settings',
                'type' => 'text',
                'placeholder' => '',
                'helper' => '<p>Please enter the API Password which will passed through API call.</p>',
                'supplemental' => '',
                'default' => '',
                'width' => '50%',
			),
            array(
                'uid' => 'fac_directory_profile_fetch_batch',
                'label' => 'Number of profiles to fetch on each batch',
                'section' => 'section_ym_settings',
                'type' => 'select',
                'options' => $batch_list,
                'default' => array(1000),
                'width' => '100%',
            ),
            array(
                'uid' => 'fac_directory_is_batch',
                'label' => 'Batch process?',
                'section' => 'section_ym_settings',
                'type' => 'select',
                'options' => $options,
                'default' => array(1),
                'width' => '100%',
            ),
            array(
                'uid' => 'fac_directory_profile_fetch_info',
                'label' => 'YM Cron Info',
                'section' => 'section_ym_settings',
                'type' => 'html',
                'default' => array(),
                'width' => '100%',
            ),
			array(
				'uid' => 'fac_directory_profile_page',
				'label' => 'Profile page',
				'section' => 'section_page_settings',
				'type' => 'select',
				'options' => $page_list,
				'default' => array(),
                'width' => '100%',
			),
			array(
				'uid' => 'fac_directory_search_result_page',
				'label' => 'Search results page',
				'section' => 'section_page_settings',
				'type' => 'select',
				'options' => $page_list,
				'default' => array(),
                'width' => '100%',
			)
		);

		// Lets go through each field in the array and set it up
		foreach( $fields as $field ){
			add_settings_field( $field['uid'], $field['label'], array($this, 'fac_directory_field_callback'), 'fac-directory-options', $field['section'], $field );
			if ($field['type'] !== 'html') {
                register_setting('fac-directory-options', $field['uid']);
            }
		}
	}

	/**
	 * This handles all types of fields for the settings
	 *
	 * @since    1.0.0
	 */
	public function fac_directory_field_callback($arguments) {
		// Set our $value to that of whats in the DB
		$value = get_option( $arguments['uid'] );
		// Only set it to default if we get no value from the DB and a default for the field has been set
		if(!$value) {
			$value = isset($arguments['default']) ? $arguments['default'] : '';
		}
		// Lets do some setup based ont he type of element we are trying to display.
		switch( $arguments['type'] ){
			case 'text':
			case 'password':
			case 'number':
				printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" style="width:%5$s" />', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value, $arguments['width'] );
				break;
			case 'textarea':
				printf( '<textarea name="%1$s" id="%1$s" placeholder="%2$s" rows="5" cols="50" style="width:%4$s">%3$s</textarea>', $arguments['uid'], $arguments['placeholder'], $value, $arguments['width'] );
				break;
			case 'select':
			case 'multiselect':
				if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
					$attributes = '';
					$options_markup = '';
					foreach( $arguments['options'] as $key => $label ){
						$options_markup .= sprintf( '<option value="%s" %s>%s</option>', $key, isset($value[ array_search( $key, $value, true ) ]) ? selected( $value[ array_search( $key, $value, true ) ], $key, false ) : '', $label );
					}
					if( $arguments['type'] === 'multiselect' ){
						$attributes = ' multiple="multiple" ';
					}
					printf( '<select name="%1$s[]" id="%1$s" %2$s style="width:%4$s">%3$s</select>', $arguments['uid'], $attributes, $options_markup, $arguments['width'] );
				}
				break;
			case 'radio':
			case 'checkbox':
				if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
					$options_markup = '';
					$iterator = 0;
					foreach( $arguments['options'] as $key => $label ){
						$iterator++;
						$is_checked = '';
						// This case handles if there is only one checkbox and we don't have anything saved yet.
						if(isset($value[ array_search( $key, $value, true ) ])) {
							$is_checked = checked( $value[ array_search( $key, $value, true ) ], $key, false );
						} else {
							$is_checked = "";
						}
						// Lets build out the checkbox
						$options_markup .= sprintf( '<label for="%1$s_%6$s"><input id="%1$s_%6$s" name="%1$s[]" type="%2$s" value="%3$s" %4$s /> %5$s</label><br/>', $arguments['uid'], $arguments['type'], $key, $is_checked, $label, $iterator );
					}
					printf( '<fieldset>%s</fieldset>', $options_markup );
				}
				break;
            case 'html':
                if( $arguments['uid'] == 'fac_directory_profile_fetch_info' ){
                    $page_number = get_option( 'fac_directory_current_page_number', 1 );
                    $timestamp = get_option('fac_directory_profiles_since', '');

                    $total_profiles = $this->getProfilesCount();
                    $total_profile_groups = $this->getProfileGroupCount();

                    printf( '<div class="last-fetch-time"><strong>Last Fetch Time: </strong> %1$s</div>', $timestamp );
                    printf( '<div class="last-fetch-page"><strong>Next Fetch Page number: </strong> %1$s</div>', $page_number );
                    printf( '<div class="total-profiles"><strong>Total Member Profiles: </strong> %1$s</div>', $total_profiles );
                    printf( '<div class="total-profile-groups"><strong>Total Member Groups: </strong> %1$s</div>', $total_profile_groups );
                }
		}

		// If there is helper text, lets show it.
		if( array_key_exists('helper', $arguments) && $helper = $arguments['helper']) {
			printf( '<span class="helper"> %s</span>', $helper );
		}

		// If there is supplemental text lets show it.
		if( array_key_exists('supplemental', $arguments) && $supplemental = $arguments['supplemental'] ){
			printf( '<p class="description">%s</p>', $supplemental );
		}
	}


	/**
	 * Admin Notice
	 *
	 * This displays the notice in the admin page for the user
	 *
	 * @since    1.0.0
	 */
	public function admin_notice($message) { ?>
		<div class="notice notice-success is-dismissible">
			<p><?php echo($message); ?></p>
		</div><?php
	}

	/**
	 * This handles setting up the rewrite rules for fac directory
	 *
	 * @since    1.0.0
	 */
	public function fac_directory_setup_rewrites() {
		//
		$url_slug = 'fac-directory';

		// Lets setup our rewrite rules
		add_rewrite_rule( $url_slug . '/?$', 'index.php?custom_plugin=index', 'top' );
		//add_rewrite_rule( $url_slug . '/page/([0-9]{1,})/?$', 'index.php?custom_plugin=items&custom_plugin_paged=$matches[1]', 'top' );
		//add_rewrite_rule( $url_slug . '/([a-zA-Z0-9\-]{1,})/?$', 'index.php?custom_plugin=detail&custom_plugin_vehicle=$matches[1]', 'top' );

		// Lets flush rewrite rules on activation
		flush_rewrite_rules();
	}

    /**
     * Handles form submission
     *
     * @since    1.0.0
     */
    public function fac_directory_form_response() {
        if( isset( $_POST['fac_directory_fetch_data_form_nonce'] ) && wp_verify_nonce( $_POST['fac_directory_fetch_data_form_nonce'], 'fac_directory_fetch_data_form_nonce') ) {
            /**
             * The class responsible for fetch the member profiles from remote server
             */
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fac-directory-fetch-data.php';

            $client_id = get_option('fac_directory_client_id', '');
            $api_key = get_option('fac_directory_api_key', '');
            $api_password = get_option('fac_directory_api_password', '');
            $fetch_time_selection = isset($_POST['fetch_time']) ? $_POST['fetch_time'] : 6; // Since 7 days
            $only_missing_profiles = isset($_POST['only_missing_profiles']) ? $_POST['only_missing_profiles'] : 0;
            $is_batch = get_option('fac_directory_is_batch', 0);

            if ($fetch_time_selection == 1) {
              $fetch_time = '1970-01-01T00:00:00';
            }
            elseif ($fetch_time_selection == 2) {
              $fetch_time = date('Y-m-d\T00:00:00');
            }
            elseif ($fetch_time_selection == 3) {
              $fetch_time = date('Y-m-d\T00:00:00', strtotime('-1 year'));
            }
            elseif ($fetch_time_selection == 4) {
              $fetch_time = date('Y-m-d\T00:00:00', strtotime('-6 months'));
            }
            elseif ($fetch_time_selection == 5) {
              $fetch_time = date('Y-m-d\T00:00:00', strtotime('-3 months'));
            }
            elseif ($fetch_time_selection == 6) {
              $fetch_time = date('Y-m-d\T00:00:00', strtotime('-7 days'));
            }
            elseif ($fetch_time_selection == 7) {
              $fetch_time = date('Y-m-d\T00:00:00', strtotime('-1 day'));
            }
            elseif ($fetch_time_selection == 8) {
              $fetch_time = get_option('fac_directory_profiles_since', '1970-01-01T00:00:00');
            }

            if (!empty($client_id) && !empty($api_key) && !empty($api_password)) {
                $max_time = ini_get("max_execution_time");
                set_time_limit(0);

                $fdFetchData = new Fac_Directory_Fetch_data($client_id, $api_key, $api_password);
                fac_directory_write_log('YM membership cron: Fetching time since: '.$fetch_time);
                $fdFetchData->fetchMemberData($fetch_time, $is_batch, $only_missing_profiles);

                /*
                  For Migration only
                  $fdFetchData->migrateProfileData();
                  $fdFetchData->migrateGroupData();
                */

                set_time_limit($max_time);

                // add the admin notice
                $admin_notice = "success";
            }
            else {
                // add the admin notice
                $admin_notice = "failed";
            }

            // redirect the user to the appropriate page
            $this->custom_redirect( $admin_notice, $_POST );

            exit;
        }
        else {
            wp_die( __( 'Invalid nonce specified', $this->plugin_name ), __( 'Error', $this->plugin_name ), array(
                'response' 	=> 403,
                'back_link' => 'admin.php?page=' . $this->plugin_name,
            ) );
        }
    }

    /**
     * Redirect
     *
     * @since    1.0.0
     */
    public function custom_redirect( $admin_notice, $response ) {
        wp_redirect( esc_url_raw( add_query_arg( array(
            'fac_directory_admin_add_notice' => $admin_notice,
            'fac_directory_response' => $response,
        ),
            admin_url('admin.php?page='. $this->plugin_name )
        ) ) );

    }


    /**
     * Print Admin Notices
     *
     * @since    1.0.0
     */
    public function print_plugin_admin_notices() {
        if ( isset( $_REQUEST['fac_directory_admin_add_notice'] ) ) {
            if( $_REQUEST['fac_directory_admin_add_notice'] === "success") {
                $html =	'<div class="notice notice-success is-dismissible"> 
							<p><strong>The request was successful. </strong></p><br>';
                $html .= '<pre>' . htmlspecialchars( print_r( $_REQUEST['fac_directory_response'], true) ) . '</pre></div>';
                echo $html;
            }

            // handle other types of form notices

        }
        else {
            return;
        }

    }

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wordpress_Custom_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wordpress_Custom_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/fac-directory-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wordpress_Custom_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wordpress_Custom_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/fac-directory-admin.js', array( 'jquery' ), $this->version, false );

	}

    /**
     * Cron job to fetch the group and profiles.
     *
     * @since    1.0.0
     */
    public function fac_directory_fetch_profiles() {
        /**
         * The class responsible for fetch the member profiles from remote server
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fac-directory-fetch-data.php';

        $client_id = get_option('fac_directory_client_id', '');
        $api_key = get_option('fac_directory_api_key', '');
        $api_password = get_option('fac_directory_api_password', '');

        if (!empty($client_id) && !empty($api_key) && !empty($api_password)) {
            $max_time = ini_get("max_execution_time");
            set_time_limit(0);

            $fdFetchData = new Fac_Directory_Fetch_data($client_id, $api_key, $api_password);
            $fdFetchData->fetchMemberData('', 0, 0);
            fac_directory_write_log('YM membership cron ran successfully on '. date('Y-m-d H:i:s'));

            set_time_limit($max_time);
        }
        else {
            fac_directory_write_log('YM membership cron failed to run on '. date('Y-m-d H:i:s') . '. Because API information was empty.');
        }
    }

    /**
     * Get the total Profile Count
     *
     * @return array|int
     */
    public function getProfilesCount() {
        global $wpdb;
        $table_name = $wpdb->prefix . "fd_member_profiles";

        $count = $wpdb->get_col("SELECT COUNT(*) as Count FROM {$table_name}");
        if (isset($count[0]) && empty($count[0])) {
            $count = 0;
        }

        return isset($count[0]) && !empty(isset($count[0])) ? $count[0] : 0;
    }

    /**
     * Get the total Profile Group Count
     *
     * @return array|int
     */
    public function getProfileGroupCount() {
        global $wpdb;
        $table_name = $wpdb->prefix . "fd_groups";

        $count = $wpdb->get_col("SELECT COUNT(*) as Count FROM {$table_name}");
        if (isset($count[0]) && empty($count[0])) {
            $count = 0;
        }

        return isset($count[0]) && !empty(isset($count[0])) ? $count[0] : 0;
    }

    /**
     * Initialize the batches.
     */
    function fac_directory_batch_processing_init() {
        /**
         * The class responsible for profile list in backend
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fac-directory-batch-process.php';

        $batch = new YM_Process_Profiles();
        WP_Batch_Processor::get_instance()->register( $batch );
    }

}
