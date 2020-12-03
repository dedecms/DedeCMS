<?php
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEADMIN."/inc/inc_catalog_options.php");
require_once(DEDEADMIN."/inc/inc_archives_functions.php");
$typeid = $cid = (!isset($cid) ? 0 : intval($cid));
$channelid = (!isset($channelid) ? 0 : intval($channelid));
$dsql = new DedeSql(false);

//读取归档信息
//------------------------------
$arcQuery = "Select t.typename as arctypename,t.smalltypes,c.* From #@__arctype t left join #@__channeltype c on c.ID=t.channeltype where t.ID='$typeid' ";
$cInfos = $typeinfo = $dsql->GetOne($arcQuery);
if(is_array($cInfos)){
  $channelid = $typeinfo['ID'];
  $addtable = $typeinfo['addtable'];
}
else if(!empty($channelid))
{
	$query = " Select * From  #@__channeltype where ID='$channelid'";
  $cInfos = $dsql->GetOne($query);
  $channelid = $cInfos['ID'];
  $addtable = $cInfos['addtable'];
}

//获取小分类
$smalltypes = '';
if(is_array($typeinfo) && !empty($typeinfo['smalltypes']))
{
	$sql = "select * from #@__smalltypes where id in($typeinfo[smalltypes]);";
	$dsql->SetQuery($sql);
	$dsql->Execute();
	while($smalltype = $dsql->GetArray()){
		$smalltypes .= '<option value="'.$smalltype['id'].'">'.$smalltype['name']."</option>\n";
	}
}
//////////////////////地区数据处理s/////////////////////////////
$dsql->SetQuery("select * from #@__area");
$dsql->Execute();
$toparea = $subarea = array();
while($sector = $dsql->GetArray())
{
	if($sector['reid'] == 0){
			$toparea[] = $sector;
	}else{
			$subarea[] = $sector;
	}
}
	$areacache = "toparea=new Array();\n\n";
	$areaidname = $areaid2name = '-不限-';
	foreach($toparea as $topkey => $topsector)
	{
		$areacache .= "toparea[$topkey]=".'"'.$topsector['id'].'~'.$topsector['name'].'";'."\n";
		$areacache .= "\t".'subareas'.$topsector['id'].'=new Array();'."\n";
		$arrCount = 0;
		foreach($subarea as $subkey => $subsector)
		{
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
		$sectorcache .= "topsectors[$topkey]=".'"'.$topsector['id'].'~'.$topsector['name'].'";'."\n";
		$sectorcache .= "\t".'subsectors'.$topsector['id'].'=new Array();'."\n";
		$arrCount = 0;
		foreach($subsectors as $subkey => $subsector)
		{
			if($subsector['reid'] == $topsector['id'])
			{
				$sectorcache .= "\t".'subsectors'.$topsector['id'].'['.$arrCount.']="'.$subsector['id'].'~'.$subsector['name'].'";'."\n";
				$arrCount++;
			}

		}
	}
	//echo $sectorcache;exit;
//////////////////////行业数据处理e/////////////////////////////
require_once(dirname(__FILE__)."/templets/info_add.htm");
ClearAllLink();
exit();
?>


