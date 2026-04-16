<?php

// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Create a new table class that will extend the WP_List_Table
 */
class Fac_Directory_Profile_List_Table extends WP_List_Table {
	/**
	 * Class constructor
	 */
	public function __construct() {

	  parent::__construct( [
		'singular' => __( 'FAC Profile', 'fac-directory' ),
		'plural' => __( 'FAC Profiles', 'fac-directory' ),
		'ajax' => true
	  ] );
	}

	/**
	* Retrieve profiles data from the database
	*
	* @param int $per_page
	* @param int $page_number
	*
	* @return mixed
	*/
	public static function get_profiles( $per_page = 50, $offset = 0 ) {
	  global $wpdb;

      $orderby = !empty( $_REQUEST['orderby'] ) ? esc_sql( $_REQUEST['orderby'] ) : 'FirstName';
	  $order = !empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : 'ASC';
      //$offset         = ( $page_number * $per_page ) - $per_page;

      $is_member = '';
      $xcond = '';
      if( isset($_GET['is-member']) && $_GET['is-member'] != ''){
        $is_member = $_GET['is-member'];
        $xcond .= " AND IsMember = " . $is_member . " ";
      }

      $county_type = '';
      if( isset($_GET['county-type']) && $_GET['county-type'] != ''){
        $county_type = $_GET['county-type'];
        $xcond .= " AND EmployerName = '" . $county_type . "' ";
      }

      $member_type = '';
      if( isset($_GET['member-type']) && $_GET['member-type'] != ''){
        $member_type = $_GET['member-type'];
        $xcond .= " AND MemberTypeCode = '" . $member_type . "' ";
      }

      $is_approved = '';
      if( isset($_GET['is-approved']) && $_GET['is-approved'] != ''){
        $is_approved = $_GET['is-approved'];
        $xcond .= " AND Approved = " . $is_approved . " ";
      }

      $is_suspended = '';
      if( isset($_GET['is-suspended']) && $_GET['is-suspended'] != ''){
        $is_suspended = $_GET['is-suspended'];
        $xcond .= " AND Suspended = " . $is_suspended . " ";
      }

      $sql  = "SELECT * FROM wp_fd_member_profiles WHERE ProfileID != '' ${xcond} ORDER BY ${orderby} ${order} LIMIT ${per_page} OFFSET ${offset}  ";

      $results = $wpdb->get_results($sql);

      $data = array();
      foreach ( $results as $row) {
        $data[] = array(
            'ProfileID' => $row->ProfileID,
            'IsMember' => $row->IsMember == 1 ? 'Yes' : 'No',
            'WorkEmailAddress' => $row->WorkEmailAddress,
            'Prefix' => $row->Prefix,
            'Suffix' => $row->Suffix,
            'FirstName' => $row->FirstName,
            'MiddleName' => $row->MiddleName,
            'LastName' => $row->LastName,
            'EmployerName' => $row->EmployerName,
            'WorkTitle' => $row->WorkTitle,
            'MemberTypeCode' => $row->MemberTypeCode,
            'Approved' => $row->Approved == 1 ? 'Yes' : 'No',
            'Suspended' => $row->Suspended == 1 ? 'Yes' : 'No',
            'WorkAddressLine1' => $row->WorkAddressLine1 . !empty($row->WorkAddressLine2) ? ' '. $row->WorkAddressLine2 : '',
            'WorkAddressCity' => $row->WorkAddressCity,
            'WorkAddressLocation' => $row->WorkAddressLocation,
            'WorkAddressPostalCode' => $row->WorkAddressPostalCode,
            'WorkPhoneNumber' => $row->WorkPhoneNumber,
            'HeadshotImageURI' => !empty($row->HeadshotImageURI) ? '<img src="'.$row->HeadshotImageURI.'" width="40" height="40" />' : '',
            'Registered' => $row->Registered,
            'LastUpdated' => $row->LastUpdated,
        );
      }

	  return $data;
	}

    public function getCounties() {
      global $wpdb;

      $sql  = "SELECT EmployerName FROM wp_fd_member_profiles GROUP BY EmployerName ORDER BY EmployerName ASC ";
      $results = $wpdb->get_results($sql);
      $data = array();
      foreach ( $results as $row) {
        if (!empty($row->EmployerName)) {
          $data[] = $row->EmployerName;
        }
      }

      return $data;
    }

    public function getMemberTypes() {
      global $wpdb;

      $sql  = "SELECT MemberTypeCode FROM wp_fd_member_profiles GROUP BY MemberTypeCode ORDER BY MemberTypeCode ASC ";
      $results = $wpdb->get_results($sql);
      $data = array();
      foreach ( $results as $row) {
        if (!empty($row->MemberTypeCode)) {
          $data[] = $row->MemberTypeCode;
        }
      }

      return $data;
    }

	/**
	* Returns the count of records in the database.
	*
	* @return null|string
	*/
	public static function record_count() {
	  global $wpdb;

	  $is_member = '';
	  $xcond = '';
	  if( isset($_GET['is-member']) && $_GET['is-member'] != ''){
        $is_member = $_GET['is-member'];
        $xcond .= " AND IsMember = " . $is_member . " ";
	  }

      $county_type = '';
      if( isset($_GET['county-type']) && $_GET['county-type'] != ''){
        $county_type = $_GET['county-type'];
        $xcond .= " AND EmployerName = '" . $county_type . "' ";
      }

      $member_type = '';
      if( isset($_GET['member-type']) && $_GET['member-type'] != ''){
        $member_type = $_GET['member-type'];
        $xcond .= " AND MemberTypeCode = '" . $member_type . "' ";
      }

      $is_approved = '';
      if( isset($_GET['is-approved']) && $_GET['is-approved'] != ''){
        $is_approved = $_GET['is-approved'];
        $xcond .= " AND Approved = " . $is_approved . " ";
      }

      $is_suspended = '';
      if( isset($_GET['is-suspended']) && $_GET['is-suspended'] != ''){
        $is_suspended = $_GET['is-suspended'];
        $xcond .= " AND Suspended = " . $is_suspended . " ";
      }

      $count_query  = "SELECT COUNT(*) as count FROM wp_fd_member_profiles WHERE ProfileID != '' " . $xcond;
      $num = $wpdb->get_var($count_query);

	  return $num;
	}

	/**
	 * Text displayed when no profile is available
	 */
	public function no_items() {
	  _e( 'No profile information available.', 'fac-directory' );
	}

	/**
	* Method for name column
	*
	* @param array $item an array of DB data
	*
	* @return string
	*/
	function column_name( $item ) {
	  $title = '<strong>' . $item['name'] . '</strong>';

	  return $title;
	}

	/**
	* Render the bulk edit checkbox
	*
	* @param array $item
	*
	* @return string
	*/
	function column_cb( $item ) {
	  return sprintf(
	    '<input type="checkbox" name="bulk-export[]" value="%s" />', $item['ID']
	  );
	}

    /**
     * Prepare the items for the table to process
	 * Handles data query and filter, sorting, and pagination.
     *
     * @return Void
     */
    public function prepare_items() {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

	    // Process bulk action
	    $this->process_bulk_action();

        $perPage = 50;
        $currentPage = $this->get_pagenum();
        $totalItems = self::record_count();
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged'] -1) * 50) : 0;

        $this->_column_headers = array($columns, $hidden, $sortable);
	    $this->items = self::get_profiles( $perPage, $paged );

        // configure pagination
        $this->set_pagination_args(array(
            'total_items' => $totalItems, // total items defined above
            'per_page' => $perPage, // per page constant defined at top of method
            'total_pages' => ceil($totalItems / $perPage) // calculate pages count
        ));
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns() {
        $columns = array(
            'ProfileID'  		=> 'Profile ID',
            'IsMember'		=> 'Member?',
            'WorkEmailAddress'		=> 'Email Address',
            'Prefix'	=> 'Name Prefix',
            'Suffix'	=> 'Name Suffix',
            'FirstName'		=> 'First Name',
            'MiddleName'	=> 'Middle Name',
            'LastName'		=> 'Last Name',
            'EmployerName'		=> 'Employer',
            'WorkTitle'		    => 'Title',
            'MemberTypeCode'	=> 'Member Type',
            'Approved'		=> 'Approved?',
            'Suspended'		=> 'Suspended?',
            'WorkAddressLine1'	=> 'Address Line',
            'WorkAddressCity'		=> 'City',
            'WorkAddressLocation'	=> 'Location',
            'WorkAddressPostalCode'	=> 'Post code',
            'WorkPhoneNumber'		=> 'Phone',
            'HeadshotImageURI'		=> 'Image',
            'Registered'	=> 'Register Date',
            'LastUpdated'	=> 'last Updated',
        );

        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns() {
        return array();
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns() {
        return array(
            'ProfileID' => array('ProfileID', false),
            'IsMember' => array('IsMember', true),
            'WorkEmailAddress' => array('WorkEmailAddress', true),
            'Prefix' => array('Prefix', false),
            'Suffix' => array('Suffix', false),
            'FirstName' => array('FirstName', true),
            'MiddleName' => array('MiddleName', false),
            'LastName' => array('LastName', true),
            'EmployerName' => array('EmployerName', true),
            'WorkTitle' => array('WorkTitle', true),
            'MemberTypeCode' => array('MemberTypeCode', true),
            'Approved' => array('Approved', true),
            'Suspended' => array('Suspended', true),
            'WorkAddressLine1' => array('WorkAddressLine1', false),
            'WorkAddressCity' => array('WorkAddressCity', true),
            'WorkAddressLocation' => array('WorkAddressLocation', false),
            'WorkAddressPostalCode' => array('WorkAddressPostalCode', true),
            'WorkPhoneNumber' => array('WorkPhoneNumber', true),
            'HeadshotImageURI' => array('HeadshotImageURI', false),
            'Registered' => array('Registered', true),
            'LastUpdated' => array('LastUpdated', true),
        );
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name ) {
        switch( $column_name ) {
            case 'ProfileID':
            case 'IsMember':
            case 'WorkEmailAddress':
            case 'Prefix':
            case 'Suffix':
            case 'FirstName':
            case 'MiddleName':
            case 'LastName':
            case 'EmployerName':
            case 'WorkTitle':
            case 'MemberTypeCode':
            case 'Approved':
            case 'Suspended':
            case 'WorkAddressLine1':
            case 'WorkAddressCity':
            case 'WorkAddressLocation':
            case 'WorkAddressPostalCode':
            case 'WorkPhoneNumber':
            case 'HeadshotImageURI':
            case 'Registered':
            case 'LastUpdated':
                return $item[ $column_name ];

            default:
                return print_r( $item, true ) ;
        }
    }

    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data( $a, $b ) {
        // Set defaults
        $orderby = 'FirstName';
        $order = 'asc';

        // If orderby is set, use this as the sort column
        if (!empty($_GET['orderby'])) {
          $orderby = $_GET['orderby'];
        }

        // If order is set use this as the order
        if(!empty($_GET['order'])) {
            $order = $_GET['order'];
        }

        $result = strcmp( $a[$orderby], $b[$orderby] );

        if ($order === 'asc') {
            return $result;
        }

        return -$result;
    }

	public function extra_tablenav( $which ) {
        $move_on_url_is_member = '';
        $move_on_url_county_type = '';
        $move_on_url_member_type = '';
        $move_on_url_is_approved = '';
        $move_on_url_is_suspended = '';

        if (isset($_GET['is-member']) && $_GET['is-member'] != '') {
            $move_on_url_is_member = '&is-member=' . $_GET['is-member'];
        }
        if (isset($_GET['county-type']) && $_GET['county-type'] != '') {
            $move_on_url_county_type = '&county-type=' . $_GET['county-type'];
        }
        if (isset($_GET['member-type']) && $_GET['member-type'] != '') {
          $move_on_url_member_type = '&member-type=' . $_GET['member-type'];
        }
        if (isset($_GET['is-approved']) && $_GET['is-approved'] != '') {
          $move_on_url_is_approved = '&is-approved=' . $_GET['is-approved'];
        }
        if (isset($_GET['is-suspended']) && $_GET['is-suspended'] != '') {
          $move_on_url_is_suspended = '&is-suspended=' . $_GET['is-suspended'];
        }

		if ( $which == "top" ){
			?>
			<div class="alignleft actions bulkactions">
				<select name="is-member" class="is-member-filter">
					<option value="<?php echo $move_on_url_county_type.$move_on_url_member_type.$move_on_url_is_approved.$move_on_url_is_suspended; ?>&is-member=">Member?</option>
					<?php
					  $selected_1 = '';
					  if( isset($_GET['is-member']) && $_GET['is-member'] == 1 ){
						$selected_1 = ' selected = "selected"';
					  }
					  $selected_2 = '';
					  if( isset($_GET['is-member']) && $_GET['is-member'] == 0 ){
						$selected_2 = ' selected = "selected"';
					  }
					?>
					<option value="<?php echo $move_on_url_county_type.$move_on_url_member_type.$move_on_url_is_approved.$move_on_url_is_suspended; ?>&is-member=1" <?php echo $selected_1; ?>>Yes</option>
					<option value="<?php echo $move_on_url_county_type.$move_on_url_member_type.$move_on_url_is_approved.$move_on_url_is_suspended; ?>&is-member=0" <?php echo $selected_2; ?>>No</option>
				</select>
				<select name="county-type" class="county-type-filter">
					<option value="<?php echo $move_on_url_is_member.$move_on_url_member_type.$move_on_url_is_approved.$move_on_url_is_suspended; ?>&county-type=">County</option>
					<?php
                      $counties = $this->getCounties();
                      foreach ($counties as $county) {
                        if( isset($_GET['county-type']) && $_GET['county-type'] == $county ){
                          $selected = ' selected = "selected"';
                        }
                        else {
                          $selected = '';
                        }
                        ?>
                        <option value="<?php echo $move_on_url_is_member.$move_on_url_member_type.$move_on_url_is_approved.$move_on_url_is_suspended; ?>&county-type=<?php echo $county; ?>" <?php echo $selected; ?>><?php echo $county ?></option>
                        <?php
                      }
					?>
				</select>
                <select name="member-type" class="member-type-filter">
                    <option value="<?php echo $move_on_url_is_member.$move_on_url_county_type.$move_on_url_is_approved.$move_on_url_is_suspended ?>&member-type=">Member Type</option>
                    <?php
                    $member_types = $this->getMemberTypes();
                    foreach ($member_types as $member_type) {
                        if( isset($_GET['member-type']) && $_GET['member-type'] == $member_type ){
                            $selected = ' selected = "selected"';
                        }
                        else {
                            $selected = '';
                        }
                        ?>
                        <option value="<?php echo $move_on_url_is_member.$move_on_url_county_type.$move_on_url_is_approved.$move_on_url_is_suspended ?>&member-type=<?php echo $member_type; ?>" <?php echo $selected; ?>><?php echo $member_type ?></option>
                        <?php
                    }
                    ?>
                </select>
                <select name="is-approved" class="is-approved-filter">
                    <option value="<?php echo $move_on_url_is_member.$move_on_url_county_type.$move_on_url_member_type.$move_on_url_is_suspended; ?>&is-approved=">Approved?</option>
                    <?php
                    $selected_1 = '';
                    if( isset($_GET['is-approved']) && $_GET['is-approved'] == 1 ){
                        $selected_1 = ' selected = "selected"';
                    }
                    $selected_2 = '';
                    if( isset($_GET['is-member']) && $_GET['is-member'] == 0 ){
                        $selected_2 = ' selected = "selected"';
                    }
                    ?>
                    <option value="<?php echo $move_on_url_is_member.$move_on_url_county_type.$move_on_url_member_type.$move_on_url_is_suspended; ?>&is-approved=1" <?php echo $selected_1; ?>>Yes</option>
                    <option value="<?php echo $move_on_url_is_member.$move_on_url_county_type.$move_on_url_member_type.$move_on_url_is_suspended; ?>&is-approved=0" <?php echo $selected_2; ?>>No</option>
                </select>
                <select name="is-suspended" class="is-suspended-filter">
                    <option value="<?php echo $move_on_url_is_member.$move_on_url_county_type.$move_on_url_member_type.$move_on_url_is_approved; ?>&is-suspended=">Suspended?</option>
                    <?php
                    $selected_1 = '';
                    if( isset($_GET['is-suspended']) && $_GET['is-suspended'] == 1 ){
                        $selected_1 = ' selected = "selected"';
                    }
                    $selected_2 = '';
                    if( isset($_GET['is-suspended']) && $_GET['is-suspended'] == 0 ){
                        $selected_2 = ' selected = "selected"';
                    }
                    ?>
                    <option value="<?php echo $move_on_url_is_member.$move_on_url_county_type.$move_on_url_member_type.$move_on_url_is_approved; ?>&is-suspended=1" <?php echo $selected_1; ?>>Yes</option>
                    <option value="<?php echo $move_on_url_is_member.$move_on_url_county_type.$move_on_url_member_type.$move_on_url_is_approved; ?>&is-suspended=0" <?php echo $selected_2; ?>>No</option>
                </select>
			</div>
			<div class="alignright actions">
				<form class="alignleft actions bulkactions" action="<?php echo admin_url('admin-post.php'); ?>" method="post">
				  <input type="hidden" name="action" value="profile_export">
				  <!--<input type="submit" value="Export to CSV" class="button action"> -->
				</form>
			</div>
			<?php
		}
		if ( $which == "bottom" ){
			//not showing now
		}
	}
}

