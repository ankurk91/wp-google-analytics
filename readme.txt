=== Ank Simplified GA ===
Tags: google analytics, tracking, light weight, simple, easy, free
Requires at least: 3.8.0
Tested up to: 4.1.1
Stable tag: 0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Contributors: ank91

The most simplified Google Analytics Plugin for WordPress.

== Description ==

Track your WordPress website with Google Analytics service.


= Some Features =
* Simplest user interface
* Most light weight plugin
* Support Universal Google and Classic Analytics
* Using untouched and latest tracking code by Google
* Choose where to place your tracking code
* Control code priority
* Choose to disable tracking if user logged in
* Track 404 and Searched items
* Sub-domain tracking
* Demographics & Interest Reports
* Enhanced Link Attribution
* Anonymize IPs
* Don't track anything inside wp-admin by default



== Installation ==
1. Search for 'Ank Simplified GA' in GitHub and Download the .zip file & extract it.
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
* Check if tracking is not disabled for current logged in user.
* Try to flush/delete your site cache.
* Try switching to the default WordPress theme.
* Make sure you have entered a valid tracking ID.

= Changes does not reflect after saving settings ? =

Are you using some Cache/Performance plugin (eg:WP Super Cache/W3 Total Cache) ?
Then flush your WP cache after saving settings.

= Where does it store settings and options ? =

WP Database->wp-options->asga_options.
Uses a Single Row, stored in array for faster access.


= What if i uninstall/remove this plugin? =

No worry! It will remove its traces from database upon uninstall.
It will also disable tracking by remove code from front-end.

= Where to find my Tracking ID ? =

Just go [here](https://support.google.com/analytics/answer/1032385?hl=en).


= Did you test it with old version of WordPress ? =

No, tested with v4.1+ (latest as of now) only.
So i recommend you to upgrade to latest WordPress today.


= Can i modify this plugin ? =

Yes you can. But you can't make money by selling this. You can ask for donation.

= Is Google Analytics service free. =

Yes, There is paid version of Google Analytics also.
Read more [here](https://developers.google.com/analytics/devguides/collection/analyticsjs/limits-quotas).

= Future Plans ? =
* I18n for Option Page.
* More options may be.
* Support for multisite

== Upgrade Notice ==

No big changes yet in this plugin, so go ahead and upgrade to new version whenever i release.
It just a matter of a second. It will cost not more than 10 KB.

== Screenshots ==
1. Plugin Option Page Screen

== Changelog ==

= 0.2 =
* Updated docs and adjustments
* Submitted to WordPress plugin directory

= 0.1 =
* First beta



== Arbitrary section ==
Nothing in this section, Read FAQ.
