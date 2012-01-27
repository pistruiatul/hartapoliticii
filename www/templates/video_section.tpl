{* Smarty *}

<h4>Clipuri video recente</h4>

<table>
<tr>
  {section name=v loop=$videos}
  {strip}
    {counter name=v assign=index}
    <td valign="top">
    <a href="javascript:inlinePlay('{$videos[v].player_url}');" title="{$videos[v].title}">
      <img src="{$videos[v].thumb}" border=0 alt="{$videos[v].title}" width="100" />
    </a><br>
    <div class="small"><span>{$videos[v].time|date_format:"%d %b"}</span>, <span class="gray">{$videos[v].duration} sec</span></div>
    <div class="small">
    <a href="javascript:inlinePlay('{$videos[v].player_url}');" title="{$videos[v].title}">
      {$videos[v].title|truncate:35:"...":true}
    </a>
    </div>
    </td>
    
    {if $index%$video_columns==0}
      </tr><tr>
    {/if}
    
  {/strip}
  {/section}
</tr>
</table>