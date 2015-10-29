/**
 * Ank-Simplified-GA option-page tab handling
 */
(function (window) {
    'use strict';

    jQuery(function ($) {
        var ga_tabs = $('h2#ga-tabs');
        ga_tabs.find('a').click(function () {
            //disable all tabs
            ga_tabs.find('a').removeClass('nav-tab-active');
            $('div.tab-content').removeClass('active');
            //activate current tab only
            var id = $(this).attr('id').replace('-tab', '');
            $('#' + id).addClass('active');
            $(this).addClass('nav-tab-active');
            //set return tab on click vent
            set_redirect_url(id);
        });
        //get requested tab from url
        var activeTab = window.location.hash.replace('#top#', '');
        //if there no active tab found , set first tab as active
        if (activeTab === '') activeTab = $('div.tab-content').attr('id');
        $('#' + activeTab).addClass('active');
        $('#' + activeTab + '-tab').addClass('nav-tab-active');
        //set return tab on page load
        set_redirect_url(activeTab);
        /**
         * Set redirect url into form's input:hidden
         * @param url
         */
        function set_redirect_url(url) {
            //This is a workaround
            var input = $("form#asga_form").find('input:hidden:nth-child(4)'),
                split = input.val().split('?', 1);
            input.val(split[0] + '?page=asga_options_page#top#' + url);
        }
    });
})(window);