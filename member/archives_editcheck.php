<?php
if(!isset($cfg_add_dftable)) exit();

require_once(dirname(__FILE__)."/inc/inc_archives_functions.php");
require_once(dirname(__FILE__)."/inc/inc_catalog_options.php");

$ID = intval($ID);
$channelid = intval($channelid);
$typeid = intval($typeid);
$dede_addonfields = (empty($dede_addonfields) ? '' : $dede_addonfields);
$dede_fieldshash = (empty($dede_fieldshash) ? '' : $dede_fieldshash);

if(!empty($dede_addonfields))
{
	$_dede_addonfields = md5($dede_addonfields.$cfg_cookie_encode);
	if($_dede_addonfields!=$dede_fieldshash){
		ShowMsg("附加数据校验出错，不允许修改！","-1");
	  exit();
	}
}

if($channelid==0){
	ShowMsg("系统频道没有指定，发表文档出错！","-1");
	exit();
}

if($typeid==0){
	ShowMsg("请指定文档隶属的栏目！","-1");
	exit();
}

if($ID==0){
	ShowMsg("文档ID不正确！","-1");
	exit();
}

$_msg = CheckChannel($typeid,$channelid);
if($_msg!=''){
	ShowMsg("系统出错，原因是：{$_msg}","-1");
	exit();
}

$dsql = new DedeSql(false);

//检测用户是否有权限操作这篇文章
//--------------------------------
$nquery = "Select c.arcsta,c.maintable,c.addtable,arc.arcrank,arc.uptime 
From `#@__full_search` arc left join #@__channeltype c on c.ID=arc.channelid 
where arc.aid='$ID' And mid='{$cfg_ml->M_ID}'; ";
$cInfos = $dsql->GetOne($nquery,MYSQL_ASSOC);
if(!is_array($cInfos)){
	$dsql->Close();
	ShowMsg("读取频道信息出错，可能指定的文档有问题！","-1");
	exit();
}

$maintable = ($cInfos['maintable']=='' ? '#@__archives' : $cInfos['maintable']);
$addtable = ($cInfos['addtable']=='' ? $cfg_add_dftable : $cInfos['addtable']);

if(!isset($cfg_id_hudong))
{
  $exday = 3600 * 24 * $cfg_locked_day;
  $ntime = mytime();
  if($cInfos['arcrank']>=0 && $cInfos['uptime']-$ntime > $exday){
	  $dsql->Close();
	  ShowMsg("对不起，这则信息已经锁定，你不能再更改!","-1");
	  exit();
  }
  if($cInfos['arcsta']==0){
	  $ismake = 0;
	  $arcrank = 0;
  }else if($cInfos['arcsta']==1){
	  $ismake = -1;
	  $arcrank = 0;
  }else{
	  $ismake = 0;
	  $arcrank = -1;
  }
}

?>