<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/inc/inc_catalog_options.php");
CheckRank(0,0);

if($cfg_mb_sendall=='N'){
	ShowMsg("对不起，系统禁用了自定义模型投稿，因此无法使用此功能！","-1");
	exit();
}

$aid = (empty($aid) ? 0 : intval($aid));
if($aid==0)
{
	ShowMsg("你没指定文档ID，不允许访问本页面！","-1");
	exit();
}

require_once(dirname(__FILE__)."/inc/inc_archives_functions.php");
require_once(dirname(__FILE__)."/inc/inc_catalog_options.php");

$dsql = new DedeSql(false);

$cInfos = $dsql->GetOne("Select c.* From `#@__full_search` a left join #@__channeltype c on c.ID=a.channelid where a.aid='$aid' And a.mid='{$cfg_ml->M_ID}' ");

if(!is_array($cInfos)){
	$dsql->Close();
	ShowMsg("读取频道信息出错，可能指定的ID有问题！","-1");
	exit();
}

if($cInfos['issend']!=1){
	$dsql->Close();
	ShowMsg("你指定的频道不允许投稿！","-1");
	exit();
}

if(!empty($cInfos['usereditcon']) && strtolower($cInfos['usereditcon'])!='archives_edit.php')
{
	$dsql->Close();
	header("location:{$cInfos['usereditcon']}?aid=$aid");
	exit();
}

$maintable = ($cInfos['maintable']=='' ? '#@__archives' : $cInfos['maintable']);
$addtable = ($cInfos['addtable']=='' ? '' : $cInfos['addtable']);

//读取归档信息
//------------------------------
$arcQuery = "Select c.typename as channelname,t.typename,ar.membername as rankname,a.* 
From `$maintable` a
left join `#@__channeltype` c on c.ID=a.channel 
left join `#@__arcrank` ar on ar.rank=a.arcrank
left join `#@__arctype` t on t.ID=a.typeid
where a.ID='$aid'";

$arcRow = $dsql->GetOne($arcQuery,MYSQL_ASSOC);
if(!is_array($arcRow)){
	$dsql->Close();
	ShowMsg("读取档案基本信息出错!","-1");
	exit();
}

$exday = 3600 * 24 * $cfg_locked_day;
$ntime = mytime();

if($arcRow['arcrank']>=0 && $ntime - $arcRow['senddate'] > $exday){
	$dsql->Close();
	ShowMsg("对不起，这则信息已经被管理员锁定，你不能再更改!","-1");
	exit();
}

$channelid = $arcRow['channel'];

if($addtable!=''){
  $addQuery = "Select * From `{$addtable}` where aid='$aid'";
  $addRow = $dsql->GetOne($addQuery,MYSQL_ASSOC);
}

$arow['typename'] = $arcRow['typename'];

require_once(dirname(__FILE__)."/templets/archives_edit.htm");

$dsql->Close();
?>