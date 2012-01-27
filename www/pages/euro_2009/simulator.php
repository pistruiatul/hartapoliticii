
<table width=970 cellspacing=15>
  <tr><td valign=top width=200 colspan=2>

    <b>Simulator</b> - introdu procentele estimate și vezi cine iese europarlamentar
</td>
<tr><td valign="top">
<?
$v = getSimulationSystemValuesFromGet();
if ($v != null) {
  $vstr = "{$v['p1']},{$v['p2']},{$v['p14']},{$v['p39']},".
          "{$v['p7']},{$v['p6']},{$v['p40']},{$v['pb']},{$v['pa']}";
}
$VOT_PRESENCE = $_GET["vot"] && is_numeric($_GET["vot"]) ? 
                $_GET["vot"] : "30.0";
?>

<script src="f/AC_OETags.js" language="javascript"></script>
<script language="JavaScript" type="text/javascript">
<!--
// -----------------------------------------------------------------------------
// Globals
// Major version of Flash required
var requiredMajorVersion = 9;
// Minor version of Flash required
var requiredMinorVersion = 0;
// Minor version of Flash required
var requiredRevision = 124;
// -----------------------------------------------------------------------------
// -->
</script>

<script language="JavaScript" type="text/javascript">
<!--
// Version check for the Flash Player that has the ability to start Player Product Install (6.0r65)
var hasProductInstall = DetectFlashVer(6, 0, 65);

// Version check based upon the values defined in globals
var hasRequestedVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);

if ( hasProductInstall && !hasRequestedVersion ) {
	// DO NOT MODIFY THE FOLLOWING FOUR LINES
	// Location visited after installation is complete if installation is required
	var MMPlayerType = (isIE == true) ? "ActiveX" : "PlugIn";
	var MMredirectURL = window.location;
    document.title = document.title.slice(0, 47) + " - Flash Player Installation";
    var MMdoctitle = document.title;

	AC_FL_RunContent(
		"src", "f/playerProductInstall",
		"FlashVars", "MMredirectURL="+MMredirectURL+'&MMplayerType='+MMPlayerType+'&MMdoctitle='+MMdoctitle+"",
		"width", "436",
		"height", "286",
		"align", "middle",
		"id", "sliders",
		"quality", "high",
		"bgcolor", "#FFFFFF",
		"name", "sliders",
		"allowScriptAccess","sameDomain",
		"type", "application/x-shockwave-flash",
		"pluginspage", "http://www.adobe.com/go/getflashplayer"
	);
} else if (hasRequestedVersion) {
	// if we've detected an acceptable version
	// embed the Flash Content SWF when all tests are passed
	AC_FL_RunContent(
			"src", "f/sliders",
			"width", "436",
			"height", "286",
			"align", "middle",
			"id", "sliders",
			"quality", "high",
			"bgcolor", "#FFFFFF",
			"name", "sliders",
			"allowScriptAccess","sameDomain",
			"type", "application/x-shockwave-flash",
			"pluginspage", "http://www.adobe.com/go/getflashplayer",
			"flashvars", "v=<? echo $vstr ?>" 
	);
  } else {  // flash is too old or we can't detect the plugin
    var alternateContent = 'Alternate HTML content should be placed here. '
  	+ 'This content requires the Adobe Flash Player. '
   	+ '<a href=http://www.adobe.com/go/getflash/>Get Flash</a>';
    document.write(alternateContent);  // insert non-flash content
  }
// -->
</script>
<noscript>
  	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
			id="sliders" width="436" height="286"
			codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab">
			<param name="movie" value="f/sliders.swf" />
			<param name="quality" value="high" />
			<param name="bgcolor" value="#869ca7" />
			<param name="allowScriptAccess" value="sameDomain" />
			<embed src="f/sliders.swf" quality="high" bgcolor="#FFFFFF"
				width="436" height="286" name="sliders" align="middle"
				play="true"
				loop="false"
				quality="high"
				allowScriptAccess="sameDomain"
				type="application/x-shockwave-flash"
				pluginspage="http://www.adobe.com/go/getflashplayer">
			</embed>
	</object>
</noscript>
<script language="JavaScript" type="text/javascript">
  function update(v) {
    //alert(v);
    document.location.hash = '' + v;
    getSimResults(v);
  }

  function loadHandler() {
    var url = document.location.href.split("#");
    if (url.length == 2) {
      var values = url[1].split(",");
      if (values.length == 9) {
        for (var i = 0; i < values.length; i++) {
          values[i] = Number(values[i]);
        }
        getSimResults(values.join(","));

        var swf = document.getElementById('sliders');
        setTimeout("document.getElementById('sliders').setValues('" + values.join(',') +"')", 3000);
      }
    }
  }
  
  function linkToThisPage() {
    document.location = "?" + globalSimParams;
  }
  
</script>
<div style="text-align:right">
<a class=small href="javascript:linkToThisPage()">Link to this page</a>
</div>
<?
/*
?>
<form action="" method=GET>
  <input type=hidden name=cid value=10>
  <input type=hidden name=sid value="<? echo $sid ?>">
  <table width=200>
    <tr>
      <td align=right>PNL: 
        <input type=text name=p1 value="<? echo $v['p1']?>" size=5>%</td>
    <tr>  
      <td align=right>PD-L: 
        <input type=text name=p2 value="<? echo $v['p2']?>"size=5>%</td>
    <tr>
      <td align=right>PSD+PC: 
        <input type=text name=p14 value="<? echo $v['p14']?>" size=5>%</td>
    <tr>
      <td align=right>PNTCD: 
        <input type=text name=p39 value="<? echo $v['p39']?>" size=5>%</td>
    <tr>
      <td align=right>UDMR: 
        <input type=text name=p7 value="<? echo $v['p7']?>" size=5>%</td>
    <tr>
      <td align=right>PRM: 
        <input type=text name=p6 value="<? echo $v['p6']?>" size=5>%</td>
    <tr>
      <td align=right>E.Băsescu: 
        <input type=text name=pb value="<? echo $v['pb']?>" size=5>%</td>
    <tr>
      <td align=right>P.Abraham: 
        <input type=text name=pa value="<? echo $v['pa']?>" size=5>%</td>
    <tr>
      <td align=right>Prezență la vot:
          <input type=text name=vot value="<? echo $VOT_PRESENCE?>" size=4>%</td>
      </tr>
      <td align=right><input type=submit name=Submit value="Simulează!"></td>
    </tr></table>
</form>
<?
*/
?>
  </td><td valign=top>
    <div id="sim_results">
  <?
  if (!maybeDisplaySimulationResults()) {
    ?>
    Să zicem că vrei să știi în ziua alegerilor cine exact o să fie 
    europarlamentar. 
    
    <p>Tot ceea ce trebuie să faci este să introduci
    aici procentele estimate de presă și vei avea o idee despre cine
    anume va fi europarlamentar și cine nu.<p>
    
    <a href="?cid=10&p1=18.64&p2=33.1&p14=33.6&p39=0.98&vot=30.0&p7=6.2&p6=5.6&pb=0.98&pa=0.98&sid=2">Click aici</a> pentru a rula simulatorul de alegeri cu procentele de la
    parlamentarele din Noiembrie.
    <?
  };
  ?>
  </div>
  </td> 
</table>
