var geocoder;
var map, w;
var isMapUpdateDisabled = false;
var isMarkerClicked = false;
var markers = [];
var center_marker = null;


function codeAddress() {
	var adrs = '';
	var q = document.getElementById('q');
	if (q) {
		adrs = q.value;
		adrs = adrs.replace(/^\s+/, '').replace(/\s+$/, '').replace(/\s+/, ' ');
	}
	if (!adrs)
		return;
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

function addMarker(pos, info) {
	//pos['$a'] += (Math.random() > .5 ? 1 : -1) * Math.random()/2000;
	//pos['ab'] += (Math.random() > .5 ? 1 : -1) * Math.random()/2000;

	var marker = new google.maps.Marker({
		position: pos,
		visible: true,
		draggable: true,
		title: info['institutie'],
		map: map
	});

	google.maps.event.addListener(marker, 'click', function() {
		isMarkerClicked = true;
		if (w)
			w.close();
		w = new google.maps.InfoWindow({
			content: '<h3>' + info['institutie'] + '</h3>'
				+ '<div><em> ' + info['adresa'] + '</em></div>'
				+ '<div>Secții de votare: ' + info['svs'] + '</div>'
		});

		google.maps.event.addListener(w, 'closeclick', function() {
			isMarkerClicked = false;
		});
		w.open(map,marker);
	});

	markers.push(marker);
}

function get_data() {
	if (map.zoom < 11)
		return;

	var bounds = map.getBounds();
	var params = {
			s: bounds.getSouthWest().lat(),
			w: bounds.getSouthWest().lng(),
			n: bounds.getNorthEast().lat(),
			e: bounds.getNorthEast().lng(),
			z: map.zoom
		};

	$.ajax({
		url: '/api/get_svs.php',
		data: params,
		success: function(data) {
			var svs = jQuery.parseJSON(data);

			if (svs.length) {
				$.each(markers, function(i) {
					markers[i].setMap(null);
				});
				markers = [];
			}

			$.each(svs, function(i) {
					var pos = new google.maps.LatLng(
							parseFloat(svs[i]['lat']), 
							parseFloat(svs[i]['lon']) 
						);
					addMarker(pos, svs[i]);
				});

		}
	});
}

function init() {
	var latlng = new google.maps.LatLng(map_center[0], map_center[1]);
	var myOptions = { zoom: 10, center: latlng, mapTypeId: google.maps.MapTypeId.ROADMAP, maxZoom: 16, minZoom:9};
	map = new google.maps.Map(document.getElementById("map_div"), myOptions);

	geocoder = new google.maps.Geocoder();

	google.maps.event.addListener(map, 'center_changed', function() {
		isMapUpdateDisabled = true;
	    window.setTimeout(function() {
		  if (isMapUpdateDisabled || isMarkerClicked)
			  return;
		  // make sure we only the data once only
		  isMapUpdateDisabled = true;
		  isMarkerClicked = false;

		  get_data();
	    }, 600);

	  });
	google.maps.event.addListener(map, 'idle', function() {
		isMapUpdateDisabled = false;
	});

	/*google.maps.event.addListener(map, 'dragend', function() {
		isMapUpdateDisabled = true;
	});
	google.maps.event.addListener(map, 'dragstart', function() {
		isMapUpdateDisabled = true;
	});*/

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
