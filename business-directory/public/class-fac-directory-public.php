<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/shafiqhossain
 * @since      1.0.0
 *
 * @package    Fac_Directory
 * @subpackage Fac_Directory/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Fac_Directory
 * @subpackage Fac_Directory/public
 * @author     Shafiq Hossain <md.shafiq.hossain@gmail.com>
 */
class Fac_Directory_Public {

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
	 * Global array of vehicles.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $vehicles    Global array of vehicles.
	 */
	private $vehicles;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

        // Add actions for shortcodes
        add_shortcode( 'dir_search_form' , array($this, 'fac_directory_dir_search_form') );
        add_shortcode( 'dir_search_results' , array($this, 'fac_directory_dir_search_results') );
        add_shortcode( 'dir_profile_info' , array($this, 'fac_directory_dir_profile_info') );
	}

    /**
     * Callback function for displaying shortcode "dir_search_form".
     *
     * @since    1.0.0
     */
    public function fac_directory_dir_search_form($attr){
        $args = shortcode_atts( array(
          'url' => '#',
          'headline' => 'FAC ONLINE DIRECTORY',
          'description' => 'Use our Directory to search for Florida County Profiles.'
        ), $attr );

        $output  = '<section class="fac-directory-contents">';
        if (!empty($args['headline'])) {
            $output .= '  <h2 class="online-directory">' . $args['headline'] . '</h2>';
            $output .= '  <hr class="directory-hr">';
        }
        $output .= '  <div class="dir-wrapper">';
        $output .= '    <div id="fac-search-content-home" class="search-header">';
        if (!empty($args['description'])) {
            $output .= '      <div class="search-header-title">' . $args['description'] . '</div>';
        }

        $output .= $this->fac_directory_get_search_form();
        $output .= $this->fac_directory_get_county_list();

		$output .= '        </div><!-- search-header -->';
        $output .= '      </div><!-- dir-wrapper -->';
        $output .= '  </section>';

        return $output;
    }


    /**
     * Get search form
     *
     * @since    1.0.0
     */
    public function fac_directory_get_search_form() {
        // Get the search results page slug
        $search_results_slug = $this->getSearchResultsPageSlug();

        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';

        $output = '      <div class="directory-main-search">';
        $output .= '	    <form action="/' . $search_results_slug . '" class="search-keywords-wrapper">';
        $output .= '	    <div class="keyword-wrapper">';
        $output .= '	      <div class="keyword-textfield-wrapper">';
        $output .= '		    <div class="header-title">Keyword Search:</div>';
        $output .= '		    <input id="search-filter-keyword" name="keyword" type="text" value="'.$keyword.'" class="advanced-text-input" placeholder="Keyword Search">';
        $output .= '	      </div>';
        $output .= '	      <div class="action-button-wrapper">';
        $output .= '		    <input id="search-keyword-button" type="submit" class="search-button" value="Submit">';
        $output .= '	      </div>';
        $output .= '	    </div>';
        $output .= '	    </form>';

        $output .= '	    <div class="county-and-job-search-wrapper">';
        $output .= '	      <div class="county-search-wrapper">';
        $output .= '		    <div class="header-title">County Search:</div>';
        $output .= '            <div class="county-filter-wrapper">';
        $output .= '		      <select class="county-search-dropdown" id="county-dropdown">';
        $output .= '			    <option value="">Select a County</option>';

        $counties = $this->get_dropdown_counties();
        foreach($counties as $county) {
            $output .= '    <option value="/' . $search_results_slug . '?county=' . $county . '">' . $county . '</option>';
        }

        $output .= '		      </select>';
        $output .= '	        </div>';
        $output .= '	      </div><!-- county-search-wrapper -->';
        $output .= '	      <div class="job-search-wrapper">';
        $output .= '		      <div class="header-title">Job, Position or Title Department:</div>';
        $output .= '              <div class="job-filter-wrapper">';
        $output .= '			    <select class="job-search-dropdown" id="job-dropdown">';
        $output .= '				  <option value="0">Select a Job, Position, or Department</option>';

        $groups = $this->get_dropdown_groups();
        foreach ($groups as $key => $name) {
            $output .= '    <option value="/'. $search_results_slug . '?group='. $key . '">' . $name . '</option>';
        }

        $output .= '			    </select>';
        $output .= '		      </div>';
        $output .= '		    </div><!-- job-search-wrapper -->';
        $output .= '	      </div><!-- county-and-job-search-wrapper -->';
        $output .= '	    </div><!-- directory-main-search -->';

        return $output;
    }

    /**
     * Get county list
     *
     * @since    1.0.0
     */
    public function fac_directory_get_county_list() {
        // Get the search results page slug
        $search_results_slug = $this->getSearchResultsPageSlug();

        $output = '	      <div class="counties-list">';
        $output .= '		    <ul class="counties">';

        $counties = $this->get_dropdown_counties();
        foreach ($counties as $county) {
            $output .= '    <li class="county-item"><a href="/' . $search_results_slug . '?county=' . $county . '">' . $county . '</a></li>';
        }

        $output .= '            </ul>';
        $output .= '	      </div><!-- counties-list -->';

        return $output;
    }

    /**
     * Callback function for displaying shortcode "dir_search_results".
     *
     * @since    1.0.0
     */
    public function fac_directory_dir_search_results($attr){
        global $wpdb;

        $args = shortcode_atts( array(
            'headline' => '',
            'description' => '.'
        ), $attr );

        $county = isset($_GET['county']) ? $_GET['county'] : '';
        $group = isset($_GET['group']) ? $_GET['group'] : '';
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';

        $group_name = '';
        if (!empty($group)) {
          $group_name = $this->getGroupName($group);
        }

        $search_by = '';
        if (!empty($county)) {
            if (!empty($search_by)) $search_by .= ', ';
            $search_by .= $county;
        }
        if (!empty($group_name)) {
            if (!empty($search_by)) $search_by .= ', ';
            $search_by .= $group_name;
        }
        if (!empty($keyword)) {
            if (!empty($search_by)) $search_by .= ', ';
            $search_by .= $keyword;
        }

        $query              = "SELECT ProfileID FROM wp_fd_member_profiles";
        $total = $this->get_records_count($keyword, $group, $county);

        $items_per_page     = 20;
        $page               = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
        $offset             = ( $page * $items_per_page ) - $items_per_page;
        $records            = $this->get_profiles($keyword, $group, $county, $offset, $items_per_page);
        $totalPage          = ceil($total / $items_per_page);

        $output  = '<section class="fac-directory-contents">';
        $output .= $this->fac_directory_get_search_form();

        $output .= '<div class="fac-directory-search-results-wrapper">';
        $output .= '  <div class="search-results-headline">Search Results for: '.$search_by.'</div>';

        $profile_count = 0;
        $blank_profile_uri = plugin_dir_url(__FILE__) . 'img/blank_profile.jpg';
        foreach($records as $row) {
            ++$profile_count;
            $output .= '<div class="dirIndex0 dirType1">';
            $output .= '    <div id="ID-' . $row['ProfileID'] . '" class="dirResultsBox ">';
            if ($row['MemberTypeCode'] == 'Commissioner' && !empty($row['HeadshotImageURI']) && (stripos($row['HeadshotImageURI'], 'mugshot1.gif') === false) ) {
                $output .= '      <div class="dirEntryTitle dirEntryElement">';
                $output .= '        <div class="commImage">';
                if (!empty($row['HeadshotImageURI'])) {
                    $output .= '          <img id="guid-' . $row['ProfileID'] . '" data-src="' . $row['HeadshotImageURI'] . '" src="' . $row['HeadshotImageURI'] . '">';
                }
                else {
                    $output .= '          <img id="guid-blank-profile-' . $profile_count . '" data-src="' . $blank_profile_uri . '" src="' . $blank_profile_uri . '">';
                }
                $output .= '        </div>';
                $output .= '      </div><!-- dirEntryTitle -->';
            }
            $output .= '      <div class="dirEntryCounty dirEntryElement">';
            $output .= '        <div class="dirEntryName">' . $row['LastName'] . ', ' . $row['FirstName'] . ' <br></div>';
            $output .= '        <div class="dirEntryPosition">';
            $output .= '          ' . $row['WorkTitle'];
            $output .= '          <br>';
            $output .= '          <span style="font-style:italic">';
            $output .= '          </span>';
            $output .= '        </div>';
            $output .= '      </div><!-- dirEntryCounty -->';
            $output .= '      <div class="dirEntryAddress dirEntryElement">';
            $output .= '        ' . $row['EmployerName'] . '<br>';
            $output .= '        ' . $row['WorkAddressLine1'] . (!empty($row['WorkAddressLine2']) ? $row['WorkAddressLine2'] : '');
            $output .= '        <br>';
            $output .= '        ' . $row['WorkAddressCity'] . ', '. $row['WorkAddressLocation'] . ' ' . $row['WorkAddressPostalCode'];
            $output .= '      </div><!-- dirEntryAddress -->';
            $output .= '      <div class="dirEntryPhone dirEntryElement">';
            $output .= '            <span class="dirPhone">' . $row['WorkPhoneNumber'] . '</span>';
            $output .= '            <br>';
            $output .= '            <span class="dirEmail">';
            $output .= '                <a href="mailto:'.$row['WorkEmailAddress'].'" class="emailLink">';
            $output .= '                    ' . $row['WorkEmailAddress']; // '&nbsp;<i class="fas fa-envelope"></i>';
            $output .= '                </a>';
            $output .= '            </span>';
            $output .= '      </div><!-- dirEntryPhone -->';
            $output .= '    </div><!-- dirResultsBox -->';
            $output .= '</div><!-- dirIndex0 dirType1 -->';
        }

        $pagination      = "";
        if ($totalPage > 1) {
            $pagination     =  '<div class="fac-profile-pagination"><span>Page '.$page.' of '.$totalPage.'</span>' . paginate_links( array(
                    'base' => add_query_arg( 'cpage', '%#%' ),
                    'format' => '',
                    'prev_text' => __('&laquo;'),
                    'next_text' => __('&raquo;'),
                    'total' => $totalPage,
                    'current' => $page
                )).'</div>';
        }


        $output .= $pagination;
        $output .= '  </div> <!-- fac-directory-search-results-wrapper -->';
        $output .= '</section>';

        return $output;
    }

    /**
     * Callback function for displaying shortcode "dir_profile_info".
     *
     * @since    1.0.0
     */
    public function fac_directory_dir_profile_info($attr){
        $args = shortcode_atts( array(
            'headline' => '',
            'description' => '.'
        ), $attr );

    }

    /**
     * Get the dropdown group list, which is little different from origin groups
     *
     * @return array
     */
	public function get_dropdown_groups() {
      global $wpdb;

      $results = $wpdb->get_results("SELECT GroupID, GroupName FROM wp_fd_groups ORDER BY GroupName ASC");
      $data = array();
      foreach($results as $row){
        if(!empty($row->GroupName)) {
            $data[$row->GroupID] = $row->GroupName;
        }
      }

      return $data;
	}

    /**
     * Get the county list
     *
     * @return array
     */
	public function get_dropdown_counties() {
        global $wpdb;

        $results = $wpdb->get_results("SELECT EmployerName FROM wp_fd_member_profiles GROUP BY EmployerName ORDER BY EmployerName ASC");
        $data = array();
        foreach($results as $row){
          if (!empty($row->EmployerName)) {
            $data[$row->EmployerName] = $row->EmployerName;
          }
        }

        return $data;
	}

    /**
     * Get the group name
     *
     * @param int $group
     * @return string
     */
    public function getGroupName($GroupID = 0) {
        global $wpdb;

        $result = $wpdb->get_row("SELECT GroupID, GroupName FROM wp_fd_groups WHERE GroupID = ${GroupID}");
        if ($result) {
            return $result->GroupName;
        }
        else {
            return '';
        }
    }

    /**
     * Get the list of groups
     *
     * @return array
     */
	public function get_groups() {
        global $wpdb;

        $results = $wpdb->get_results("SELECT GroupID, GroupName FROM wp_fd_groups ORDER BY GroupName ASC");
        $data = array();
        foreach($results as $row){
            if (!empty($row->GroupName)) {
                $data[$row->GroupID] = $row->GroupName;
            }
        }

        return $data;
	}

    /**
     * Get the list of counties
     *
     * @return array
     */
	public function get_counties() {
        global $wpdb;

        $results = $wpdb->get_results("SELECT EmployerName FROM wp_fd_member_profiles GROUP BY EmployerNAme ORDER BY EmployerName ASC");
        $data = array();
        foreach($results as $row){
            if (!empty($row->EmployerName)) {
                $data[$row->EmployerName] = $row->EmployerName;
            }
        }

        return $data;
	}

	public function get_profiles($keyword = '', $group = '', $county = '', $offset = 0, $items_per_page = 20) {
        global $wpdb;

        $sql  = "SELECT * FROM wp_fd_member_profiles ";

        $sqls = [];

        $sql1  = "";
        if (!empty($keyword)) {
            $sql1  .= "(";
            $sql1  .= "FirstName LIKE '%" . $keyword ."%' ";
            $sql1  .= "OR ";
            $sql1  .= "MiddleName LIKE '%" . $keyword ."%' ";
            $sql1  .= "OR ";
            $sql1  .= "LastName LIKE '%" . $keyword ."%' ";
            $sql1  .= "OR ";
            $sql1  .= "EmployerName LIKE '%" . $keyword ."%' ";
            $sql1  .= "OR ";
            $sql1  .= "WorkAddressCity LIKE '%" . $keyword ."%' ";
            $sql1  .= "OR ";
            $sql1  .= "WorkTitle LIKE '%" . $keyword ."%' ";
            $sql1  .= ")";

            $sqls[] = $sql1;
        }

        $sql2   = "";
        if (!empty($county)) {
            $sql2  .= "(";
            $sql2  .= "EmployerName LIKE '%" . $county ."%'";
            $sql2  .= ")";

            $sqls[] = $sql2;
        }

        $sql3  = "";
        if (!empty($group)) {
            $profile_ids = $this->getGroupProfileIDs($group);

            foreach($profile_ids as $id) {
                if (!empty($sql3)) {
                    $sql3 .= "OR ";
                }
                $sql3 .= "ProfileID = '" . $id . "' ";
            }
            $sql3  = "(" . $sql3 . ")";

            $sqls[] = $sql3;
        }


        $sql .= "WHERE Approved = 1 ";
        $sql .= "AND Suspended = 0 ";
        if (count($sqls)) {
            $sql .= "AND " . implode(" OR ", $sqls)." ";
        }
        $sql .= "ORDER BY MemberTypeCode ASC, LastName ASC, FirstName ASC ";
        $sql  .= "LIMIT ${offset}, ${items_per_page}";
        $results = $wpdb->get_results($sql, ARRAY_A);
        $data = array();
        foreach($results as $row){
            $data[] =  $row;
        }

        return $data;
	}

    public function get_records_count($keyword = '', $group = '', $county = '') {
        global $wpdb;

        $sql  = "SELECT * FROM wp_fd_member_profiles ";

        $sqls = [];

        $sql1  = "";
        if (!empty($keyword)) {
            $sql1  .= "(";
            $sql1  .= "FirstName LIKE '%" . $keyword ."%' ";
            $sql1  .= "OR ";
            $sql1  .= "MiddleName LIKE '%" . $keyword ."%' ";
            $sql1  .= "OR ";
            $sql1  .= "LastName LIKE '%" . $keyword ."%' ";
            $sql1  .= "OR ";
            $sql1  .= "EmployerName LIKE '%" . $keyword ."%' ";
            $sql1  .= "OR ";
            $sql1  .= "WorkAddressCity LIKE '%" . $keyword ."%' ";
            $sql1  .= "OR ";
            $sql1  .= "WorkTitle LIKE '%" . $keyword ."%' ";
            $sql1  .= ")";

            $sqls[] = $sql1;
        }

        $sql2   = "";
        if (!empty($county)) {
            $sql2  .= "(";
            $sql2  .= "EmployerName LIKE '%" . $county ."%'";
            $sql2  .= ")";

            $sqls[] = $sql2;
        }

        $sql3  = "";
        if (!empty($group)) {
            $profile_ids = $this->getGroupProfileIDs($group);

            foreach($profile_ids as $id) {
                if (!empty($sql3)) {
                    $sql3 .= "OR ";
                }
                $sql3 .= "ProfileID = '" . $id . "' ";
            }
            $sql3  = "(" . $sql3 . ")";

            $sqls[] = $sql3;
        }


        $sql .= "WHERE Approved = 1 ";
        $sql .= "AND Suspended = 0 ";
        if (count($sqls)) {
            $sql .= "AND " . implode(" OR ", $sqls)." ";
        }
        $sql .= "ORDER BY FirstName ASC, LastName ASC ";

        $total_query = "SELECT COUNT(1) FROM (${sql}) AS count_table";
        $total = $wpdb->get_var( $total_query );

        return $total;
    }

    public function getGroupProfileIDs($group = 0) {
        global $wpdb;

        $sql = 'SELECT a.ProfileID FROM wp_fd_member_groups a ';
        $sql .= 'INNER JOIN wp_fd_groups b ON a.GroupID = b.GroupID ';
        $sql .= 'WHERE b.GroupID = ' . $group;

        $results = $wpdb->get_results($sql);
        $data = array();
        foreach ($results as $row){
            $data[] =  $row->ProfileID;
        }

        return $data;
    }

    public function getSearchResultsPageSlug() {
        $result_page_slug = '';

        $search_result_page_id = get_option('fac_directory_search_result_page', 0);
        if (is_array($search_result_page_id)) {
            $search_result_page_id = reset($search_result_page_id);
        }
        $post = get_post($search_result_page_id);
        $result_page_slug = $post->post_name;
        if (empty($result_page_slug)) {
            $result_page_slug = 'directory-results';
        }

        return $result_page_slug;
    }


	/**
     * Custom Plugin Rewrites
     *
     * @since    1.0.0
     */
    public function register_fac_directory_redirect() {
        // Check if we have the custom plugin query, if so lets display the page
        if (get_query_var('fac_directory')) {
            add_filter('template_include', function () {
                return plugin_dir_path(__FILE__) . 'partials/wordpress-fac-directory-public-index.php';
            });
		}
		// Check if its a detail page for a vehicle
        if (get_query_var('fac_directory') && get_query_var('fac_directory_results')) {
            add_filter('template_include', function () {
                return plugin_dir_path(__FILE__) . 'partials/wordpress-fac-directory-public-detail.php';
            });
        }
	}

	/**
     * Register Query Values for Custom Plugin
     *
     * Filters that are needed for rendering the custom plugin page
     *
     * @since    1.0.0
     */
    public function register_query_values($vars) {
        // Equivalent to array_push($vars, 'custom_plugin', ..)
		$vars[] = 'fac_directory';
		$vars[] = 'fac_directory_paged';
        $vars[] = 'fac_directory_results';

        return $vars;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/fac-directory-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/fac-directory-public.js', array( 'jquery' ), $this->version, false );
	}

}
