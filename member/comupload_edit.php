<?php
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/com_config.php");

setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
$dsql=new DedeSql(false);

$aid = ereg_replace("[^0-9]","",$aid);
$arow = $dsql->GetOne("Select * From #@__uploads where aid='$aid ';");
if($arow['memberid']!=$cfg_ml->M_ID){
	$dsql->Close();
	ShowMsg("你没有修改这个附件的权限！","-1");
	exit();
}
require_once(dirname(__FILE__)."/templets/comupload_edit.htm");

?>