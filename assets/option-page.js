(function (window, jQuery) {
  'use strict';
  // Get requested tab from url
  var requestedTab = window.location.hash.replace('#top#', '');

  jQuery(function ($) {
	/**
	 * Cache DOM elements for later use
	 */
	var $gaTabs = $('h2#ga-tabs'),
		$input = $("form#asga_form").find('input:hidden[name="_wp_http_referer"]'),
		$sections = $('section.tab-content');

	// If there no active tab found , set first tab as active
	if (requestedTab === '' || $('#' + requestedTab).length === 0) requestedTab = $sections.attr('id');
	// Notice: we are not using cached DOM in next line
	$('#' + requestedTab).addClass('active');
	$('#' + requestedTab + '-tab').addClass('nav-tab-active');
	// Set return tab on page load
	setRedirectURL(requestedTab);

	// Bind a click event to all tabs
	$gaTabs.find('a.nav-tab').on('click.asga', (function (e) {
	  e.stopPropagation();
	  // Hide all tabs
	  $gaTabs.find('a.nav-tab').removeClass('nav-tab-active');
	  $sections.removeClass('active');
	  // Activate only clicked tab
	  var id = $(this).attr('id').replace('-tab', '');
	  $('#' + id).addClass('active');
	  $(this).addClass('nav-tab-active');
	  // Set return tab url
	  setRedirectURL(id);
	}));

	/**
	 * Set redirect url into form's input:hidden
	 * Note: Using hardcoded plugin option page slug
	 * @param url String
	 */
	function setRedirectURL(url) {
	  var split = $input.val().split('?', 1);
	  //Update the tab id in last while keeping base url same
	  $input.val(split[0] + '?page=asga_options_page#top#' + url);
	}
  });
})(window, jQuery);