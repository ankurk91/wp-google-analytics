<?php

/*
*   uninstall.php file for this Plugin
*   This file will be used to remove all traces of this plugin when uninstalled
*/

// Make sure that we are uninstalling
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();


$option_name = 'asga_options' ;
$transient_name = 'ank_simplified_ga_ja';

/*
 * lets remove the database entry created by this plugin
 */

if ( !is_multisite() )
{
    delete_option( $option_name );
    delete_transient($transient_name);
}
else
{
    global $wpdb;
    $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
    $original_blog_id = get_current_blog_id();

    foreach ( $blog_ids as $blog_id )
    {
        switch_to_blog( $blog_id );
        delete_option( $option_name );
        delete_transient($transient_name);

    }
    switch_to_blog( $original_blog_id );
}
