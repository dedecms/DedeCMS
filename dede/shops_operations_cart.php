<?php
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEINC."/datalistcp.class.php");
CheckPurview('shops_Operations');
if(!isset($oid)){
	exit("<a href='javascript:window.close()'>无效操作!</a>");
}
$oid 	= ereg_replace("[^-0-9A-Z]","",$oid);
if(empty($oid)){
	exit("<a href='javascript:window.close()'>无效订单号!</a>");
}

$sql = "SELECT * FROM #@__shops_products WHERE oid='$oid'";

$dlist = new DataListCP();
$dlist->pageSize = 20;
$dlist->SetParameter("oid",$oid);
$dlist->SetTemplate(DEDEADMIN."/templets/shops_operations_cart.htm");
$dlist->SetSource($sql);
$dlist->Display();
$dlist->Close();
?>