<?php
namespace Ank91\Plugins\Ank_Simplified_GA;
?><?php
/*
Plugin Name: Ank Simplified Google Analytics
Plugin URI: https://github.com/ank91/ank-simplified-ga
Description: Simple, light weight, and non-bloated Google Analytics plugin for WordPress.
Version: 1.0.2
Author: Ankur Kumar
Author URI: http://ank91.github.io/
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: ank-simplified-ga
Domain Path: /languages
*/

/* No direct access*/
if (!defined('ABSPATH')) exit;

define('ASGA_PLUGIN_VER', '1.0.2');
define('ASGA_BASE_FILE', __FILE__);
define('ASGA_OPTION_NAME', 'asga_options');
define('ASGA_TEXT_DOMAIN', 'ank-simplified-ga');


/**
 * Initiate required classes
 * Note: We are not using AJAX anywhere in this plugin
 */
if (is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX)) {
    require 'inc/class-admin.php';
    Ank_Simplified_GA_Admin::get_instance();

} else {
    require 'inc/class-frontend.php';
    Ank_Simplified_GA_Frontend::get_instance();
}


