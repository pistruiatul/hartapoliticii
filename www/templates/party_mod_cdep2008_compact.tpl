{* Smarty *}
<img src="https://chart.googleapis.com/chart?cht=p&chs=450x110&
chd=t:{$not_voted_votes/100},{$non_party_line_votes/100},{$party_line_votes/100}&
chco=FF0000|FF9900|FFDD00&
chp=-1.57&
chdl=Voturi finale la care {$party_short_name} n-a participat ({$not_voted_votes}) - 
{$not_voted_votes_percent|string_format:"%.1f"}%|
Voturi finale fără linie de partid ({$non_party_line_votes}) - 
{$non_party_line_votes_percent|string_format:"%.1f"}%|
Voturi finale pe linie de partid ({$party_line_votes}) - 
{$party_line_votes_percent|string_format:"%.1f"}%" width=450 height=110>

<table width="100%">
<td valign="top">
  {* ----------------------------------------------------- *}
  {* Most present *}
  <b>Prezența membrilor în parlament</b>
  <table>
    {section name=i loop=$presTop}
    {strip}
      {include file="party_mod_cdep2008_compact_name_percent.tpl"
          person=$presTop[i]
          percent=$presTop[i].percent}
    {/strip}
    {/section}
    <tr><td>...</td></tr>
    {section name=i loop=$presBot}
    {strip}
      {include file="party_mod_cdep2008_compact_name_percent.tpl"
          person=$presBot[i]
          percent=$presBot[i].percent}
    {/strip}
    {/section}
  </table>
  <div class="small green_link">
    <a href="?cid={$see_all_cid}&sid=1&sort=3">vezi lista întreagă...</a>
  </div>
</td>
<td valign="top">
  {* ----------------------------------------------------- *}
  {* Maverick section *}
  <b>Cât de des votează pe linie de partid</b>
  <table>
    {section name=i loop=$presTop}
    {strip}
      {include file="party_mod_cdep2008_compact_name_percent.tpl"
          person=$maverickTop[i]
          percent=$maverickTop[i].anti_maverick}
    {/strip}
    {/section}
    <tr><td>...</td></tr>
    {section name=i loop=$presBot}
    {strip}
      {include file="party_mod_cdep2008_compact_name_percent.tpl"
          person=$maverickBot[i]
          percent=$maverickBot[i].anti_maverick}
    {/strip}
    {/section}
  </table>
  <div class="small green_link">
    <a href="?cid={$see_all_cid}&sid=1&sort=4">vezi lista întreagă...</a>
  </div>
</td>
</table>