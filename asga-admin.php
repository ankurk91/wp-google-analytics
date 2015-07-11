<?php
/*
 * Settings Page for "Ank Simplified GA" Plugin
 * Lets keep admin area code here
 */

/* No direct access */
if (!defined('ABSPATH')) exit;
if (!class_exists('Ank_Simplified_GA')) die('What ?');

class Ank_Simplified_GA_Admin
{

    private static $instances = array();
    /*store plugin option page slug, so that we can change it with ease */
    const PLUGIN_SLUG = 'asga_options_page';
    const PLUGIN_OPTION_GROUP = 'asga_plugin_options';

    private function __construct()
    {

        /*to save default options upon activation*/
        register_activation_hook(ASGA_BASE_FILE, array($this, 'do_upon_plugin_activation'));
        /*delete transients when deactivated*/
        register_deactivation_hook(ASGA_BASE_FILE, array($this, 'do_upon_plugin_deactivation'));

        /*for register setting*/
        add_action('admin_init', array($this, 'register_plugin_settings'));

        /*settings link on plugin listing page*/
        add_filter('plugin_action_links_' . ASGA_BASE_FILE, array($this, 'add_plugin_actions_links'), 10, 2);

        /* Add settings link under admin->settings menu */
        add_action('admin_menu', array($this, 'add_to_settings_menu'));

        /* Show warning if debug mode is on  */
        add_action('admin_notices', array($this, 'show_admin_notice'));

        /*check for database upgrades*/
        add_action( 'plugins_loaded', array( $this, 'may_be_upgrade' ) );

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

    /*
     * Save default settings upon plugin activation
     */
    function do_upon_plugin_activation()
    {
        //delete transient upon activation
        $this->delete_transient_js();

        //if options not exists then update with defaults
        if (get_option(ASGA_OPTION_NAME)==false){
            update_option(ASGA_OPTION_NAME, $this->get_default_options());
        }

    }

    function do_upon_plugin_deactivation()
    {
        //delete transient upon activation
        $this->delete_transient_js();

    }

    /*Register our settings, using WP settings API*/
    function register_plugin_settings()
    {
        register_setting(self::PLUGIN_OPTION_GROUP, ASGA_OPTION_NAME, array($this, 'ASGA_validate_options'));
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
                sprintf('<a href="%s">%s</a>', $build_url, __('Settings'))
            );
        }

        return $links;
    }

    /*
     * Adds link to Plugin Option page + do related stuff
     */
    function add_to_settings_menu()
    {
        $page_hook_suffix = add_submenu_page('options-general.php', 'Ank Simplified Google Analytics', 'Ank Simplified GA', 'manage_options', self::PLUGIN_SLUG, array($this, 'ASGA_options_page'));
        /*add help stuff via tab*/
        add_action("load-$page_hook_suffix", array($this, 'add_help_menu_tab'));
        /*we can load additional css/js to our option page here */

    }

    /**
     * Return all roles plus superAdmin if multi-site is enabled
     * @return array
     */
    private function get_all_roles()
    {
        global $wp_roles;
        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }
        $role_list = $wp_roles->get_names();
        //append a custom role if multi-site is enabled
        if (is_multisite()) {
            $role_list['networkAdmin'] = 'Network Admin';
        }

        return $role_list;
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
            'log_search' => 0,
            'ua_enabled' => 1,
            'displayfeatures' => 0,
            'ga_ela' => 0,
            'anonymise_ip' => 0,
            'ga_domain' => '',
            'debug_mode' => 0,
            'force_ssl' => 1,
            'custom_tracker' => '',
            'allow_linker' => 0 ,
            'allow_anchor' => 0

        );
        //store roles as well
        foreach ($this->get_all_roles() as $role => $role_info) {
            $defaults = array_merge($defaults, array('ignore_role_' . $role => 0));
        }
        return $defaults;
    }


    /**
     * Callback Function to handle and validate the form data
     *
     * @param array $in - POST array
     * @returns array - Validated array
     */
    function ASGA_validate_options($in)
    {

        $out = array();
        //always store plugin version to db
        $out['plugin_ver'] = ASGA_PLUGIN_VER;

        // Get the actual tracking ID
        if (!preg_match('|^UA-\d{4,}-\d+$|', (string)$in['ga_id'])) {
            $out['ga_id'] = '';
            //warn user that the entered id is not valid
            add_settings_error(ASGA_OPTION_NAME, 'ga_id', 'Your GA tracking ID seems invalid. Please validate.');
        } else {
            $out['ga_id'] = sanitize_text_field($in['ga_id']);
        }

        $radio_items = array('js_location','js_load_later');

        foreach($radio_items as $item){
            $out[$item] = absint($in[$item]);
        }

        $out['js_priority'] = (empty($in['js_priority'])) ? 20 : absint($in['js_priority']);

        $out['ga_domain'] = sanitize_text_field($in['ga_domain']);

        $out['custom_trackers'] = trim($in['custom_trackers']);

        $checkbox_items = array('ua_enabled', 'anonymise_ip', 'displayfeatures', 'ga_ela', 'log_404', 'log_search','debug_mode','force_ssl','allow_linker','allow_anchor');
         //add rolls to checkbox_items array
        foreach ($this->get_all_roles() as $role => $role_info) {
            $checkbox_items[] = 'ignore_role_' . $role;
        }

        foreach ($checkbox_items as $item) {
            if (isset($in[$item]) && '1' == $in[$item])
                $out[$item] = 1;
            else
                $out[$item] = 0;
        }
        //delete transient upon change in settings
        $this->delete_transient_js();
        return $out;
    }

    /**
     * Function will print our option page form
     */
     function ASGA_options_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        ?>
        <style type="text/css"> .tab-content{display: none} .tab-content.active{display: block} </style>
        <div class="wrap">
            <h2>Ank Simplified Google Analytics <small>(v<?php echo ASGA_PLUGIN_VER; ?>)</small> </h2>

            <h2 class="nav-tab-wrapper" id="ga-tabs">
                <a class="nav-tab" id="ga-general-tab" href="#top#ga-general">General</a>
                <a class="nav-tab" id="ga-advanced-tab" href="#top#ga-advanced">Advanced</a>
                <a class="nav-tab" id="ga-events-tab" href="#top#ga-events">Monitor</a>
                <a class="nav-tab" id="ga-control-tab" href="#top#ga-control">Control</a>
                <a class="nav-tab" id="ga-troubleshoot-tab" href="#top#ga-troubleshoot">Troubleshoot</a>
            </h2><!--.nav-tab-wrapper-->

            <form action="<?php echo admin_url('options.php') ?>" method="post" id="asga_form">
            <?php
                $options = $this->get_safe_options();
                //wp inbuilt nonce field , etc
                settings_fields(self::PLUGIN_OPTION_GROUP);
            ?>
            <div class="tab-wrapper">
               <div id="ga-general" class="tab-content">
                   <table class="form-table">
                   <tr>
                       <th scope="row">Google Analytics tracking ID :</th>
                       <td><input type="text" placeholder="UA-XXXXXXXX-X" name="asga_options[ga_id]" value="<?php echo esc_attr($options['ga_id']); ?>"> <br>
                           <p class="description">Paste your Google Analytics <a target="_blank" href="https://support.google.com/analytics/answer/1032385">tracking ID</a> (e.g. "<code>UA-XXXXXXXX-X</code>")</p>
                       </td>
                   </tr>
                   <tr>
                       <th scope="row">Analytics Version:</th>
                       <td>
                           <select name="asga_options[ua_enabled]">
                               <option value="1" <?php selected($options['ua_enabled'], 1) ?>>Universal (analytics.js)</option>
                               <option value="0" <?php selected($options['ua_enabled'], 0) ?>>Classic (ga.js)</option>
                           </select>
                           <p class="description">Classic vs Universal, <a href="https://support.google.com/analytics/answer/3450662" target="_blank">read more</a>. </p>
                       </td>
                   </tr>
                   <tr>
                       <th scope="row">Set domain :</th>
                       <td><input type="text" placeholder="auto" name="asga_options[ga_domain]" value="<?php echo esc_attr($options['ga_domain']); ?>">
                           <?php
                           //print sub-domain url on screen , when multi-site is enabled
                           if(!is_main_site()){
                               printf('<p class="description">%s</p>',get_blogaddress_by_id(get_current_blog_id())) ;
                           }
                           ?>
                       </td>
                   </tr>
                  </table>
               </div>
               <div id="ga-advanced" class="tab-content">
                   <table class="form-table">
                       <tr>
                           <th scope="row">Enable Display Features :</th>
                           <td><label><input type="checkbox" name="asga_options[displayfeatures]" value="1" <?php checked($options['displayfeatures'], 1) ?>>Check to enable <a target="_blank" href="https://support.google.com/analytics/answer/3450482">Read More</a></label>
                               <p class="description"><a target="_blank" href="https://support.google.com/analytics/answer/2611268">Remarketing</a> | <a target="_blank" href="https://support.google.com/analytics/answer/2799357">Demographics and Interest Reporting</a></p>
                           </td>
                       </tr>
                       <tr>
                           <th scope="row">Enhanced Link Attribution :</th>
                           <td><label><input type="checkbox" name="asga_options[ga_ela]" value="1" <?php checked($options['ga_ela'], 1) ?>>Check to Enable <a target="_blank" href="https://support.google.com/analytics/answer/2558867">Read more</a> </label>
                           </td>
                       </tr>
                       <tr>
                           <th scope="row">Allow Linker :</th>
                           <td><label><input type="checkbox" name="asga_options[allow_linker]" value="1" <?php checked($options['allow_linker'], 1) ?>>Check to Enable  </label>
                           </td>
                       </tr>
                       <tr>
                           <th scope="row">Allow Anchor :</th>
                           <td><label><input type="checkbox" name="asga_options[allow_anchor]" value="1" <?php checked($options['allow_anchor'], 1) ?>>Check to Enable  </label>
                           </td>
                       </tr>
                       <tr>
                           <th scope="row">Anonymize IP's :</th>
                           <td><label><input type="checkbox" name="asga_options[anonymise_ip]" value="1" <?php checked($options['anonymise_ip'], 1) ?>>Anonymize IP <a href="https://support.google.com/analytics/answer/2763052" target="_blank">Read more</a></label>
                           </td>
                       </tr>
                       <tr>
                           <th scope="row">Force SSL :</th>
                           <td><label><input type="checkbox" name="asga_options[force_ssl]" value="1" <?php checked($options['force_ssl'], 1) ?>>Force SSL </label>
                           <p class="description">Transmit data over https (secure) connection</p>
                           </td>
                       </tr>
                       <tr>
                           <th scope="row">Custom Trackers :</th>
                           <td><textarea placeholder="Please don't not include &lt;script&gt tags" rows="5" cols="35" name="asga_options[custom_trackers]" style="resize: vertical;max-height: 300px;"><?php echo stripslashes($options['custom_trackers']) ?></textarea>
                           <p class="description">To be added before the <code>pageview</code> call.</p>
                           </td>
                       </tr>
                   </table>
               </div>
               <div id="ga-events" class="tab-content">
                   <table class="form-table">
                       <tr>
                           <th scope="row">Event Tracking :</th>
                           <td><fieldset>
                               <?php
                               $events = array(
                                   'log_404' => 'Log 404 errors as events',
                                   'log_search' => 'Log searched items as page views',
                               );
                               //loop through each event item
                               foreach ($events as $event => $label) {
                                   echo '<label>';
                                   echo '<input type="checkbox" name="asga_options[' . $event . ']" value="1" ' . checked($options[$event], 1, 0) . '/>';
                                   echo '&ensp;' . $label . '</label><br>';
                               }
                               ?></fieldset>
                           </td>
                       </tr>
                       <tr>
                           <th scope="row">Stop Analytics when :</th>
                           <td>
                               <?php
                               foreach ($this->get_all_roles() as $id => $label) {
                                   echo '<label>';
                                   echo '<input type="checkbox" name="asga_options[ignore_role_' . $id . ']" value="1" ' . checked($options['ignore_role_' . $id], 1, 0) . '/>';
                                   echo '&ensp;' . esc_attr($label) . ' is logged in';
                                   echo '</label><br />';
                               }
                               ?>
                           </td>
                       </tr>
                   </table>
               </div>
               <div id="ga-control" class="tab-content">
                    <table class="form-table">
                        <tr>
                            <th scope="row">Code Location :</th>
                            <td>
                                <fieldset>
                                    <label><input type="radio" name="asga_options[js_location]" value="1" <?php checked($options['js_location'], 1) ?>>&ensp;Place in document header</label><br>
                                    <label><input type="radio" name="asga_options[js_location]" value="2" <?php checked($options['js_location'], 2) ?>>&ensp;Place in document footer</label>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Code Execution :</th>
                            <td>
                                <fieldset>
                                    <label><input type="radio" name="asga_options[js_load_later]" value="0" <?php checked($options['js_load_later'], 0) ?>>&ensp;Immediately</label><br>
                                    <label><input type="radio" name="asga_options[js_load_later]" value="1" <?php checked($options['js_load_later'], 1) ?>>&ensp;On page load</label>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Action Priority :</th>
                            <td><input type="number" min="0" max="999" placeholder="20" name="asga_options[js_priority]" value="<?php echo esc_attr($options['js_priority']); ?>">
                                <p class="description">0 means highest priority</p>
                            </td>
                        </tr>
                    </table>
                </div>
               <div id="ga-troubleshoot" class="tab-content">
                   <table class="form-table">
                       <tr>
                           <th scope="row">Troubleshoot :</th>
                           <td><label><input type="checkbox" name="asga_options[debug_mode]" value="1" <?php checked($options['debug_mode'], 1) ?>>Enable Debugging mode for admins <a target="_blank" href="https://developers.google.com/analytics/resources/articles/gaTrackingTroubleshooting#gaDebug">Read more</a> </label>

                               <p class="description">This should only be used temporarily or during development, don't forget to disable it in production.</p>
                           </td>
                       </tr>
                       <tr>
                           <th scope="row">Debug database options :</th>
                           <td><?php var_dump($options); ?></td>
                       </tr>
                   </table>
               </div>
            </div> <!--.tab-wrapper -->
            <?php submit_button() ?>
            </form>
            <hr>
            <p>
                Developed by- <a target="_blank" href="http://ank91.github.io/">Ankur Kumar</a> |
                Fork on <a href="https://github.com/ank91/ank-simplified-ga" target="_blank">GitHub</a> |
                ★ Rate this on <a href="https://wordpress.org/support/view/plugin-reviews/ank-simplified-ga?filter=5" target="_blank">WordPress</a>
            </p>
        </div> <!-- .wrap-->
        <script type="text/javascript">
            <?php
            $is_min = ( WP_DEBUG == 1) ? '' : '.min';
            echo file_get_contents(__DIR__."/js/asga-admin".$is_min.".js") ;
             ?>
        </script>
    <?php
    } //end function

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
                '<ul>'.
                '<li>If you are using a cache/performance plugin, you need to flush/delete your site cache after saving settings here.</li>'.
                '<li>It can take up to 24-48 hours after adding the tracking code before any analytical data appears in your Google Analytics account. </li>'.
                '</ul>'.
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
     * Show a warning notice if debug mode is on
     */
    function show_admin_notice()
    {
        if ($this->check_admin_notice()) {
             ?>
            <div id="asga_message" class="notice notice-warning is-dismissible">
                <p><b>Google Analytics debug mode is enabled for this site.Don't forget to disable this option in production. </b></p>
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
        if(strpos(get_current_screen()->id, self::PLUGIN_SLUG) === false) return false;

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
        if(!is_array($db_options)) {$db_options=array();}
        //if options not exists in db then init with defaults , also always append default options to existing options
        $db_options = empty($db_options) ? $this->get_default_options() : array_merge($this->get_default_options(),$db_options);
        return $db_options;

    }

    /**
     * Delete cache version of tracking code
     */
    private function delete_transient_js()
    {
        delete_transient(ASGA_TRANSIENT_JS_NAME);
    }

    /**
     * Upgrade plugin database options
     */
    function may_be_upgrade()
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
        //delete transient as well
        $this->delete_transient_js();
    }

    /**
     * Check if we need to upgrade database options or not
     * @param $db_options
     * @return bool|mixed
     */
    private function can_proceed_to_upgrade($db_options)
    {

        if (empty($db_options) || !is_array($db_options)) return true;

        if (!isset($db_options['plugin_ver'])) return true;

        return version_compare($db_options['plugin_ver'], ASGA_PLUGIN_VER, '<');

    }

} //end class
