//
// NOTE(vivi): This is obviously a big unsorted pile of all the javascript
// we need. It should be split as some point, as needed.
//
// TODO(vivi): Replace some of these with jQuery methods.


// -----------------------------------------------------
// DOM and NET Utils

var profile = profile || {};

// A namespace for the functions related to declarations.
var declarations = declarations || {};


/**
 * Global initializer for whatever listeners we might need on pages.
 */
$(document).ready(function() {
  // For the my_account section, add a listener to the name element for adding
  // a new position, so that on blur we can look the person up.
  console.log('set up the listener');
  $('#new_position_display_name').focusout(function() {
    console.log('focusout!');
    profile.handleDisplayNameTyped();
  });
});


/* ------------------------------------------------------ */
/* Some util functions */

function elem(id) {
  return document.getElementById(id);
}


function toggleDiv(id) {
  var el = elem(id);
  if (el.style.display == "none") {
    el.style.display = "block";
  } else {
    el.style.display = "none";
  }
}


function clearValue(targetId, origText) {
  var el = elem(targetId);
  if (el && el.value == origText) {
    el.value = '';
  }
}


function sendPayload_(url, opt_callback, opt_method, opt_payload) {
  var method = opt_method || "GET";

  var xmlhttp = null;
  if (window.XMLHttpRequest) {  // code for all new browsers
    xmlhttp = new XMLHttpRequest();
  } else if (window.ActiveXObject) {  // code for IE5 and IE6
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

// end utils


// -----------------------------------------------------
// Europarlamentare


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
    var div = elem('sim_results');
    div.innerHTML = "Simulez alegerile... please wait.";
    setTimeout('updateEuroResults_(\'eurosim.php?'+globalSimParams+'\')', 1000);
  }
}


function updateEuroResults_(url) {
  sendPayload_(url, function(r) {
    var div = elem('sim_results');
    if (div) {
      div.innerHTML = r;
    }
  });
}


// -----------------------------------------------------
// Person page javascript

function togglePhotoSuggestForm() {
  toggleDiv('suggest_photo');
}


function sendPhoto() {
  var url = getInputValue('suggest_photo_input');
  var pid = getInputValue('ps_pid');
  var type = getInputValue('ps_type');

  var sendUrl = "/api/suggest_edit.php?value=" + escape(url) + "&pid=" + pid +
                "&type=" + type;

  sendPayload_(sendUrl, function() {
    var div = elem('suggest_photo');
    if (div) {
      div.innerHTML = "Mulțumesc pentru sugestie. Ea va fi adăugată imediat "+
                      "ce un moderator o va verifica.";
    }
  });
}


function getInputValue(id) {
  var el = elem(id);
  return el ? el.value : '';
}


// -----------------------------------------------------
// Youtube player stuff - for presidential candidate pages.


function onYouTubePlayerReady(playerId) {
  var ytplayer = elem("myytplayer");
  if (ytplayer) {
    ytplayer.playVideo();
  }
}


function getSize() {
  var myWidth = 0, myHeight = 0;
  if( typeof( window.innerWidth ) == 'number' ) {
    //Non-IE
    myWidth = window.innerWidth;
    myHeight = window.innerHeight;
  } else if (document.documentElement &&
      (document.documentElement.clientWidth ||
       document.documentElement.clientHeight)) {
    //IE 6+ in 'standards compliant mode'
    myWidth = document.documentElement.clientWidth;
    myHeight = document.documentElement.clientHeight;
  } else if (document.body &&
      (document.body.clientWidth || document.body.clientHeight)) {
    //IE 4 compatible
    myWidth = document.body.clientWidth;
    myHeight = document.body.clientHeight;
  }
  return {
    width: myWidth,
    height: myHeight
  }
}


function inlinePlay(url) {
  removeInlinePlayer();

  var wrapper = elem("playerwrapper");
  wrapper.style.display = 'block';

  wrapper.innerHTML =
      '<div id="ytcontrols" style="background:#EEEEEE;padding:4px;">' +
      '<a href="javascript:removeInlinePlayer();">Închide</a></div>' +
      '<div id="ytapiplayer"></div>';

  var size = getSize();

  wrapper.style.top = (size.height - 400) + 'px';
  wrapper.style.left = (size.width - 460) + 'px';

  var params = { allowScriptAccess: "always" };
  var atts = { id: "myytplayer" };
  swfobject.embedSWF(url + "&enablejsapi=1&playerapiid=ytplayer", "ytapiplayer",
                 "425", "356", "8", null, null, params, atts);
}

function removeInlinePlayer() {
  var wrapper = elem("playerwrapper");
  wrapper.style.display = 'none';
  wrapper.innerHTML = '<div id="ytapiplayer"></div>';
}


// -----------------------------------------------------
// Functions related to tagging of laws.


// Adding and removing vote tags.
function addVoteTag(room, year, idvote) {
  var tag = getInputValue('input_' + idvote);
  var inverse = getInputValue('select_' + idvote);

  if (tag) {
    // Here's where we make a request to the API.
    var url = '/api/add_vote_tag.php' +
        '?room=' + room +
        '&year=' + year +
        '&idvote=' + idvote +
        '&tag=' + tag +
        '&inverse=' + inverse;

    sendPayload_(url, function(response) {
      toggleDiv('holder_' + idvote);
      elem('input_' + idvote).value = '';
    });
  }
}


function removeVoteTag(room, year, idvote, tag, idtag) {
  // Here's where we make a request to the API.
  var url = '/api/add_vote_tag.php' +
      '?room=' + room +
      '&year=' + year +
      '&idvote=' + idvote +
      '&tag=' + tag +
      '&delete=' + 1;

  sendPayload_(url, function(response) {
	  if (response == 'done') {
	    elem('tag_' + idtag).innerHTML = '';
	  }
    window.console.log('done!? ' + response);
  });
}


/**
 * Handles the click on a '+' on a score-card page. The method will then load
 * the individual votes for this person on this tag id and display them
 * in the according div.
 * @param personId
 * @param room
 * @param year
 * @param tagId
 */
function compassShowDetailsFor(personId, room, year, tagId) {
  var url = '/api/compass_vote_details.php' +
      '?room=' + room +
      '&year=' + year +
      '&tagId=' + tagId +
      '&personId=' + personId;

  sendPayload_(url, function(response) {
    var el = elem('compass_vote_details_' + tagId + '_' + personId);
	  el.innerHTML = response;

    toggleDiv('compass_vote_details_' + tagId + '_' + personId);

    var img = elem('compass_details_link_' + tagId + '_' + personId);
    if (img.src.indexOf('/images/plus.png') > 0) {
      img.src = '/images/minus.png';
    } else {
      img.src = '/images/plus.png';
    }
  });
}


// -----------------------------------------------------
// Functions for code that's in the user's my account page.

/**
 * Handles the submission of the form for adding a person from the admin
 * interface.
 */
profile.addPerson = function() {
  // First of all, find the data on the page.
  var nameAll = elem('person_name_all').value;
  var displayName = elem('person_display_name').value;
  var photoUrl = elem('person_photo_url').value;

  if (nameAll == '' || displayName == '') {
    // The user didn't enter a full name and a compact name, warn.
    alert('N-ai completat numele compact sau numele complet.');
    return;
  }

  // Set the fields to empty values so that if we click again we don't
  // add the person twice.
  elem('person_name_all').value = '';
  elem('person_display_name').value = '';
  elem('person_photo_url').value = '';

  // Now call the server hook to add the person to the db.
  var url = '/hooks/add_new_person.php' +
      '?name_all=' + nameAll +
      '&display_name=' + displayName +
      '&photo_url=' + photoUrl;

  elem('person_add_message').innerHTML =
      'Așteatpă... <img src=/images/activity_indicator.gif>';
  sendPayload_(url, function(response) {
    elem('person_add_message').innerHTML = response;
  });
};

/**
 * The person ID that comes back from the server.
 */
profile.person_id = 0;

/**
 * Handles the user tabbing out of the display name field after typing a
 * person's name. We look that name up, and if it's legit, we store the
 * id of the person so we can submit stuff.
 */
profile.handleDisplayNameTyped = function() {
  var name = $('#new_position_display_name').val();
  var url = '/api/search.php?q=' + name;

  sendPayload_(url, function(response) {
    // The response looks like this:
    // [ {"id":"3393", "name":"Traian B\u0103sescu", ...}]
    var people = eval(response);
    if (people.length == 1) {
      $('#new_position_searched_person').html(
          '<b>' + people[0].name + '</b><br>' +
          people[0].snippet);
      profile.person_id = people[0].id;
    } else {
      $('#new_position_searched_person').html('Ambiguous or no person.');
    }
  });
};


profile.handleSubmitPosition = function() {
  var what = $('#new_position_what').val();
  var title = $('#new_position_title').val();
  var url = $('#new_position_url').val();
  var start_time = $('#new_position_start_time').val();

  if (profile.person_id == 0 || what == '' || title == '' || url == '') {
    elem('new_position_searched_person').innerHTML = 'fill everything!';
    return;
  }

  // Now call the server hook to add the person to the db.
  var hook = '/hooks/add_new_position.php' +
      '?person_id=' + profile.person_id +
      '&what=' + what +
      '&title=' + title +
      '&url=' + url +
      '&start_time=' + start_time;

  elem('new_position_searched_person').innerHTML =
      'Așteatpă... <img src=/images/activity_indicator.gif>';
  sendPayload_(hook, function(response) {
    elem('new_position_searched_person').innerHTML = response;
  });
};


// -----------------------------------------------------
// Functions for declaration utils.

/**
 * A global list of starts and ends of ranges. This is populated server side,
 * the client side is pretty ignorant about it.
 *
 * The ranges in here will not overlap.
 *
 * @type {Object.<Array>}
 */
declarations.globalRanges = {};

/**
 * The user's one personal ranges. These take precedence over the global ones.
 * These can't overlap!
 */
declarations.myRanges = {};


/**
 * A place where we keep the original DOM structures stored, so that when we
 * render them we don't have to worry about removing past formatting.
 */
declarations.originalDoms = {};


declarations.initSelectHandlers = function(loggedIn) {
  // Go through all the divs on the page that are 'select' enabled and install
  // a select handler on all of them.
  $('.declaration').each(function(index, element) {
    var id = element.getAttribute('id');
    declarations.originalDoms[id] = element.innerHTML;
  });

  // Keep a model in memory so that I can have the original text with tags and
  // all of that so I can mark selects on the tagged text.
  $(".declaration").mouseup(function(foo) {
    if (!loggedIn) return;

    // Wrap this in a timeout so that we let the browser deselect the text
    // first, and only then run this method for when the user clicks to
    // deselect a text.
    setTimeout(function() {
      var selection = declarations.getCurrentSelection();

      var startNode = selection.getRangeAt(0).startContainer;
      var startWordId = declarations.getWordTokenIdBefore(startNode);
      var startDeclarationId = declarations.getDeclarationIdFor(startNode);

      var endNode = selection.getRangeAt(0).endContainer;
      var endWordId = declarations.getWordTokenIdBefore(endNode);
      var endDeclarationId = declarations.getDeclarationIdFor(endNode);

      var selectedText = $.trim(declarations.getSelectedText());
      if (selectedText == '') {
        // See if the startWordId belongs to any of MY ranges.
        var range = declarations.getRangeForWordId(
            declarations.myRanges['declaration-' + startDeclarationId],
            startWordId);
        if (!range) {
          return;
        }

        if (confirm('ATENȚIE!!!\n\n' +
            'Ești sigur că vrei să ștergi acest highlight?')) {
          // Get the proper declaration ids from this.
          declarations.recordHighlight(startDeclarationId, range.start,
                                       range.end, selectedText, 'delete');
          // Basically delete the range.
          range.start = -1;
          range.end = -1;

          declarations.refreshDeclaration(startDeclarationId);
        }
        return;
      }

      // We selected some pretty random stuff, so we just return.
      if (startWordId == -1 || endWordId == -1 ||
          startDeclarationId != endDeclarationId) {
        return;
      }

      if (confirm('Vrei să marchezi textul ăsta ca important?')) {
        if (declarations.addRangeToMarkedPassages(startDeclarationId,
                startWordId, endWordId, true)) {
          selection.collapse();
          declarations.refreshDeclaration(startDeclarationId);

          // Store this on the server.
          declarations.recordHighlight(startDeclarationId, startWordId,
                                       endWordId, selectedText, 'add');
        }
      }
    }, 0);
  });
};


/**
 * Records the declaration once we know everything is good to go.
 * @param declarationId
 * @param startWord
 * @param endWord
 * @param selectedText
 */
declarations.recordHighlight = function(declarationId, startWord, endWord,
    selectedText, action) {
  var url = '/hooks/highlight.php?' +
      'action=' + action +
      '&declaration_id=' + declarationId +
      '&start_word=' + startWord +
      '&end_word=' + endWord +
      '&content=' + selectedText;

  sendPayload_(url, function(response) {
    console.log(response);
  });
};


/**
 * Checks whether a particular word id is in the myRanges of this declaration.
 *
 * @param {Array} ranges The array of ranges.
 * @param wordId
 */
declarations.getRangeForWordId = function(ranges, wordId) {
  if (!ranges) return null;

  // First check if there are overlapping ranges.
  for (var i = 0; i < ranges.length; i++) {
    var range = ranges[i];
    if (wordId >= range.start && wordId <= range.end) return range;
  }
  return null;
};


/**
 * Adds a certain range to a declaration id as being a marked important range.
 *
 * TODO(vivi): This needs to become more sophisticated and overlap user's
 * ranges with ranges from the server.
 *
 * @param declarationId
 * @param start
 * @param end
 *
 * @return {Boolean} True if it was successful, false if it wasn't. We won't
 *     add a range that's overlapping.
 */
declarations.addRangeToMarkedPassages = function(declarationId, start, end) {
  var ranges = declarations.myRanges['declaration-' + declarationId] || [];
  declarations.myRanges['declaration-' + declarationId] = ranges;

  // First check if there are overlapping ranges.
  for (var i = 0; i < ranges.length; i++) {
    var range = ranges[i];
    if (start >= range.start && start <= range.end) return false;
    if (end >= range.start && end <= range.end) return false;
    if (start <= range.start && end >= range.end) return false;
  }

  // Now just add our own range into the mix.
  var index = 0;
  while (index < ranges.length && ranges[index].end > start) {
    index++;
  }
  ranges.splice(index, 0, {
    'start': start,
    'end': end
  });

  return true;
};


declarations.refreshAllDeclarations = function() {
  $(".declaration").each(function(index, elem) {
    var domId = elem.getAttribute('id');
    var declarationId = parseInt(domId.match(/declaration-(\d+)/)[1]);
    declarations.refreshDeclaration(declarationId);
  });
};


/**
 * Refreshes a certain declaration to underline the right passages.
 *
 * @param declarationId
 */
declarations.refreshDeclaration = function(declarationId) {
  var domId = 'declaration-' + declarationId;
  $('#' + domId).get(0).innerHTML = declarations.originalDoms[domId];

  var ranges = declarations.mergeRanges(
      declarations.myRanges[domId] || [],
      declarations.globalRanges[domId] || []);

  for (var i = 0; i < ranges.length; i++) {
    var range = ranges[i];
    declarations.underlineRange(declarationId, range.start, range.end,
                                range.type);
  }
};


/**
 * Given two series of ranges, merge them. The myRanges will ALWAYS take
 * priority and overwrite the global ranges.
 *
 * @param {Array} myRanges
 * @param {Array} globalRanges
 */
declarations.mergeRanges = function(myRanges, globalRanges) {
  var maxA = myRanges.length == 0 ? 0 : myRanges[myRanges.length - 1].end;
  var maxB = globalRanges.length == 0 ?
      0 : globalRanges[globalRanges.length - 1].end;
  var maxIndex = Math.max(maxA, maxB);

  // We can afford looping through all the indexes because usually there are
  // not that many words in each declaration. Hence we avoid the serious
  // complications of merging ranges with all their stupid edge cases.
  // NOTE: This is really fairly inefficient.
  var colors = [];
  for (var i = 0; i <= maxIndex; i++) {
    colors[i] = 0;
  }
  for (var i = 0; i < globalRanges.length; i++) {
    for (var j = globalRanges[i].start; j <= globalRanges[i].end; j++) {
      colors[j] = 2;
    }
  }
  for (var i = 0; i < myRanges.length; i++) {
    for (var j = myRanges[i].start; j <= myRanges[i].end; j++) {
      colors[j] = 1;
    }
  }

  // Push a zero at the end of the colors array so that I simplify things.
  colors.push(0);

  // Now generate the ranges.
  var state = 0;
  var start = 0;
  var ranges = [];

  console.log(colors);
  for (var i = 0; i < colors.length; i++) {
    colors[i] = colors[i] || 0;
    if (colors[i] != state) {
      // wrap up the previous range.
      if (state != 0) {
        // I just ended a range of 1 or 2.
        var newRange = {
          'start': start,
          'end': i - 1,
          'type': state
        };
        ranges.push(newRange);
      }
      start = i;
      state = colors[i];
    }
  }

  return ranges;
};


/**
 * Compares two ranges. Returns -1 if a < b, 0 for equal, 1 for a > b
 * @param a
 * @param b
 */
declarations.compareRanges = function(a, b) {
  if (a.end < b.start) {
    return -1;
  }
  if (a.start > b.end) {
    return 1;
  }

};


/**
 * Marks a range as important, meaning that it underlines it in the text.
 *
 * TODO(vivi): Figure out how to highlight overlapping passages! Maybe I should
 * always re-render the entire text instead of doing it incrementally, just
 * hold in an array the beginnings and ends of passages sorted by start point
 * and then go over and render.
 *
 * NOTE: This method needs to be refactored, but not by much, what's in here
 * now is still useful.
 *
 * @param declarationId
 */
declarations.underlineRange = function(declarationId, startId, endId, type) {
  var declaration = $('#declaration-' + declarationId).get(0);

  var start = $('#declaration-' + declarationId +
      ' > #word-' + startId).get(0);
  var end = $('#declaration-' + declarationId +
      ' > #word-' + endId).get(0);

  console.log('Trying to underline ' + startId + ' ' + endId + ' ' + type);

  // Now I need to extract all the nodes that are under declaration that are
  // between start and end, append them to a <span underline> node and then
  // insert that span node in the right place.
  var newNode = $('<span class="text_highlight_' + type + '"></div>');
  var state = 0;
  var i = 0;
  var beforeStartNode = null;

  while (i < declaration.childNodes.length) {
    if (declaration.childNodes[i] == start) {
      state = 1;
      beforeStartNode = declaration.childNodes[i - 1];
    }

    if (state == 1) {
      $(declaration.childNodes[i]).appendTo(newNode);
      if (start == end) {
        $(newNode).insertAfter(beforeStartNode);
        return;
      }
    }

    // First the exit condition.
    if (declaration.childNodes[i] == end) {
      state = 0;

      // Now append the new div into the declaration and then just exit here.
      $(declaration.childNodes[i]).appendTo(newNode);
      $(newNode).insertAfter(beforeStartNode);
      return;
    }

    if (state == 0) {
      i++;
    }
  }
};


/**
 * Given a node, returns the declaration id that's holding it.
 *
 * @param {Node} node A node that probably belongs into one of the snippets
 *     on the page.
 * @return {Number} The id of the declaration that holds the node that is
 *     passed in as a parameter.
 */
declarations.getDeclarationIdFor = function(node) {
  // Walk the DOM up until we find a node that says 'declaration-'
  while (!node.getAttribute ||
      !node.getAttribute('id') ||
      node.getAttribute('id').indexOf('declaration-') != 0) {
    node = node.parentNode;
    if (!node) return -1;
  }

  return parseInt(node.getAttribute('id').match(/declaration-(\d+)/)[1]);
};


/**
 * Returns the word's token id that this node represents or
 * @param {Node} wordNode A node that probably belongs into one of the snippets
 *     on the page.
 * @return {Number} The id of the declaration that holds the node that is
 *     passed in as a parameter.
 */
declarations.getWordTokenIdBefore = function(wordNode) {
  var node = wordNode;
  // First, see if this is part of a node that holds a word id or a declaration
  // id.
  while (!node.getAttribute ||
      !node.getAttribute('id') ||
      (node.getAttribute('id').indexOf('word-') == -1 &&
       node.getAttribute('id').indexOf('declaration-') == -1)) {
    node = node.parentNode;
    if (!node) return -1;
  }

  var nodeId = node.getAttribute('id');
  // If the beginning of the selection was part of a marked word, just return
  // that.
  if (/word-(\d+)/.test(nodeId)) {
    return parseInt(nodeId.match(/word-(\d+)/)[1]);
  }

  // At this point we know that wordNode is a text node in between words, so we
  // just iterate all the children of the declaration node.
  for (var i = 0; i < node.childNodes.length; i++) {
    if (node.childNodes[i] == wordNode) {
      nodeId = node.childNodes[i - 1].getAttribute('id');
      return parseInt(nodeId.match(/word-(\d+)/)[1]);
    }
  }
  return -1;
};


declarations.getSelectedText = function() {
  var t = '';
  if(window.getSelection) {
    t = window.getSelection();
  } else if(document.getSelection) {
    t = document.getSelection();
  } else if(document.selection) {
    t = document.selection.createRange().text;
  }
  return t;
};


/**
 * Returns the selection object. This object will be used to get the next or
 * previous dom elements at the next one so that we can figure out what the
 * user has selected.
 *
 * @return {Selection}
 */
declarations.getCurrentSelection = function() {
  if(window.getSelection) {
    return window.getSelection();
  } else if(document.getSelection) {
    return document.getSelection();
  } else if(document.selection) {
    return document.selection;
  }
  return null;
};
