jQuery(document).ready(function() {
  jQuery(".site-social-icons-twitter .fa-twitter").removeClass("fa-twitter").addClass("fa-vk");

  if (window.location.pathname == '/') {
      jQuery("#header-top").removeClass("header-top").addClass("header-top-transparent");
  }


  var hideActiveTab = function () {
    jQuery('ul.tabs.wc-tabs > li.active').removeClass("active");
    jQuery('div#tab-additional_information').hide();
    jQuery('div#tab-custom_tab').hide();
    jQuery('div#tab-video').hide();
  }

  var scrollToSizeTable = function() {
    hideActiveTab();
    jQuery('ul.tabs.wc-tabs > li.custom_tab_tab').addClass("active");
    jQuery('div#tab-custom_tab').show();
    jQuery('html, body').animate({
      scrollTop: jQuery("#tab-custom_tab").offset().top-120
    }, 160);
  };

  var scrollToVideo = function() {
    hideActiveTab();
    jQuery('ul.tabs.wc-tabs > li.video_tab').addClass("active");
    jQuery('div#tab-video').show();
    jQuery('html, body').animate({
      scrollTop: jQuery("#tab-video").offset().top-120
    }, 160);
  };

  jQuery("#size-table-link").on('click', scrollToSizeTable);


  if (jQuery( "#tab-video" ).length === 0) {
    jQuery( ".scroll-to-video-wrapper" ).hide();
  }
  else {
    jQuery(".scroll-to-video-wrapper").click(scrollToVideo);
    //jQuery(".scroll-to-video-wrapper").css("height", "95px");

  }
});

