<?php
/*
Plugin Name: Ank Simplified Google Analytics
Plugin URI: https://github.com/ank91/ank-simplified-ga
Description: Simple, light weight, and non-bloated WordPress Google Analytics Plugin.
Version: 0.9.4
Author: Ankur Kumar
Author URI: http://ank91.github.io/
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: ank-simplified-ga
Domain Path: /languages
*/
?>
<?php
/* No direct access*/
if (!defined('ABSPATH')) exit;

define('ASGA_PLUGIN_VER', '0.9.4');
define('ASGA_BASE_FILE', plugin_basename(__FILE__));
define('ASGA_OPTION_NAME', 'asga_options');
define('ASGA_TEXT_DOMAIN', 'ank-simplified-ga');


if (is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX)) {
    require(__DIR__ . "/admin/asga-admin.php");
    global $Ank_Simplified_GA_Admin;
    $Ank_Simplified_GA_Admin = Ank_Simplified_GA_Admin::get_instance();
} else {
    require(__DIR__ . "/frontend/asga-frontend.php");
    global $Ank_Simplified_GA;
    $Ank_Simplified_GA = Ank_Simplified_GA::get_instance();
}

