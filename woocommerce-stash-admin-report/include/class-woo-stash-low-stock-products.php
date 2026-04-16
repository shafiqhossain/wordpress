<?php

// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Create a new table class that will extend the WP_List_Table
 */
class Warehouse_Product_Stock_List_Table extends WP_List_Table {
	/**
	 * Class constructor
	 */
	public function __construct() {

	  parent::__construct( [
		'singular' => __( 'Product Stock', 'woo-stash' ),
		'plural' => __( 'Products Stock', 'woo-stash' ),
		'ajax' => true
	  ] );
	}

	/**
	* Retrieve customer’s data from the database
	*
	* @param int $per_page
	* @param int $page_number
	*
	* @return mixed
	*/
	public static function get_products_stock( $per_page = 50, $page_number = 1 ) {
	  global $wpdb;

	  $low_stock_amount = get_option('woocommerce_notify_low_stock_amount', 0);
	  $no_stock_amount = get_option('woocommerce_notify_no_stock_amount', 0);

	  $orderby = !empty( $_REQUEST['orderby'] ) ? esc_sql( $_REQUEST['orderby'] ) : 'title';
	  $order = !empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : 'ASC';

	  // get all simple products where stock is managed
	  $args = array(
		'post_type'			=> 'product',
		'post_status' 		=> 'publish',
		'posts_per_page' 	=> $per_page,
		'offset'			=> ( $page_number - 1 ) * $per_page,
		'orderby'			=> $orderby,
		'order'				=> $order,
		'meta_query' 		=> array(
    	  'relation' => 'OR',
		),
	  );



	  $warehouse_id = '';
	  if( !empty($_GET['warehouse-filter'])){
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
	  //echo $loop->request.'<br>';

	  $data = array();
	  while ( $loop->have_posts() ) : $loop->the_post();
		$post_id = get_the_ID();
		$product = wc_get_product($post_id);

		$stock_1 = intval(get_post_meta($post_id, '_stock', true));
		$stock_2 = intval(get_post_meta($product->get_id(), 'stash_product_stock_2', true));
		$stock_3 = intval(get_post_meta($product->get_id(), 'stash_product_stock_3', true));

		if( !empty($_GET['warehouse-filter'])){
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
		  $stock  = '<strong>Stash 1: </strong>'.$stock_1.' | ';
		  $stock .= '<strong>Stash 2: </strong>'.$stock_2.' | ';
		  $stock .= '<strong>Stash 3: </strong>'.$stock_3;
		}

		$data[] = array(
		  'id'  		=> $product->get_id(),
		  'name'		=> $product->get_name(),
		  'sku' 		=> $product->get_sku(),
		  'stock'		=> $stock,
		);

	  endwhile;

	  return $data;
	}

	/**
	* Returns the count of records in the database.
	*
	* @return null|string
	*/
	public static function record_count() {
	  global $wpdb;

	  $low_stock_amount = get_option('woocommerce_notify_low_stock_amount', 0);
	  $no_stock_amount = get_option('woocommerce_notify_no_stock_amount', 0);

	  // get all simple products where stock is managed
	  $args = array(
		'post_type'			=> 'product',
		'post_status' 		=> 'publish',
		'posts_per_page' 	=> -1,
	  );

	  $warehouse_id = '';
	  if( !empty($_GET['warehouse-filter'])){
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
			}
			elseif ($stock_type == 'no-stock') {
			  $args['meta_query'][] = array(
				'key' 	=> '_stock',
				'value' => $no_stock_amount,
				'compare' => '<=',
				'type' => 'NUMERIC'
			  );
			}
			else {
			  $args['meta_query'][] = array(
				'key' 	=> '_stock',
				'value' => $no_stock_amount,
				'compare' => '<=',
				'type' => 'NUMERIC'
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
			}
			elseif ($stock_type == 'no-stock') {
			  $args['meta_query'][] = array(
				'key' 	=> 'stash_product_stock_2',
				'value' => $no_stock_amount,
				'compare' => '<=',
				'type' => 'NUMERIC'
			  );
			}
			else {
			  $args['meta_query'][] = array(
				'key' 	=> 'stash_product_stock_2',
				'value' => $no_stock_amount,
				'compare' => '<=',
				'type' => 'NUMERIC'
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
			}
			elseif ($stock_type == 'no-stock') {
			  $args['meta_query'][] = array(
				'key' 	=> 'stash_product_stock_3',
				'value' => $no_stock_amount,
				'compare' => '<=',
				'type' => 'NUMERIC'
			  );
			}
			else {
			  $args['meta_query'][] = array(
				'key' 	=> 'stash_product_stock_3',
				'value' => $no_stock_amount,
				'compare' => '<=',
				'type' => 'NUMERIC'
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
			}
			elseif ($stock_type == 'no-stock') {
			  $args['meta_query'][] = array(
				'key' 	=> '_stock',
				'value' => $no_stock_amount,
				'compare' => '<=',
				'type' => 'NUMERIC'
			  );
			}
			else {
			  $args['meta_query'][] = array(
				'key' 	=> '_stock',
				'value' => $no_stock_amount,
				'compare' => '<=',
				'type' => 'NUMERIC'
			  );
			}
		}
	  }

	  $loop = new WP_Query( $args );
	  $num = $loop->post_count;

	  return $num;
	}

	/**
	 * Text displayed when no low stock product data is available
	 */
	public function no_items() {
	  _e( 'No low/no stock information avaliable.', 'woo-stash' );
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

        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );

        $this->_column_headers = array($columns, $hidden, $sortable);
	    $this->items = self::get_products_stock( $perPage, $currentPage );
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns() {
        $columns = array(
            'id'  		=> 'ID',
            'name'		=> 'Name',
            'sku' 		=> 'SKU',
            'stock'		=> 'Stock',
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
          'id' => array('id', false),
          'name' => array('name', false),
          'sku' => array('sku', false)
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
            case 'id':
            case 'name':
            case 'sku':
            case 'stock':
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
        $orderby = 'name';
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

	public function extra_tablenav( $which ) {
		if(!empty($_GET['stock-type'])) {
		  $move_on_url_1 = '&stock-type=' . $_GET['stock-type'] . '&warehouse-filter=';
		}
		else {
		  $move_on_url_1 = '&warehouse-filter=';
		}

		if(!empty($_GET['warehouse-filter'])) {
		  $move_on_url_2 = '&warehouse-filter=' . $_GET['warehouse-filter'] . '&stock-type=';
		}
		else {
		  $move_on_url_2 = '&stock-type=';
		}

		if ( $which == "top" ){
			?>
			<div class="alignleft actions bulkactions">
				<select name="warehouse-filter" class="woo-stash-filter-warehouse">
					<option value="">Filter by Warehouse</option>
					<?php
					  $selected_1 = '';
					  if( $_GET['warehouse-filter'] == 1 ){
						$selected_1 = ' selected = "selected"';
					  }
					  $selected_2 = '';
					  if( $_GET['warehouse-filter'] == 2 ){
						$selected_2 = ' selected = "selected"';
					  }
					  $selected_3 = '';
					  if( $_GET['warehouse-filter'] == 3 ){
						$selected_3 = ' selected = "selected"';
					  }
					?>
					<option value="<?php echo $move_on_url_1 ?>1" <?php echo $selected_1; ?>>#1</option>
					<option value="<?php echo $move_on_url_1 ?>2" <?php echo $selected_2; ?>>#2</option>
					<option value="<?php echo $move_on_url_1 ?>3" <?php echo $selected_3; ?>>#3</option>
				</select>
				<select name="stock-type" class="woo-stash-filter-stock-type">
					<option value="">Filter by Stock Type</option>
					<?php
					  $selected_1 = '';
					  if( $_GET['stock-type'] == 'low-stock' ){
						$selected_1 = ' selected = "selected"';
					  }
					  $selected_2 = '';
					  if( $_GET['stock-type'] == 'no-stock' ){
						$selected_2 = ' selected = "selected"';
					  }
					?>
					<option value="<?php echo $move_on_url_2 ?>low-stock" <?php echo $selected_1; ?>>Low Stock</option>
					<option value="<?php echo $move_on_url_2 ?>no-stock" <?php echo $selected_2; ?>>No Stock</option>
				</select>
			</div>
			<div class="alignright actions">
				<form class="alignleft actions bulkactions" action="<?php echo admin_url('admin-post.php'); ?>" method="post">
				  <input type="hidden" name="action" value="stash_export">
				  <input type="submit" value="Export to CSV" class="button action">
				</form>
			</div>
			<?php
		}
		if ( $which == "bottom" ){
			//not showing now
		}
	}
}





