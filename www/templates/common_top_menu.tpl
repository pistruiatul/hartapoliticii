{* Smarty *}

<table width="970">
  <td width="250">
    <a href="/">
      <img  itemprop="image" src="images/top_title.png" border=0>
    </a>
  </td>

  <td width="300">
  {* The search form *}
  <form action="" method="get">
    <input type="hidden" name="cid" value="search" />
    <input type="text" size="18" name="q" id="search_form" value="{$escaped_query}" id="q" />
    <input type="submit" value="Caută" id="cauta" />
  </form>
  <div class="small gray">
    Exemplu: "Basescu", "d1 prahova", etc
  </div>
  </td>

  <td align="right" valign="top">
    {include file="login_bar.tpl"}
    <div class="title">{$title}</div>
  </td>
</table>

<table class=menu width=970>
  <td>
    Camera Deputaților
    <div class=submenu>
      <a href="{$site_path}?cid=1&room=camera_deputatilor" class="{if $cid==1}black_link{/if}">
        2004-2008
      </a> |
      <a href="{$site_path}?cid=11&room=camera_deputatilor" class="{if $cid==11}black_link{/if}">
        2008-2012
      </a> |
      <a href="{$site_path}?cid=21&room=camera_deputatilor" class="new_link{if $cid==21}_sel{/if}">
        Alegeri 2012
      </a>
    </div>
  </td>

  <td>
    Senat
    <div class=submenu>
      <a href="{$site_path}?cid=3&room=senat" class="{if $cid==3}black_link{/if}">
        2004-2008
      </a> |
      <a href="{$site_path}?cid=12&room=senat" class="{if $cid==12}black_link{/if}">
        2008-2012
      </a> |
      <a href="{$site_path}?cid=22&room=senat" class="new_link{if $cid==22}_sel{/if}">
        Alegeri 2012
      </a>
      </div>
  </td>

  <td>
    Euro
    <div class=submenu>
      <a href="{$site_path}?c=alegeri+europarlamentare+2009&cid=10" class="{if $cid==10}black_link{/if}">
        Alegeri 2009
      </a></div>
  </td>

 <td>
    Prezidențiale
    <div class=submenu>
      <a href="{$site_path}?c=alegeri+prezidentiale+2009&cid=13" class="{if $cid==13}black_link{/if}">
        Alegeri 2009
      </a></div>
  </td>

  <td>
    Utile
    <div class=submenu>
      <a href="{$site_path}?cid=14" class="{if $cid==14}black_link{/if}">Presă</a> |
      <a href="{$site_path}?cid=16" class="{if !$cid}black_link{/if}">Actualizări</a>

  </td>
</table>
