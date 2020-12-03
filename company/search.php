<?php

//require_once(dirname(__FILE__)."./../include/config_base.php");
require_once(dirname(__FILE__)."./../member/config.php");
require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
$dsql = new DedeSql(false);
if(empty($areaid)) $areaid = 0;
if(empty($areaid2)) $areaid2 = 0;
if(!isset($sectorid)) $sectorid = 0;
if(!isset($sectorid2)) $sectorid2 = 0;
if(empty($page)) $page = 1;

$areaid = intval($areaid);
$areaid2 = intval($areaid2);
$sectorid = intval($sectorid);
$sectorid2 = intval($sectorid2);
$page = intval($page);

isset($comname) || $comname = '';
$comname = trim($comname);
$allsectors = $areas = array();
$dsql->setquery("select id, name from #@__sectors order by disorder desc,id asc");
$dsql->Execute();
while($row = $dsql->getarray())
{
	$allsectors[$row['id']] = $row['name'];

}

	//////////////////////地区数据处理s/////////////////////////////
		$sql = "select * from #@__area order by disorder desc,id asc";
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



$wheresql = '';
$subsectors = $sectors = array();
$sectorinfo = $resectorinfo = '';

if($areaid2 > 0){
	$wheresql .= " and areaid2=$areaid2";
}elseif($areaid > 0)
{
	$wheresql .= " and areaid=$areaid";
}
if($sectorid > 0){
	$dsql->setquery("select id, name, reid from #@__sectors
	where id=$sectorid or reid=$sectorid order by disorder desc,id asc");
	$dsql->Execute();
	while($row = $dsql->GetArray()) {
			if($row['id'] == $sectorid){
				$topsectorinfo = $sectorinfo = $row;
			}else{
				$subsectors[] = $row;
			}
	}

	$wheresql .= " and typeid1=$sectorid";

}elseif($sectorid2 > 0)
{
	$sectorinfo = $dsql->getone("select id, name, reid from #@__sectors where id=$sectorid2");
	if($sectorinfo){
		$sectorid = $sectorinfo['reid'];
		$dsql->setquery("select id, name, reid from #@__sectors
		where id=$sectorid or reid=$sectorid order by disorder desc,id asc");
		$dsql->Execute();
		while($row = $dsql->GetArray()) {

				if($row['id'] == $sectorid){
					$topsectorinfo = $row;
				}else{
					$subsectors[] = $row;
				}
		}
		$sectorid = 0;
	}
	$wheresql .= " and typeid2=$sectorid2";
}

if($comname != ''){
	$wheresql .= " and comname LIKE '%".$comname."%'";
}

$wheresql = trim($wheresql);
if(eregi("^and", $wheresql)) {
	$wheresql = substr($wheresql,3);
}
if($wheresql != ''){
	$wheresql = 'where '.trim($wheresql);
}

$companys = array();
$query = "select id, comname, regyear, areaid, areaid2, service, typeid1, typeid2, comaddr, website, postid
from #@__member_cominfo $wheresql";
$dlist = new DataList();
$dlist->pageSize = 10;
$dlist->SetParameter("sectorid",$sectorid);
$dlist->SetParameter("sectorid2",$sectorid2);
$dlist->SetParameter("areaid",$areaid);
$dlist->SetParameter("comname",$comname);
$dlist->SetParameter("areaid2",$areaid2);
$dlist->SetSource($query);

include(dirname(__FILE__)."/template/default/search_list.htm");

?>