<?
include ('_top.php');
include('hp-includes/europarlament_functions.php');
include('hp-includes/people_lib.php');
include('functions.php');

$v = getSimulationSystemValuesFromGet();
$VOT_PRESENCE = $_GET["vot"] && is_numeric($_GET["vot"]) ?
                $_GET["vot"] : "30.0";

maybeDisplaySimulationResults();

include('_bottom.php');
?>