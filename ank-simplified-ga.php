<?php

namespace Ankur\Plugins\Ank_Simplified_GA;

/**
 * Plugin Name: Google Analytics Simplified
 * Plugin URI: https://github.com/ankurk91/wp-google-analytics
 * Description: Simple, light weight, and non-bloated Google Analytics plugin for WordPress.
 * Version: 1.5.0
 * Author: Ankur Kumar
 * Author URI: https://ankurk91.github.io/
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: ank-simplified-ga
 * Domain Path: /languages
 */

// No direct access
if (!defined('ABSPATH')) exit;

define('ASGA_PLUGIN_VER', '1.5.0');
define('ASGA_BASE_FILE', __FILE__);
define('ASGA_OPTION_NAME', 'asga_options');


require 'inc/class-singleton.php';
/**
 * Initiate required classes
 * Note: We are not using AJAX anywhere in this plugin
 */
if (is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX)) {
    require 'inc/class-admin.php';
    Admin::instance();

} else {
    require 'inc/class-frontend.php';
    Frontend::instance();
}


