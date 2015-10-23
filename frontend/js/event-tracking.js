(function (window) {

    if (typeof window.asga_opt === 'undefined') {
        return;
    }
    //jQuery Filter Ref: http://api.jquery.com/filter/
    jQuery(function ($) {

        //Track Downloads
        $('a').filter(function () {
            //include only internal links
            if (this.href.indexOf(window.location.host) !== -1) {
                return this.href.match(/.*\.(zip|mp3*|mpe*g|pdf|docx*|pptx*|xlsx*|rar*)(\?.*)?$/);
            }
        }).click(function (e) {
            _sendEvent('Downloads', this.href)
        });

        //Track Mailto links
        $('a[href^="mailto"]').click(function (e) {
            //href should not include 'mailto'
            _sendEvent('Email', this.href.replace('mailto:', ''))
        });

        //Track Outbound Links
        $('a[href^="http"]').filter(function () {
            if (this.href.indexOf(window.location.host) == -1) return this.href;
        }).click(function (e) {
            _sendEvent('Outbound', this.href);
        });
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
        } else if (window._gaq && window._gaq._getAsyncTracker) {
            //classic event tracking
            //https://developers.google.com/analytics/devguides/collection/gajs/eventTrackerGuide
            _gaq.push(['_trackEvent', category, 'click', label, 1, true]);
        } else {
            console.info('Google analytics not found in this page')
        }
    }
})(window);