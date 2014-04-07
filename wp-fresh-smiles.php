<?php

/*
Plugin Name: WP Fresh Smiles
Plugin URI: https://github.com/pressable/wp-fresh-smiles
Description: A WP Plugin of the Fresh Smiles application for displaying FreshDesk customer satisfaction.
Author: A. Kai Armstrong
Version: 0.1.0
Author URI: http://www.kaiarmstrong.com
*/

global $wfs_db_version
$wfs_db_version = "1.0";

wfs_setup() {
  global $wpdb;
  global $wfs_db_version;

  $table_name = $wpdb->prefix . 'freshsmiles';

  $sql = "CREATE TABLE $table_name (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    `survey_created_at` datetime DEFAULT NULL,
    `survey_updated_at` datetime DEFAULT NULL,
    `ticket_id` int(11) DEFAULT NULL,
    `survey_rating` int(1) DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `ticket_id` (`ticket_id`),
    KEY `survey_updated_at` (`survey_updated_at`)
  ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=UTF8;";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );

  add_option( "wfs_db_version", $wfs_db_verison );
  add_option( "wfs_freshdesk_url", "" );
  add_option( "wfs_freshdesk_api", "" );
  add_option( "wfs_freshdesk_view", "" );
}
register_activation_hook( __FILE__, 'wfs_setup' );

//Add our Admin Menu
add_action('admin_menu', 'wfs_menu');
function wfs_menu() {
  add_options_page('WP Fresh Smiles', 'WP Fresh Smiles', 'manage_options', 'wp-fresh-smiles', 'wfs_admin');
}

//Setup Paths and API
require_once( plugin_dir_path( __FILE__ ) . "inc/FreshdeskRest.php" );

function wfs_admin() {
  //Update Options
  if ( isset($_POST['action']) && ( $_POST['action'] == 'wfs_update_options' ) ) {
    if ( isset($_POST['wfs_freshdesk_url']) ) { update_option('wfs_freshdesk_url', $_POST['wfs_freshdesk_url']); }
    if ( isset($_POST['wfs_freshdesk_api']) ) { update_option('wfs_freshdesk_api', $_POST['wfs_freshdesk_api']); }
    if ( isset($_POST['wfs_freshdesk_view']) ) { update_option('wfs_freshdesk_view', $_POST['wfs_freshdesk_view']); }
    wfs_schedule_activate();
  } else {
    //Do Nothing
  }
?>
  <div class="wrap">
    <h2>WP Fresh Smiles</h2>
    <p><form method="post" action="">
      <table class="form-table"><tbody>
        <tr valign="top">
          <th scope="row"><label for="wfs_freshdesk_url">Freshdesk Subdomain:</label></th>
          <td>
            <input type="text" name="wfs_freshdesk_url" value="REPLACE THIS LATER" class="regular-text code" />
            <span class="description">Your Freshdesk Subdomain. <code>XXXXXXXXXXXX.freshdesk.com</code></span>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="wfs_freshdesk_api">Freshdesk API Key:</label></th>
          <td>
            <input type="text" name="wfs_freshdesk_api" value="REPLACE THIS LATER" class="regular-text code" />
            <span class="description">Your Freshdesk API Key</span>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="wfs_freshdesk_view">Freshdesk Ticket View ID:</label></th>
          <td>
            <input type="text" name="wfs_freshdesk_view" value="REPLACE THIS LATER" class="regular-text code" />
            <span class="description">Your Freshdesk Ticket View ID</span>
          </td>
        </tr>
      </tbody></table>

      <input type="hidden" name="action" value="wfs_update_options" />
        <p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>
    </form></p>
  </div>
<?php }

//Executes the Schedule Event Function
add_action("wfs_schedule", "wfs_schedule_event");

//This sets up the wp-cron function
function wfs_schedule_activate() {
  if ( !wp_next_scheduled( 'wfs_schedule' ) ) {
    wp_schedule_event(time(), 'hourly', 'wfs_schedule');
  } else {
    //Do Nothing
  }
}

//This is the actual core of the schedule
function wfs_schedule_event() {
  //STUFF HAPPENS HERE
}
