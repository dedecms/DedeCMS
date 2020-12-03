<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/inc/inc_catalog_options.php");
CheckRank(0,0);

$aid = ereg_replace("[^0-9]","",$aid);
$channelid="15";
$dsql = new DedeSql(false);
//读取归档信息
//------------------------------
$arcQuery = "Select 
#@__archives.*,#@__addonvote.*,#@__arctype.typename
From #@__archives
left join #@__addonvote on #@__addonvote.aid=#@__archives.ID
left join #@__arctype on #@__arctype.ID=#@__archives.typeid
where #@__archives.ID='$aid' And #@__archives.memberID='".$cfg_ml->M_ID."'";
$dsql->SetQuery($arcQuery);
$row = $dsql->GetOne($arcQuery);
if(!is_array($row)){
	$dsql->Close();
	ShowMsg("读取作品信息出错!","-1");
	exit();
}


require_once(dirname(__FILE__)."/templets/vote_edit.htm");

?>