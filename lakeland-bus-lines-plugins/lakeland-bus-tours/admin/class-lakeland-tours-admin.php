<?php

/*************************** LOAD THE BASE CLASS *******************************
 *******************************************************************************
 * The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary. In this tutorial, we are
 * going to use the WP_List_Table class directly from WordPress core.
 *
 * IMPORTANT:
 * Please note that the WP_List_Table class technically isn't an official API,
 * and it could change at some point in the distant future. Should that happen,
 * I will update this plugin with the most current techniques for your reference
 * immediately.
 *
 * If you are really worried about future compatibility, you can make a copy of
 * the WP_List_Table class (file path is shown just below) to use and distribute
 * with your plugins. If you do that, just remember to change the name of the
 * class to avoid conflicts with core.
 *
 * Since I will be keeping this tutorial up-to-date for the foreseeable future,
 * I am going to work with the copy of the class provided in WordPress core.
 */
if(!class_exists('WP_List_Table')){
	require_once(ABSPATH . 'wp-admin/includes/template.php' );
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}





/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    lakeland_bus_tours
 * @subpackage lakeland_bus_tours/admin
 * @author     Shafiq Hossain <md.shafiq.hossain@gmail.com>
 */
class LakeLand_Tours_Admin extends WP_List_Table{

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

	public $sub_query;

    public $table_tours;
    public $table_tour_details;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version ) {
		global $status, $page, $wpdb;
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->sub_query = '';

        $this->table_tours = $wpdb->prefix . 'lakeland_tours';
        $this->table_tour_details = $wpdb->prefix . 'tour_details';

		//Set parent defaults
        parent::__construct( array(
            'singular'  => 'Tour Managment ',     //singular name of the listed records
            'plural'    => 'Tour Managment',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );

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
		 * defined in LakeLand_Tours_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The LakeLand_Tours_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
        wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/lakeland-tours-admin.css', array(), $this->version, 'all' );


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
		 * defined in LakeLand_Tours_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The LakeLand_Tours_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
        wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/lakeland-tours-admin.js', array( 'jquery', 'jquery-ui-datepicker' ), $this->version, false );

	}



    /** ************************************************************************
     * Recommended. This method is called when the parent class can't find a method
     * specifically build for a given column. Generally, it's recommended to include
     * one method for each column you want to render, keeping your package class
     * neat and organized. For example, if the class needs to process a column
     * named 'title', it would first see if a method named $this->column_title()
     * exists - if it does, that method will be used. If it doesn't, this one will
     * be used. Generally, you should try to use custom column methods as much as
     * possible.
     *
     * Since we have defined a column_title() method later on, this method doesn't
     * need to concern itself with any column with a name of 'title'. Instead, it
     * needs to handle everything else.
     *
     * For more detailed insight into how columns are handled, take a look at
     * WP_List_Table::single_row_columns()
     *
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     **************************************************************************/
    function column_default($item, $column_name){
        switch($column_name){
            case 'title':
                return stripslashes($item[$column_name]);
            case 'date':
                return $item['from_date'] . '-' . $item['to_date'] ;
            case 'from_date':
                return $item[$column_name];
            case 'month':
               return  date('F', strtotime('2015-' . $item[$column_name] . '-01' ));

            case 'expiry':
                 return date('Y-m-d H:i A', strtotime($item['expiry']));
            case 'status':
                $status = array('1' => 'Limited Availability', '2' => 'Sold Out');
                return $status[$item[$column_name]];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }


     /** ************************************************************************
     * Recommended. This is a custom column method and is responsible for what
     * is rendered in any column with a name/slug of 'title'. Every time the class
     * needs to render a column, it first looks for a method named
     * column_{$column_title} - if it exists, that method is run. If it doesn't
     * exist, column_default() is called instead.
     *
     * This example also illustrates how to implement rollover actions. Actions
     * should be an associative array formatted as 'slug'=>'link html' - and you
     * will need to generate the URLs yourself. You could even ensure the links
     *
     *
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_title($item){
    	global $wpdb;

		$sub_query = ' where tour_id=' . $item['id'] . ' LIMIT 0,1';
		$results = $wpdb->get_results( 'SELECT * FROM  ' . $this->table_tour_details .   $sub_query, ARRAY_A );
		$data = $results[0];
		//print_r($data);


        //Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&tour=%s">Edit Tour Title</a>',$_REQUEST['page'],'edit',$item['id']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&tour=%s">Delete</a>',$_REQUEST['page'],'delete',$item['id']),
        );
        if(!empty($data['id'])) {
          $actions['detail_edit'] = sprintf('<a href="?page=%s&action=%s&tour_detail_id=%s">Edit Tour Detail</a>','tour_management_detail','edit',$data['id']);
        }
        else {
          $actions['detail_add'] = '<a href="admin.php?page=tour_management_detail&action=edit&tour_detail_id=0">Add Tour Detail</a>';
        }

        //Return the title contents
        return sprintf('%1$s <span style="color:silver"></span>%3$s',
            /*$1%s*/ ucfirst(stripslashes($item['title'])),
            /*$2%s*/ $item['id'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }

 	/** ************************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     *
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
        );
    }


    /** ************************************************************************
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     *
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     *
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'title'     => 'Title',
            'date'  => 'Date',
            'month'    => 'Month',
            'expiry'      => 'Expire Date',
            'status'      => 'Status'
        );
        return $columns;
    }

     /** ************************************************************************
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
     * you will need to register it here. This should return an array where the
     * key is the column that needs to be sortable, and the value is db column to
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     *
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within prepare_items() and sort
     * your data accordingly (usually by modifying your query).
     *
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     **************************************************************************/
    function get_sortable_columns() {
        $sortable_columns = array(
            'title'     => array('title',false),     //true means it's already sorted
            'date'  => array('to_date',false),
            'month'    => array('month',false),
            'expiry'  => array('expiry',false),
            'status'  => array('status',false)
        );
        return $sortable_columns;
    }


     /** ************************************************************************
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     *
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     *
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     *
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_bulk_actions() {
        $actions = array(
            'delete'    => 'Delete'
        );
        return $actions;
    }

     /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     *
     * @see $this->prepare_items()
     **************************************************************************/
    function process_bulk_action() {

        //Detect when a bulk action is being triggered...
        if( 'delete'===$this->current_action() ) {
        	$this->delete();
            //wp_die('Items deleted (or they would be if we had items to delete)!');
        }

         //Detect when a bulk action is being triggered...
        if( 'edit'===$this->current_action() ) {
        	$this->render_form($_REQUEST['tour']);
             wp_die();
        }
    }

    public function render_form( $id = 0 ) {
    	global $wpdb;
    	if(isset($_POST['update_tour'])) {

            $expire = $_REQUEST['expiry'] . ' ' . $_REQUEST['expiry_hh'] . ':' . $_REQUEST['expiry_mm'] . ' ' . $_REQUEST['expiry_am'];
    		$wpdb->replace(
				$this->table_tours,
				array(
			        'id' => $_REQUEST['tour'],
					'title' => $_REQUEST['title'],
					'to_date' => $_REQUEST['to_date'],
                    'from_date' => $_REQUEST['from_date'],
					'month' => $_REQUEST['month'],
					'expiry' => date('Y-m-d H:i:s', strtotime($expire)),
                    'status' => $_REQUEST['status']
				),
				array(
			        '%d',
					'%s',
					'%s',
					'%s',
                    '%s',
					'%s',
                    '%s'
				)
			);

            //echo $wpdb->last_query; die;
    	}

    	if($id) {
	        $this->sub_query = ' where id=' . $id;
	        $results = $wpdb->get_results( 'SELECT * FROM  ' . $this->table_tours .   $this->sub_query, ARRAY_A );
	        $data = $results[0];
        } else {
        	$data = array(
        		'name' => '',
        		'title' => '',
        		'date' => '',
        		'month' => '',
                'expire' => '',
                'status' => ''
        	);
        }
        extract($data);

    ?>

        <div class="wrap">
			<h1 id="add-new-user">
			<?php if($id) {
				echo 'Update ';
			} else {
				echo 'Add ';
			}
			?>
			 Tour</h1>
			<form name="lakeland_contact_edit"  method="post">
            <table class="form-table">
				<tbody>
				<tr class="form-field">
					<th scope="row"><label for="title"> Title </label></th>
					<td><input name="title" type="text" id="title"  value="<?php echo stripslashes(wp_specialchars($title)); ?>"></td>
				</tr>
				<tr class="form-field">
					<th scope="row"><label for="month">Month </label></th>
					<td>
                    <select name="month"  id="month" >
                          <option value="1" <?php echo ($month=='1' ? 'selected' : ''); ?>>January</option>
                          <option value="2" <?php echo ($month=='2' ? 'selected' : ''); ?>>February</option>
                          <option value="3" <?php echo ($month=='3' ? 'selected' : ''); ?>>March</option>
                          <option value="4" <?php echo ($month=='4' ? 'selected' : ''); ?>>April</option>
                          <option value="5" <?php echo ($month=='5' ? 'selected' : ''); ?>>May</option>
                          <option value="6" <?php echo ($month=='6' ? 'selected' : ''); ?>>June</option>
                          <option value="7" <?php echo ($month=='7' ? 'selected' : ''); ?>>July</option>
                          <option value="8" <?php echo ($month=='8' ? 'selected' : ''); ?>>August</option>
                          <option value="9" <?php echo ($month=='9' ? 'selected' : ''); ?>>September</option>
                          <option value="10" <?php echo ($month=='10' ? 'selected' : ''); ?>>October</option>
                          <option value="11" <?php echo ($month=='11' ? 'selected' : ''); ?>>November</option>
                          <option value="12" <?php echo ($month=='12' ? 'selected' : ''); ?>>December</option>
                    </select>
                    </td>
				</tr>
                <tr class="form-field">
                    <th scope="row"><label for="date">Date Range </label></th>
                    <td><input name="date_range" type="checkbox" id="date_range" <?php echo ($to ? 'checked' : ''); ?> class=""></td>
                </tr>
				<tr class="form-field">
					<th scope="row"><label for="date">Date </label></th>
					<td><table border="0" cellpadding="0" cellspacing="0" class="inner-table">

                            <tr>
                                <td>From<br/><input name="from_date" type="text" id="from_date" value="<?php echo $from_date; ?>" class=""></td>
                                <td>To <br/><input name="to_date" type="text" id="to_date" value="<?php echo $to_date; ?>" class=""></td>
                            </tr>
                        </table>
                    </td>
				</tr>
                <tr class="form-field">
                    <th scope="row"><label for="date">Status  </label></th>
                    <td>
                    <select name="status" id="status">
                        <option value="" >-Select Status-</option>
                        <option value="1" <?php echo ($status == 1 ? 'selected' : ''); ?> >Limited Availability</option>
                        <option value="2" <?php echo ($status == 2 ? 'selected' : ''); ?>>Sold Out</option>
                    </select>
                    <?php

                        $hours = date('H', strtotime($expiry));
                        $minutes = date('i', strtotime($expiry));
                        $am = date('A', strtotime($expiry));
                     ?>
                </tr>
                <tr class="form-field">
                    <th scope="row"><label for="expiry">Expire Date  </label></th>
                    <td>
                        <table border="0" cellpadding="0" cellspacing="0" class="inner-table">
                            <tr>
                                <td><input name="expiry" type="text" id="expiry" value="<?php echo ($expiry ? date('Y-m-d', strtotime($expiry)): ''); ?>"></td>
                                <td>
                                    <select name="expiry_hh" id="expiry_hh">
                                        <option value="01"  <?php echo ($hours=='01' ? 'selected' : ''); ?>>01</option>
                                        <option value="02"  <?php echo ($hours=='02' ? 'selected' : ''); ?>>02</option>
                                        <option value="03"  <?php echo ($hours=='03' ? 'selected' : ''); ?>>03</option>
                                        <option value="04"  <?php echo ($hours=='04' ? 'selected' : ''); ?>>04</option>
                                        <option value="05"  <?php echo ($hours=='05' ? 'selected' : ''); ?>>05</option>
                                        <option value="06"  <?php echo ($hours=='06' ? 'selected' : ''); ?>>06</option>
                                        <option value="07"  <?php echo ($hours=='07' ? 'selected' : ''); ?>>07</option>
                                        <option value="08"  <?php echo ($hours=='08' ? 'selected' : ''); ?>>08</option>
                                        <option value="09"  <?php echo ($hours=='09' ? 'selected' : ''); ?>>09</option>
                                        <option value="10"  <?php echo ($hours=='10' ? 'selected' : ''); ?>>10</option>
                                        <option value="11"  <?php echo ($hours=='11' ? 'selected' : ''); ?>>11</option>
                                        <option value="12"  <?php echo ($hours=='12' ? 'selected' : ''); ?>>12</option>
                                    </select>
                                    <select name="expiry_mm" id="expiry_mm" >
                                       <option value="00" <?php echo ($minutes=='00' ? 'selected' : ''); ?>>00</option>
                                        <option value="05" <?php echo ($minutes=='05' ? 'selected' : ''); ?>>05</option>
                                        <option value="10" <?php echo ($minutes=='10' ? 'selected' : ''); ?>>10</option>
                                        <option value="15" <?php echo ($minutes=='15' ? 'selected' : ''); ?>>15</option>
                                        <option value="20" <?php echo ($minutes=='20' ? 'selected' : ''); ?>>20</option>
                                        <option value="25" <?php echo ($minutes=='25' ? 'selected' : ''); ?>>25</option>
                                        <option value="30"  <?php echo ($minutes=='30' ? 'selected' : ''); ?>>30</option>
                                        <option value="35" <?php echo ($minutes=='35' ? 'selected' : ''); ?>>35</option>
                                        <option value="40" <?php echo ($minutes=='40' ? 'selected' : ''); ?>>40</option>
                                        <option value="45"  <?php echo ($minutes=='45' ? 'selected' : ''); ?>>45</option>
                                        <option value="50"  <?php echo ($minutes=='50' ? 'selected' : ''); ?>>50</option>
                                        <option value="55"  <?php echo ($minutes=='55' ? 'selected' : ''); ?>>55</option>
                                    </select>
                                    <select name="expiry_am" id="expiry_am">
                                       <option value="AM" <?php echo ($am=='AM' ? 'selected' : ''); ?> >AM</option>
                                        <option value="PM" <?php echo ($am=='PM' ? 'selected' : ''); ?>>PM</option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
				</tbody>
			</table>

			<p class="submit"><input type="submit" name="update_tour" id="update_tour" class="button button-primary" value="Submit"></p>
			</form>
        </div>
    <?php
    }

    function delete() {
 		global $wpdb;
    	if(isset($_REQUEST['tour']) && $_REQUEST['tour'] ) {
            $ids = is_array($_REQUEST['tour']) ? implode(',', $_REQUEST['tour']) : $_REQUEST['tour'];
    		$query = "DELETE FROM  ". $this->table_tours ." where id in (" . $ids . ")";

    		$wpdb->get_results($query);
    	}
    	return '';
    }

    function search_action($keyword = null) {
    	$sub_query = '';
    	if($keyword) {
    		$sub_query = " where title like '%" . $keyword . "%'";
    	}
    	return $sub_query;
    }

    /** ************************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     *
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     **************************************************************************/
    function prepare_items() {
        global $wpdb; //This is used only if making any database queries

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 15;


        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();


        /**
         * REQUIRED. Finally, we build an array to be used by the class for column
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);


        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();


        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example
         * package slightly different than one you might build on your own. In
         * this example, we'll be using array manipulation to sort and paginate
         * our data. In a real-world implementation, you will probably want to
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
         */

        if(isset($_REQUEST['s']) && $_REQUEST['s'] != '') {
        	$this->sub_query = $this->search_action($_REQUEST['s']);

        }


        $results = $wpdb->get_results( 'SELECT * FROM  ' . $this->table_tours .   $this->sub_query, ARRAY_A );

        $data = $results;

        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         *
         * In a real-world situation involving a database, you would probably want
         * to handle sorting by passing the 'orderby' and 'order' values directly
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');


        /***********************************************************************
         * ---------------------------------------------------------------------
         * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
         *
         * In a real-world situation, this is where you would place your query.
         *
         * For information on making queries in WordPress, see this Codex entry:
         * http://codex.wordpress.org/Class_Reference/wpdb
         *
         * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
         * ---------------------------------------------------------------------
         **********************************************************************/


        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently
         * looking at. We'll need this later, so you should always include it in
         * your own package classes.
         */
        $current_page = $this->get_pagenum();

        /**
         * REQUIRED for pagination. Let's check how many items are in our data array.
         * In real-world use, this would be the total number of items in your database,
         * without filtering. We'll need this later, so you should always include it
         * in your own package classes.
         */
        $total_items = count($data);


        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to
         */
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);



        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where
         * it can be used by the rest of the class.
         */
        $this->items = $data;


        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }

}



/** ************************ REGISTER THE TOUR PAGE ****************************
 *******************************************************************************
 * Now we just need to define an admin page. For this example, we'll add a top-level
 * menu item to the bottom of the admin menus.
 */
function tour_management_items(){

    add_menu_page('Tour Manager', 'Tour Manager', 'activate_plugins', 'tour_management', 'tour_management_list_page', 'dashicons-id', 6);
    add_submenu_page('tour_management', 'Tour Manager', 'Tour Details', 'activate_plugins', 'tour_management_detail', 'tour_management_detail_list_page');

}
add_action('admin_menu', 'tour_management_items');


/** *************************** RENDER  PAGE ********************************
 *******************************************************************************
 * This function renders the admin page and the example list table. Although it's
 * possible to call prepare_items() and display() from the constructor, there
 * are often times where you may need to include logic here between those steps,
 * so we've instead called those methods explicitly. It keeps things flexible, and
 * it's the way the list tables are used in the WordPress core.
 */
function tour_management_list_page(){

    //Create an instance of our package class...
    $testListTable = new LakeLand_Tours_Admin($plugin_name, $version);
    //Fetch, prepare, sort, and filter our data...

    $testListTable->prepare_items();

    ?>


    <div class="wrap">

        <div id="icon-users" class="icon32"><br/></div>
        <h2>Tours  <a href="admin.php?page=tour_management&action=edit&tour=0"     class=" page-title-action">Add New Tour</a></h2>

        <form method="post">
    		<input type="hidden" name="page"  value="tour_management">
            <!-- Now we can render the completed list table -->


            <?php
            $testListTable->search_box('Search', 'search_id');
            $testListTable->display();
            ?>
        </form>

    </div>

    <?php
}
