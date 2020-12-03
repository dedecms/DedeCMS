<?php 
require(dirname(__FILE__)."/config.php");
CheckPurview('member_Edit');
if(!isset($_COOKIE['ENV_GOBACK_URL'])) $ENV_GOBACK_URL = "";
else $ENV_GOBACK_URL="member_main.php";
$ID = ereg_replace("[^0-9]","",$ID);
$dsql = new DedeSql(false);
$row=$dsql->GetOne("select  * from #@__member where ID='$ID'");
$rowper=$dsql->GetOne("select  * from #@__member_perinfo where ID='$ID'");
require_once(dirname(__FILE__)."/templets/member_view.htm");

ClearAllLink();
?>