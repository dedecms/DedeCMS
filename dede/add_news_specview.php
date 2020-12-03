<?
require_once("config.php");
require_once("inc_makespec.php");
$conn = connectMySql();
$ms = new MakeSpec($ID);
echo $ms->ParMode();
?>