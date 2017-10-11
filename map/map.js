var markersArray = [];
var delay = 100;
var addressArray = [
  {"cityname": "Санкт Петербург", "city_sales": 3},
  {"cityname": "Москва", "city_sales": 1},
  {"cityname": "Уфа", "city_sales": 1},
  {"cityname": "Новосибирск", "city_sales": 3},
  {"cityname": "Владивосток", "city_sales": 1},
  {"cityname": "Омск", "city_sales": 1},
  {"cityname": "Нижний Тагил", "city_sales": 4}
];

var startLatLng;
var mapOptions;
var infoWindow;
var geocoder;
var bounds;
var map;

function initMap() {
  geocoder = new google.maps.Geocoder();
  bounds = new google.maps.LatLngBounds();
  map = new google.maps.Map(document.getElementById("map"), mapOptions);
  mapOptions = {
    zoom: 2,
    center: startLatLng,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    navigationControlOptions: {
      style: google.maps.NavigationControlStyle.SMALL
    },
  };
  startLatLng = new google.maps.LatLng(40.914558176141895, -72.35767979999997);
  infoWindow = new google.maps.InfoWindow({
    content: ''
  });

  for (var key in  addressArray) {
    getAddressLatLng(addressArray[key]);
  }
  theNext();
}

function getAddressLatLng(address, sales, next) {

  geocoder.geocode({'address': address}, function (results, status) {

    if (status == google.maps.GeocoderStatus.OK) {

      marker = new google.maps.Marker({
        map: map,
        position: results[0].geometry.location,
      });

      google.maps.event.addListener(marker, 'click', function () {

        var saleTextTitle = '<p class="map-sales-count">Заказов : ' + sales + '</p>';
        var cityname = '<span class="map-city-name">' + address + '</span>';

        infoWindow.setContent(cityname + saleTextTitle);
        infoWindow.open(map, this);
      });

      bounds.extend(results[0].geometry.location);

      markersArray.push(marker);
    } else {
      // === if we were sending the requests to fast, try this one again and increase the delay
      if (status == google.maps.GeocoderStatus.OVER_QUERY_LIMIT) {
        nextAddress--;
        delay++;
      } else {
        var reason = "Code error " + status;
        console.log(reason);
      }

      console.log("Geocode was not successful for the following reason: " + status);
    }
    //map.fitBounds(bounds);
    next();
  });
}

// ======= Global variable to remind us what to do next
var nextAddress = 0;

function theNext() {
  if (nextAddress < addressArray.length) {
    setTimeout('getAddressLatLng("' + addressArray[nextAddress].cityname + '","' + addressArray[nextAddress].city_sales + '",theNext)', delay);
    nextAddress++;
  } else {
    map.fitBounds(bounds);
  }
}


