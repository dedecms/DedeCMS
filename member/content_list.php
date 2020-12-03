<?php 
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);

if(!isset($cid)) $cid = 0;
if(!isset($keyword)) $keyword = "";
if(!isset($channelid)) $channelid = 0;
if(!isset($arcrank)) $arcrank = "";
$positionname = "";

$memberid = $cfg_ml->M_ID;

////////////////////////////////////////////////////////////////////
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
require_once(dirname(__FILE__)."/inc/inc_list_functions.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");

$cid = ereg_replace("[^0-9]","",$cid);
$channelid = ereg_replace("[^0-9-]","",$channelid);
$tl = new TypeLink($cid);

$cInfos = $tl->dsql->GetOne("Select arcsta From `#@__channeltype`  where ID='$channelid'; ");
$arcsta = $cInfos['arcsta'];

if($cid==0){
	$row = $tl->dsql->GetOne("Select typename From `#@__channeltype` where ID='$channelid'");
	if(is_array($row)) $positionname = $row[0]." &gt;&gt; ";
}else{
	$positionname = str_replace($cfg_list_symbol," &gt;&gt; ",$tl->GetPositionName())." &gt;&gt; ";
}

$whereSql = " where arcs.channelid = '$channelid' And arcs.mid='$memberid' ";

if(!empty($mtype)){
	$mtype = ereg_replace("[^0-9]","",$mtype);
	$whereSql .= " And (arcs.mtype='$mtype') ";
}

if($keyword!=""){
	$keyword = cn_substr(trim(ereg_replace($cfg_egstr,"",stripslashes($keyword))),30);
  $keyword = addslashes($keyword);
	$whereSql .= " And (arcs.title like '%$keyword%') ";
}

if($cid!=0){
	$tlinkSql = $tl->GetSunID($cid,'',0);
	$whereSql .= " And arcs.typeid in($tlinkSql) ";
}

$tl->Close();

$query = "
select arcs.aid,arcs.adminid,arcs.typeid,arcs.channelid,arcs.arcrank,arcs.channelid,arcs.uptime as senddate,
arcs.click,arcs.title,arcs.litpic,arcs.uptime,arcs.mid,t.typename,c.typename as channelname 
from `#@__full_search` arcs 
left join `#@__arctype` t on t.ID=arcs.typeid
left join `#@__channeltype` c on c.ID=arcs.channelid
$whereSql
order by arcs.aid desc
";

$dlist = new DataList();
$dlist->pageSize = 20;
$dlist->SetParameter("dopost","listArchives");
$dlist->SetParameter("keyword",$keyword);
$dlist->SetParameter("cid",$cid);
$dlist->SetParameter("channelid",$channelid);
$dlist->SetSource($query);
include(dirname(__FILE__)."/templets/content_list.htm");
$dlist->Close();
$dsql->Close();
?>