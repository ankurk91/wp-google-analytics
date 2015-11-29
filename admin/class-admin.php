<?php

namespace Ank91\Ank_Simplified_GA_Plugin;
/**
 * Class Ank_Simplified_GA_Admin
 * @package Ank-Simplified-GA
 */
class Ank_Simplified_GA_Admin
{

    private static $instances = array();
    /* Store plugin option page slug, so that we can change it with ease */
    const PLUGIN_SLUG = 'asga_options_page';
    const PLUGIN_OPTION_GROUP = 'asga_plugin_options';

    private function __construct()
    {

        /* To save default options upon activation*/
        register_activation_hook(ASGA_BASE_FILE, array($this, 'do_upon_plugin_activation'));

        /* For register setting*/
        add_action('admin_init', array($this, 'register_plugin_settings'));

        /* Settings link on plugin listing page*/
        add_filter('plugin_action_links_' . ASGA_BASE_FILE, array($this, 'add_plugin_actions_links'), 10, 2);

        /* Add settings link under admin->settings menu */
        add_action('admin_menu', array($this, 'add_to_settings_menu'));

        /* Show warning if debug mode is on  */
        add_action('admin_notices', array($this, 'show_admin_notice'));

        /* Check for database upgrades*/
        add_action('plugins_loaded', array($this, 'perform_upgrade'));

        add_action('plugins_loaded', array($this, 'load_text_domain'));

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

    public static function load_text_domain()
    {
        load_plugin_textdomain(ASGA_TEXT_DOMAIN, false, dirname(ASGA_BASE_FILE) . '/languages/');
    }

    /*
     * Save default settings upon plugin activation
     */
    function do_upon_plugin_activation()
    {

        //if db options not exists then update with defaults
        if (get_option(ASGA_OPTION_NAME) == false) {
            update_option(ASGA_OPTION_NAME, $this->get_default_options());
        }

    }

    /**
     * Register plugin settings, using WP settings API
     */
    function register_plugin_settings()
    {
        register_setting(self::PLUGIN_OPTION_GROUP, ASGA_OPTION_NAME, array($this, 'validate_form_post'));
    }


    /**
     * Adds a 'Settings' link for this plugin on plugin listing page
     *
     * @param $links
     * @return array  Links array
     */
    function add_plugin_actions_links($links)
    {

        if (current_user_can('manage_options')) {
            $build_url = add_query_arg('page', self::PLUGIN_SLUG, 'options-general.php');
            array_unshift(
                $links,
                sprintf('<a href="%s">%s</a>', $build_url, __('Settings', ASGA_TEXT_DOMAIN))
            );
        }

        return $links;
    }

    /**
     * Adds link to Plugin Option page and do related stuff
     */
    function add_to_settings_menu()
    {
        $page_hook_suffix = add_submenu_page('options-general.php', 'Ank Simplified Google Analytics', '<span style="color:#f29611">Google Analytics</span>', 'manage_options', self::PLUGIN_SLUG, array($this, 'load_options_page'));
        /*add help stuff via tab*/
        add_action("load-$page_hook_suffix", array($this, 'add_help_menu_tab'));
        /*we can load additional css/js to our option page here */
        add_action('admin_print_scripts-' . $page_hook_suffix, array($this, 'enqueue_admin_js'));

    }


    /**
     * Return default options for this plugin
     * @return array
     */
    private function get_default_options()
    {
        $defaults = array(
            'plugin_ver' => ASGA_PLUGIN_VER,
            'ga_id' => '',
            'js_location' => 1,
            'js_load_later' => 0,
            'js_priority' => 20,
            'log_404' => 0,
            'ua_enabled' => 1,
            'displayfeatures' => 0,
            'ga_ela' => 0,
            'anonymise_ip' => 0,
            'ga_domain' => '',
            'debug_mode' => 0,
            'force_ssl' => 1,
            'custom_trackers' => '',
            'allow_linker' => 0,
            'allow_anchor' => 0,
            'track_mail_links' => 0,
            'track_outbound_links' => 0,
            'track_outbound_link_type' => 1,
            'track_download_links' => 0,
            'track_download_ext' => 'doc*,xls*,ppt*,pdf,zip,rar,exe,mp3',
            'track_non_interactive' => 1,
            'webmaster' => array(
                'google_code' => ''
            )

        );
        //ignored roles by default
        $ignored_roles = array('networkAdmin', 'administrator', 'editor');
        //store roles as well
        foreach ($this->get_all_roles() as $role) {
            //ignore some roles by-default
            if (in_array($role['id'], $ignored_roles)) {
                $defaults['ignore_role_' . $role['id']] = 1;
            } else {
                $defaults['ignore_role_' . $role['id']] = 0;
            }
        }

        return $defaults;
    }


    /**
     * Callback Function to handle and validate the form data
     *
     * @param array $in - POST array
     * @returns array - Validated array
     */
    function validate_form_post($in)
    {

        $out = array();
        //always store plugin version to db
        $out['plugin_ver'] = ASGA_PLUGIN_VER;

        // Get the actual tracking ID
        if (!preg_match('|^UA-\d{4,}-\d+$|', (string)$in['ga_id'])) {
            $out['ga_id'] = '';
            //warn user that the entered id is not valid
            add_settings_error(ASGA_OPTION_NAME, 'ga_id', __('Your GA tracking ID seems invalid. Please validate.', ASGA_TEXT_DOMAIN));
        } else {
            $out['ga_id'] = sanitize_text_field($in['ga_id']);
        }

        $radio_items = array('js_location', 'js_load_later');

        foreach ($radio_items as $item) {
            $out[$item] = absint($in[$item]);
        }

        $out['js_priority'] = (empty($in['js_priority'])) ? 20 : absint($in['js_priority']);

        $out['ga_domain'] = sanitize_text_field(($in['ga_domain']));

        $out['custom_trackers'] = trim($in['custom_trackers']);

        $checkbox_items = array('ua_enabled', 'anonymise_ip', 'displayfeatures', 'ga_ela', 'log_404', 'debug_mode', 'force_ssl', 'allow_linker', 'allow_anchor', 'track_mail_links', 'track_outbound_links', 'track_download_links', 'track_outbound_link_type', 'track_non_interactive');
        //add rolls to checkbox_items array
        foreach ($this->get_all_roles() as $role) {
            $checkbox_items[] = 'ignore_role_' . $role['id'];
        }

        foreach ($checkbox_items as $item) {
            if (isset($in[$item]) && '1' == $in[$item]) {
                $out[$item] = 1;
            } else {
                $out[$item] = 0;
            }

        }

        // Google webmaster code
        $out['webmaster']['google_code'] = sanitize_text_field($in['webmaster']['google_code']);
        //Extensions to track as downloads
        $out['track_download_ext'] = sanitize_text_field($in['track_download_ext']);


        return $out;
    }

    /**
     * Function will print our option page form
     */
    function load_options_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', ASGA_TEXT_DOMAIN));
        }

        $file_path = __DIR__ . '/views/settings_page.php';

        if (is_readable($file_path)) {
            require($file_path);
        } else {
            throw new \Exception("Unable to load settings page, Template File not found, (v" . ASGA_PLUGIN_VER . ")");
        }

    }

    /**
     * Function will add help tab to our option page
     * @require wp v3.3+
     */
    function add_help_menu_tab()
    {
        /*get current screen object*/
        $curr_screen = get_current_screen();

        $curr_screen->add_help_tab(
            array(
                'id' => 'asga-overview',
                'title' => 'Basic',
                'content' => '<p><strong>Do you have a Google Analytics Account ?</strong><br>' .
                    'In order to use this plugin you need to have a Google Analytics Account. Create an account <a target="_blank" href="http://www.google.com/analytics">here</a>. It is FREE. <br>' .
                    '<strong>How do i find my Google Analytics ID ?</strong><br>' .
                    'Please check out this <a target="_blank" href="https://support.google.com/analytics/answer/1032385?hl=en">link</a><br>' .
                    '<strong>How do i view my stats ?</strong><br>' .
                    'Login to Google Analytics Account with your G-Mail ID to view stats.' .
                    '</p>'

            )
        );

        $curr_screen->add_help_tab(
            array(
                'id' => 'asga-troubleshoot',
                'title' => 'Troubleshoot',
                'content' => '<p><strong>Things to remember</strong><br>' .
                    '<ul>' .
                    '<li>If you are using a cache/performance plugin, you need to flush/delete your site cache after saving settings here.</li>' .
                    '<li>It can take up to 24-48 hours after adding the tracking code before any analytical data appears in your Google Analytics account. </li>' .
                    '</ul>' .
                    '</p>'

            )
        );
        $curr_screen->add_help_tab(
            array(
                'id' => 'asga-more-info',
                'title' => 'More',
                'content' => '<p><strong>Need more information ?</strong><br>' .
                    'A brief FAQ is available to solve your common issues, ' .
                    'click <a href="https://wordpress.org/plugins/ank-simplified-ga/faq/" target="_blank">here</a> to read more.<br>' .
                    'Support is only available on WordPress Forums, click <a href="http://wordpress.org/support/plugin/ank-simplified-ga" target="_blank">here</a> to ask anything about this plugin.<br>' .
                    'You can also browse the source code of this  plugin on <a href="https://github.com/ank91/ank-simplified-ga" target="_blank">GitHub</a>. ' .
                    '</p>'

            )
        );

        /*add a help sidebar with links */
        $curr_screen->set_help_sidebar(
            '<p><strong>Quick Links</strong></p>' .
            '<p><a href="https://wordpress.org/plugins/ank-simplified-ga/faq/" target="_blank">Plugin FAQ</a></p>' .
            '<p><a href="https://github.com/ank91/ank-simplified-ga" target="_blank">Plugin Home</a></p>'
        );
    }

    /**
     * Return all roles plus superAdmin if multi-site is enabled
     * @return array
     */
    private function get_all_roles()
    {
        global $wp_roles;
        $return_roles = array();

        if (!isset($wp_roles))
            $wp_roles = new \WP_Roles();

        $role_list = $wp_roles->roles;

        /**
         * Filter: 'editable_roles' - Allows filtering of the roles shown within the plugin (and elsewhere in WP as it's a WP filter)
         *
         * @api array $role_list
         */
        $editable_roles = apply_filters('editable_roles', $role_list);

        foreach ($editable_roles as $id => $role) {
            $return_roles[] = array(
                'id' => $id,
                'name' => translate_user_role($role['name']),
            );
        }

        //append a custom role if multi-site is enabled
        if (is_multisite()) {
            $return_roles[] = array(
                'id' => 'networkAdmin',
                'name' => __('Network Administrator', ASGA_TEXT_DOMAIN)
            );
        }

        return $return_roles;
    }

    /**
     * Show a warning notice if debug mode is on
     */
    function show_admin_notice()
    {
        if ($this->check_admin_notice() === true) {
            ?>
            <div id="asga_message" class="notice notice-warning is-dismissible">
                <p>
                    <b><?php _e("Google Analytics debug mode is enabled for this site. Don't forget to disable this option in production.", ASGA_TEXT_DOMAIN) ?></b>
                </p>
            </div>
            <?php
        }
    }

    /**
     * Check if to show admin notice or not
     * @return bool
     */
    private function check_admin_notice()
    {
        //show only for this plugin option page
        if (strpos(get_current_screen()->id, self::PLUGIN_SLUG) === false) return false;

        $options = $this->get_safe_options();
        //id ga id is not set return early
        if (empty($options['ga_id'])) return false;
        //if debug mode is off return early
        if ($options['debug_mode'] == 0) return false;
        //else return true
        return true;

    }

    /**
     * Get fail safe options
     * @return array
     */
    private function get_safe_options()
    {
        //get fresh options from db
        $db_options = get_option(ASGA_OPTION_NAME);
        //be fail safe, if not array then array_merge may fail
        if (is_array($db_options) === false) {
            $db_options = array();
        }
        //if options not exists in db then init with defaults , also always append default options to existing options
        $db_options = empty($db_options) ? $this->get_default_options() : array_merge($this->get_default_options(), $db_options);
        return $db_options;

    }

    /**
     * Upgrade plugin database options
     */
    function perform_upgrade()
    {
        //get fresh options from db
        $db_options = get_option(ASGA_OPTION_NAME);
        //check if we need to proceed , if no return early
        if ($this->can_proceed_to_upgrade($db_options) === false) return;
        //get default options
        $default_options = $this->get_default_options();
        //merge with db options , preserve old
        $new_options = (empty($db_options)) ? $default_options : array_merge($default_options, $db_options);
        //update plugin version
        $new_options['plugin_ver'] = ASGA_PLUGIN_VER;
        //write options back to db
        update_option(ASGA_OPTION_NAME, $new_options);

    }

    /**
     * Check if we need to upgrade database options or not
     * @param $db_options
     * @return bool
     */
    private function can_proceed_to_upgrade($db_options)
    {

        if (empty($db_options) || !is_array($db_options)) return true;

        if (!isset($db_options['plugin_ver'])) return true;

        return version_compare($db_options['plugin_ver'], ASGA_PLUGIN_VER, '<');

    }

    /**
     * Print option page javascript
     */
    function enqueue_admin_js()
    {
        $is_min = (WP_DEBUG == 1) ? '' : '.min';
        wp_enqueue_script('asga-admin', plugins_url("/js/option-page" . $is_min . ".js", __FILE__), array('jquery'), ASGA_PLUGIN_VER, false);
    }

} //end class
