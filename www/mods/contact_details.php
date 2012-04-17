<?php

// TODO: move these Romanian names in a localization file
$details_ro = array(
  "website"  => "Site",    // actually, English
  "email"    => "E-mail",  // actually, English
  "phone"    => "Telefon",
  "address"  => "Adresă",
  "facebook" => "Facebook",
	"twitter"  => "Twitter"
);

$t = new Smarty();

$t->assign('id', $person->id);
$t->assign('title', $title);
$t->assign('details', $person->getContactDetails());
$t->assign('details_ro', $details_ro);

$t->display('person_contact_details.tpl');

?>
