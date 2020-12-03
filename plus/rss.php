<?
require(dirname(__FILE__)."/../include/inc_rss_view.php");
if(empty($tid)) exit();
$tid = ereg_replace("[^0-9]","",$tid);
$rv = new RssView($tid);
$rv->Display();
$rv->Close();
?>