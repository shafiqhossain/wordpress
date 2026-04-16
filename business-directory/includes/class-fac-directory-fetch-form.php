<?php

/**
 * The form to be loaded on the plugin's admin page
 */
if( current_user_can( 'edit_users' ) ) {
    $fac_directory_fetch_data_form_nonce = wp_create_nonce( 'fac_directory_fetch_data_form_nonce' );
    delete_option( 'batch_ym_process_profiles_processed' );

    // Build the Form
    ?>
    <h2><?php _e( 'Fetch Directory Data', 'fac-directory' ); ?></h2>
    <div class="fac_directory_fetch_data_form">
        <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" id="fac_directory_fetch_data_form" >
            <p class="dropdown">
                <label for="fetch_time">Select Fetch Time:</label><br>
                <select name="fetch_time" id="fetch-time">
                    <option value="1">Since Beginning</option>
                    <option value="2">Since Today</option>
                    <option value="3">Since 1 year</option>
                    <option value="4">Since 6 months</option>
                    <option value="5">Since 3 months</option>
                    <option value="6">Since 7 days</option>
                    <option value="7">Since yesterday</option>
                    <option value="8" selected="selected">Since last time fetched</option>
                </select>
            </p>
            <p class="dropdown">
                <input type="checkbox" id="only-missing-profiles" name="only_missing_profiles" value="1">
                <label for="only-missing-profiles"> Fetch only missing profiles</label>
            </p>
            <input type="hidden" name="action" value="fac_directory_form_response">
            <input type="hidden" name="fac_directory_fetch_data_form_nonce" value="<?php echo $fac_directory_fetch_data_form_nonce ?>" />
            <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Fetch Membership Data"></p>
        </form>
        <br/><br/>
        <div id="fac_directory_fetch_data_form_feedback"></div>
        <br/><br/>
    </div>
    <?php
}
else {
    ?>
    <p> <?php __("You are not authorized to perform this operation.", $this->plugin_name) ?> </p>
    <?php
}
