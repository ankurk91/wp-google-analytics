=== Ank Simplified Google Analytics ===
Tags: google analytics, tracking, light weight, simple, easy, free , multisite
Requires at least: 3.8.0
Tested up to: 4.2.2
Stable tag: 0.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Contributors: ank91

The most simplified Google Analytics Plugin for WordPress.

== Description ==

Track your WordPress website with Google Analytics service.

> Fork Here : https://github.com/ank91/ank-simplified-ga


= Some Features =
* Simplest user interface
* Most light weight plugin
* Support Universal Google and Classic Analytics
* Using untouched and latest tracking code by Google
* Choose where to place your tracking code
* Control code priority
* Choose to disable tracking if user logged-in
* Track 404 and Searched items
* Sub-domain tracking
* Demographics & Interest Reports
* Enhanced Link Attribution
* Anonymize IPs
* Don't track anything inside wp-admin by default
* Supports multi-site
* Disable tracking when Network Admin is logged-in
* Debugging mode
* Force SSL
* Track user engagement



== Installation ==
0. Remove existing Google Analytics plugin or disable them.
1. Search for 'Ank Simplified GA' in WordPress Plugin Directory and Download the .zip file & extract it.
2. Upload the folder `ank-simplified-ga` to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins List' page in WordPress Admin Area.
4. Configure this plugin via Settings-->Ank Simplified GA
5. Login to Google Analytics account to view stats.


== Frequently Asked Questions ==


= What is different with this plugin ? =

WordPress plugin directory already have many of these kind of plugins.
But not all are optimized for performance.
Most of them lacks some features, while some of them have unused features.

This plugin was developed to provide most used feature in one place without compromising the speed.
So give it a try , i am sure you will not regret for your decision.


= Tracking code not shown up in front end =

There may be several reasons for this.

* Make sure you have entered a valid tracking ID.
* Check if tracking is not disabled for current logged in user.
* Try to flush/delete your site cache.
* Try switching to the default WordPress theme.


= Changes does not reflect after saving settings ? =

Are you using some Cache/Performance plugin (eg:WP Super Cache/W3 Total Cache) ?

Then flush your WP cache after saving settings.

= Where does it store settings and options ? =

WP Database->wp-options->asga_options.

Uses a Single Row, stored in array for faster access.


= What if i uninstall/remove this plugin? =

No worry! It will remove its traces from database upon uninstall.

It will also disable tracking by remove code from front-end.

= Where to find my GA Tracking ID ? =

Just go [here](https://support.google.com/analytics/answer/1032385).

= Am i using Classic or Universal GA ? =

[This](https://support.google.com/analytics/answer/4457764) might help.

= What is debugging mode, How do i use it ? =

Debugging mode allows you to troubleshot problems with Google Analytics web tracking.
Once you enable this mode. Open up your site homepage and press F12 to open developer tools,
now switch to console tab to see detailed messages.

You can also use [this](https://chrome.google.com/extensions/detail/jnkmfdileelhofjcijamephohjechhna) Google Chrome extension for easy debugging.

You can read more about troubleshooting [here](https://developers.google.com/analytics/resources/articles/gaTrackingTroubleshooting#gaDebug)

Don't forget to disable this mode in production.
This mode is only available for administrators only when they are logged-in to WordPress dashboard.

= How does it work for multi-site ? =

You need to configure the plugin for each of sub-site individually.

= Did you test it with old version of WordPress ? =

No, tested with v4.1 and up only.
So i recommend you to upgrade to latest WordPress today.


= Can i modify this plugin ? =

Yes you can. Do whatever you want do.

= Is Google Analytics service free. =

Yes, There is paid version of Google Analytics also.

Read more [here](https://developers.google.com/analytics/devguides/collection/analyticsjs/limits-quotas).


= Future Plans ? =
* I18n for Option Page.
* More options may be.


== Upgrade Notice ==

No big changes yet in this plugin, so go ahead and upgrade to new version whenever i release.

It just a matter of a second. It will cost not more than 10 KB.


== Screenshots ==
1. General Options
2. Advanced Options
3. Tracking Option
4. Troubleshooting


== Changelog ==

= 0.5 =
* Plugin name changed to 'Ank Simplified Google Analytics'
* New tabbed interface
* Force SSL
* User engagement tracking
* Control tracking code execution

= 0.4 =
* Debugging mode
* Updated docs

= 0.3 =
* Multi-site support
* More stable and secure
* Tested with WordPress v4.2

= 0.2 =
* Updated docs and adjustments
* Submitted to WordPress plugin directory

= 0.1 =
* First beta



== Arbitrary section ==
Nothing in this section, Read FAQ.
