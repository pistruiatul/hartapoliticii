{* Smarty *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="../styles.css?v=2" />
  <script type="text/javascript" src="../politica.js"></script>
  <script type="text/javascript" src="../js/swfobject/swfobject.js"></script>
  {literal}
  <script type="text/javascript">
  /**
   * Approves or deletes a qualifier associated with a name.
   */
  function approve(id, approve) {
    var url = 'approve.php?table=yt_videos&approve=' + approve + '&id=' + id;  
    sendPayload_(url, function() {
      var div = document.getElementById((approve == 1 ? 'y' : 'n') + id);
      if (div) {
        if (approve == 1) {
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

<div id="playerwrapper" style="position:fixed;">
</div>

<h3>Clipuri recente de pe YouTube</h3>
{section name=v loop=$videos}
{strip}
  {counter name=v assign=index}
  {$index}.&nbsp;
  <div class="small" style="display:inline">
    <a id="y{$videos[v].id}" href="javascript:approve({$videos[v].id}, 1);">da</a> /&nbsp;
    <a id="n{$videos[v].id}" href="javascript:approve({$videos[v].id}, 2);">nu</a>&nbsp;
  </div>

  &nbsp;-&nbsp;
  <a href="../?cid=9&id={$videos[v].idperson}" target="_blank">
    {$videos[v].name}
  </a>
  &nbsp;<span class="small gray">{$videos[v].idperson}</span>
  &nbsp;-&nbsp;
  <a href="{$videos[v].watch_url}">
    <img src="{$videos[v].thumb}" border=0 />
  </a>
  &nbsp;-&nbsp;
  {$videos[v].duration} sec, {$videos[v].time|date_format:"%d %b %Y, %H:%M"}
  &nbsp;-&nbsp;
  <a href="javascript:inlinePlay('{$videos[v].player_url}');">
    {$videos[v].title}
  </a>
  
  <br>
{/strip}
{/section}
</body>
</html>