<?php
/**
 * Plugin Name: Fetch External Products RestApi
 * Plugin URI: http://isoftbd.com
 * Description: This plugin adds a widget that pulls products through the REST API
 * Version: 2.0.1
 * Author: Md. Shafiq Hossain
 * Author URI: http://isoftbd.com
 * License: GPL2
 */

class Product_List_Widget extends WP_Widget {



	public function __construct() {
		$widget_details = array(
			'classname' => 'product-ext-rest-api',
			'description' => 'a widget that pulls products through the REST API'
		);

		parent::__construct( 'product-ext-rest-api', 'EXTERNAL REST API', $widget_details );

	}

	//Function to set up title in widgets
	public function form( $instance ) {
        $title = ( !empty( $instance['title'] ) ) ? $instance['title'] : '';
        ?>

        <p>
            <label for="<?php echo $this->get_field_name( 'title' ); ?>">Title: </label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>

        <?php
	}

	//Function to build child list
	public function child_list($sub, $array) {
		if(!is_array($sub)) return '';

		$output = '<ul>';
		foreach($sub as $item){
			$output .= '<li>' . $item->name;

				foreach($array as $child) {
					if($item->id == $child->parent_id) {
						$ch[] = $child;
					}
				}
				if(isset($ch)){
					$output .= $this -> child_list($ch, $sub);
					unset($ch);
				}

				$output .= '</li>';
		}
		$output .= '</ul>';

		return $output;
	}

	//function to build array list
	public function make_list($array){
		if(!is_array($array)) return '';

		$output = '<ul>';
		foreach($array as $item){
			if($item->parent_id == null){
				$output .= '<li>' . $item->name;

				foreach($array as $child) {
					if($item->id == $child->parent_id) {
						$matches[] = $child;
					}
				}
				if(isset($matches)){
					$output .= $this -> child_list($matches, $array);
					unset($matches);
				}

				$output .= '</li>';
			}

		}
		$output .= '</ul>';

		return $output;
	}

	//Function to display all the information in a widget
	public function widget( $args, $instance ) {
        echo $args['before_widget'];

    	if( !empty( $instance['title'] ) ) {
    		echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $args['after_title'];
    	}

		$cats = get_transient( 'remote_cats' );

		if( empty( $cats ) ) {
			$response = wp_remote_get( 'http://infinitewebsolution.com.au/blue-api/db.json' );

			if( is_wp_error( $response ) ) {
				return array();
			}

			$cats = json_decode( wp_remote_retrieve_body( $response ) );

			if( empty( $cats ) ) {
				return array();
			}

			set_transient( 'remote_cats', $cats, 1800 );
		}



		echo $this -> make_list($cats -> products);

		echo $args['after_widget'];

	}



}

add_action( 'widgets_init', function(){
     register_widget( 'Product_List_Widget' );
});


/** Backend Menu **/

add_action('admin_menu', 'era_plugin_setup_menu');

function era_plugin_setup_menu(){
        add_menu_page( 'Exnternal REST API', 'External REST API', 'manage_options', 'product-ext-rest-api', 'era_update' );
}

function era_update(){

        echo "<h1>External REST API</h1>";

  // Check whether the button has been pressed AND also check the nonce
  if (isset($_POST['test_button']) && check_admin_referer('era_button_clicked')) {
    // the button has been pressed AND we've passed the security check
    era_button_action();
  }

  echo '<form action="options-general.php?page=product-ext-rest-api" method="post">';

  wp_nonce_field('era_button_clicked');
  echo '<input type="hidden" value="true" name="test_button" />';
  submit_button('Update products now');
  echo '</form>';

  echo '</div>';

}

/* Code to display button in General settings page

$new_general_setting = new new_general_setting();

class new_general_setting {
    function new_general_setting( ) {
        add_filter( 'admin_init' , array( &$this , 'register_fields' ) );
    }

    function register_fields() {
		// Check whether the button has been pressed AND also check the nonce
		if (isset($_POST['era_button']) && check_admin_referer('era_button_clicked')) {
			// the button has been pressed AND we've passed the security check
			era_button_action();
		  }
		  echo '<div class="clear"></div><div class="wrap">';
		  echo '<form action="options-general.php" method="post">';

		  wp_nonce_field('era_button_clicked');
		  echo '<h1>External REST API data update</h1>';
		  echo '<input type="hidden" value="true" id="era_button" name="era_button" />';
		  submit_button('Update products now');
		  echo '</form>';

		  echo '</div>';
	}
}*/

//Function to update API data on click on update button
function era_button_action()
{
	$cats = get_transient( 'remote_cats' );

	$response = wp_remote_get( 'http://stash.ch/products/recent' );

		if( is_wp_error( $response ) ) {
			return array();
		}

		$cats = json_decode( wp_remote_retrieve_body( $response ) );

		if( empty( $cats ) ) {
			return array();
		}

		set_transient( 'remote_cats', $cats, 0 );

  echo '<div id="message" class="updated fade"><p>'
    .'The External REST API data is updated' . '</p></div>';
}

?>
