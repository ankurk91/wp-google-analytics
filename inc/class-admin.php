<?php

namespace Ankur\Plugins\Ank_Simplified_GA;

/**
 * Class Admin
 * @package Ankur\Plugins\Ank_Simplified_GA
 */
class Admin extends Singleton
{

    /**
     * Store plugin option page slug, so that we can change it with ease
     */
    const PLUGIN_SLUG = 'asga_options_page';
    const PLUGIN_OPTION_GROUP = 'asga_plugin_options';

    protected function __construct()
    {
        // To save default options upon activation
        register_activation_hook(plugin_basename(ASGA_BASE_FILE), array($this, 'do_upon_plugin_activation'));

        // Register setting
        add_action('admin_init', array($this, 'register_plugin_settings'));

        // Settings link on plugin listing page
        add_filter('plugin_action_links_' . plugin_basename(ASGA_BASE_FILE), array($this, 'add_plugin_actions_links'), 10, 2);

        // Add settings link under admin->settings menu
        add_action('admin_menu', array($this, 'add_to_settings_menu'));

        // Show warning if debug mode is on
        add_action('admin_notices', array($this, 'show_admin_notice'));

        // Check for database upgrades
        add_action('plugins_loaded', array($this, 'perform_upgrade'));

        add_action('plugins_loaded', array($this, 'load_text_domain'));

    }


    public static function load_text_domain()
    {
        load_plugin_textdomain('ank-simplified-ga', false, dirname(plugin_basename(ASGA_BASE_FILE)) . '/languages/');
    }

    /*
     * Save default settings upon plugin activation
     */
    public function do_upon_plugin_activation()
    {
        if (get_option(ASGA_OPTION_NAME, false) === false) {
            update_option(ASGA_OPTION_NAME, $this->get_default_options());
        }
    }

    /**
     * Register plugin settings, using WP settings API
     */
    public function register_plugin_settings()
    {
        register_setting(self::PLUGIN_OPTION_GROUP, ASGA_OPTION_NAME, array($this, 'validate_form_post'));
    }


    /**
     * Adds a 'Settings' link for this plugin on plugin listing page
     * @param $links
     * @return array  Links array
     */
    public function add_plugin_actions_links($links)
    {

        if (current_user_can('manage_options')) {
            $url = add_query_arg('page', self::PLUGIN_SLUG, 'options-general.php');
            array_unshift(
                $links,
                sprintf('<a href="%s">%s</a>', $url, __('Settings', 'ank-simplified-ga'))
            );
        }

        return $links;
    }

    /**
     * Adds link to Plugin Option page and do related stuff
     */
    public function add_to_settings_menu()
    {
        $page_hook_suffix = add_submenu_page(
            'options-general.php',
            'Google Analytics Simplified ', //page title
            'Google Analytics',  //menu name
            'manage_options',
            self::PLUGIN_SLUG,
            array($this, 'load_options_page'));

        // We can load additional css/js to our option page here
        add_action('admin_print_scripts-' . $page_hook_suffix, array($this, 'add_admin_assets'));

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
            'js_priority' => 20,
            'log_404' => 0,
            'ua_enabled' => 1,
            'displayfeatures' => 0,
            'ga_ela' => 0,
            'anonymise_ip' => 0,
            'ga_domain' => '',
            'sample_rate' => 100,
            'debug_mode' => 0,
            'force_ssl' => 1,
            'custom_trackers' => '',
            'allow_linker' => 0,
            'allow_anchor' => 0,
            'tag_rss_links' => 1,
            'track_mail_links' => 0,
            'track_outbound_links' => 0,
            'track_outbound_link_type' => 1,
            'track_download_links' => 0,
            'track_download_ext' => 'doc*,xls*,ppt*,pdf,zip,rar,exe,mp3',
            'track_non_interactive' => 1
        );

        // Ignored some roles by default
        $ignored_roles = array('networkAdmin', 'administrator', 'editor');

        // Store roles to db
        foreach ($this->get_all_roles() as $role) {
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
    public function validate_form_post($in)
    {

        $out = array();
        $errors = array();
        //Always store plugin version to db
        $out['plugin_ver'] = ASGA_PLUGIN_VER;

        // Get the actual tracking ID
        if (!preg_match('|^UA-\d{4,}-\d+$|', (string)$in['ga_id'])) {
            $out['ga_id'] = '';
            // Warn user that the entered id is not valid
            $errors[] = __('Your GA tracking ID seems invalid. Please validate.', 'ank-simplified-ga');
        } else {
            $out['ga_id'] = sanitize_text_field($in['ga_id']);
        }

        $radio_items = array('js_location');

        foreach ($radio_items as $item) {
            $out[$item] = absint($in[$item]);
        }

        $out['js_priority'] = (trim($in['js_priority']) == '') ? 20 : absint($in['js_priority']);

        $out['ga_domain'] = sanitize_text_field(($in['ga_domain']));
        // http://stackoverflow.com/questions/9549866/php-regex-to-remove-http-from-string
        $out['ga_domain'] = preg_replace('#^https?://#', '', $out['ga_domain']);

        $out['sample_rate'] = floatval(($in['sample_rate']));
        // Sample rate should be between 1 to 100
        if ($out['sample_rate'] <= 0 || $out['sample_rate'] > 100) {
            $out['sample_rate'] = 100;
            $errors[] = __('Sample rate should be between 1 to 100.', 'ank-simplified-ga');
        }

        $out['custom_trackers'] = trim($in['custom_trackers']);

        $checkbox_items = array('ua_enabled', 'anonymise_ip', 'displayfeatures', 'ga_ela', 'log_404', 'debug_mode', 'force_ssl', 'allow_linker', 'allow_anchor', 'tag_rss_links', 'track_mail_links', 'track_outbound_links', 'track_download_links', 'track_outbound_link_type', 'track_non_interactive');
        // Add rolls to checkbox_items array
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

        // Extensions to be tracked as downloads
        $out['track_download_ext'] = sanitize_text_field($in['track_download_ext']);

        // Show all form errors in a single notice
        if (!empty($errors)) {
            add_settings_error('ank-simplified-ga', 'ank-simplified-ga', implode('<br>', $errors));
        }

        return $out;
    }

    /**
     * Function will print our option page form
     */
    public function load_options_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You don\'t have sufficient permissions to access this page.', 'ank-simplified-ga'));
        }

        $this->load_view('settings-page.php');

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

        // Append a custom role if multi-site is enabled
        if (is_multisite()) {
            $return_roles[] = array(
                'id' => 'networkAdmin',
                'name' => __('Network Administrator', 'ank-simplified-ga')
            );
        }

        return $return_roles;
    }

    /**
     * Show a warning notice if debug mode is on
     */
    public function show_admin_notice()
    {
        // Show only for this plugin option page
        if (strpos(get_current_screen()->id, self::PLUGIN_SLUG) === false) return;

        $options = $this->get_safe_options();

        // If debug mode is off return early
        if ($options['debug_mode'] == 0) return;

        $this->load_view('admin-notice.php', array());

    }


    /**
     * Get fail safe options
     * @return array
     */
    private function get_safe_options()
    {
        // Get fresh options from db
        $db_options = get_option(ASGA_OPTION_NAME);

        // Be fail safe, if not array then array_merge may fail
        if (!is_array($db_options)) {
            $db_options = array();
        }

        // If options not exists in db then init with defaults , also always append default options to existing options
        $db_options = empty($db_options) ? $this->get_default_options() : array_merge($this->get_default_options(), $db_options);
        return $db_options;

    }

    /**
     * Upgrade plugin database options
     */
    public function perform_upgrade()
    {
        // Get fresh options from db
        $db_options = get_option(ASGA_OPTION_NAME);
        // Check if we need to proceed , if no return early
        if ($this->should_proceed_to_upgrade($db_options) === false) return;
        // Get default options
        $default_options = $this->get_default_options();
        // Merge with db options , preserve old
        $new_options = (empty($db_options)) ? $default_options : array_merge($default_options, $db_options);
        // Update plugin version
        $new_options['plugin_ver'] = ASGA_PLUGIN_VER;
        // Write options back to db
        update_option(ASGA_OPTION_NAME, $new_options);

    }

    /**
     * Check if we need to upgrade database options or not
     * @param $db_options
     * @return bool
     */
    private function should_proceed_to_upgrade($db_options)
    {

        if (empty($db_options) || !is_array($db_options)) return true;

        if (!isset($db_options['plugin_ver'])) return true;

        return version_compare($db_options['plugin_ver'], ASGA_PLUGIN_VER, '<');

    }

    /**
     * Print option page javascript,css
     */
    public function add_admin_assets()
    {
        $is_min = (defined('WP_DEBUG') && WP_DEBUG == true) ? '' : '.min';
        wp_enqueue_style('asga-admin', plugins_url('/assets/option-page' . $is_min . '.css', ASGA_BASE_FILE), array(), ASGA_PLUGIN_VER);
        wp_enqueue_script('asga-admin', plugins_url("/assets/option-page" . $is_min . ".js", ASGA_BASE_FILE), array('jquery'), ASGA_PLUGIN_VER, false);
    }


    /**
     * Load view and show it to front-end
     * @param $file string File name
     * @param $options array Array to be passed to view, not an unused variable
     * @throws \Exception
     */
    private function load_view($file, $options = array())
    {
        $file_path = plugin_dir_path(ASGA_BASE_FILE) . 'views/' . $file;
        if (is_readable($file_path)) {
            require $file_path;
        } else {
            throw new \Exception('Unable to load template file - ' . esc_html($file_path));
        }
    }
}
