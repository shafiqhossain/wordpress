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
 * @package    lakeland_bus_tours
 * @subpackage lakeland_bus_tours/includes
 * @author     Shafiq Hossain <md.shafiq.hossain@gmail.com>
 */
class LakeLand_Tours_Metabox {


	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */


	public function add_meta_box( $post_type ) {
        $post_types = array( 'lakeland_tours' );
        if ( in_array( $post_type, $post_types )) {
            add_meta_box(
                'lakeland_tours_name',            // Unique ID
                'Tour',      // Box title
                array( $this, 'render_form'), // Content callback
                $post_type
            );

        }
    }

    public function save( $post_id ) {
        if ( array_key_exists('lakeland_contacts_email', $_POST ) ) {

            update_post_meta( $post_id,
               '_lakeland_contacts_name',
                $_POST['lakeland_contacts_name']
            );

            update_post_meta( $post_id,
               '_lakeland_contacts_phone',
                $_POST['lakeland_contacts_phone']
            );

            update_post_meta( $post_id,
               '_lakeland_contacts_email',
                $_POST['lakeland_contacts_email']
            );
        }
    }


    public function render_form( $post ) {
    ?>
        <?php $name = get_post_meta( $post->ID,
            '_lakeland_contacts_name', true );

            $email = get_post_meta( $post->ID,
            '_lakeland_contacts_email', true );

            $phone = get_post_meta( $post->ID,
            '_lakeland_contacts_phone', true );
            ?>
        <div>
            <label class="lakeland-label">Name</label>
            <input name="lakeland_contacts_name" id="lakeland_contacts_name" type="text"
            class="postbox" value="<?php echo $name; ?>" />
        </div>
        <div>
            <label class="lakeland-label">Email</label>
            <input name="lakeland_contacts_email" id="lakeland_contacts_email" type="text"
            class="postbox" value="<?php echo $email; ?>" />
        </div>
        <div>
            <label class="lakeland-label">Phone</label>
            <input name="lakeland_contacts_phone" id="lakeland_contacts_phone" type="text"
            class="postbox" value="<?php echo $phone; ?>" />
        </div>
    <?php
    }


}
