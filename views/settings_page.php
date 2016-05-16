<div class="wrap">
    <h2><?php _e('Ank Simplified Google Analytics', ASGA_TEXT_DOMAIN) ?>
        <small>(v<?php echo ASGA_PLUGIN_VER; ?>)</small>
    </h2>

    <h2 class="nav-tab-wrapper" id="ga-tabs">
        <a class="nav-tab" id="ga-general-tab" href="#top#ga-general"><?php _e('General', ASGA_TEXT_DOMAIN) ?></a>
        <a class="nav-tab" id="ga-events-tab" href="#top#ga-events"><?php _e('Event Tracking', ASGA_TEXT_DOMAIN) ?></a>
        <a class="nav-tab" id="ga-advanced-tab" href="#top#ga-advanced"><?php _e('Advanced', ASGA_TEXT_DOMAIN) ?></a>
        <a class="nav-tab" id="ga-control-tab" href="#top#ga-control"><?php _e('Control', ASGA_TEXT_DOMAIN) ?></a>
        <a class="nav-tab" id="ga-troubleshoot-tab" href="#top#ga-troubleshoot"><?php _e('Troubleshoot', ASGA_TEXT_DOMAIN) ?></a>
    </h2><!--.nav-tab-wrapper-->

    <form action="<?php echo admin_url('options.php') ?>" method="post" id="asga_form">
        <?php
        $options = $this->get_safe_options();
        //wp inbuilt nonce field , etc
        settings_fields(self::PLUGIN_OPTION_GROUP);
        ?>
        <div class="tab-wrapper">
            <section id="ga-general" class="tab-content">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Google Analytics tracking ID', ASGA_TEXT_DOMAIN) ?> :</th>
                        <td><input type="text" size="25" placeholder="UA-012345678-9" name="asga_options[ga_id]"
                                   value="<?php echo esc_attr($options['ga_id']); ?>">
                            <a target="_blank" href="https://support.google.com/analytics/answer/1032385"><i
                                    class="dashicons-before dashicons-external"></i></a>
                            <br>
                            <p class="description"><?php _e('Paste your Google Analytics tracking ID e.g.', ASGA_TEXT_DOMAIN) ?>
                                ("<code>UA-XXXXXXXX-Y</code>")</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Analytics version', ASGA_TEXT_DOMAIN) ?>:</th>
                        <td>
                            <select name="asga_options[ua_enabled]">
                                <option
                                    value="1" <?php selected($options['ua_enabled'], 1) ?>><?php _e('Universal', ASGA_TEXT_DOMAIN) ?>
                                    (analytics.js)
                                </option>
                                <option
                                    value="0" <?php selected($options['ua_enabled'], 0) ?>><?php _e('Classic', ASGA_TEXT_DOMAIN) ?>
                                    (ga.js)
                                </option>
                            </select>
                            <a href="https://support.google.com/analytics/answer/3450662" target="_blank"><i
                                    class="dashicons-before dashicons-external"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Google webmaster code', ASGA_TEXT_DOMAIN) ?> :</th>
                        <td><input type="text" size="25" autocomplete="off"
                                   placeholder="<?php _e('Optional', ASGA_TEXT_DOMAIN) ?>"
                                   name="asga_options[webmaster][google_code]"
                                   value="<?php echo esc_attr($options['webmaster']['google_code']); ?>">
                            <a href="https://www.google.com/webmasters/tools/home?hl=en" target="_blank"><i
                                    class="dashicons-before dashicons-external"></i></a>
                            <p class="description"
                               style="color:#ba281e"><?php _e('This options has been deprecated and will be removed in future', ASGA_TEXT_DOMAIN) ?></p>
                        </td>
                    </tr>
                </table>
            </section>
            <section id="ga-events" class="tab-content">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Events to track', ASGA_TEXT_DOMAIN) ?> :</th>
                        <td>
                            <fieldset>
                                <?php
                                $events = array(
                                    'log_404' => __('Log 404 pages as events', ASGA_TEXT_DOMAIN),
                                    'track_mail_links' => __('Track email links as events', ASGA_TEXT_DOMAIN),
                                    'track_outbound_links' => __('Track outbound links as events', ASGA_TEXT_DOMAIN),
                                    'track_download_links' => __('Track downloads as events', ASGA_TEXT_DOMAIN),
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
                        <th scope="row"><?php _e('Non interactive events', ASGA_TEXT_DOMAIN) ?> :</th>
                        <td><label><input type="checkbox" name="asga_options[track_non_interactive]"
                                          value="1" <?php checked($options['track_non_interactive'], 1) ?>><?php _e('Events should not affect bounce rate', ASGA_TEXT_DOMAIN) ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Extensions for downloads', ASGA_TEXT_DOMAIN) ?> :</th>
                        <td>
                            <input size="25" type="text" placeholder="doc,docx,xls,xlsx,pdf,zip,rar,exe"
                                   name="asga_options[track_download_ext]"
                                   value="<?php echo esc_attr($options['track_download_ext']); ?>">
                            <p class="description"><?php _e('Please use comma (,) separated values', ASGA_TEXT_DOMAIN) ?> </p>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Track outbound link type', ASGA_TEXT_DOMAIN) ?> :</th>
                        <td>
                            <select name="asga_options[track_outbound_link_type]">
                                <option
                                    value="1" <?php selected($options['track_outbound_link_type'], 1) ?>><?php _e('Just the domain', ASGA_TEXT_DOMAIN) ?></option>
                                <option
                                    value="0" <?php selected($options['track_outbound_link_type'], 0) ?>><?php _e('Full URL', ASGA_TEXT_DOMAIN) ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
            </section>
            <section id="ga-advanced" class="tab-content">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Set domain', ASGA_TEXT_DOMAIN) ?> :</th>
                        <td><input type="text" size="25" placeholder="auto" name="asga_options[ga_domain]"
                                   value="<?php echo esc_attr($options['ga_domain']); ?>">
                            <?php
                            if (is_multisite()) {
                                $url = get_blogaddress_by_id(get_current_blog_id());
                            } else {
                                $url = home_url();
                            }
                            //print current domain
                            printf('<br><p class="description">Use <code>%s</code> or leave empty</p>', preg_replace('#^https?://#', '', $url));
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Sample rate', ASGA_TEXT_DOMAIN) ?> :</th>
                        <td><input type="number" placeholder="100" name="asga_options[sample_rate]"
                                   value="<?php echo esc_attr($options['sample_rate']); ?>">%
                            <a target="_blank" href="https://developers.google.com/analytics/devguides/collection/analyticsjs/field-reference#sampleRate"><i
                                    class="dashicons-before dashicons-external"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Demographics and interest reports', ASGA_TEXT_DOMAIN) ?> :</th>
                        <td><label><input type="checkbox" name="asga_options[displayfeatures]"
                                          value="1" <?php checked($options['displayfeatures'], 1) ?>><?php _e('Enable advertising features', ASGA_TEXT_DOMAIN) ?>
                                <a target="_blank" href="https://support.google.com/analytics/answer/3450482"><i
                                        class="dashicons-before dashicons-external"></i></a></label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Enhanced link attribution', ASGA_TEXT_DOMAIN) ?> :</th>
                        <td><label><input type="checkbox" name="asga_options[ga_ela]"
                                          value="1" <?php checked($options['ga_ela'], 1) ?>><?php _e('Check to enable', ASGA_TEXT_DOMAIN) ?>
                                <a target="_blank" href="https://support.google.com/analytics/answer/2558867"><i
                                        class="dashicons-before dashicons-external"></i></a> </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Cross-domain user tracking', ASGA_TEXT_DOMAIN) ?> :</th>
                        <td><label><input type="checkbox" name="asga_options[allow_linker]"
                                          value="1" <?php checked($options['allow_linker'], 1) ?>><?php _e('_setAllowLinker', ASGA_TEXT_DOMAIN) ?>
                                <a target="_blank"
                                   href="https://developers.google.com/analytics/devguides/collection/gajs/methods/gaJSApiDomainDirectory#_gat.GA_Tracker_._setAllowLinker"><i
                                        class="dashicons-before dashicons-external"></i> </a> </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Campaign tracking', ASGA_TEXT_DOMAIN) ?> :</th>
                        <td><label><input type="checkbox" name="asga_options[allow_anchor]"
                                          value="1" <?php checked($options['allow_anchor'], 1) ?>><?php _e('_setAllowAnchor', ASGA_TEXT_DOMAIN) ?>
                                <a target="_blank"
                                   href="https://developers.google.com/analytics/devguides/collection/gajs/methods/gaJSApiCampaignTracking#_gat.GA_Tracker_._setAllowAnchor"><i
                                        class="dashicons-before dashicons-external"></i></a> </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Anonymize IP', ASGA_TEXT_DOMAIN) ?> :</th>
                        <td><label><input type="checkbox" name="asga_options[anonymise_ip]"
                                          value="1" <?php checked($options['anonymise_ip'], 1) ?>><?php _e('Anonymizes IP addresses', ASGA_TEXT_DOMAIN) ?>
                                <a href="https://support.google.com/analytics/answer/2763052" target="_blank"><i
                                        class="dashicons-before dashicons-external"></i></a></label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Force SSL', ASGA_TEXT_DOMAIN) ?> :</th>
                        <td><label><input type="checkbox" name="asga_options[force_ssl]"
                                          value="1" <?php checked($options['force_ssl'], 1) ?>><?php _e('Transmit data over secure (https) connection', ASGA_TEXT_DOMAIN) ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Custom trackers', ASGA_TEXT_DOMAIN) ?> :</th>
                        <td><textarea placeholder="Please don't not include &lt;script&gt tags" rows="5" cols="35"
                                      name="asga_options[custom_trackers]"
                                      style="resize: vertical;max-height: 300px;"><?php echo stripslashes($options['custom_trackers']) ?></textarea>
                            <p class="description"><?php _e('To be added before the', ASGA_TEXT_DOMAIN) ?>
                                <code><?php _e('pageview', ASGA_TEXT_DOMAIN) ?></code> <?php _e('call', ASGA_TEXT_DOMAIN) ?>
                                </p>
                        </td>
                    </tr>
                </table>
            </section>
            <section id="ga-control" class="tab-content">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Place tracking code in', ASGA_TEXT_DOMAIN) ?> :</th>
                        <td>
                            <fieldset>
                                <label><input type="radio" name="asga_options[js_location]"
                                              value="1" <?php checked($options['js_location'], 1) ?>>&ensp;<?php _e('Document header', ASGA_TEXT_DOMAIN) ?>
                                </label><br>
                                <label><input type="radio" name="asga_options[js_location]"
                                              value="2" <?php checked($options['js_location'], 2) ?>>&ensp;<?php _e('Document footer', ASGA_TEXT_DOMAIN) ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Code execution', ASGA_TEXT_DOMAIN) ?> :</th>
                        <td>
                            <fieldset>
                                <label><input type="radio" name="asga_options[js_load_later]"
                                              value="0" <?php checked($options['js_load_later'], 0) ?>>&ensp;<?php _e('Immediately', ASGA_TEXT_DOMAIN) ?>
                                </label><br>
                                <label><input type="radio" name="asga_options[js_load_later]"
                                              value="1" <?php checked($options['js_load_later'], 1) ?>>&ensp;<?php _e('On page load', ASGA_TEXT_DOMAIN) ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Action priority', ASGA_TEXT_DOMAIN) ?> :</th>
                        <td><input type="number" size="25" placeholder="20" name="asga_options[js_priority]"
                                   value="<?php echo esc_attr($options['js_priority']); ?>">
                            <p class="description"><?php _e('0 means highest priority', ASGA_TEXT_DOMAIN) ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Stop analytics when', ASGA_TEXT_DOMAIN) ?> :</th>
                        <td>
                            <fieldset>
                                <?php
                                foreach ($this->get_all_roles() as $role) {
                                    echo '<label>';
                                    echo '<input type="checkbox" name="asga_options[ignore_role_' . $role['id'] . ']" value="1" ' . checked($options['ignore_role_' . $role['id']], 1, 0) . '/>';
                                    echo '&ensp;' . esc_attr($role['name']) . ' ' . __('is logged in', ASGA_TEXT_DOMAIN);
                                    echo '</label><br />';
                                }
                                ?></fieldset>
                        </td>
                    </tr>
                </table>
            </section>
            <section id="ga-troubleshoot" class="tab-content">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Debug mode', ASGA_TEXT_DOMAIN) ?> :</th>
                        <td><label><input type="checkbox" name="asga_options[debug_mode]"
                                          value="1" <?php checked($options['debug_mode'], 1) ?>><?php _e('Enable debugging mode for administrators', ASGA_TEXT_DOMAIN) ?>
                                <a target="_blank"
                                   href="https://developers.google.com/analytics/resources/articles/gaTrackingTroubleshooting#gaDebug"><i
                                        class="dashicons-before dashicons-external"></i></a> </label>

                            <p class="description"><?php _e("This should only be used temporarily or during development, don't forget to disable it in production", 'asga') ?> </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Debug database options', ASGA_TEXT_DOMAIN) ?> :</th>
                        <td>
                            <pre class="db-dump"><?php print_r($options); ?></pre>
                        </td>
                    </tr>
                </table>
            </section>
        </div> <!--.tab-wrapper -->
        <?php submit_button() ?>
    </form>
    <hr>
    <p>
        <?php _e('Developed with ♥ by', ASGA_TEXT_DOMAIN) ?> - <a target="_blank" href="https://ank91.github.io/">Ankur Kumar</a> |
        <?php _e('Contribute on', ASGA_TEXT_DOMAIN) ?> <a href="https://github.com/ank91/ank-simplified-ga" target="_blank">GitHub</a> |
        ★ <?php _e('Rate this on', ASGA_TEXT_DOMAIN) ?>
        <a href="https://wordpress.org/support/view/plugin-reviews/ank-simplified-ga#plugin-info" target="_blank"><?php _e('WordPress') ?></a>
    </p>
</div> <!-- .wrap-->
