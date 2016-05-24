<?php

namespace Ank91\Plugins\Ank_Simplified_GA;
/**
 * Class Ank_Simplified_GA
 * @package Ank-Simplified-GA
 */
class Ank_Simplified_GA_Frontend
{
    private static $instances = array();
    /**
     * Stores database options
     * @var array
     */
    private $db_options = array();


    private function __construct()
    {
        //Store database options in a local array
        $this->db_options = get_option(ASGA_OPTION_NAME);

        //Get action's priority
        $js_priority = absint($this->db_options['js_priority']);

        //Decide where to print code
        if ($this->db_options['js_location'] == 1) {
            add_action('wp_head', array($this, 'print_tracking_code'), $js_priority);
        } else {
            add_action('wp_footer', array($this, 'print_tracking_code'), $js_priority);
        }

        //Check for webmaster code, (deprecated)
        if (!empty($this->db_options['webmaster']['google_code'])) {
            add_action('wp_head', array($this, 'print_webmaster_code'), 9);
        }

        if ($this->need_to_load_event_tracking_js()) {
            //Load event tracking js file
            add_action('wp_footer', array($this, 'add_event_tracking_js'));
        }

        if ($this->db_options['tag_rss_links'] == 1) {
            add_filter('the_permalink_rss', array($this, 'rss_link_tagger'), 99);
        }

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
        return new \Exception("Cannot unserialize singleton");
    }

    /**
     * Prepare and print javascript code to front end
     */
    function print_tracking_code()
    {
        //Store database options into a local variable coz it is going to modified
        $options = $this->db_options;

        //Check if to proceed or not, return early with a message if not
        $tracking_status = $this->is_tracking_possible(true);

        if ($tracking_status['status'] === false) {
            $this->load_view('ga_disabled.php', $tracking_status);
            return;
        }

        //Finalize some db options
        $options['ga_id'] = esc_js($options['ga_id']);
        $options['ga_domain'] = empty($options['ga_domain']) ? 'auto' : esc_js($options['ga_domain']);

        //These flags will be used in view
        $view_array = array(
            'gaq' => array()
        );

        //Check for debug mode
        $view_array['debug_mode'] = $this->check_debug_mode();
        $view_array['js_load_later'] = (absint($options['js_load_later']) === 1);

        if ($options['ua_enabled'] == 1) {
            //If universal is enabled
            $view_array = $this->prepare_universal_code($view_array, $options);
            $this->load_view('universal_script.php', $view_array);

        } else {
            //Classic ga is enabled
            $view_array = $this->prepare_classic_code($view_array, $options);
            $this->load_view('classic_script.php', $view_array);

        }

    }

    /**
     * Prepare classic tracing code and print
     * @param $view_array array Array to be passed to view
     * @param $options array
     * @return array
     */
    private function prepare_classic_code($view_array, $options)
    {

        $view_array['ga_src'] = "('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js'";

        if ($view_array['debug_mode'] == true) {
            //Did u notice additional /u in url ?
            //@source https://developers.google.com/analytics/resources/articles/gaTrackingTroubleshooting#gaDebug
            $view_array['ga_src'] = "('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/u/ga_debug.js'";
        }

        //@source https://support.google.com/analytics/answer/2444872
        if ($options['displayfeatures'] == 1) {
            $view_array['ga_src'] = "('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js'";
            if ($view_array['debug_mode'] == true) {
                $view_array['ga_src'] = "('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc_debug.js'";
            }
        }

        $view_array['gaq'][] = "['_setAccount', '" . $options['ga_id'] . "']";

        $view_array['gaq'][] = "['_setDomainName', '" . $options['ga_domain'] . "']";

        if ($options['sample_rate'] != 100) {
            $view_array['gaq'][] = "['_setSampleRate', '" . $options['sample_rate'] . "']";
        }

        if ($options['allow_linker'] == 1) {
            $view_array['gaq'][] = "['_setAllowLinker', true]";
        }

        if ($options['allow_anchor'] == 1) {
            $view_array['gaq'][] = "['_setAllowAnchor', true]";
        }

        if ($options['force_ssl'] == 1) {
            $view_array['gaq'][] = "['_gat._forceSSL']";
        }

        if ($options['anonymise_ip'] == 1) {
            $view_array['gaq'][] = "['_gat._anonymizeIp']";
        }

        if ($options['ga_ela'] == 1) {
            $view_array['gaq'][] = "['_require', 'inpage_linkid', '//www.google-analytics.com/plugins/ga/inpage_linkid.js']";
        }

        if (is_404()) {
            if ($options['log_404'] == 1) {
                $view_array['gaq'][] = "['_trackEvent','error','404','/404.html?page=' + document.location.pathname + document.location.search + '&from=' + document.referrer,1,true]";
            } else {
                $view_array['gaq'][] = "['_trackPageview','/404.html?page=' + document.location.pathname + document.location.search + '&from=' + document.referrer]";
            }
        } else {
            $view_array['gaq'][] = "['_trackPageview']";
        }

        $view_array['custom_trackers'] = $options['custom_trackers'];

        return $view_array;

    }

    /**
     * Prepare universal tracking code and print
     * @param $view_array array Array to be passed to view
     * @param $options array
     * @return array
     */
    private function prepare_universal_code($view_array, $options)
    {
        $create_args = array(
            'trackingId' => $options['ga_id'],
            'cookieDomain' => $options['ga_domain']
        );

        if ($options['allow_linker'] == 1) {
            $create_args['allowLinker'] = true;
        }

        if ($options['allow_anchor'] == 1) {
            $create_args['allowAnchor'] = true;
        }

        if ($options['sample_rate'] != 100) {
            $create_args['sampleRate'] = $options['sample_rate'];
        }

        //@source https://developers.google.com/analytics/devguides/collection/analyticsjs/creating-trackers#specifying_fields_at_creation_time
        $view_array['gaq'][] = "'create', " . json_encode($create_args, JSON_HEX_QUOT);

        if ($options['force_ssl'] == 1) {
            $view_array['gaq'][] = "'set', 'forceSSL', true";
        }

        if ($options['anonymise_ip'] == 1) {
            $view_array['gaq'][] = "'set', 'anonymizeIp', true";
        }

        if ($options['displayfeatures'] == 1) {
            $view_array['gaq'][] = "'require', 'displayfeatures'";
        }

        if ($options['ga_ela'] == 1) {
            $view_array['gaq'][] = "'require', 'linkid'";
        }

        if ($options['custom_trackers'] !== '') {
            $view_array['gaq'][] = array(
                'custom_trackers' => $options['custom_trackers']
            );
        }

        if (is_404()) {
            if ($options['log_404'] == 1) {
                $view_array['gaq'][] = "'send','event','error','404','/404.html?page=' + document.location.pathname + document.location.search + '&from=' + document.referrer, 1, {nonInteraction: true}";
            } else {
                $view_array['gaq'][] = "'send','pageview','/404.html?page=' + document.location.pathname + document.location.search + '&from=' + document.referrer";
            }
        } else {
            $view_array['gaq'][] = "'send','pageview'";
        }

        return $view_array;
    }

    /**
     * Print google webmaster meta tag to document header
     */
    function print_webmaster_code()
    {

        $this->load_view('google_webmaster.php', array('code' => $this->db_options['webmaster']['google_code']));

    }

    /**
     * Enqueue event tracking javascript file
     */
    function add_event_tracking_js()
    {
        //if tracking not possible return early
        if ($this->is_tracking_possible() === false) return;

        //Load jquery if not loaded by theme
        if (wp_script_is('jquery', $list = 'enqueued') === false) {
            wp_enqueue_script('jquery');
        }

        $is_min = (defined('WP_DEBUG') && WP_DEBUG == true) ? '' : '.min';
        //Depends on jquery
        wp_enqueue_script('asga-event-tracking', plugins_url('/js/front-end' . $is_min . '.js', ASGA_BASE_FILE), array('jquery'), ASGA_PLUGIN_VER, true);
        //WP inbuilt hack to print js options object just before this script
        wp_localize_script('asga-event-tracking', '_asgaOpt', $this->get_js_options());

    }


    /**
     * Load view and show it to front-end
     * @param $file string File name
     * @param $options array Array to be passed to view, not an unused variable
     * @throws \Exception
     */
    private function load_view($file, $options)
    {
        $file_path = plugin_dir_path(ASGA_BASE_FILE) . 'views/' . $file;
        if (is_readable($file_path)) {
            require $file_path;
        } else {
            throw new \Exception('Unable to load template file - ' . esc_html($file_path));
        }
    }


    /**
     * Check if to enable debugging mode
     * @return bool
     */
    private function check_debug_mode()
    {
        //debug mode is only for logged-in admins/network admins
        if (current_user_can('manage_options') || is_super_admin()) {
            return ($this->db_options['debug_mode'] == 1);
        }
        return false;
    }

    /**
     * Function determines whether to print tracking code or not
     * @param $reason bool
     * @return bool|array
     */
    private function is_tracking_possible($reason = false)
    {
        $status = array(
            'status' => false,
            'reason' => ''
        );

        if (is_preview()) {
            $status['reason'] = 'GA Tracking is disabled in preview mode';
        } //if GA id is not set return early with a message
        else if (empty($this->db_options['ga_id'])) {
            $status['reason'] = 'GA ID is not set';
        } //if a user is logged in
        else if (is_user_logged_in()) {

            if (is_multisite() && is_super_admin()) {
                //if a network admin is logged in
                if (isset($this->db_options['ignore_role_networkAdmin']) && ($this->db_options['ignore_role_networkAdmin'] == 1)) {
                    $status['reason'] = 'GA Tracking is disabled for networkAdmin';
                } else {
                    $status['status'] = true;
                }
            } else {
                //If a normal user is logged in
                $role = array_shift(wp_get_current_user()->roles);
                if (isset($this->db_options['ignore_role_' . $role]) && ($this->db_options['ignore_role_' . $role] == 1)) {
                    $status['reason'] = 'GA Tracking is disabled for - ' . $role;
                } else {
                    $status['status'] = true;
                }
            }
        } else {
            $status['status'] = true;
        }
        //Don't return reason
        if ($reason === false) {
            return $status['status'];
        }
        return $status;

    }

    /**
     * Return array of options to be used in event tracking js
     * @return array
     */
    private function get_js_options()
    {
        return array(
            'mailLinks' => esc_js($this->db_options['track_mail_links']),
            'outgoingLinks' => esc_js($this->db_options['track_outbound_links']),
            'downloadLinks' => esc_js($this->db_options['track_download_links']),
            'downloadExt' => esc_js($this->db_options['track_download_ext']),
            'outboundLinkType' => esc_js($this->db_options['track_outbound_link_type']),
            'nonInteractive' => esc_js($this->db_options['track_non_interactive']),
        );

    }

    /**
     * Check if user wants any kind of event tracking
     * @return bool
     */
    private function need_to_load_event_tracking_js()
    {
        return ($this->db_options['track_mail_links'] == 1 || $this->db_options['track_outbound_links'] == 1 || $this->db_options['track_download_links'] == 1);
    }

    /**
     * Add the UTM source parameters in the RSS feeds links to track traffic
     *
     * @param string $guid
     * @source https://github.com/awesomemotive/google-analytics-for-wordpress/blob/trunk/frontend/class-frontend.php#L42
     * @return string
     */
    public function rss_link_tagger($guid)
    {
        global $post;
        if (is_feed()) {
            if ($this->db_options['allow_anchor'] == 1) {
                $delimiter = '#';
            } else {
                $delimiter = '?';
                if (strpos($guid, $delimiter) > 0) {
                    $delimiter = '&amp;';
                }
            }
            return $guid . $delimiter . 'utm_source=rss&amp;utm_medium=rss&amp;utm_campaign=' . urlencode($post->post_name);
        }
        return $guid;
    }

} //end class
