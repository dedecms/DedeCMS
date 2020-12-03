<?
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);

if($cfg_mb_sendall=='否'){
	ShowMsg("对不起，系统禁用了自定义模型投稿，因此无法使用此功能！","-1");
	exit();
}
require_once(dirname(__FILE__)."/inc/inc_catalog_options.php");
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
require_once(dirname(__FILE__)."/inc/inc_archives_all.php");
$aid = ereg_replace("[^0-9]","",$aid);
if($aid=="")
{
	ShowMsg("你没指定文档ID，不允许访问本页面！","-1");
	exit();
}
$dsql = new DedeSql(false);
//读取归档信息
//------------------------------
$arcQuery = "Select 
#@__channeltype.typename as channelname,
#@__arcrank.membername as rankname,
#@__archives.* 
From #@__archives
left join #@__channeltype on #@__channeltype.ID=#@__archives.channel 
left join #@__arcrank on #@__arcrank.rank=#@__archives.arcrank
where #@__archives.ID='$aid'";

$dsql->SetQuery($arcQuery);
$arcRow = $dsql->GetOne($arcQuery);
if(!is_array($arcRow)){
	$dsql->Close();
	ShowMsg("读取档案基本信息出错!","-1");
	exit();
}
if($arcRow['arcrank']>=0){
	$dsql->Close();
	ShowMsg("对不起，这则信息已经被管理员锁定，你不能再更改!","-1");
	exit();
}


$query = "Select * From #@__channeltype where ID='".$arcRow['channel']."'";
$cInfos = $dsql->GetOne($query);
if(!is_array($cInfos)){
	$dsql->Close();
	ShowMsg("读取频道配置信息出错!","-1");
	exit();
}
if($cInfos['issystem']!=0 || $cInfos['issend']!=1){
	$dsql->Close();
	ShowMsg("你指定的频道参数的错误！","-1");
	exit();
}

$channelid = $arcRow['channel'];
//-----------------------
$addQuery = "Select * From ".$cInfos['addtable']." where aid='$aid'";
$addRow = $dsql->GetOne($addQuery);
$arow = $dsql->GetOne(" Select typename From #@__arctype where ID='".$arcRow['typeid']."'; ");
require_once(dirname(__FILE__)."/templets/archives_edit.htm");

?>