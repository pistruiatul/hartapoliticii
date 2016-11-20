<?php

include_once('hp-includes/elections_2016.php');

$t = new Smarty();
$t->caching = 1;

if (!$t->is_cached("mod_results_2016.tpl", $college)) {
  $words = explode(" ", $person->get2016College());
  $t->assign("college_name", $words[1]);
  $t->assign("cam", $words[0]);

  $t->assign("party_name", $person->get2016Party());

  $t->assign("compact", true);
  $t->assign("candidates", getCollegeCandidates($college, "2016"));
}
$t->display("mod_results_2016.tpl", $college);


?>
