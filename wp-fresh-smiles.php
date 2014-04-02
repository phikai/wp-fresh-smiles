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

wfs_create_table() {
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
  ) ENGINE=InnoDB AUTO_INCREMENT=31159 DEFAULT CHARSET=latin1;";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );

  add_option( "wfs_db_version", $wfs_db_verison );
}
register_activation_hook( __FILE__, 'wfs_create_table' );
