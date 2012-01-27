{* Smarty *}
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<script src="../politica.js"></script>  
{literal}
<script>
  function submitPersonFact(id, type) {
    var value = getInputValue(type);
    if (value.indexOf('http://') >= 0) {
      value = value.substr(7);
    }
    
    var sendUrl = "set_fact.php?id=" + id + 
        "&value=" + value + "&attribute=" + type;
 
    sendPayload_(sendUrl, function() {
      var div = elem('status');
      if (div) {
        div.innerHTML = "Adăugat " + value + ".";
      }
    });
  } 
</script>
{/literal}  
</head>
<body>
<table width="100%">
<td valign="top">
  <a href="?start={$prev}">prev</a> / <a href="?start={$next}">next</a>
  <br><br>

  {foreach from=$people item=person}
    <img src="../{$person.tiny_image}">
    <a href="links.php?start={$start}&id={$person.id}">{$person.display_name}</a>
    <a href="/?cid=9&id={$person.id}" target="_blank">h</a>
    
    <br>
  {foreachelse}
    No people.
  {/foreach}
  
  </td>
  <td valign="top">

    <table>
      <td width="200">
        <b>{$name}</b> 
      </td>
      <td>
        <form action="javascript:submitPersonFact({$id}, 'link/blog');">
          Blog: <input size=20 id="link/blog"> <input type="submit" value="add">
        </form>
      </td>
      <td>
        <form action="javascript:submitPersonFact({$id}, 'link/site');">
          Site: <input size=20 id="link/site"> <input type="submit" value="add">  
        </form>
      </td>
      <td>
        <form action="javascript:submitPersonFact({$id}, 'link/wiki');">
          Wiki: <input size=20 id="link/wiki"> <input type="submit" value="add">  
        </form>
      </td>
    </table>
    <div id="status"></div>
    <p>
    <table width="100%">
    <td>
      <iframe width="100%" height="510" src="http://www.google.ro/search?q={$name}"></iframe>
    </td>
    </table>


</td></table>
</body>
</html>
