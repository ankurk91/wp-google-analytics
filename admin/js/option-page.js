/**
 * Ank-Simplified-GA
 */
(function (window) {
    'use strict';
    jQuery(function ($) {
        var ga_tabs = $('h2#ga-tabs');
        ga_tabs.find('a').click(function () {
            ga_tabs.find('a').removeClass('nav-tab-active');
            $('div.tab-content').removeClass('active');
            var id = $(this).attr('id').replace('-tab', '');
            $('#' + id).addClass('active');
            $(this).addClass('nav-tab-active');
            set_redirect_url(id);
        });
        var activeTab = window.location.hash.replace('#top#', '');
        if (activeTab === '') activeTab = $('div.tab-content').attr('id');
        $('#' + activeTab).addClass('active');
        $('#' + activeTab + '-tab').addClass('nav-tab-active');
        set_redirect_url(activeTab);
        function set_redirect_url(url) {
            var input = $("form#asga_form").find('input:hidden:nth-child(4)');
            var split = input.val().split('?', 1);
            input.val(split[0] + '?page=asga_options_page#top#' + url);
        }
    });
})(window);