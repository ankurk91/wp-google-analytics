<div class="wrap">
    <h2><?php _e('Simplified Google Analytics', 'ank-simplified-ga') ?>
        <small>(v<?php echo ASGA_PLUGIN_VER; ?>)</small>
    </h2>

    <h2 class="nav-tab-wrapper" id="ga-tabs">
        <a class="nav-tab" id="ga-general-tab" href="#top#ga-general"><?php _e('General', 'ank-simplified-ga') ?></a>
        <a class="nav-tab" id="ga-events-tab" href="#top#ga-events"><?php _e('Event Tracking', 'ank-simplified-ga') ?></a>
        <a class="nav-tab" id="ga-advanced-tab" href="#top#ga-advanced"><?php _e('Advanced', 'ank-simplified-ga') ?></a>
        <a class="nav-tab" id="ga-control-tab" href="#top#ga-control"><?php _e('Control', 'ank-simplified-ga') ?></a>
        <a class="nav-tab" id="ga-troubleshoot-tab" href="#top#ga-troubleshoot"><?php _e('Troubleshoot', 'ank-simplified-ga') ?></a>
    </h2><!--.nav-tab-wrapper-->

    <form action="<?php echo admin_url('options.php') ?>" method="post" id="asga_form" novalidate>
        <?php
        $options = $this->get_safe_options();
        //wp inbuilt nonce field , etc
        settings_fields(self::PLUGIN_OPTION_GROUP);
        ?>
        <div class="tab-wrapper">
            <section id="ga-general" class="tab-content">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Google Analytics tracking ID', 'ank-simplified-ga') ?> :</th>
                        <td><input type="text" size="25" placeholder="UA-012345678-9" name="asga_options[ga_id]"
                                   value="<?php echo esc_attr($options['ga_id']); ?>">
                            <a target="_blank" href="https://support.google.com/analytics/answer/1032385"><i
                                    class="dashicons-before dashicons-external"></i></a>
                            <br>
                            <p class="description"><?php _e('Paste your Google Analytics tracking ID e.g.', 'ank-simplified-ga') ?>
                                ("<code>UA-XXXXXXXX-Y</code>")</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Analytics version', 'ank-simplified-ga') ?>:</th>
                        <td>
                            <select name="asga_options[ua_enabled]">
                                <option
                                    value="1" <?php selected($options['ua_enabled'], 1) ?>><?php _e('Universal', 'ank-simplified-ga') ?>
                                    (analytics.js)
                                </option>
                                <option
                                    value="0" <?php selected($options['ua_enabled'], 0) ?>><?php _e('Classic', 'ank-simplified-ga') ?>
                                    (ga.js)
                                </option>
                            </select>
                            <a href="https://support.google.com/analytics/answer/3450662" target="_blank"><i
                                    class="dashicons-before dashicons-external"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Set domain', 'ank-simplified-ga') ?> :</th>
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
                </table>
            </section>
            <section id="ga-events" class="tab-content">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Events to track', 'ank-simplified-ga') ?> :</th>
                        <td>
                            <fieldset>
                                <?php
                                $events = array(
                                    'log_404' => __('Log 404 pages as events', 'ank-simplified-ga'),
                                    'track_mail_links' => __('Track email links as events', 'ank-simplified-ga'),
                                    'track_outbound_links' => __('Track outbound links as events', 'ank-simplified-ga'),
                                    'track_download_links' => __('Track downloads as events', 'ank-simplified-ga'),
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
                        <th scope="row"><?php _e('Non interactive events', 'ank-simplified-ga') ?> :</th>
                        <td><label><input type="checkbox" name="asga_options[track_non_interactive]"
                                          value="1" <?php checked($options['track_non_interactive'], 1) ?>><?php _e('Events should not affect bounce rate', 'ank-simplified-ga') ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Extensions for downloads', 'ank-simplified-ga') ?> :</th>
                        <td>
                            <input size="25" type="text" placeholder="doc,docx,xls,xlsx,pdf,zip,rar,exe"
                                   name="asga_options[track_download_ext]"
                                   value="<?php echo esc_attr($options['track_download_ext']); ?>">
                            <p class="description"><?php _e('Please use comma (,) separated values', 'ank-simplified-ga') ?> </p>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Track outbound link type', 'ank-simplified-ga') ?> :</th>
                        <td>
                            <select name="asga_options[track_outbound_link_type]">
                                <option
                                    value="1" <?php selected($options['track_outbound_link_type'], 1) ?>><?php _e('Just the domain', 'ank-simplified-ga') ?></option>
                                <option
                                    value="0" <?php selected($options['track_outbound_link_type'], 0) ?>><?php _e('Full URL', 'ank-simplified-ga') ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
            </section>
            <section id="ga-advanced" class="tab-content">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Sample rate', 'ank-simplified-ga') ?> :</th>
                        <td><input type="number" step="any" min="0" placeholder="100" name="asga_options[sample_rate]"
                                   value="<?php echo esc_attr($options['sample_rate']); ?>">%
                            <a target="_blank" href="https://developers.google.com/analytics/devguides/collection/analyticsjs/field-reference#sampleRate"><i
                                    class="dashicons-before dashicons-external"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Demographics and interest reports', 'ank-simplified-ga') ?> :</th>
                        <td><label><input type="checkbox" name="asga_options[displayfeatures]"
                                          value="1" <?php checked($options['displayfeatures'], 1) ?>><?php _e('Enable advertising features', 'ank-simplified-ga') ?>
                                <a target="_blank" href="https://support.google.com/analytics/answer/3450482"><i
                                        class="dashicons-before dashicons-external"></i></a></label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Enhanced link attribution', 'ank-simplified-ga') ?> :</th>
                        <td><label><input type="checkbox" name="asga_options[ga_ela]"
                                          value="1" <?php checked($options['ga_ela'], 1) ?>><?php _e('Check to enable', 'ank-simplified-ga') ?>
                                <a target="_blank" href="https://support.google.com/analytics/answer/2558867"><i
                                        class="dashicons-before dashicons-external"></i></a> </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Cross-domain user tracking', 'ank-simplified-ga') ?> :</th>
                        <td><label><input type="checkbox" name="asga_options[allow_linker]"
                                          value="1" <?php checked($options['allow_linker'], 1) ?>><?php _e('_setAllowLinker', 'ank-simplified-ga') ?>
                                <a target="_blank"
                                   href="https://developers.google.com/analytics/devguides/collection/gajs/methods/gaJSApiDomainDirectory#_gat.GA_Tracker_._setAllowLinker"><i
                                        class="dashicons-before dashicons-external"></i> </a> </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Campaign tracking', 'ank-simplified-ga') ?> :</th>
                        <td><label><input type="checkbox" name="asga_options[allow_anchor]"
                                          value="1" <?php checked($options['allow_anchor'], 1) ?>><?php _e('_setAllowAnchor', 'ank-simplified-ga') ?>
                                <a target="_blank"
                                   href="https://developers.google.com/analytics/devguides/collection/gajs/methods/gaJSApiCampaignTracking#_gat.GA_Tracker_._setAllowAnchor"><i
                                        class="dashicons-before dashicons-external"></i></a> </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Tag RSS links with campaign variables', 'ank-simplified-ga') ?> :</th>
                        <td><label><input type="checkbox" name="asga_options[tag_rss_links]"
                                          value="1" <?php checked($options['tag_rss_links'], 1) ?>><?php _e('Check to enable', 'ank-simplified-ga') ?>
                                <a target="_blank"
                                   href="https://support.google.com/analytics/answer/1033863?hl=en"><i
                                        class="dashicons-before dashicons-external"></i></a> </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Anonymize IP', 'ank-simplified-ga') ?> :</th>
                        <td><label><input type="checkbox" name="asga_options[anonymise_ip]"
                                          value="1" <?php checked($options['anonymise_ip'], 1) ?>><?php _e('Anonymizes IP addresses', 'ank-simplified-ga') ?>
                                <a href="https://support.google.com/analytics/answer/2763052" target="_blank"><i
                                        class="dashicons-before dashicons-external"></i></a></label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Force SSL', 'ank-simplified-ga') ?> :</th>
                        <td><label><input type="checkbox" name="asga_options[force_ssl]"
                                          value="1" <?php checked($options['force_ssl'], 1) ?>><?php _e('Transmit data over secure (https) connection', 'ank-simplified-ga') ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Custom trackers', 'ank-simplified-ga') ?> :</th>
                        <td><textarea placeholder="Please don't not include &lt;script&gt tags" rows="5" cols="35"
                                      name="asga_options[custom_trackers]"
                                      style="resize: vertical;max-height: 300px;"><?php echo stripslashes($options['custom_trackers']) ?></textarea>
                            <p class="description"><?php _e('To be added before the', 'ank-simplified-ga') ?>
                                <code><?php _e('pageview', 'ank-simplified-ga') ?></code> <?php _e('call', 'ank-simplified-ga') ?>
                                </p>
                        </td>
                    </tr>
                </table>
            </section>
            <section id="ga-control" class="tab-content">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Place tracking code in', 'ank-simplified-ga') ?> :</th>
                        <td>
                            <fieldset>
                                <label><input type="radio" name="asga_options[js_location]"
                                              value="1" <?php checked($options['js_location'], 1) ?>>&ensp;<?php _e('Document header', 'ank-simplified-ga') ?>
                                </label><br>
                                <label><input type="radio" name="asga_options[js_location]"
                                              value="2" <?php checked($options['js_location'], 2) ?>>&ensp;<?php _e('Document footer', 'ank-simplified-ga') ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Action priority', 'ank-simplified-ga') ?> :</th>
                        <td><input type="number" size="25" placeholder="20" name="asga_options[js_priority]"
                                   value="<?php echo esc_attr($options['js_priority']); ?>">
                            <p class="description"><?php _e('0 means highest priority', 'ank-simplified-ga') ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Stop analytics when', 'ank-simplified-ga') ?> :</th>
                        <td>
                            <fieldset>
                                <?php
                                foreach ($this->get_all_roles() as $role) {
                                    echo '<label>';
                                    echo '<input type="checkbox" name="asga_options[ignore_role_' . $role['id'] . ']" value="1" ' . checked($options['ignore_role_' . $role['id']], 1, 0) . '/>';
                                    echo '&ensp;' . esc_attr($role['name']) . ' ' . __('is logged in', 'ank-simplified-ga');
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
                        <th scope="row"><?php _e('Debug mode', 'ank-simplified-ga') ?> :</th>
                        <td><label><input type="checkbox" name="asga_options[debug_mode]"
                                          value="1" <?php checked($options['debug_mode'], 1) ?>><?php _e('Enable debugging mode for administrators', 'ank-simplified-ga') ?>
                                <a target="_blank"
                                   href="https://developers.google.com/analytics/resources/articles/gaTrackingTroubleshooting#gaDebug"><i
                                        class="dashicons-before dashicons-external"></i></a> </label>

                            <p class="description"><?php _e("This should only be used temporarily or during development, don't forget to disable it in production", 'ank-simplified-ga') ?> </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Debug database options', 'ank-simplified-ga') ?> :</th>
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
        <?php _e('Developed with ♥ by', 'ank-simplified-ga') ?> - <a target="_blank" href="https://twitter.com/ankurk91">Ankur Kumar</a> |
        <?php _e('Contribute on', 'ank-simplified-ga') ?> <a href="https://github.com/ankurk91/wp-google-analytics" target="_blank">GitHub</a> |
        ★ <?php _e('Rate this on', 'ank-simplified-ga') ?>
        <a href="https://wordpress.org/support/plugin/ank-simplified-ga/reviews/?filter=5" target="_blank"><?php _e('WordPress') ?></a>
    </p>
</div> <!-- .wrap-->
