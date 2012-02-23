
function editField(id) {
  toggleDiv('content_' + id);
  toggleDiv('edit_' + id);
}


function updateContent(id) {
  var content = document.getElementById('text_' + id);
  payload = 'details=' + content.value;
  
  sendPayload_('api/wiki_update.php?id=' + id, function(data) {
    var status = document.getElementById('status_' + id);
	    
	if (data == "ok") {
      status.innerHTML = 'updated at ' + (new Date());
	} else {
      status.innerHTML = 'error saving ' + data;
	}
  }, "POST", payload);
}


function toggleDiv(id) {
  var el = document.getElementById(id);
  if (el.style.display == "none") {
    el.style.display = "block";
  } else {
    el.style.display = "none";
  }
}


function sendPayload_(url, opt_callback, opt_method, opt_payload) {
  var method = opt_method || "GET";
	
  var xmlhttp = null;
  if (window.XMLHttpRequest) {// code for all new browsers
    xmlhttp = new XMLHttpRequest();
  } else if (window.ActiveXObject) {// code for IE5 and IE6
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
  }
  
  if (xmlhttp != null) {
    xmlhttp.onreadystatechange = onPayloadResponse_(xmlhttp, opt_callback);
    xmlhttp.open(method, url, true);
	if (opt_method == "POST") {
	  xmlhttp.setRequestHeader("Content-type", 
			                   "application/x-www-form-urlencoded");
	}
    
    xmlhttp.send(opt_payload);
  }
}


function onPayloadResponse_(xmlhttp, opt_callback, opt_err) {
  return function() {
    if (xmlhttp.readyState == 4) {// 4 = "loaded"
      if (xmlhttp.status == 200) {// 200 = OK
        if (opt_callback) {
          opt_callback(xmlhttp.responseText);
        }
      } else {
        if (opt_err) {
          opt_err(xmlhttp);
        }
      }
    }
  }
}


function loadHandler() {
}


var parts = document.location.href.split("?");
var globalSimParams = parts.length == 2 ? parts[1] : '';

function getSimResults(values) {
  // make up an URL with the right values added in GET
  var arr = values.split(",");
  var newGlobalSimParams = 'p1=' + arr[0] +
    '&p2=' + arr[1] +
    '&p14=' + arr[2] +
    '&p39=' + arr[3] +
    '&p7=' + arr[4] +
    '&p6=' + arr[5] +
    '&p40=' + arr[6] +
    '&pb=' + arr[7] +
    '&pa=' + arr[8] + 
    '&cid=10&sid=2';
  
  if (globalSimParams != newGlobalSimParams) {
    globalSimParams = newGlobalSimParams;
    var div = document.getElementById('sim_results');
    div.innerHTML = "Simulez alegerile... please wait.";
    setTimeout('updateEuroResults_(\'eurosim.php?'+globalSimParams+'\')', 1000);
  }
}


function updateEuroResults_(url) {
  sendPayload_(url, function(r) {
    var div = document.getElementById('sim_results');
    if (div) {
      div.innerHTML = r;
    }
  });
}


function clearValue(targetId, origText) {
  var el = document.getElementById(targetId);
  if (el && el.value == origText) {
    el.value = '';
  }
}


function togglePhotoSuggestForm() {
  toggleDiv('suggest_photo');
}


function sendPhoto() {
  var url = getInputValue('suggest_photo_input');
  var pid = getInputValue('ps_pid');
  var type = getInputValue('ps_type');
  
  var sendUrl = "?cid=suggest&value=" + escape(url) + "&pid=" + pid +
                "&type=" + type;

  sendPayload_(sendUrl, function() {
    var div = document.getElementById('suggest_photo');
    if (div) {
      div.innerHTML = "Mulțumesc pentru sugestie. Ea va fi adăugată imediat " +
                      "ce un moderator o va verifica.";
    }
  });
}


function getInputValue(id) {
  var el = document.getElementById(id);
  return el ? el.value : '';
}
