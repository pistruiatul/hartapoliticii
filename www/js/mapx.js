var geocoder;
var map;

var openedInfowindow;
var openedMarker;
var openedMarkerData;

var isMapUpdateDisabled = false;
var markers = [];
var center_marker = null;
var zoom_level = 13;

var svsToMarkerHash = {};

var permalinkMarkerSvs;

function codeAddress() {
  var adrs = '';
  var q = document.getElementById('q');
  if (q) {
    adrs = q.value;
    adrs = adrs.replace(/^\s+/, '').replace(/\s+$/, '').replace(/\s+/, ' ');
  }
  if (!adrs)
    return;

  if (openedInfowindow) {
    openedInfowindow.close();
  }

  geocoder.geocode( { 'address': adrs}, function(results, status) {
    if (status == google.maps.GeocoderStatus.OK) {

    //console.info(results);

    var pos = results[0].geometry.location;
    if (pos) {
      if (results[0].types && results[0].types[0] == 'route') {
        map.setZoom(15);
      }
      else {
        map.setZoom(13);
      }
      map.panTo(pos);
      //console.info(results[0]);
      //console.info(map.getBounds().toString());

      if (center_marker) {
        center_marker.setMap(null);
      }
      center_marker = new google.maps.Marker({
        position: pos,
        visible: true,
        draggable: false,
        title: results[0]['formatted_address'],
        map: map
      });
      var icon = new google.maps.MarkerImage("http://www.google.com/mapfiles/arrow.png",
        new google.maps.Size(40, 40),
        new google.maps.Point(0,0),  
        new google.maps.Point(0, 40) 
      );
      center_marker.setIcon(icon);

    }


    } else {
    alert("Geocode was not successful for the following reason: " + status);
    }
  });
}

function addMarker(pos, markerData) {
  //pos['$a'] += (Math.random() > .5 ? 1 : -1) * Math.random()/2000;
  //pos['ab'] += (Math.random() > .5 ? 1 : -1) * Math.random()/2000;

  var marker = new google.maps.Marker({
    position: pos,
    visible: true,
    draggable: true,
    title: markerData['institutie'],
    icon: 'http://cdn1.iconfinder.com/data/icons/splashyIcons/marker_rounded_red.png',
    map: map
  });

  google.maps.event.addListener(marker, 'click', function() {
    openInfoWindow(marker, markerData);
  });

  markers.push(marker);

  // See if this is the marker from the permalink.
  if (markerData['svs'] == permalinkMarkerSvs) {
    openInfoWindow(marker, markerData);
    permalinkMarkerSvs = null;
  }

  if (markerData['is_match']) {
    loadPollingStationDetails(markerData['is_match'].split(','));
  }

  var svs = markerData['svs'].split(',');
  $.each(svs, function(i) {
    svsToMarkerHash[svs[i]] = [marker, markerData];
  })
}

function sendEdit(markerCode) {
  var newLatLng = $('#new_lat_lng').val();
  console.log(markerCode + ' ' + newLatLng);

  var url = "/hooks/update_lat_lng.php?" +
      "markerCode=" + markerCode +
      "&latlng=" + newLatLng;

  console.log(url);
  sendPayload_(url, function(response) {
    $('#new_lat_lng_message').html(response);
  });
}


function openInfoWindow(marker, markerData) {
  if (openedInfowindow) {
    openedInfowindow.close();
  }

  var links = [];
  var svs = markerData['svs'].split(',');

  $.each(svs, function(i){
      //links += '<a href="#" onclick="javascript:load_sv_det(\'' + svs[i]+ '\')">';
      links.push('<b>' + svs[i].replace(/-\d+/, '') + '</b>');
    });

  loadPollingStationDetails(svs);

  var windowHtml = '<div style="width:350px">' +
        '<div class=infowindow_title>' + markerData['institutie'] + '</div>' +
        '<div class=infowindow_addr>' + markerData['adresa'] + '</div>' +
        '<div>Secții de votare: ' + links.join(', ') + '</div></div>';
  if (markerData['can_edit']) {
    windowHtml += '<div><input id=new_lat_lng size=20>' +
        '<input type=button onclick="sendEdit(\'' +
         markerData['svs'] + '\');" value=" update "></div>' +
        '<div id=new_lat_lng_message></div>';
  }

  openedInfowindow = new google.maps.InfoWindow({
    content: windowHtml
  });

  google.maps.event.addListener(openedInfowindow, 'closeclick', function() {
    openedInfowindow = null;
    openedMarker = null;
    openedMarkerData = null;
  });

  openedInfowindow.open(map, marker);
  openedMarker = marker;
  openedMarkerData = markerData;

  updatePermalink();
}


/**
 *
 * @param pollingStations {Array} An array of polling station codes
 *     like ["63-42","64-42"]
 */
function loadPollingStationDetails(pollingStations) {
  $("#sv").html("");

  $.each(pollingStations, function(i) {
    load_sv_det(pollingStations[i], true);
  });
}


function get_data() {
  if (map.zoom < 13) {
    removeMarkersFromMap();
    $('#map_message').html('Caută o adresă anume sau un oraș. ' +
      'Markere nu sunt afișate dacă harta e prea de ansamblu.');
    return;
  }

  var bounds = map.getBounds();
  var params = {
    s: bounds.getSouthWest().lat() - 0.02,
    w: bounds.getSouthWest().lng() - 0.02,
    n: bounds.getNorthEast().lat() + 0.02,
    e: bounds.getNorthEast().lng() + 0.02,
    z: map.zoom
  };

  if (!openedInfowindow) {
    params.q = $('#q').val();
  }

  updatePermalink();

  $.ajax({
    url: '/api/get_svs.php',
    data: params,
    success: function(data) {
      var svs = jQuery.parseJSON(data);

      if (svs.length) {
        removeMarkersFromMap();
      }

      $.each(svs, function(i) {
        var pos = new google.maps.LatLng(
            parseFloat(svs[i]['lat']),
            parseFloat(svs[i]['lon'])
          );
        addMarker(pos, svs[i]);
      });

      $('#map_message').html('Dă click pe un marker pentru a vedea detalii');

    }
  });
}

function updatePermalink() {
  var value = 'http://hartapoliticii.ro/?cid=sectii_votare#' +
      'z=' + map.zoom +
      '&c=' + map.getCenter().lat() + ',' + map.getCenter().lng() +
      '&q=' + $('#q').val();

  if (openedMarkerData) {
    value += '&m=' + openedMarkerData['svs'];
  }

  $("#permalink").val(value);

}


function removeMarkersFromMap() {
  $.each(markers, function(i) {
    if (markers[i] != openedMarker) {
      markers[i].setMap(null);
    }
  });
  markers = [];
  if (openedMarker) markers.push(openedMarker);
}

/**
 * @param sv
 * @param opt_append {Boolean} If set to true it will not erase the console
 *     where we erase things, instead it will just append to it.
 */
function load_sv_det(sv, opt_append) {
  $.ajax({
    url: '/api/get_svs.php',
    data: {
      sv: sv,
      q: $('#q').val()
    },
    success: function(data) {
      var currentHtml = opt_append ? $('#sv').html() : '';

      $('#sv').html(currentHtml + data);
      $('.polling_title').click(function() {
        // Go through all the markers that are on the map and open the
        // infowindow for the one that matches.
        var data = svsToMarkerHash[this.getAttribute('code')];
        openInfoWindow(data[0], data[1]);
      });
    }
  });
}

function init() {
  maybeReadAnchor();

  var latlng = new google.maps.LatLng(map_center[0], map_center[1]);
  var myOptions = {
    zoom: zoom_level,
    center: latlng,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    mapTypeControl: false,
    streetViewControl: false,
    maxZoom: 16,
    minZoom:9
  };
  map = new google.maps.Map(document.getElementById("map_div"), myOptions);

  geocoder = new google.maps.Geocoder();

  google.maps.event.addListener(map, 'center_changed', updateMap);
  google.maps.event.addListenerOnce(map, 'idle', function() {
    get_data();
  });

  google.maps.event.addListener(map, 'idle', function() {
    isMapUpdateDisabled = false;
  });

  $('.polling_title').click(function() {
    console.log('oh hi');
  });

  /*google.maps.event.addListener(map, 'dragend', function() {
    isMapUpdateDisabled = true;
  });
  google.maps.event.addListener(map, 'dragstart', function() {
    isMapUpdateDisabled = true;
  });*/
}

function maybeReadAnchor() {
  // #z=14&c=44.43011140566203,26.09130203631598
  var hash = window.location.hash;
  hash = hash.substr(1, hash.length - 1);
  var parts = hash.split("&");

  console.log(parts);
  for (var i = 0; i < parts.length; i++) {
    var pair = parts[i].split("=");

    if (pair[0] == 'c') {
      map_center = pair[1].split(",");
    }
    if (pair[0] == 'z') {
      zoom_level = Number(pair[1]);
    }
    if (pair[0] == 'm') {
      permalinkMarkerSvs = pair[1];
    }
    if (pair[0] == 'q') {
      $("#q").val(pair[1]);
    }
  }
}

  
function updateMap() {
  isMapUpdateDisabled = true;

  window.setTimeout(function() {
    if (isMapUpdateDisabled) return;

    // make sure we only the data once only
    isMapUpdateDisabled = true;

    get_data();
  }, 600);
}


function loadScript() {

  
  /*var script = document.createElement("script");
  script.type = "text/javascript";
  script.src = "/js/markerclusterer/markerclusterer_compiled.js";
  document.body.appendChild(script);
  */
  script = document.createElement("script");
  script.type = "text/javascript";
  script.src = "http://maps.googleapis.com/maps/api/js?sensor=false&callback=init&language=ro";
  document.body.appendChild(script);  

}


//google.maps.event.addDomListener(window, 'load', initialize);
window.onload = loadScript;
