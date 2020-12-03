<?php 
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);
$channelid = (empty($channelid) ? 1 : intval($channelid));
$dsql = new DedeSql(false);
$cInfos = $dsql->GetOne("Select * From #@__channeltype  where ID='$channelid'; ");
	
if($cInfos['issend']!=1){
	$dsql->Close();
	ShowMsg("你指定的频道不允许投稿！","-1");
	exit();
}

if($cInfos['sendrank'] > $cfg_ml->M_Type){
	$row = $dsql->GetOne("Select membername From `#@__arcrank` where rank='".$cInfos['sendrank']."' ");
	$dsql->Close();
	ShowMsg("对不起，需要[".$row['membername']."]才能在这个频道发布文档！","-1","0",5000);
	exit();
}

require_once(dirname(__FILE__)."/inc/inc_catalog_options.php");
require_once(dirname(__FILE__)."/templets/article_add.htm");
$dsql->Close();
?>