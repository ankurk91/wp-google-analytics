<?php
/*
Plugin Name: Ank Simplified Google Analytics
Plugin URI: https://github.com/ank91/ank-simplified-ga
Description: Simple, light weight, and non-bloated WordPress Google Analytics Plugin.
Version: 0.5
Author: Ankur Kumar
Author URI: http://ank91.github.io/
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
?>
<?php
/* No direct access*/
if (!defined('ABSPATH')) exit;

define('ASGA_PLUGIN_VER', '0.5');
define('ASGA_BASE_FILE', __FILE__);

class Ank_Simplified_GA
{
    protected static $instance = null;
    private $option_name = 'asga_options';
    private $asga_options = array();

    private function __construct()
    {
        // If instance is null, create it. Prevent creating multiple instances of this class
        if (is_null(self::$instance)) {
            self::$instance = $this;
        }
        //store all options in a local array
        $this->asga_options = get_option($this->option_name);

        //get action's priority
        $js_priority = absint($this->asga_options['js_priority']);

        //decide where to print code
        if ($this->asga_options['js_location'] == 1)
            add_action('wp_head', array($this, 'print_js_code'), $js_priority);
        else
            add_action('wp_footer', array($this, 'print_js_code'), $js_priority);
    }

    /**
     * Function to instantiate our class and make it a singleton
     */
    public static function get_instance()
    {

        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Prepare and print javascript code to front end
     */
    function print_js_code()
    {
        $options = $this->asga_options;

        //check if to proceed or not
        if (!$this->is_tracking_possible($options)) return;


        //get tracking id
        $ga_id = $options['ga_id'];
        //decide sub-domain
        $domain = $options['ga_domain'];
        if (empty($domain)) $domain = 'auto';
        $gaq = array();
        global $wp_query;
        //check for debug mode
        $debug_mode = $this->check_debug_mode($options);
        //this flag will be used in view
        $user_engagement = absint($options['log_user_engagement']);
        $js_load_later = absint($options['js_load_later']);


        if ($options['ua_enabled'] == 1) {
            //if universal is enabled

            $gaq[] = "'create', '" . esc_attr($ga_id) . "', '" . esc_attr($domain) . "'";

            if($options['force_ssl']==1){
                $gaq[] = "'set', 'forceSSL', true";
            }

            if ($options['anonymise_ip'] == 1) {
                $gaq[] = "'set', 'anonymizeIp', true";
            }
            /* Enable demographics and interests reports */
            if ($options['displayfeatures'] == 1) {
                $gaq[] = "'require', 'displayfeatures'";
            }
            /* Enhanced Link Attribution */
            if ($options['ga_ela'] == 1) {
                $gaq[] = "'require', 'linkid', 'linkid.js'";
            }
            if (is_404() && $options['log_404'] == 1) {
                $gaq[] = "'send','event','404',document.location.href,document.referrer";
            } elseif ($wp_query->is_search && $options['log_search'] == 1) {
                $gaq[] = "'send','pageview','/?s=" . rawurlencode($wp_query->query_vars['s']) . "'";
            } else {
                $gaq[] = "'send','pageview'";
            }

            require('views/universal_script.php');

        } else {
            //classic ga is enabled

            $ga_src = "('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js'";
            if ($debug_mode == true) {
                //Did u notice additional /u in url ?
                //@source https://developers.google.com/analytics/resources/articles/gaTrackingTroubleshooting#gaDebug
                $ga_src = "('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/u/ga_debug.js'";
            }
            //@source https://support.google.com/analytics/answer/2444872
            if ($options['displayfeatures'] == 1) {
                $ga_src = "('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js'";
                if ($debug_mode == true) {
                    $ga_src = "('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc_debug.js'";
                }
            }
            $gaq[] = "'_setAccount', '" . esc_attr($ga_id) . "'";

            if ($domain !== 'auto') {
                $gaq[] = "'_setDomainName', '" . esc_attr($domain) . "'";
            }

            if($options['force_ssl']==1){
                $gaq[] = "'_gat._forceSSL'";
            }

            if ($options['anonymise_ip'] == 1) {
                $gaq[] = "'_gat._anonymizeIp'";
            }

            $ela_plugin_url = '';
            if ($options['ga_ela'] == 1) {
                $ela_plugin_url = "var pluginUrl = '//www.google-analytics.com/plugins/ga/inpage_linkid.js';\n";
                $gaq[] = "['_require', 'inpage_linkid', pluginUrl]";
            }

            if (is_404() && $options['log_404'] == 1) {
                $gaq[] = "'_trackEvent','404',document.location.href,document.referrer";
            } elseif ($wp_query->is_search && $options['log_search'] == 1) {
                $gaq[] = "'_trackPageview','/?s=" . rawurlencode($wp_query->query_vars['s']) . "'";
            } else {
                $gaq[] = "'_trackPageview'";
            }

            require('views/classic_script.php');
        }

    }

    /**
     * Check if to enable debugging mode
     * @param $options - Options array
     * @return bool
     */
    private function check_debug_mode($options)
    {
        //debug mode is for logged-in admins/network admins only
        if (current_user_can('manage_options') || is_super_admin()) {
            if ($options['debug_mode'] == 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * Function determines whether to print tracking code or not
     * @param array $options
     * @return bool
     */
    private function is_tracking_possible($options)
    {
        if (is_preview()) {
            echo '<!-- GA Tracking is disabled in preview mode -->';
            return false;
        }

        //if GA id is not set return early with a message
        if (empty($options['ga_id'])) {
            echo '<!-- GA ID is not set -->';
            return false;
        }

        //if a user is logged in
        if (is_user_logged_in()) {

            if (is_super_admin()) {
                //if a network admin is logged in
                if (isset($options['ignore_role_networkAdmin']) && ($options['ignore_role_networkAdmin'] == 1)) {
                    echo '<!-- GA Tracking is disabled for you -->';
                    return false;
                }
            } else {
                //If a normal user is logged in
                $role = array_shift(wp_get_current_user()->roles);
                if (isset($options['ignore_role_' . $role]) && ($options['ignore_role_' . $role] == 1)) {
                    echo '<!-- GA Tracking is disabled for you -->';
                    return false;
                }
            }
        }

        return true;
    }

} //end class


if (is_admin()) {
    /* Load admin part only if we are inside wp-admin */
    require(trailingslashit(dirname(__FILE__)) . "asga-admin.php");
    //init admin class
    global $Ank_Simplified_GA_Admin;
    $Ank_Simplified_GA_Admin = Ank_Simplified_GA_Admin::get_instance();
}
else {
    /*init front end part*/
    global $Ank_Simplified_GA;
    $Ank_Simplified_GA = Ank_Simplified_GA::get_instance();
}

