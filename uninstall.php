<?php
/**
 * uninstall.php file for this Plugin
 * This file will be used to remove all traces of this plugin when uninstalled
 * @package Ank-Simplified-GA
 */


// Make sure that we are uninstalling
if (!defined('WP_UNINSTALL_PLUGIN'))
    exit;


/**
 * Remove the database entry(s) created by this plugin
 * @param $option_name string
 */
function uninstall_ank_simplified_ga($option_name)
{
    if (is_multisite() === false) {
        delete_option($option_name);
    } else {
        global $wpdb;
        $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs LIMIT 100");
        $original_blog_id = get_current_blog_id();

        foreach ($blog_ids as $blog_id) {
            switch_to_blog($blog_id);
            delete_option($option_name);

        }
        switch_to_blog($original_blog_id);
    }
}

uninstall_ank_simplified_ga('asga_options');
