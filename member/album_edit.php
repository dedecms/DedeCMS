<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/inc/inc_catalog_options.php");
CheckRank(0,0);

if($cfg_mb_album=='N'){
	ShowMsg("对不起，系统禁用了图集的功能，因此无法使用！","-1");
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

$cInfos = $dsql->GetOne("Select c.* From `#@__full_search` a left join #@__channeltype c on c.ID=a.channelid where a.aid='$aid' And a.mid='{$cfg_ml->M_ID}' ",MYSQL_ASSOC);

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

$maintable = ($cInfos['maintable']=='' ? '#@__archives' : $cInfos['maintable']);
$addtable = ($cInfos['addtable']=='' ? '#@__addonarticle' : $cInfos['addtable']);

//读取归档信息
//------------------------------
$arcQuery = "Select c.typename as channelname,t.typename,ar.membername as rankname,a.* 
From `$maintable` a
left join `#@__channeltype` c on c.ID=a.channel 
left join `#@__arcrank` ar on ar.rank=a.arcrank
left join `#@__arctype` t on t.ID=a.typeid
where a.ID='$aid'";

$row = $dsql->GetOne($arcQuery,MYSQL_ASSOC);
if(!is_array($row)){
	$dsql->Close();
	ShowMsg("读取档案基本信息出错!","-1");
	exit();
}

$exday = 3600 * 24 * $cfg_locked_day;
$ntime = mytime();

if($row['arcrank']>=0 && ($ntime - $row['senddate']) > $exday){
	$dsql->Close();
	ShowMsg("对不起，这则信息已经被管理员锁定，你不能再更改!","-1");
	exit();
}

$channelid = $row['channel'];

if($addtable!=''){
  $addQuery = "Select * From `{$addtable}` where aid='$aid'";
  $addRow = $dsql->GetOne($addQuery,MYSQL_ASSOC);
}

$arow['typename'] = $row['typename'];

require_once(dirname(__FILE__)."/templets/album_edit.htm");

$dsql->Close();

?>