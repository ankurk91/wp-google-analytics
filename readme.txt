=== Google Analytics Simplified ===
Tags: google analytics, tracking, light weight, simple, easy, free, multi-site
Requires at least: 5.0.0
Requires PHP: 5.6.0
Tested up to: 5.6.1
Stable tag: 1.5.0
License: MIT
License URI: https://opensource.org/licenses/MIT
Contributors: ankurk91

The most simplified Google Analytics Plugin for WordPress.

== Description ==

Track your WordPress website with Google Analytics service.


= Highlights =
* Supports Universal Google and Classic Analytics both
* Using untouched and latest tracking code by Google
* Covers most used analytics features
* Most lightweight plugin
	* Does not provide any dashboard or statistics reporting tool, no one can beat the default Google Analytics reporting dashboard
	* Follows best WordPress coding practices
	* No Ads, No banner, No usage tracking
* Simplest user interface
	* Single page tab based interface
	* Minimal and non confusing settings
* Multi-site ready
    * Each sub site need to be configured separately
    * Each sub site will store its own configuration in database, there is no global settings for this plugin
* Translation ready, you are welcome [here](https://translate.wordpress.org/projects/wp-plugins/ank-simplified-ga)
* Ability to place code in header or footer, control priority


= Google Analytics features covered =
* Demographics & Interest Reports
* Enhanced Link Attribution
* Anonymize IPs
* Cross-domain user tracking (AllowLinker)
* Campaign tracking (AllowAnchor)
* Sub-domain tracking
* Force SSL
* Sample Rate

= Event Tracking =
* Track 404 pages as events
* Track email links as events
* Track outbound links as events
* Track downloads as events
* Ability to add your own custom trackers
* Option to toggle non-interactive events

= Exclude users based on their role =
* Ability to exclude (stop tracking) for different WordPress roles
* Not tracking anything inside wp-admin area.
* Not tracking anything in preview mode.

= Debug mode =
* Allows you to debug tracking code through your browser's inbuilt dev tools
* Works only when a administrator user is logged in


== Installation ==
0. Remove existing Google Analytics plugin or disable them.
1. Search for 'Ank Simplified GA' in WordPress Plugin Directory and Download the .zip file & extract it.
2. Upload the folder `ank-simplified-ga` to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins List' page in WordPress Admin Area.
4. Configure this plugin via Settings-->Google Analytics
5. Login to Google Analytics account to view stats.


== Frequently Asked Questions ==

= Tracking code not shown up in front end =

There may be several reasons for this.

* Make sure you have entered a valid tracking ID.
* Check if tracking is not disabled for current logged in user type.
* Try to flush/delete your site cache.
* Try re-installing the plugin.


= Changes does not reflect after saving settings? =

Are you using some Cache/Performance plugin (eg: WP Super Cache/W3 Total Cache) ?

Then flush your WP cache after saving settings.


= Where to find my GA Tracking ID? =

Just go [here](https://support.google.com/analytics/answer/1032385).

= Am i using Classic or Universal Google Analytics? =

[This](https://support.google.com/analytics/answer/4457764) guide may help.

= What is debugging mode, How do i use it? =

Debugging mode allows you to troubleshot problems with Google Analytics web tracking.
Once you enable this mode. Open up your site homepage and press F12 to open developer tools,
now switch to console tab to see detailed messages.

You can read more about troubleshooting [here](https://developers.google.com/analytics/resources/articles/gaTrackingTroubleshooting#gaDebug)

Don't forget to disable this mode in production.

This mode is only available for administrators only when they are logged-in to WordPress dashboard.

= How does it work for multi-site? =

You need to configure the plugin for each of sub-site individually.


== Screenshots ==
1. General Options
2. Advanced Options
3. Tracking/Monitor Options
4. Control code execution
5. Troubleshooting Options


== Upgrade Notice ==


== Changelog ==

= 1.5.0 =
* Tested on php 7.4 and WordPress 5.6
* Requires php 5.6+

= 1.4.2 =
* Tested with WP v4.9

= 1.4.1 =
* Fix: send `1` as value in events
* Fix: php namespace

= 1.4.0 =
* Compatible with WP v4.8.0
* Remove: Option to load JS on `window.load` event

= 1.3.0 =
* Fix text domain
* Minimum WordPress requirement 4.0

= 1.2.1 =
* Updated links
* Change namespace

= 1.2.0 =
* Tested up to WP v4.5.3
* Remove: Google Webmaster option
* Event Tracking - No longer depends on jQuery
* Event Tracking - Dropped IE8 support

= 1.1.0 =
* New: Set Sample Rate
* New: Tag RSS links with campaign variables
* Fix: Improved 404 page tracking
* Fix: Refactor the code a lot
* Fix: Non interactive issue

= 1.0.2 =
* Tested upto WP v4.5.1
* Deprecate 'Google Webmaster Code' options in favour of [Yoast SEO](https://wordpress.org/plugins/wordpress-seo/)

= 1.0.1 =
* Tested up to WP v4.4.2
* Minor updates and fixes

= 1.0.0 =
* Removed : Log search query as event, read [more](https://support.google.com/analytics/answer/1012264?hl=en)
* Allow events to be non-interactive

= 0.9.9 =
* Minimum php version required 5.3.0 because of [namespace](http://php.net/manual/en/language.namespaces.rationale.php)
* Updated docs
* Several speed improvements

= 0.9.8 =
* Fix: A bug while checking for event tracking js

= 0.9.7 =
* Code organization
* Minor bug fixes
* Tweak Docs

= 0.9.6 =
* New: Event tracking, tracking outbound links , track downloads

= 0.9.5 =
* Bug Fix: Admin role was not being ignored

= 0.9.4 =
* Bug Fix: Fixed a bug with handling different database options

= 0.9.3 =
* Post release: Minor tweaks and fixes, sorry for two updates in a single day
* Updates screen-shots
* Updated docs


= 0.9.2 =
* Feature: Ability to link to Google Webmaster

= 0.9.1 =
* Minor Fix : Debugging info was not shown for classic ga when 'On page load' is enabled
* Code improvements

= 0.9.0 =
* Bug Fix - Rollback transient js feature

= 0.8.7 =
* Fixed translation issues
* Code organization
* UI Fixes

= 0.8.6 =
* Fix text domain issues
* Changed text-domain
* Tested up to WordPress v4.3.1

= 0.8.5 =
* Plugin is now translation ready
* Fixed typos
* Tested up to WordPress v4.2.5

= 0.8.4 =
* More debugging options
* Fixed bugs

= 0.8.3 =
* Some new options added
* Minified admin js

= 0.8.2 =
* Custom Trackers
* Return to same tab upon save
* Minor adjustments

= 0.8.1 =
* Track user engagement option removed in favor of [this](http://riveted.parsnip.io/)

= 0.8 =
* Bug fixes

= 0.7 =
* Improved upgrade paths

= 0.6 =
* Cache processed js code for faster access
* More docs coming soon

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


== Other Notes ==

