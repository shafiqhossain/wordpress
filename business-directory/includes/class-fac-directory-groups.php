<?php

// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Create a new table class that will extend the WP_List_Table
 */
class Fac_Directory_Groups_List_Table extends WP_List_Table {
	/**
	 * Class constructor
	 */
	public function __construct() {

	  parent::__construct( [
		'singular' => __( 'FAC Group', 'fac-directory' ),
		'plural' => __( 'FAC Groups', 'fac-directory' ),
		'ajax' => true
	  ] );
	}

	/**
	* Retrieve groups data from the database
	*
	* @param int $per_page
	* @param int $page_number
	*
	* @return mixed
	*/
	public static function get_fac_groups( $per_page = 50, $offset = 0 ) {
	  global $wpdb;

	  $orderby = !empty( $_REQUEST['orderby'] ) ? esc_sql( $_REQUEST['orderby'] ) : 'GroupName';
	  $order = !empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : 'ASC';

      $sql  = "SELECT * FROM wp_fd_groups ORDER BY ${orderby} ${order} LIMIT ${per_page} OFFSET ${offset} ";

      $results = $wpdb->get_results($sql);
	  $data = array();
	  foreach ( $results as $row) {
          $data[] = array(
              'GroupID' => $row->GroupID,
              'GroupName' => $row->GroupName,
              'GroupCode' => $row->GroupCode,
          );

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

      $count_query  = "SELECT COUNT(*) as count FROM wp_fd_groups";
      $num = $wpdb->get_var($count_query);

	  return $num;
	}

	/**
	 * Text displayed when no profile data is available
	 */
	public function no_items() {
	  _e( 'No group information available.', 'fac-directory' );
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
	    '<input type="checkbox" name="bulk-export[]" value="%s" />', $item['id']
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

        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );

        $this->_column_headers = array($columns, $hidden, $sortable);
	    $this->items = self::get_fac_groups( $perPage, $paged );
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns() {
        $columns = array(
            'GroupID'                => 'ID',
            'GroupName'          => 'Group Name',
            'GroupCode'         => 'Group Code',
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
          'GroupID'                => array('GroupID', true),
          'GroupName'          => array('GroupName', true),
          'GroupCode'         => array('GroupCode', true),
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
            case 'GroupID':
            case 'GroupName':
            case 'GroupCode':
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
        $orderby = 'GroupName';
        $order = 'asc';

        // If orderby is set, use this as the sort column
        if(!empty($_GET['orderby'])) {
            $orderby = $_GET['orderby'];
        }

        // If order is set use this as the order
        if(!empty($_GET['order'])) {
            $order = $_GET['order'];
        }

        $result = strcmp( $a[$orderby], $b[$orderby] );

        if($order === 'asc') {
            return $result;
        }

        return -$result;
    }
}





