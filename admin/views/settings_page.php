<style type="text/css">
    .tab-content {
        display: none
    }
    .tab-content.active {
        display: block
    }
    pre.xdebug-var-dump {
        max-height: 200px;
        overflow: auto;
        border: 1px solid #e2e2e2;
        padding: 5px;
    } </style>
<div class="wrap">
    <h2><?php _e('Ank Simplified Google Analytics',ASGA_TEXT_DOMAIN) ?> <small>: (v<?php echo ASGA_PLUGIN_VER; ?>)</small> </h2>

    <h2 class="nav-tab-wrapper" id="ga-tabs">
        <a class="nav-tab" id="ga-general-tab" href="#top#ga-general"><?php _e('General',ASGA_TEXT_DOMAIN) ?></a>
        <a class="nav-tab" id="ga-advanced-tab" href="#top#ga-advanced"><?php _e('Advanced',ASGA_TEXT_DOMAIN) ?></a>
        <a class="nav-tab" id="ga-events-tab" href="#top#ga-events"><?php _e('Monitor',ASGA_TEXT_DOMAIN) ?></a>
        <a class="nav-tab" id="ga-control-tab" href="#top#ga-control"><?php _e('Control',ASGA_TEXT_DOMAIN) ?></a>
        <a class="nav-tab" id="ga-troubleshoot-tab" href="#top#ga-troubleshoot"><?php _e('Troubleshoot',ASGA_TEXT_DOMAIN) ?></a>
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
                        <th scope="row"><?php _e('Google Analytics tracking ID',ASGA_TEXT_DOMAIN) ?> :</th>
                        <td><input type="text" placeholder="UA-012345678-9" name="asga_options[ga_id]" value="<?php echo esc_attr($options['ga_id']); ?>">
                            <a title="Help" target="_blank" href="https://support.google.com/analytics/answer/1032385"><i class="dashicons-before dashicons-editor-help"></i></a>
                            <br>
                            <p class="description"><?php _e('Paste your Google Analytics tracking ID e.g.',ASGA_TEXT_DOMAIN) ?> ("<code>UA-XXXXXXXX-X</code>")</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Analytics version',ASGA_TEXT_DOMAIN) ?>:</th>
                        <td>
                            <select name="asga_options[ua_enabled]">
                                <option value="1" <?php selected($options['ua_enabled'], 1) ?>><?php _e('Universal',ASGA_TEXT_DOMAIN) ?> (analytics.js)</option>
                                <option value="0" <?php selected($options['ua_enabled'], 0) ?>><?php _e('Classic',ASGA_TEXT_DOMAIN) ?> (ga.js)</option>
                            </select>
                            <a title="Help" href="https://support.google.com/analytics/answer/3450662" target="_blank"><i class="dashicons-before dashicons-editor-help"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Set domain',ASGA_TEXT_DOMAIN) ?> :</th>
                        <td><input type="text" placeholder="auto" name="asga_options[ga_domain]" value="<?php echo esc_attr($options['ga_domain']); ?>">
                            <?php
                            //print sub-domain url on screen , when multi-site is enabled
                            if(!is_main_site()){
                                printf('<br><p class="description"><code>%s</code></p>',get_blogaddress_by_id(get_current_blog_id())) ;
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Google webmaster code',ASGA_TEXT_DOMAIN) ?> :</th>
                        <td><input type="text" autocomplete="off" placeholder="<?php _e('Optional',ASGA_TEXT_DOMAIN) ?>" name="asga_options[webmaster][google_code]" value="<?php echo esc_attr($options['webmaster']['google_code']); ?>">
                            <a title="Help" href="https://www.google.com/webmasters/tools/home?hl=en" target="_blank"><i class="dashicons-before dashicons-editor-help"></i></a>
                            <p class="description"><?php _e('Paste your Google webmaster verification code here',ASGA_TEXT_DOMAIN) ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="ga-advanced" class="tab-content">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Demographics and Interest Reports',ASGA_TEXT_DOMAIN) ?> :</th>
                        <td><label><input type="checkbox" name="asga_options[displayfeatures]" value="1" <?php checked($options['displayfeatures'], 1) ?>><?php _e('Check to enable',ASGA_TEXT_DOMAIN) ?>
                                <a target="_blank" href="https://support.google.com/analytics/answer/3450482"><i class="dashicons-before dashicons-editor-help"></i></a></label>
                            <p class="description"><a target="_blank" href="https://support.google.com/analytics/answer/2611268"><?php _e('Remarketing',ASGA_TEXT_DOMAIN) ?></a> | <a target="_blank" href="https://support.google.com/analytics/answer/2799357"><?php _e('Demographics and Interest Reporting',ASGA_TEXT_DOMAIN) ?></a></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Enhanced Link Attribution',ASGA_TEXT_DOMAIN) ?> :</th>
                        <td><label><input type="checkbox" name="asga_options[ga_ela]" value="1" <?php checked($options['ga_ela'], 1) ?>><?php _e('Check to enable',ASGA_TEXT_DOMAIN) ?>
                                <a target="_blank" href="https://support.google.com/analytics/answer/2558867"><i class="dashicons-before dashicons-editor-help"></i></a> </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Cross-domain user tracking',ASGA_TEXT_DOMAIN) ?> :</th>
                        <td><label><input type="checkbox" name="asga_options[allow_linker]" value="1" <?php checked($options['allow_linker'], 1) ?>><?php _e('Check to enable',ASGA_TEXT_DOMAIN) ?>
                                <a target="_blank" href="https://developers.google.com/analytics/devguides/collection/gajs/methods/gaJSApiDomainDirectory#_gat.GA_Tracker_._setAllowLinker"><i class="dashicons-before dashicons-editor-help"></i> </a> </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Campaign tracking',ASGA_TEXT_DOMAIN) ?> :</th>
                        <td><label><input type="checkbox" name="asga_options[allow_anchor]" value="1" <?php checked($options['allow_anchor'], 1) ?>><?php _e('Check to enable',ASGA_TEXT_DOMAIN) ?>
                                <a target="_blank" href="https://developers.google.com/analytics/devguides/collection/gajs/methods/gaJSApiCampaignTracking#_gat.GA_Tracker_._setAllowAnchor"><i class="dashicons-before dashicons-editor-help"></i></a> </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Anonymize IP',ASGA_TEXT_DOMAIN) ?> :</th>
                        <td><label><input type="checkbox" name="asga_options[anonymise_ip]" value="1" <?php checked($options['anonymise_ip'], 1) ?>><?php _e('Anonymize IP',ASGA_TEXT_DOMAIN) ?>
                                <a href="https://support.google.com/analytics/answer/2763052" target="_blank"><i class="dashicons-before dashicons-editor-help"></i></a></label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Force SSL',ASGA_TEXT_DOMAIN) ?> :</th>
                        <td><label><input type="checkbox" name="asga_options[force_ssl]" value="1" <?php checked($options['force_ssl'], 1) ?>><?php _e('Force SSL',ASGA_TEXT_DOMAIN) ?> </label>
                            <p class="description"><?php _e('Transmit data over https (secure) connection',ASGA_TEXT_DOMAIN) ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Custom trackers',ASGA_TEXT_DOMAIN) ?> :</th>
                        <td><textarea placeholder="Please don't not include &lt;script&gt tags" rows="5" cols="35" name="asga_options[custom_trackers]" style="resize: vertical;max-height: 300px;"><?php echo stripslashes($options['custom_trackers']) ?></textarea>
                            <p class="description"><?php _e('To be added before the',ASGA_TEXT_DOMAIN) ?> <code><?php _e('pageview',ASGA_TEXT_DOMAIN) ?></code> <?php _e('call',ASGA_TEXT_DOMAIN) ?>.</p>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="ga-events" class="tab-content">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Event tracking',ASGA_TEXT_DOMAIN) ?>  :</th>
                        <td><fieldset>
                                <?php
                                $events = array(
                                    'log_404' => __('Log 404 errors as events', ASGA_TEXT_DOMAIN),
                                    'log_search' => __('Log searched items as page views', ASGA_TEXT_DOMAIN),
                                    'track_mail_links' => __('Track mailto links as event', ASGA_TEXT_DOMAIN),
                                    'track_outgoing_links' => __('Track outbound links as event', ASGA_TEXT_DOMAIN),
                                    'track_download_links' => __('Track downloads as event', ASGA_TEXT_DOMAIN),
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
                        <th><?php _e('Extensions for downloads', ASGA_TEXT_DOMAIN) ?> :</th>
                        <td>
                            <input size="30" type="text" placeholder="doc,docx,xls,xlsx,pdf,zip,rar,exe" name="asga_options[track_download_ext]" value="<?php echo esc_attr($options['track_download_ext']); ?>">
                            <p class="description"><?php _e('Please use comma (,) separated values', ASGA_TEXT_DOMAIN) ?> .</p>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Stop analytics when',ASGA_TEXT_DOMAIN) ?> :</th>
                        <td>
                            <?php
                            foreach ($this->get_all_roles() as $role) {
                                echo '<label>';
                                echo '<input type="checkbox" name="asga_options[ignore_role_' . $role['id'] . ']" value="1" ' . checked($options['ignore_role_' . $role['id']], 1, 0) . '/>';
                                echo '&ensp;' . esc_attr($role['name']) . ' '.__('is logged in',ASGA_TEXT_DOMAIN);
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
                        <th scope="row"><?php _e('Code location',ASGA_TEXT_DOMAIN) ?> :</th>
                        <td>
                            <fieldset>
                                <label><input type="radio" name="asga_options[js_location]" value="1" <?php checked($options['js_location'], 1) ?>>&ensp;<?php _e('Place in document header',ASGA_TEXT_DOMAIN) ?></label><br>
                                <label><input type="radio" name="asga_options[js_location]" value="2" <?php checked($options['js_location'], 2) ?>>&ensp;<?php _e('Place in document footer',ASGA_TEXT_DOMAIN) ?></label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Code execution',ASGA_TEXT_DOMAIN) ?> :</th>
                        <td>
                            <fieldset>
                                <label><input type="radio" name="asga_options[js_load_later]" value="0" <?php checked($options['js_load_later'], 0) ?>>&ensp;<?php _e('Immediately',ASGA_TEXT_DOMAIN) ?></label><br>
                                <label><input type="radio" name="asga_options[js_load_later]" value="1" <?php checked($options['js_load_later'], 1) ?>>&ensp;<?php _e('On page load',ASGA_TEXT_DOMAIN) ?></label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Action priority',ASGA_TEXT_DOMAIN) ?> :</th>
                        <td><input type="number" placeholder="20" name="asga_options[js_priority]" value="<?php echo esc_attr($options['js_priority']); ?>">
                            <p class="description"><?php _e('0 means highest priority',ASGA_TEXT_DOMAIN) ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="ga-troubleshoot" class="tab-content">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Debug mode',ASGA_TEXT_DOMAIN) ?> :</th>
                        <td><label><input type="checkbox" name="asga_options[debug_mode]" value="1" <?php checked($options['debug_mode'], 1) ?>><?php _e('Enable debugging mode for administrators',ASGA_TEXT_DOMAIN) ?>
                                <a target="_blank" href="https://developers.google.com/analytics/resources/articles/gaTrackingTroubleshooting#gaDebug"><i class="dashicons-before dashicons-editor-help"></i></a> </label>

                            <p class="description"><?php _e("This should only be used temporarily or during development, don't forget to disable it in production",'asga') ?>.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Debug database options',ASGA_TEXT_DOMAIN) ?> :</th>
                        <td><?php var_dump($options); ?></td>
                    </tr>
                </table>
            </div>
        </div> <!--.tab-wrapper -->
        <?php submit_button() ?>
    </form>
    <hr>
    <p>
        <?php _e('Developed by',ASGA_TEXT_DOMAIN) ?>- <a target="_blank" href="https://ank91.github.io/">Ankur Kumar</a> |
        <?php _e('Fork on',ASGA_TEXT_DOMAIN) ?> <a href="https://github.com/ank91/ank-simplified-ga" target="_blank">GitHub</a> |
        ★ <?php _e('Rate this on',ASGA_TEXT_DOMAIN) ?> <a href="https://wordpress.org/support/view/plugin-reviews/ank-simplified-ga?filter=5" target="_blank"><?php _e('WordPress') ?></a>
    </p>
</div> <!-- .wrap-->
