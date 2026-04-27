<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    lakeland_bus_schedules
 * @subpackage lakeland_bus_schedules/includes
 * @author     Shafiq Hossain <md.shafiq.hossain@gmail.com>
 */
class LakeLand_Schedules_Texonomy {


	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */


	public function lakeland_schedules_taxonomies() {
        $labels = array(
            'name'              => _x( 'Types', 'taxonomy general name' ),
            'singular_name'     => _x( 'Type', 'taxonomy singular name' ),
            'search_items'      => __( 'Search Types' ),
            'all_items'         => __( 'All Types' ),
            'parent_item'       => __( 'Parent Type' ),
            'parent_item_colon' => __( 'Parent Type:' ),
            'edit_item'         => __( 'Edit Type' ),
            'update_item'       => __( 'Update Type' ),
            'add_new_item'      => __( 'Add New Type' ),
            'new_item_name'     => __( 'New Type Name' ),
            'menu_name'         => __( 'Type' ),
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'contact-type' ),
        );

        register_taxonomy( 'contact-type', array( 'lakeland_bus_tours' ), $args );
    }

}
