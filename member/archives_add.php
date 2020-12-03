<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/inc/inc_catalog_options.php");
CheckRank(0,0);

if($cfg_mb_sendall=='N'){
	ShowMsg("对不起，系统禁用了自定义模型投稿，因此无法使用此功能！","-1");
	exit();
}

require_once(dirname(__FILE__)."/inc/inc_catalog_options.php");


$channelid = (empty($channelid) ? 1 : intval($channelid));

$dsql = new DedeSql(false);
$cInfos = $dsql->GetOne("Select * From #@__channeltype  where ID='$channelid'; ");	

if($cInfos['issend']!=1){
	$dsql->Close();
	ShowMsg("你指定的频道不允许投稿！","-1");
	exit();
}


if($cInfos['sendrank'] > $cfg_ml->M_Type){
	$row = $dsql->GetOne("Select membername From #@__arcrank where rank='".$cInfos['sendrank']."' ");
	$dsql->Close();
	ShowMsg("对不起，需要[".$row['membername']."]才能在这个频道发布文档！","-1","0",5000);
	exit();
}


$channelid = $cInfos['ID'];
$addtable  = $cInfos['addtable'];

require_once(dirname(__FILE__)."/templets/archives_add.htm");

$dsql->Close();
?>