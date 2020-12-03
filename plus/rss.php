<?php
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(DEDEINC."/arc.rssview.class.php");

$tid = isset($tid) && is_numeric($tid) ? $tid : 0;
if($tid==0) die(" Request Error! ");

$rv = new RssView($tid);
$rv->Display();
?>