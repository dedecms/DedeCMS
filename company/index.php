<?php
//require_once(dirname(__FILE__)."./../include/config_base.php");
require_once(dirname(__FILE__)."./../member/config.php");

$dsql = new DedeSql(false);
$areas = $sectors = $topsectors = $subsectors = array();
$dsql->setquery("select id, name, reid from #@__sectors order by disorder desc,id asc");
$dsql->Execute();
while($row = $dsql->GetArray())
{
	if($row['reid'] == 0){
		$topsectors[] = $row;
	}else{
		$subsectors[] = $row;
	}
}

foreach($topsectors as $topsector){
	$sectors[] = $topsector;
	foreach($subsectors as $key => $subsector){
		if($subsector['reid'] == $topsector['id']){
			$sectors[] = $subsector;
			unset($subsectors[$key]);
		}
	}
}

//////////////////////地区数据处理s/////////////////////////////
$sql = "select * from #@__area order by disorder asc,id asc";
$dsql->SetQuery($sql);
$dsql->Execute();
$toparea = $subarea = array();
while($sector = $dsql->GetArray())
{
	$areas[$sector['id']] = $sector['name'];

	if($sector['reid'] == 0)
	{
		$toparea[] = $sector;
	}else
	{
		$subarea[] = $sector;
	}
}
$areacache = "toparea=new Array();\n\n";
$areaidname = $areaid2name = '-不限-';
foreach($toparea as $topkey => $topsector)
{
	if($topsector['id'] == $row['areaid'])
	{
		$areaidname = $topsector['name'];
	}
	$areacache .= "toparea[$topkey]=".'"'.$topsector['id'].'~'.$topsector['name'].'";'."\n";
	$areacache .= "\t".'subareas'.$topsector['id'].'=new Array();'."\n";
	$arrCount = 0;
	foreach($subarea as $subkey => $subsector)
	{
		if($subsector['id'] == $row['areaid2'])
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

include(dirname(__FILE__)."/template/default/search_index.htm");