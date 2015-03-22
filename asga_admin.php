<?php
/*
 * Settings Page for "Ank Simplified GA" Plugin
 * Lets keep admin area code here
 * This class can run independently without the front-end class
 */

/* no direct access */
if (!defined('ABSPATH')) exit;
if(!defined('ASGA_BASE_FILE')) wp_die('What ?');

class ASGA_Admin_Class
{

    /*store plugin option page slug,so that we can change it with ease */
    private $plugin_slug = 'asga_options_page';
    /*store database option field name to avoid confusion */
    private $option_name = 'asga_options';

     function __construct()
    {
        /*to save default options upon activation*/
        register_activation_hook (plugin_basename(ASGA_BASE_FILE), array($this,'add_default_options'));

        /*for register setting*/
        add_action ('admin_init', array($this, 'ASGA_admin_init'));

        /*settings link on plugin listing page*/
        add_filter('plugin_action_links_'.plugin_basename(ASGA_BASE_FILE), array($this, 'add_plugin_actions_links'), 10, 2);

        /* Add settings link under admin->settings menu */
        add_action('admin_menu', array($this, 'add_to_settings_menu'));


    }

    /*
     * Save default settings upon plugin activation
     */
    function add_default_options()
    {
        //if options already exists then return early
        if (get_option('asga_options')) return;
        $defaults = array(
            'plugin_ver' => ASGA_PLUGIN_VER,
            'ga_id' => '',
            'js_location' => 1, //header
            'js_priority' => 20, //default is 20
            'log_404' => 0,
            'log_search' => 0,
            'ua_enabled' => 1, //UA is enabled by default
            'displayfeatures' => 0,
            'ga_ela' => 0,
            'anonymise_ip' => 0,
            'ga_domain' => ''
        );
        global $wp_roles;
        //store roles as well
        foreach( $wp_roles->roles as $role => $role_info ) {
            $defaults=array_merge($defaults,array('ignore_role_' . $role=>0));
        }

        update_option('asga_options',$defaults);
    }

    /*register our settings, using WP setting api*/
    function ASGA_admin_init() {
        register_setting('asga_plugin_options', $this->option_name, array($this,'ASGA_validate_options'));
    }

    /**
     * Adds a 'Settings' link for this plugin on plugin listing page
     * @return array Links array
     */
    function add_plugin_actions_links($links, $file)
    {
        if (current_user_can('manage_options')) {
            $build_url= add_query_arg('page', $this->plugin_slug, 'options-general.php');
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
        $page_hook_suffix =add_submenu_page('options-general.php', 'Ank Simplified GA', 'Ank Simplified GA', 'manage_options', $this->plugin_slug, array($this, 'ASGA_Options_Page'));
        /*add help stuff via tab*/
        add_action( "load-$page_hook_suffix", array( $this, 'add_help_menu_tab' ) );
        /*we can load additional css/js to our option page here */

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
        //start handle form data

        // Get the actual tracking ID
        if (preg_match('#UA-[\d-]+#', $in['ga_id'], $matches))
            $out['ga_id'] = $matches[0];
        else
            $out['ga_id'] = '';

        //warn user that the entered id is not valid
        if (empty($out['ga_id']) || $out['ga_id'] === '') {
            add_settings_error('asga_options', 'ga_id', 'Your GA tracking ID seems invalid.');
        }

        $out['js_location'] = absint($in['js_location']);
        $out['js_priority'] = absint($in['js_priority']);

        $out['ga_domain'] = esc_attr($in['ga_domain']);

        $checkbox_items = array('ua_enabled', 'anonymise_ip', 'displayfeatures', 'ga_ela', 'log_404', 'log_search');
        global $wp_roles;
        foreach ($wp_roles->roles as $role => $role_info) {
            $checkbox_items[] = 'ignore_role_' . $role;
        }
        foreach ($checkbox_items as $checkbox_item) {
            if (isset($in[$checkbox_item]) && '1' == $in[$checkbox_item])
                $out[$checkbox_item] = 1;
            else
                $out[$checkbox_item] = 0;
        }
        return $out;
    }

    /**
     * Function will print our option page form
     * @initiated by add_submenu_page
     */
    function ASGA_Options_Page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        ?>
    <div class="wrap">
        <h2><i class="dashicons dashicons-chart-bar" style="vertical-align: middle"></i> Ank Simplified GA <small>(v<?php echo ASGA_PLUGIN_VER; ?>)</small></h2>
        <form action="<?php echo admin_url('options.php') ?>" method="post" id="asga_form">
            <?php
            //always get fresh options from db
            $options = get_option($this->option_name);
            //wp inbuilt nonce field etc
            settings_fields('asga_plugin_options');
            ?>
            <table class="form-table">
             <tr>
                 <th scope="row">Google Analytics tracking ID:</th>
                 <td><input type="text" placeholder="UA-XXXXXX-X" name="asga_options[ga_id]" value="<?php echo esc_attr($options['ga_id']); ?>" required="" aria-required="true">
                   <br><p class="description">Paste your Google Analytics <a href="https://support.google.com/analytics/answer/1032385?hl=en">tracking ID</a> (e.g. "UA-XXXXXX-X")</p>
                 </td>
             </tr>
                <tr>
                    <th scope="row">Enable Universal GA:</th>
                    <td><label><input type="checkbox" name="asga_options[ua_enabled]" value="1" <?php checked( $options['ua_enabled'],1) ?>>Enable Universal Google Analytics</label>
                        <p class="description">Un-check this, If you are using Classic GA . OR <a href="https://support.google.com/analytics/answer/3450662?hl=en" target="_blank">upgrade</a>.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Set domain:</th>
                    <td><input type="text" placeholder="default = auto" name="asga_options[ga_domain]" value="<?php echo esc_attr($options['ga_domain']); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Enable Display Advertising :</th>
                    <td><label><input type="checkbox" name="asga_options[displayfeatures]" value="1" <?php checked( $options['displayfeatures'],1) ?>>Check to enable
                            <a href="https://support.google.com/analytics/answer/2444872?hl=en">Read More</a></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Enhanced Link Attribution :</th>
                    <td><label><input type="checkbox" name="asga_options[ga_ela]" value="1" <?php checked( $options['ga_ela'],1) ?>>Check to Enable
                            <a target="_blank" href="https://support.google.com/analytics/answer/2558867?hl=en">Read More</a> </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Anonymize IP's:</th>
                    <td><label><input type="checkbox" name="asga_options[anonymise_ip]" value="1" <?php checked( $options['anonymise_ip'],1) ?>>Anonymize IP
                            <a href="https://support.google.com/analytics/answer/2763052?hl=en" target="_blank">Read more</a></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Code Location:</th>
                    <td>
                        <label><input type="radio" name="asga_options[js_location]" value="1" <?php checked($options['js_location'],1) ?>>&ensp;Place in document head</label><br>
                        <label><input type="radio" name="asga_options[js_location]" value="2" <?php checked($options['js_location'],2) ?>>&ensp;Place in document footer</label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Action Priority:</th>
                    <td><input type="number" min="0" max="999" placeholder="10" name="asga_options[js_priority]" value="<?php echo esc_attr($options['js_priority']); ?>">
                     <p class="description">0 is highest, default is 20</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Disable Tracking when :</th>
                    <td>
                        <?php
                        global $wp_roles;
                        $role_list=array();
                        foreach( $wp_roles->roles as $role => $role_info ) {
                            $role_list['ignore_role_' . $role] = sprintf( '<i>%s</i> is logged in', rtrim( $role_info['name'], 's' ) );
                        }
                        //loop through each roles
                        foreach( $role_list as $id => $label ) {
                            echo '<label>';
                            echo '<input type="checkbox" name="asga_options[' . $id . ']" value="1" '.checked( $options[$id],1,0 ) .'/>';
                            echo '&ensp;' . $label;
                            echo '</label><br />';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Event Tracking:</th>
                    <td>
                    <?php
                    $events=array(
                        'log_404'=>'Log 404 errors as events',
                        'log_search'=>'Log searched items'
                    );
                    //loop through array
                    foreach($events as $event=>$label){
                        echo '<label>';
                        echo '<input type="checkbox" name="asga_options['.$event.']" value="1" '.checked($options[$event],1,0).'/>';
                        echo '&ensp;'.$label.'</label><br>';
                    }
                    ?>
                    </td>
                </tr>
            </table>
<?php submit_button('Save Options')?>
        </form>
        <p>Developed by- <a target="_blank" href="http://ank91.github.io/">Ankur Kumar</a> |
            Fork on <a href="https://github.com/ank91/ank-simplified-ga" target="_blank">GitHub</a> </p>
    </div> <!-- .wrap-->
    <?php
        //debugging information shows options saved in database
        if (isset($_GET['debug']) || WP_DEBUG == true) {
            echo '<hr><p><h5>Showing Debugging Info:</h5>';
            var_dump($options);
            echo '</p><hr>';
        }?>
    <?php
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
                'id'		=> 'asga-overview',
                'title'		=> 'Basic',
                'content'	=>'<p><strong>Do you have a Google Analytics Account ?</strong><br>'.
                    'In order to use this plugin you need to gave a Google Analytics Account. Create an account <a target="_blank" href="http://www.google.com/analytics">here</a>. It is FREE. <br>'.
                    '<strong>How do i find my Google Analytics ID ?</strong><br>'.
                    'Please check out this <a target="_blank" href="https://support.google.com/analytics/answer/1032385?hl=en">link</a><br>'.
                    '<strong>How do i view my stats ?</strong><br>'.
                    'Login to Google Analytics Account with your Gmail ID to view stats.'.
                    '</p>'

            )
        );

        $curr_screen->add_help_tab(
            array(
                'id'		=> 'asga-troubleshoot',
                'title'		=> 'Troubleshoot',
                'content'	=>'<p><strong>Things to remember</strong><br>'.
                '<ul>
                <li>If you are using a cache/performance plugin, you need to flush/delete your site cache after saving settings here.</li>
                <li>It can take up to 24-48 hours after adding the tracking code before any analytical data appears in your Google Analytics account. </li>
                </ul>
                </p>'

            )
        );
        $curr_screen->add_help_tab(
            array(
                'id'		=> 'asga-more-info',
                'title'		=> 'More',
                'content'	=>'<p><strong>Need more information ?</strong><br>'.
                    'A brief FAQ is available on plugin&apos;s official website. '.
                    'OR click <a href="#" target="_blank">here</a> for more.<br>'.
                    'Support is only available on WordPress Forums, click <a href="#" target="_blank">here</a> to ask anything about this plugin.<br>'.
                    'You can also browse the source code of this  plugin on <a href="https://github.com/ank91/ank-simplified-ga" target="_blank">GitHub</a>. '.
                    '</p>'

            )
        );

        /*add a help sidebar with links */
        $curr_screen->set_help_sidebar(
            '<p><strong>Quick Links</strong></p>' .
            '<p><a href="#" target="_blank">Plugin FAQ</a></p>' .
            '<p><a href="#" target="_blank">Plugin Home</a></p>'
        );
    }


} //end class

//init this class
global $ASGA_Admin_Class;
$ASGA_Admin_Class=  new ASGA_Admin_Class();
