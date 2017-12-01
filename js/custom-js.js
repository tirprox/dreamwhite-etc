jQuery(".site-social-icons-twitter .fa-twitter").removeClass("fa-twitter").addClass("fa-vk");
var hideTabs = function() {
  jQuery('ul.tabs.wc-tabs > li.active').removeClass("active");
  jQuery('div#tab-additional_information').hide();
  jQuery('ul.tabs.wc-tabs > li.custom_tab_tab').addClass("active");
  jQuery('div#tab-custom_tab').show();
  jQuery('html, body').animate({
    scrollTop: jQuery("#tab-custom_tab").offset().top-120
  }, 120);
};
jQuery("#size-table-link").on('click', hideTabs);
