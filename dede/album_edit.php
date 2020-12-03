<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('a_Edit,a_AccEdit,a_MyEdit');
require_once(DEDEADMIN."/inc/inc_catalog_options.php");
require_once(DEDEADMIN."/../include/pub_dedetag.php");
require_once(DEDEADMIN."/inc/inc_archives_functions.php");
$aid = intval($aid);

$dsql = new DedeSql(false);
//读取归档信息
//------------------------------
$tables = GetChannelTable($dsql,$aid,'arc');

$arcQuery = "Select c.typename as channelname,r.membername as rankname,a.* ,full.keywords as words
From `{$tables['maintable']}` a 
left join #@__channeltype c on c.ID=a.channel  
left join #@__arcrank r on r.rank=a.arcrank
left join #@__full_search full on full.aid=a.ID 
where a.ID='$aid'";

$arcRow = $dsql->GetOne($arcQuery);
$arcRow['keywords'] = $arcRow['words'];
if(!is_array($arcRow)){
	$dsql->Close();
	ShowMsg("读取档案基本信息出错!","javascript:;");
	exit();
}

$query = "Select * From #@__channeltype where ID='".$arcRow['channel']."'";
$cInfos = $dsql->GetOne($query);
if(!is_array($cInfos)){
	$dsql->Close();
	ShowMsg("读取频道配置信息出错!","javascript:;");
	exit();
}
$channelid = $arcRow['channel'];
$addtable = $cInfos['addtable'];

$addRow = $dsql->GetOne("Select * From $addtable where aid='$aid'");
if(!is_array($addRow)){
	$imgurls = "";
	$pagestyle = 1;
	$maxwidth = $cfg_album_width;
	$irow = 4;
	$icol = 4;
	$isrm = 1;
}
else
{
	$imgurls = $addRow["imgurls"];
	$maxwidth = $addRow["maxwidth"];
	$pagestyle = $addRow["pagestyle"];
	$irow = $addRow["row"];
	$icol = $addRow["col"];
	$isrm = $addRow["isrm"];
	$maxwidth = $addRow["maxwidth"];
}

$tags = GetTagFormLists($dsql,$aid);

require_once(dirname(__FILE__)."/templets/album_edit.htm");

ClearAllLink();
?>