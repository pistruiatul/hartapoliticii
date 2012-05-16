{* Smarty *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="../styles.css?v=2" />
  <script type="text/javascript" src="../js/politica.js"></script>
  {literal}
	<script type="text/javascript">
	/**
	 * Approves or deletes a qualifier associated with a name.
	 */
	function qualify(state, id, name, qualifier) {
		var url = 'qualifier_set.php?' + 
	      'name=' + name +
	      '&qualifier=' + encodeURIComponent(qualifier) +
	      '&state=' + (state ? '1' : '2');
	
		sendPayload_(url, function() {
		  var div = document.getElementById((state ? 'y' : 'n') + id);
	    if (div) {
		    if (state) {
          div.innerHTML = "<span class=brightgreen>da</span>";
          div = document.getElementById('n' + id);
          div.innerHTML = "nu";
        } else {
        	div.innerHTML = "<span class=brightred>nu</span>";
        	div = document.getElementById('y' + id);
          div.innerHTML = "da";
        }
	    }
	  });
	}
	</script>
  {/literal}
</head>
<body> 
<h3>Mențiuni în ziare</h3>
{section name=q loop=$qualifiers}
{strip}
  {counter name=q assign=index}
  {$index}.&nbsp;
  <div class="small" style="display:inline">
    <a id="y{$index}" href="javascript:qualify(true, {$index}, '{$qualifiers[q].name}', '{$qualifiers[q].qualifier|escape:'url'}');">da</a> /&nbsp;
    <a id="n{$index}" href="javascript:qualify(false, {$index}, '{$qualifiers[q].name}', '{$qualifiers[q].qualifier|escape:'url'}');">nu</a>&nbsp;
  </div>
  
  &nbsp;-&nbsp;
  {if $qualifiers[q].idperson > 0}
    <a href="../?cid=9&id={$qualifiers[q].idperson}" target="_blank">
      {$qualifiers[q].name}
    </a>
  {else}
    {$qualifiers[q].name}
  {/if}    
  &nbsp;<span class="small gray">{$qualifiers[q].idperson}</span>
  &nbsp;-&nbsp;  
  {$qualifiers[q].num}
  &nbsp;-&nbsp;
  {$qualifiers[q].qualifier} 
  
  &nbsp;-&nbsp;
  <a href="{$qualifiers[q].link}" target=_blank>
    <img src="../images/popout_icon.gif" border=0>
  </a>
  <br>
{/strip}
{/section}
</body>
</html>