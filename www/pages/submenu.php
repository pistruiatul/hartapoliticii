<?php
// All this file does is, assuming there is a $page variable, look for
// the configuration files for subpages and display them in a submenu.

$dir = str_replace("/", "_", $page);

if (is_dir("pages/$dir")) {
  // Read the config file from there.
  include("pages/$dir/config.php");

  // what is the current subpage id?
  $sid = (int)$_GET['sid'] ? (int)$_GET['sid'] : 0;

  if (count($subpages) > 1) {
    echo "<div class=submenu style=\"background: url(images/submenu_$dir.png);".
         "padding-top:13px;text-align:center;background-position:0 -1px\">";
    for ($i = 0; $i < count($subpages); $i++) {
      if ($i == $sid) {
        echo "<a class=selected
                 href=\"?c=$c&cid=$cid&sid=$i\">{$subpages[$i]['link']}</a> ";
      } else {
        echo "<a href=\"?c=$c&cid=$cid&sid=$i\">{$subpages[$i]['link']}</a> ";
      }
      if ($i < count($subpages) - 1) {
        echo " | ";
      }
    }
    echo "</div>";
  }

  if (is_file("pages/$dir/{$subpages[$sid]['page']}")) {
    include("pages/$dir/{$subpages[$sid]['page']}");
  }
}

?>
