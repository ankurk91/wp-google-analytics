<?php
/*
Plugin Name: Ank Simplified GA
Plugin URI: https://github.com/ank91/ank-simplified-ga
Description: Simple, light weight, and non-bloated WordPress Google Analytics Plugin.
Version: 0.2
Author: Ankur Kumar
Author URI: http://ank91.github.io/
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
?>
<?php
/* no direct access*/
if (!defined('ABSPATH')) exit;

define('ASGA_PLUGIN_VER', '0.2');
define('ASGA_BASE_FILE',__FILE__);

class Ank_Simplified_GA
{
    static $instance = false;
    private $option_name = 'asga_options';
    private  $asga_options = array();

    private function __construct()
    {
        if ( is_null( self::$instance ) ) {
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
    public static function get_instance() {
        if ( !self::$instance )
            self::$instance = new self;

        return self::$instance;
    }

    /**
     * Prepare and print javascript code to front end
     */
    function print_js_code()
    {
        //check if to proceed
        if (!$this->is_tracking_possible()) return;

        $options = $this->asga_options;
        //get tracking id
        $ga_id = $options['ga_id'];
        //decide sub-domain
        $domain = $options['ga_domain'];
        if (empty($domain) || $domain === '') $domain = 'auto';
        $gaq = array();
        global $wp_query;

        if ($options['ua_enabled'] == 1) {
            //if universal is enabled

            $gaq[] = "'create', '" . esc_attr($ga_id) . "', '" . esc_attr($domain) . "'";
            $gaq[] = "'set', 'forceSSL', true";
            if ($options['anonymise_ip'] == 1) {
                $gaq[] = "'set', 'anonymizeIp', true";
            }
            if ($options['displayfeatures'] == 1) {
                $gaq[] = "'require', 'displayfeatures'";
            }
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

            if ($options['displayfeatures'] == 1) {
                $ga_src = "('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js'";
            }
            $gaq[] = "'_setAccount', '" . esc_attr($ga_id) . "'";
            if ($domain !== 'auto') {
                $gaq[] = "'_setDomainName', '" . esc_attr($domain) . "'";
            }
            // enable SSL data
            $gaq[] = "'_gat._forceSSL'";
            // Anonymous data
            if ($options['anonymise_ip'] == 1) {
                $gaq[] = "'_gat._anonymizeIp'";
            }
            $plugin_url = '';
            if ($options['ga_ela'] == 1) {
                $plugin_url = "var pluginUrl = '//www.google-analytics.com/plugins/ga/inpage_linkid.js';\n";
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
     * Function determines whether to print tracking code or not
     * @return bool
     */
    function is_tracking_possible()
    {
            if (is_preview() ) {
                echo '<!-- GA Tracking is disabled in preview mode -->';
                return false;
            }

            $options = $this->asga_options;
            $ga_id = $options['ga_id'];
            //if GA id is not set return early with a message
            if (empty($ga_id) || $ga_id === '') {
                echo '<!-- GA ID is not set -->';
                return false;
            }


            //If the user's role has been set not to track, return
        if (is_user_logged_in()) {
            $role = array_shift(wp_get_current_user()->roles);
            if (1 == $options['ignore_role_' . $role]) {
                echo '<!-- GA Tracking is disabled for you -->';
                return false;
            }

        }
            return true;
        }


} //end class


if (is_admin()) {
    /* load admin part only if we are inside wp-admin */
    require(trailingslashit(dirname(__FILE__)) . "asga_admin.php");
} else {
    /*init front end part*/
    global $ank_simplified_ga;
    $ank_simplified_ga = Ank_Simplified_GA::get_instance();
}

