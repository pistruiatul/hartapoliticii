<?
include("../_top.php");

$s = mysql_query("select idperson, imgurl ". 
                 "from cdep_2004_deputies as dep ");

while ($r = mysql_fetch_array($s)) {
  if ($r['imgurl']) {
    $imgurl = str_replace('imagini/l2004', 'parlamentari/l2004/mari', 
                          $r['imgurl']);
    $imgurl = "http://www.cdep.ro" . $imgurl;
    echo "curl {$imgurl} -o {$r['idperson']}.jpg\n";
    mysql_query("insert into people_facts(idperson, attribute, value) ".
                "values({$r['idperson']}, 'image', ".
                "'images/people/{$r['idperson']}.jpg')");
  }
}

$s = mysql_query("select idperson, imgurl ". 
                 "from senat_2004_senators as sen ");

while ($r = mysql_fetch_array($s)) {
  if ($r['imgurl']) {
    $imgurl = str_replace('imagini', 'parlamentari',
                          $r['imgurl']);
    $imgurl = "http://www.cdep.ro" . $imgurl;
    echo "curl {$imgurl} -o {$r['idperson']}.jpg\n";
    
    mysql_query("insert into people_facts(idperson, attribute, value) ".
                "values({$r['idperson']}, 'image', ".
                "'images/people/{$r['idperson']}.jpg')");
  }
}


?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<body>
  
  
</body>
</html>
<?
include("../_bottom.php");
?>