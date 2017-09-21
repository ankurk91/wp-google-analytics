<?php

namespace Ankur\Plugins\Ank_Simplified_GA;


/**
 * Class Frontend
 * @package Ankur\Plugins\Ank_Simplified_GA
 */
class Frontend extends Singleton
{

    /**
     * Stores database options
     * @var array
     */
    private $db = array();


    protected function __construct()
    {
        // Store database options in a local array
        $this->db = get_option(ASGA_OPTION_NAME);

        // Get action's priority
        $js_priority = absint($this->db['js_priority']);

        // Decide where to print code
        if ($this->db['js_location'] == 1) {
            add_action('wp_head', array($this, 'print_tracking_code'), $js_priority);
        } else {
            add_action('wp_footer', array($this, 'print_tracking_code'), $js_priority);
        }

        if ($this->need_to_load_event_tracking_js()) {
            // Load event tracking js file
            add_action('wp_footer', array($this, 'add_event_tracking_js'), 9);
        }

        if ($this->db['tag_rss_links'] == 1) {
            add_filter('the_permalink_rss', array($this, 'rss_link_tagger'), 99);
        }

    }

    /**
     * Prepare and print javascript code to front end
     */
    public function print_tracking_code()
    {
        // Store database options into a local variable coz it is going to modified
        $options = $this->db;

        // Check if to proceed or not, return early with a message if not
        $tracking_status = $this->is_tracking_possible(true);

        if ($tracking_status['status'] === false) {
            $this->load_view('ga-disabled.php', $tracking_status);
            return;
        }

        // Finalize some db options
        $options['ga_id'] = esc_js($options['ga_id']);
        $options['ga_domain'] = empty($options['ga_domain']) ? 'auto' : esc_js($options['ga_domain']);

        // These flags will be used in view
        $view_array = array(
            'gaq' => array()
        );

        // Check for debug mode
        $view_array['debug_mode'] = $this->check_debug_mode();

        if ($options['ua_enabled'] == 1) {
            // If universal is enabled
            $view_array = $this->prepare_universal_code($view_array, $options);
            $this->load_view('universal-script.php', $view_array);

        } else {
            // Classic ga is enabled
            $view_array = $this->prepare_classic_code($view_array, $options);
            $this->load_view('classic-script.php', $view_array);

        }

    }

    /**
     * Prepare classic tracing code and print
     * @param $data array Array to be passed to view
     * @param $options array
     * @return array
     */
    private function prepare_classic_code($data, $options)
    {

        $data['ga_src'] = "('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js'";

        if ($data['debug_mode'] == true) {
            // Did u notice additional /u in url ?
            // @source https://developers.google.com/analytics/resources/articles/gaTrackingTroubleshooting#gaDebug
            $data['ga_src'] = "('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/u/ga_debug.js'";
        }

        // @source https://support.google.com/analytics/answer/2444872
        if ($options['displayfeatures'] == 1) {
            $data['ga_src'] = "('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js'";
            if ($data['debug_mode'] == true) {
                $data['ga_src'] = "('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc_debug.js'";
            }
        }

        $data['gaq'][] = "['_setAccount', '" . $options['ga_id'] . "']";

        $data['gaq'][] = "['_setDomainName', '" . $options['ga_domain'] . "']";

        if ($options['sample_rate'] != 100) {
            $data['gaq'][] = "['_setSampleRate', '" . $options['sample_rate'] . "']";
        }

        if ($options['allow_linker'] == 1) {
            $data['gaq'][] = "['_setAllowLinker', true]";
        }

        if ($options['allow_anchor'] == 1) {
            $data['gaq'][] = "['_setAllowAnchor', true]";
        }

        if ($options['force_ssl'] == 1) {
            $data['gaq'][] = "['_gat._forceSSL']";
        }

        if ($options['anonymise_ip'] == 1) {
            $data['gaq'][] = "['_gat._anonymizeIp']";
        }

        if ($options['ga_ela'] == 1) {
            $data['gaq'][] = "['_require', 'inpage_linkid', '//www.google-analytics.com/plugins/ga/inpage_linkid.js']";
        }

        if (is_404()) {
            if ($options['log_404'] == 1) {
                $data['gaq'][] = "['_trackEvent','error','404','/404.html?page=' + document.location.pathname + document.location.search + '&from=' + document.referrer,1,true]";
            } else {
                $data['gaq'][] = "['_trackPageview','/404.html?page=' + document.location.pathname + document.location.search + '&from=' + document.referrer]";
            }
        } else {
            $data['gaq'][] = "['_trackPageview']";
        }

        $data['custom_trackers'] = $options['custom_trackers'];

        return $data;

    }

    /**
     * Prepare universal tracking code and print
     * @param $data array Array to be passed to view
     * @param $options array
     * @return array
     */
    private function prepare_universal_code($data, $options)
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

        // @source https://developers.google.com/analytics/devguides/collection/analyticsjs/creating-trackers#specifying_fields_at_creation_time
        $data['gaq'][] = "'create', " . json_encode($create_args, JSON_HEX_QUOT);

        if ($options['force_ssl'] == 1) {
            $data['gaq'][] = "'set', 'forceSSL', true";
        }

        if ($options['anonymise_ip'] == 1) {
            $data['gaq'][] = "'set', 'anonymizeIp', true";
        }

        if ($options['displayfeatures'] == 1) {
            $data['gaq'][] = "'require', 'displayfeatures'";
        }

        if ($options['ga_ela'] == 1) {
            $data['gaq'][] = "'require', 'linkid'";
        }

        if ($options['custom_trackers'] !== '') {
            $data['gaq'][] = array(
                'custom_trackers' => $options['custom_trackers']
            );
        }

        if (is_404()) {
            if ($options['log_404'] == 1) {
                $data['gaq'][] = "'send','event','error','404','/404.html?page=' + document.location.pathname + document.location.search + '&from=' + document.referrer, 1, {nonInteraction: true}";
            } else {
                $data['gaq'][] = "'send','pageview','/404.html?page=' + document.location.pathname + document.location.search + '&from=' + document.referrer";
            }
        } else {
            $data['gaq'][] = "'send','pageview'";
        }

        return $data;
    }

    /**
     * Enqueue event tracking javascript file
     */
    public function add_event_tracking_js()
    {
        // If tracking not possible return early
        if ($this->is_tracking_possible() === false) return;

        $is_min = (defined('WP_DEBUG') && WP_DEBUG == true) ? '' : '.min';
        // No longer depends on jquery
        wp_enqueue_script('asga-event-tracking', plugins_url('/assets/front-end' . $is_min . '.js', ASGA_BASE_FILE), array(), ASGA_PLUGIN_VER, true);
        // WP inbuilt hack to print js options object just before this script
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
        // Debug mode is only meant for logged-in admins/network admins
        if (current_user_can('manage_options') || is_super_admin()) {
            return ($this->db['debug_mode'] == 1);
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
        } // if GA id is not set return early with a message
        else if (empty($this->db['ga_id'])) {
            $status['reason'] = 'GA ID is not set';
        } // if a user is logged in
        else if (is_user_logged_in()) {

            if (is_multisite() && is_super_admin()) {
                // if a network admin is logged in
                if (isset($this->db['ignore_role_networkAdmin']) && ($this->db['ignore_role_networkAdmin'] == 1)) {
                    $status['reason'] = 'GA Tracking is disabled for networkAdmin';
                } else {
                    $status['status'] = true;
                }
            } else {
                // If a normal user is logged in
                $role = $this->get_current_user_role();
                if (isset($this->db['ignore_role_' . $role]) && ($this->db['ignore_role_' . $role] == 1)) {
                    $status['reason'] = 'GA Tracking is disabled for - ' . $role;
                } else {
                    $status['status'] = true;
                }
            }
        } else {
            $status['status'] = true;
        }

        return ($reason) ? $status : $status['status'];
    }

    /**
     * Return array of options to be used in event tracking js
     * @return array
     */
    private function get_js_options()
    {
        return array(
            'mailLinks' => esc_js($this->db['track_mail_links']),
            'outgoingLinks' => esc_js($this->db['track_outbound_links']),
            'downloadLinks' => esc_js($this->db['track_download_links']),
            'downloadExt' => esc_js($this->db['track_download_ext']),
            'outboundLinkType' => esc_js($this->db['track_outbound_link_type']),
            'nonInteractive' => esc_js($this->db['track_non_interactive']),
        );

    }

    /**
     * Better way to get logged in user role
     * @return string
     */
    private function get_current_user_role()
    {
        $user = get_userdata(get_current_user_id());
        return empty($user) ? '' : array_shift($user->roles);
    }

    /**
     * Check if user wants any kind of event tracking
     * @return bool
     */
    private function need_to_load_event_tracking_js()
    {
        return ($this->db['track_mail_links'] == 1 || $this->db['track_outbound_links'] == 1 || $this->db['track_download_links'] == 1);
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
            if ($this->db['allow_anchor'] == 1) {
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

}
