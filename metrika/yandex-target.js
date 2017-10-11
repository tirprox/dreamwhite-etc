var referrer = document.referrer,
  instagram = "instagram";

var popupCode = "7454";
var popup = jQuery('#pum-'+popupCode);

// analog of string.contains, checking url for instagram substring
var isFromInstagram = referrer.indexOf(instagram) !== -1;

if (isFromInstagram) {
  // creating cookie to prevent popup, max-age = 1 month in seconds
  document.cookie = "pum-"+popupCode+"=true;max-age=2592000";
  // removing popup if needed
  popup.parentNode.removeChild(popup);
  console.log("popup removed");
}
else {
  if (yaCounter42085949 != null) {
    popup.on('pumAfterOpen', function () {
      yaCounter42085949.reachGoal('instagram-popup-shown')
    });
    popup.on('pumAfterClose', function () {
      yaCounter42085949.reachGoal('instagram-popup-closed')
    });
  
    jQuery('#popup-instagram-subscribe-button').on('click',instagramOpened);
    jQuery('.popup-instagram-feed').on('click',instagramOpened);
    jQuery('.woo-social-buttons a').on('click', shared);
  }
}

function instagramOpened() {
  yaCounter42085949.reachGoal('instagram-opened-from-popup');
}

function shared(){
  yaCounter42085949.reachGoal('shared');
  console.log("yandex target - shared");
}