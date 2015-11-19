/**
 * Ank-Simplified-GA event tracking
 */
(function (window, asga_opt, jQuery) {
    'use strict';
    //if options not exists then exit early
    if (typeof asga_opt === 'undefined' || asga_opt.length === 0) {
        return;
    }
    //jQuery Filter Ref: http://api.jquery.com/filter/
    jQuery(function ($) {

        if (asga_opt.download_links === '1') {
            //Track Downloads
            //@source https://developer.mozilla.org/en/docs/Web/JavaScript/Guide/Regular_Expressions
            var exts = (asga_opt.download_ext === '') ? 'doc*|xls*|ppt*|pdf|zip|rar|exe|mp3' : asga_opt.download_ext.replace(/,/g, '|');
            //@source https://developer.mozilla.org/en/docs/Web/JavaScript/Reference/Global_Objects/RegExp
            var regExt = new RegExp(".*\\.(" + exts + ")(\\?.*)?$");

            $('a').filter(function () {
                //include only internal links for downloads
                if (this.hostname && (this.hostname === window.location.hostname)) {
                    return this.href.match(regExt);
                }
            }).prop('download', '') //force download of these files
                .click(function (e) {
                    logClickEvent('Downloads', this.href, e)
                });
        }

        if (asga_opt.mail_links === '1') {
            //Track Mailto links
            $('a[href^="mailto"]').click(function (e) {
                //href should not include 'mailto'
                logClickEvent('Email', this.href.replace(/^mailto\:/i, '').toLowerCase(), e)
            });
        }

        if (asga_opt.outgoing_links === '1') {
            //Track Outbound Links
            //@source https://css-tricks.com/snippets/jquery/target-only-external-links/
            $('a[href^="http"]').filter(function () {
                return (this.hostname && this.hostname !== window.location.hostname)
            }).prop('target', '_blank')  // make sure these links open in new tab
                .click(function (e) {
                    logClickEvent('Outbound', (asga_opt.outbound_link_type === '1') ? this.hostname : this.href, e);
                });
        }

    });

    /**
     * Detect Analytics type and send event
     * @ref https://support.google.com/analytics/answer/1033068
     * @param category string
     * @param label string
     * @param event click event
     */
    function logClickEvent(category, label, event) {
        //return early if event.preventDefault() was ever called on this event object.
        if (event.isDefaultPrevented()) return;

        //label is not set then exit
        if(typeof label === 'undefined' || label==='') return;

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
})(window, window.asga_opt, jQuery);