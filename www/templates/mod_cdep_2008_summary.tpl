{* Smarty *}

<img src="https://chart.googleapis.com/chart?cht=p&chs=250x90&
chd=t:{$chd1},{$chd2},{$chd3}&
chco=22CC22|FF8822|EEEEEE&
chp=-1.57&
chma=0,0,0,0&
chdl=Linie partid|
Rebel|
Absent {$chd3|string_format:"%.1f"}%" width=250 height=90 align="right">

Deputat intre <b>{$dep_time_in}</b> si <b>{$dep_time_out}</b>{$dep_motif}, 
din partea <b>{$dep_party}</b>. 

Prezent la <b>{$dep_percent|string_format:"%.2f"}%</b> din voturile
electronice dintre {$dep_time_in} si {$dep_time_out} ({$dep_possible_votes}).

Mai multe detalii pe 
<a href="http://www.cdep.ro/pls/parlam/structura.mp?idm={$dep_idm}&cam=2&leg=2008">site-ul cdep.ro</a>.

<p>
A votat 
<a href="/?name={$name}&exp=cdep/2008&maverick=1">
  diferit de partid în <b>{$maverick|string_format:"%.2f"}%</b></a>
din voturile finale la care a participat.