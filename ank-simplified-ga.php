<?php
/**
 * Main php file for 'Ank_Simplified_GA' plugin
 * Adding namespace on top, no content allowed before namespace declaration
 * Namespace requires php v5.3.0
 */
namespace Ank91\Ank_Simplified_GA_Plugin;

?><?php
/*
Plugin Name: Ank Simplified Google Analytics
Plugin URI: https://github.com/ank91/ank-simplified-ga
Description: Simple, light weight, and non-bloated <a target="_blank" href="https://www.google.co.in/analytics/">Google Analytics</a> plugin for WordPress.
Version: 1.0.0
Author: Ankur Kumar
Author URI: http://ank91.github.io/
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: ank-simplified-ga
Domain Path: /languages
*/
?><?php

/* No direct access*/
if (!defined('ABSPATH')) exit;

define('ASGA_PLUGIN_VER', '1.0.0');
define('ASGA_BASE_FILE', plugin_basename(__FILE__));
define('ASGA_OPTION_NAME', 'asga_options');
define('ASGA_TEXT_DOMAIN', 'ank-simplified-ga');

/**
 * Registering class auto-loader
 * @requires php v5.1.2
 */
spl_autoload_register(__NAMESPACE__ . '\ank_simplified_ga_autoloader');

/**
 * Auto-loader for our plugin classes
 * @param $class_name
 */
function ank_simplified_ga_autoloader($class_name)
{
    //make sure this loader work only for this plugin's related classes
    if (false !== strpos($class_name, __NAMESPACE__)) {
        if ($class_name === __NAMESPACE__ . '\Ank_Simplified_GA_Frontend') {
            require_once(__DIR__ . "/frontend/class-frontend.php");
        } elseif ($class_name === __NAMESPACE__ . '\Ank_Simplified_GA_Admin') {
            require_once(__DIR__ . "/admin/class-admin.php");
        }
    }
}

/**
 * Initiate required classes
 * Note: We are not using AJAX anywhere in this plugin
 */
if (is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX)) {
    Ank_Simplified_GA_Admin::get_instance();

} else {
    Ank_Simplified_GA_Frontend::get_instance();
}


