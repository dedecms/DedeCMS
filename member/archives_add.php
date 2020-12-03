<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_imgbt.php");
CheckRank(0,0);

if($cfg_mb_sendall=='否'){
	ShowMsg("对不起，系统禁用了自定义模型投稿，因此无法使用此功能！","-1");
	exit();
}
require_once(dirname(__FILE__)."/inc/inc_catalog_options.php");
require_once(dirname(__FILE__)."/inc/inc_archives_all.php");
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");

if(!isset($channelid)) $channelid=0;
else $channelid = trim(ereg_replace("[^0-9]","",$channelid));

$dsql = new DedeSql(false);
$cInfos = $dsql->GetOne("Select * From #@__channeltype  where ID='$channelid'; ");	

if($cInfos['issystem']!=0 || $cInfos['issend']!=1){
	$dsql->Close();
	ShowMsg("你指定的频道参数错误或不允许投稿！","-1");
	exit();
}

if($cInfos['sendrank'] > $cfg_ml->M_Type){
	$row = $dsql->GetOne("Select membername From #@__arcrank where rank='".$cInfos['sendrank']."' ");
	$dsql->Close();
	ShowMsg("对不起，需要[".$row['membername']."]才能在这个频道发布文档！","-1","0",5000);
	exit();
}


$channelid = $cInfos['ID'];
$addtable = $cInfos['addtable'];

require_once(dirname(__FILE__)."/templets/archives_add.htm");

?>