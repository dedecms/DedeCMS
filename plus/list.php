<?php 
require_once(dirname(__FILE__)."/../include/inc_arclist_view.php");
if(!empty($typeid)) $tid = $typeid;
$tid = ereg_replace("[^0-9]","",$tid);
$lv = new ListView($tid);
$lv->Display();
$lv->Close();
?>