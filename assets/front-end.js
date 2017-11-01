(function (window, document) {
  'use strict';

  // Get dynamic options from page
  var asgaOpt = window._asgaOpt;

  document.addEventListener("DOMContentLoaded", function (event) {

    // Track Downloads
    if (asgaOpt.downloadLinks === '1') {
      // https://developer.mozilla.org/en/docs/Web/JavaScript/Guide/Regular_Expressions
      var exts = (asgaOpt.downloadExt === '') ? 'doc*|xls*|ppt*|pdf|zip|rar|mp3' : asgaOpt.downloadExt.replace(/,/g, '|');

      // https://developer.mozilla.org/en/docs/Web/JavaScript/Reference/Global_Objects/RegExp
      var regExt = new RegExp(".*\\.(" + exts + ")(\\?.*)?$");

      var downLinks = document.querySelectorAll('a');

      Array.prototype.forEach.call(downLinks, function (link) {
        // Include only internal links for downloads
        if (link.hostname && (link.hostname === window.location.hostname) && link.href.match(regExt)) {
          link.addEventListener('click', function (e) {
            logClickEvent('Downloads', this.href, e)
          });

          // Only add download attribute if does not have
          if (!link.hasAttribute('download'))
            link.setAttribute('download', '');

        }
      });

    }

    // Track Mailto links
    if (asgaOpt.mailLinks === '1') {
      var mailLinks = document.querySelectorAll('a[href^="mailto"]');

      Array.prototype.forEach.call(mailLinks, function (link) {
        link.addEventListener('click', function (e) {
          // Label should not include 'mailto'
          logClickEvent('Email', this.href.replace(/^mailto\:/i, '').toLowerCase(), e)
        })
      });

    }

    // Track Outbound Links
    if (asgaOpt.outgoingLinks === '1') {
      var outLinks = document.querySelectorAll('a[href^="http"]');

      Array.prototype.forEach.call(outLinks, function (link) {
        // https://css-tricks.com/snippets/jquery/target-only-external-links/
        if (link.hostname && link.hostname !== window.location.hostname) {
          link.addEventListener('click', function (e) {
            logClickEvent('Outbound', (asgaOpt.outboundLinkType === '1') ? this.hostname : this.href, e);
          });
          link.setAttribute('target', '_blank'); // make sure these links open in new tab

        }

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
    // Return early if event.preventDefault() was ever called on this event object.
    if (event.defaultPrevented) return;

    // If label is not set then exit
    if (typeof label === 'undefined' || label === '') return;
    var nonInteractive = (asgaOpt.nonInteractive === '1');

    if (window.ga && ga.hasOwnProperty('loaded') && ga.loaded === true && ga.create) {
      // Universal event tracking
      // https://developers.google.com/analytics/devguides/collection/analyticsjs/events
      ga('send', 'event', category, 'click', label, 1,
        {
          nonInteraction: nonInteractive
        }
      );
    } else if (window._gaq && _gaq._getAsyncTracker) {
      // Classic event tracking
      // https://developers.google.com/analytics/devguides/collection/gajs/eventTrackerGuide
      _gaq.push(['_trackEvent', category, 'click', label, 1, nonInteractive]);
    } else {
      (window.console) ? console.info('Google analytics not loaded yet.') : null
    }
  }

})(window, document);
