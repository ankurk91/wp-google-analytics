<?php
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
define('ASGA_BASE_FILE', __FILE__);
define('ASGA_OPTION_NAME', 'asga_options');
define('ASGA_TEXT_DOMAIN', 'ank-simplified-ga');

/**
 * Loading classes via composer
 * @require php v5.3.2
 */
require __DIR__.'/vendor/autoload.php';

/**
 * Initiate required classes
 * Note: We are not using AJAX anywhere in this plugin
 */
if (is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX)) {
    Ank91\Plugins\Ank_Simplified_GA\Ank_Simplified_GA_Admin::get_instance();

} else {
    Ank91\Plugins\Ank_Simplified_GA\Ank_Simplified_GA_Frontend::get_instance();
}


