<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/shafiqhossain
 * @since      1.0.0
 *
 * @package    Fac_Directory
 * @subpackage Fac_Directory/admin/partials
 */
?>

<div class="wrap">
  <h1>FAC Directory Settings</h1>
  <?php
  // Let see if we have a caching notice to show
  $admin_notice = get_option('fac_directory_admin_notice');
  if($admin_notice) {
    // We have the notice from the DB, lets remove it.
    delete_option( 'fac_directory_admin_notice' );
    // Call the notice message
    $this->admin_notice($admin_notice);
  }
  if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ){
    $this->admin_notice("Your settings have been updated!");
  }
  ?>
  <form method="POST" action="options.php">
  <?php
    settings_fields('fac-directory-options');
    do_settings_sections('fac-directory-options');
    submit_button();
  ?>
  </form>
</div>