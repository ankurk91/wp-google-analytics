/**
 * Ank-Simplified-GA event tracking
 */
(function (window, asga_opt) {
    'use strict';
    //if options not exists then exit early
    if (typeof asga_opt === 'undefined' || asga_opt.length === 0) {
        return;
    }
    //jQuery Filter Ref: http://api.jquery.com/filter/
    jQuery(function ($) {

        if (asga_opt.track_download_links === '1') {
            //Track Downloads
            //@source https://developer.mozilla.org/en/docs/Web/JavaScript/Guide/Regular_Expressions
            var exts = (asga_opt.track_download_ext === '') ? 'doc*|xls*|ppt*|pdf|zip|rar|exe|mp3' : asga_opt.track_download_ext.replace(/,/g, '|');
            //@source https://developer.mozilla.org/en/docs/Web/JavaScript/Reference/Global_Objects/RegExp
            var regExt = new RegExp(".*\\.(" + exts + ")(\\?.*)?$");

            $('a').filter(function () {
                //include only internal links for downloads
                if (this.hostname && (this.hostname === window.location.hostname)) {
                    return this.href.match(regExt);
                }
            }).prop('download', '') //force download of these files
                .click(function (e) {
                    logClickEvent('Downloads', this.href)
                });
        }

        if (asga_opt.track_mail_links === '1') {
            //Track Mailto links
            $('a[href^="mailto"]').click(function () {
                //href should not include 'mailto'
                logClickEvent('Email', this.href.replace('mailto:', '').toLowerCase())
            });
        }

        if (asga_opt.track_outgoing_links === '1') {
            //Track Outbound Links
            //@source https://css-tricks.com/snippets/jquery/target-only-external-links/
            $('a[href^="http"]').filter(function () {
                return (this.hostname && this.hostname !== window.location.host)
            }).prop('target', '_blank')  // make sure these links open in new tab
                .click(function () {
                    logClickEvent('Outbound', this.href);
                });
        }


    });

    /**
     * Detect Analytics type and send event
     * @ref https://support.google.com/analytics/answer/1033068
     * @param category string
     * @param label string
     */
    function logClickEvent(category, label) {
        if (window.ga && ga.create) {
            //Universal event tracking
            //https://developers.google.com/analytics/devguides/collection/analyticsjs/events
            ga('send', 'event', category, 'click', label, {
                nonInteraction: true
            });
        } else if (window._gaq && _gaq._getAsyncTracker) {
            //Classic event tracking
            //https://developers.google.com/analytics/devguides/collection/gajs/eventTrackerGuide
            _gaq.push(['_trackEvent', category, 'click', label, 1, true]);
        } else {
            (window.console) ? console.info('Google analytics not loaded') : null
        }
    }
})(window, window.asga_opt);