<?
require("../dede/inc_makelistcode.php");
$mlc = new MakeListCode();
$id = ereg_replace("[^0123456789]","",$id);
if(!isset($page)) $page=1;
if(!isset($totalrecord)) $totalrecord=0;
$mlc->SetTypeDynamic($id,$page,$totalrecord);
$mlc->Display();
?>