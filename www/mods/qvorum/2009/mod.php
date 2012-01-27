<?
// Prints all the stuff that a guy did that was in the 2004-2008 senate.
// We know that the person we are talking about is $person.

$sql =
  "SELECT `text`, name " .
  "FROM euro_parliament_2007_qvorum " .
  "WHERE idperson = $person->id ";

$s = mysql_query($sql);
$r = mysql_fetch_array($s);

$text = $r['text'];
$text = preg_replace("/o contribuţie (\S+)/", 
                     "<b>o contribuție $1</b>", $text);

$tok = "Pe baza acestor observa";
$parts = split($tok, $text);

echo "<blockquote>
     <div id=raport_qvorum style=\"display:none\">
     {$parts[0]}
     </div>
     $tok{$parts[1]} 
     (<a class=small href=\"javascript:toggleDiv('raport_qvorum')\">
     citește întreg textul despre {$person->displayName}... 
     </a>)</blockquote>";

echo "<div class=>Acesta este concluzia Raportului Qvorum asupra ".
     "activitatii individuale a europarlamentarilor (2008-2009). ".
     "Mai multe detalii despre acest studiu <a href=\"http://qvorum.ro/".
     "ViewAnalyse.aspx?analyseID=13\">pe site-ul Qvorum</a>."


?>