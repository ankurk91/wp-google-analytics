/**
 * Ank-Simplified-GA option-page javascript
 */
(function (window, jQuery) {
    'use strict';
    //Get requested tab from url
    var requestedTab = window.location.hash.replace('#top#', '');

    jQuery(function ($) {
        //if there no active tab found , set first tab as active
        if (requestedTab === '') requestedTab = $('section.tab-content').attr('id');
        $('#' + requestedTab).addClass('active');
        $('#' + requestedTab + '-tab').addClass('nav-tab-active');
        //Set return tab on page load
        setRedirectURL(requestedTab);

        /**
         * Storing DOM element for later use
         * @type {*|jQuery|HTMLElement}
         */
        var $gaTabs = $('h2#ga-tabs');
        /**
         * Bind a click event to all tabs
         */
        $gaTabs.find('a.nav-tab').on('click', (function (e) {
            e.stopPropagation();
            //Hide all tabs
            $gaTabs.find('a.nav-tab').removeClass('nav-tab-active');
            $('section.tab-content').removeClass('active');
            //Activate only clicked tab
            var id = $(this).attr('id').replace('-tab', '');
            $('#' + id).addClass('active');
            $(this).addClass('nav-tab-active');
            //Set return tab url
            setRedirectURL(id);
        }));

        /**
         * Storing DOM element for faster processing
         * @type {*|{}|jQuery}
         */
        var $input = $("form#asga_form").find('input:hidden[name="_wp_http_referer"]');

        /**
         * Set redirect url into form's input:hidden
         * Note: Using hardcoded plugin option page slug
         * @param url String
         */
        function setRedirectURL(url) {
            if (typeof $input === 'undefined')  return;
            var split = $input.val().split('?', 1);
            //Update the tab id in last while keeping base url same
            $input.val(split[0] + '?page=asga_options_page#top#' + url);
        }
    });
})(window, jQuery);