
<div class="module">
<div class="moduletitle">Câteva căutări pe web după nume</div>
<div class="modulecontent">
<style type="text/css">

 #searchcontrol .gsc-control { width : 680px; }

 </style>
 <script src="http://www.google.com/jsapi" type="text/javascript"></script>
 <script type="text/javascript">
 //<![CDATA[
 google.load('search', '1.0');

 function OnLoad() {
   // Create a search control
   var searchControl = new google.search.SearchControl();
   
   var webSearcher = new google.search.WebSearch()
   webSearcher.setRestriction(google.search.Search.RESTRICT_EXTENDED_ARGS,
                              { "lr" : "lang_ro", "filter" : "0"});
   // Add in a full set of searchers
   searchControl.addSearcher(webSearcher);
   searchControl.addSearcher(new google.search.VideoSearch());
   
   var newsSearcher = new google.search.NewsSearch()
    newsSearcher.setRestriction(google.search.Search.RESTRICT_EXTENDED_ARGS,
                               { "lr" : "lang_ro", "filter" : "0"});
   
   searchControl.addSearcher(new google.search.NewsSearch());
   searchControl.addSearcher(new google.search.ImageSearch());

   // tell the searcher to draw itself and tell it where to attach
   searchControl.draw(document.getElementById("searchcontrol"));

   // execute an inital search
   searchControl.execute("<? echo $title ?>");
 }
 google.setOnLoadCallback(OnLoad, true);

 //]]>
 </script>
 
<div id="searchcontrol">Loading</div>
</div>