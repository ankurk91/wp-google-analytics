/**
 * Ank-Simplified-GA
 */
(function (window) {
    'use strict';
    //if options not exists then exit early
    if (typeof window.asga_opt === 'undefined' || asga_opt.length === 0) {
        return;
    }
    //jQuery Filter Ref: http://api.jquery.com/filter/
    jQuery(function ($) {

        if (asga_opt.track_download_links === 1) {
            //Track Downloads
            var exts = (asga_opt.track_download_ext === '') ? 'doc*|xls*|ppt*|pdf|zip|rar|exe|mp3' : asga_opt.track_download_ext.replace(',', '|');
            $('a').filter(function () {
                //include only internal links
                if (this.href.indexOf(window.location.host) !== -1) {
                    return this.href.match(/.*\.(exts)(\?.*)?$/);
                }
            }).click(function () {
                _sendEvent('Downloads', this.href)
            });
        }

        if (asga_opt.track_mail_links === 1) {
            //Track Mailto links
            $('a[href^="mailto"]').click(function () {
                //href should not include 'mailto'
                _sendEvent('Email', this.href.replace('mailto:', ''))
            });
        }

        if (asga_opt.track_outgoing_links === 1) {
            //Track Outbound Links
            $('a[href^="http"]').filter(function () {
                return (this.href.indexOf(window.location.host) == -1)
            }).prop('target', '_blank')  // make sure these links open in new tab
                .click(function () {
                    _sendEvent('Outbound', this.href);
                });
        }

    });

    /**
     * Detect Analytics type and send event
     * @ref https://support.google.com/analytics/answer/1033068
     * @param category string
     * @param label string
     */
    function _sendEvent(category, label) {
        if (window.ga && ga.create) {
            //Universal event tracking
            //https://developers.google.com/analytics/devguides/collection/analyticsjs/events
            ga('send', 'event', category, 'click', label, {
                nonInteraction: true
            });
        } else if (window._gaq && _gaq._getAsyncTracker) {
            //classic event tracking
            //https://developers.google.com/analytics/devguides/collection/gajs/eventTrackerGuide
            _gaq.push(['_trackEvent', category, 'click', label, 1, true]);
        } else {
            console.info('Google analytics not loaded')
        }
    }
})(window);