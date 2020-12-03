<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
require_once(dirname(__FILE__)."/inc/inc_list_functions.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");

if(!isset($cid)) $cid = 0;
if(!isset($keyword)) $keyword = "";
if(!isset($channelid)) $channelid = 0;
if(!isset($arcrank)) $arcrank = "";
$typeid = $cid;

$tl = new TypeLink($cid);
$seltypeids = 0;

if(!empty($cid)){
	$seltypeids = $tl->dsql->GetOne("Select ID,typename,channeltype From #@__arctype where ID='$cid' ");
}

$opall=1;
if(is_array($seltypeids)){
	$optionarr = GetTypeidSel('form3','cid','selbt1',$channelid,$seltypeids['ID'],$seltypeids['typename']);
}else{
	$optionarr = GetTypeidSel('form3','cid','selbt1',$channelid,0,'请选择...');
}

if(empty($channelid)) $whereSql = " where arc.channelid != -1 ";
else $whereSql = " where arc.channelid = '$channelid' ";

if($keyword!=""){
	$whereSql .= " And (arc.title like '%$keyword%' Or arc.keywords like '%$keyword%' Or arc.addinfos like '%$keyword%') ";
}

if($typeid!=0){
	$tids = $tl->GetSunID($typeid,'',$channelid,true);
	$whereSql .= " And arc.typeid in($tids) ";
}

$query = "
select arc.aid as ID,arc.typeid,arc.uptime,arc.channelid,arc.arcrank,arc.title,
arc.litpic,arc.adminID,arc.mid,t.typename,c.typename as channelname 
from `#@__full_search` arc 
left join #@__arctype t on t.ID=arc.typeid
left join #@__channeltype c on c.ID=arc.channelid
$whereSql
order by aid desc
";

$dlist = new DataList();
$dlist->pageSize = 20;
$dlist->SetParameter("dopost","listArchives");
$dlist->SetParameter("keyword",$keyword);
$dlist->SetParameter("cid",$cid);
$dlist->SetParameter("f",$f);
$dlist->SetParameter("arcrank",$arcrank);
$dlist->SetParameter("channelid",$channelid);
$dlist->SetSource($query);
include(dirname(__FILE__)."/templets/content_select_list.htm");

ClearAllLink();
?>