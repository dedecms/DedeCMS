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

$arcQuery = "Select a.*,
arctype.typename, arctype.smalltypes,c.typename as channelname,r.membername as rankname,full.keywords as words
From `{$tables['maintable']}` a
left join #@__arctype arctype on arctype.ID = a.typeid
left join #@__channeltype c on c.ID=a.channel
left join #@__arcrank r on r.rank=a.arcrank
left join #@__full_search full on full.aid=a.ID 
where a.ID='$aid' ";

$info = $dsql->GetOne($arcQuery);
$info['keywords'] = $info['words'];
if(!is_array($info)){
  $dsql->Close();
  ShowMsg("读取档案基本信息出错!","javascript:;");
  exit();
}

$query = "Select * From #@__channeltype where ID='".$info['channel']."'";
$cInfos = $dsql->GetOne($query);
if(!is_array($cInfos)){
	$dsql->Close();
	ShowMsg("读取频道配置信息出错!","javascript:;");
	exit();
}
$channelid = $info['channel'];
$addtable = $cInfos['addtable'];

$addQuery = "Select * From `{$cInfos['addtable']}` where aid='$aid'";
$addRow = $dsql->GetOne($addQuery,MYSQL_ASSOC);
$tags = GetTagFormLists($dsql,$aid);
foreach($addRow as $k=>$v){ if(!isset($info[$k])) $info[$k] = $v; }

//文章信息处理
$info['endtime'] = ($info['endtime']-$info['senddate'])/86400;

//小分类处理
if(!empty($info['smalltypes']))
{
  $sql = "select * from #@__smalltypes where id in($info[smalltypes]);";
  $dsql->SetQuery($sql);
  $dsql->Execute();
  $smalltypes = '';
  while($smalltype = $dsql->GetArray())
  {
     $ifcheck ='';
     if($smalltype['id'] == $info['smalltypeid']){ $ifcheck = 'selected'; }
     $smalltypes .= '<option value="'.$smalltype['id'].'"'.$ifcheck.'>'.$smalltype['name']."</option>\n";
  }
}

$body = $info["message"];

//////////////////////地区数据处理s/////////////////////////////
$sql = "select * from #@__area";
$dsql->SetQuery($sql);
$dsql->Execute();
$toparea = $subarea = array();
while($sector = $dsql->GetArray())
{
	if($sector['reid'] == 0){ $toparea[] = $sector; }
	else{ $subarea[] = $sector; }
}
$areacache = "toparea=new Array();\n\n";
$areaidname = $areaid2name = '-不限-';
foreach($toparea as $topkey => $topsector)
{
	if($topsector['id'] == $info['areaid'])
	{
		$areaidname = $topsector['name'];
	}
	$areacache .= "toparea[$topkey]=".'"'.$topsector['id'].'~'.$topsector['name'].'";'."\n";
	$areacache .= "\t".'subareas'.$topsector['id'].'=new Array();'."\n";
	$arrCount = 0;
	foreach($subarea as $subkey => $subsector)
	{
		if($subsector['id'] == $info['areaid2'])
		{
			$areaid2name = $subsector['name'];
		}
		if($subsector['reid'] == $topsector['id'])
		{
			$areacache .= "\t".'subareas'.$topsector['id'].'['.$arrCount.']="'.$subsector['id'].'~'.$subsector['name'].'";'."\n";
			$arrCount++;
		}

	}
}
//echo $areacache;exit;
//////////////////////地区数据处理e/////////////////////////////

//////////////////////行业数据处理s/////////////////////////////
$sql = "select * from #@__sectors";
$dsql->SetQuery($sql);
$dsql->Execute();
$topsectors = $subsectors = array();
while($sector = $dsql->GetArray())
{
	if($sector['reid'] == 0)
	{
		$topsectors[] = $sector;
	}else
	{
		$subsectors[] = $sector;
	}
}
$sectorcache = "topsectors=new Array();\n\n";
$sectoridname = $sectorid2name = '-不限-';
foreach($topsectors as $topkey => $topsector)
{
	if($topsector['id'] == $info['sectorid'])
	{
		$sectoridname = $topsector['name'];
	}
	$sectorcache .= "topsectors[$topkey]=".'"'.$topsector['id'].'~'.$topsector['name'].'";'."\n";
	$sectorcache .= "\t".'subsectors'.$topsector['id'].'=new Array();'."\n";
	foreach($subsectors as $subkey => $subsector)
	{
		if($subsector['id'] == $info['sectorid2']){
			$sectorid2name = $subsector['name'];
		}
		if($subsector['reid'] == $topsector['id'])
		{
			$sectorcache .= "\t".'subsectors'.$topsector['id'].'['.$subkey.']="'.$subsector['id'].'~'.$subsector['name'].'";'."\n";
		}
	}
}
//echo $sectorcache;exit;
//////////////////////行业数据处理e/////////////////////////////
if($info['adminid'] == 0)
{
	$adminid = $cuserLogin->getUserID();
}else{
	$adminid = $info['adminid'];
}
require_once(dirname(__FILE__)."/templets/info_edit.htm");

ClearAllLink();
?>