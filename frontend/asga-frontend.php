<?php
/*
 * Frontend class for "Ank Simplified GA" Plugin
 * This class can run independently without admin class
 */
class Ank_Simplified_GA
{
    private static $instances = array();
    private $asga_options = array();

    private function __construct()
    {
        $this->set_db_options();
        $this->init();

    }

    /**
     * Function to instantiate our class and make it a singleton
     */
    public static function get_instance()
    {

        $cls = get_called_class();
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static;
        }
        return self::$instances[$cls];
    }

    protected function __clone()
    {
        //don't not allow clones
    }

    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }

    /**
     * Store database options in a local array
     */
    private function set_db_options()
    {
        $this->asga_options = get_option(ASGA_OPTION_NAME);
    }

    /**
     * Init front end part
     */
    private function init()
    {
        //get action's priority
        $js_priority = absint($this->asga_options['js_priority']);

        //decide where to print code
        if ($this->asga_options['js_location'] == 1)
            add_action('wp_head', array($this, 'print_tracking_code'), $js_priority);
        else
            add_action('wp_footer', array($this, 'print_tracking_code'), $js_priority);
    }

    /**
     * Prepare and print javascript code to front end
     */
    function print_tracking_code()
    {

        //get database options
        $options = $this->asga_options;

        //check if to proceed or not
        if (!$this->is_tracking_possible($options)) return;

        //get tracking id
        $ga_id = esc_js($options['ga_id']);
        //decide sub-domain
        $domain = empty($options['ga_domain']) ? 'auto' : esc_js($options['ga_domain']);

        //check for debug mode
        $debug_mode = $this->check_debug_mode($options);
        //these flags will be used in view
        $js_load_later = absint($options['js_load_later']);

        $gaq = array();
        global $wp_query;


        if ($options['ua_enabled'] == 1) {
            //if universal is enabled

            if ($options['allow_linker'] == 1 && $options['allow_anchor'] != 1) {
                $gaq[] = "'create', '" . $ga_id . "', '" . $domain . "', {'allowLinker': true}";
            } else {
                if ($options['allow_anchor'] == 1 && $options['allow_anchor'] == 1) {
                    $gaq[] = "'create', '" . $ga_id . "', '" . $domain . "', {'allowLinker': true,'allowAnchor': true}";
                } else {
                    $gaq[] = "'create', '" . $ga_id . "', '" . $domain . "'";
                }
            }

            if ($options['force_ssl'] == 1) {
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
            if ($options['custom_trackers'] !== '') {
                $gaq[] = array(
                    'custom_trackers' => $options['custom_trackers']
                );
            }

            if (is_404() && $options['log_404'] == 1) {
                $gaq[] = "'send','event','404',document.location.href,document.referrer";
            } elseif ($wp_query->is_search && $options['log_search'] == 1) {
                $gaq[] = "'send','pageview','/?s=" . rawurlencode($wp_query->query_vars['s']) . "'";
            } else {
                $gaq[] = "'send','pageview'";
            }

            require(__DIR__.'/views/universal_script.php');

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
            $gaq[] = "'_setAccount', '" . $ga_id . "'";

            if ($domain !== 'auto') {
                $gaq[] = "'_setDomainName', '" . $domain . "'";
            }

            if ($options['allow_linker'] == 1) {
                $gaq[] = "'_setAllowLinker', true";
            }

            if ($options['allow_anchor'] == 1) {
                $gaq[] = "'_setAllowAnchor', true";
            }

            if ($options['force_ssl'] == 1) {
                $gaq[] = "'_gat._forceSSL'";
            }

            if ($options['anonymise_ip'] == 1) {
                $gaq[] = "'_gat._anonymizeIp'";
            }

            $ela_plugin_url = ''; //init with empty url
            if ($options['ga_ela'] == 1) {
                $ela_plugin_url = "var pluginUrl = '//www.google-analytics.com/plugins/ga/inpage_linkid.js';\n";
                $gaq[] = "['_require', 'inpage_linkid', pluginUrl]";
            }

            if ($options['custom_trackers'] !== '') {
                $gaq[] = array(
                    'custom_trackers' => $options['custom_trackers']
                );
            }

            if (is_404() && $options['log_404'] == 1) {
                $gaq[] = "'_trackEvent','404',document.location.href,document.referrer";
            } elseif ($wp_query->is_search && $options['log_search'] == 1) {
                $gaq[] = "'_trackPageview','/?s=" . rawurlencode($wp_query->query_vars['s']) . "'";
            } else {
                $gaq[] = "'_trackPageview'";
            }

            require(__DIR__.'/views/classic_script.php');
        }


    }

    /**
     * Check if to enable debugging mode
     * @param $options - Options array
     * @return bool
     */
    private function check_debug_mode($options)
    {
        //debug mode is only for logged-in admins/network admins
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
