<?php

/*
*   uninstall.php file for this Plugin
*   This file will be used to remove all traces of this plugin when uninstalled
*/

//if uninstall not called by WordPress do exit

if (!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN'))
    exit;

/*
 * lets remove the database entry created by this plugin
 */
delete_option('asga_options');
