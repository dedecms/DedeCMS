<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('spec_List');
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
require_once(dirname(__FILE__)."/inc/inc_list_functions.php");

setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");

if(!isset($cid)) $cid = 0;
if(!isset($keyword)) $keyword = "";
if(!isset($channelid)) $channelid = 0;
if(!isset($arcrank)) $arcrank = "";
$typeid = $cid;
if($cuserLogin->getUserRank()<5) $arcrank = -1;

$tl = new TypeLink($cid);
$seltypeids = 0;
if(!empty($cid)){
	$seltypeids = $tl->dsql->GetOne("Select ID,typename,channeltype From #@__arctype where ID='$cid' ");
}
$opall=1;
if(is_array($seltypeids)){
	$optionarr = GetTypeidSel('form3','cid','selbt1',0,$seltypeids['ID'],$seltypeids['typename']);
}else{
	$optionarr = GetTypeidSel('form3','cid','selbt1',0,0,'请选择...');
}

$whereSql = " where arcs.channel = -1 ";

if($keyword!=""){
	$whereSql .= " And (arcs.title like '%$keyword%' Or arcs.writer like '%$keyword%' Or arcs.source like '%$keyword%') ";
}

if($typeid!=0){
	$tlinkSql = $tl->GetSunID($typeid,"arcs",0);
	$whereSql .= " And $tlinkSql ";
}

if($arcrank!=""){
	$whereSql .= " And arcs.arcrank=$arcrank ";
	$CheckUserSend = "<input type='button' onClick=\"location='content_s_list.php?cid=".$cid."';\" value='所有专题' class='inputbut' />";
}
else
{
	$CheckUserSend = "<input type='button' onClick=\"location='content_s_list.php?cid=".$cid."&arcrank=-1';\" value='待审核专题' class='inputbut' />";
}

$tl->Close();

$query = "
select arcs.ID,arcs.typeid,arcs.senddate,arcs.iscommend,arcs.ismake,arcs.channel,arcs.arcrank,arcs.click,arcs.title,arcs.color,arcs.litpic,arcs.pubdate,arcs.adminID,arcs.memberID,`#@__arctype`.typename,`#@__channeltype`.typename as channelname 
from `#@__archivesspec` arcs 
left join `#@__arctype` on #@__arctype.ID=arcs.typeid
left join `#@__channeltype` on #@__channeltype.ID=arcs.channel
$whereSql
order by arcs.ID desc
";

$dlist = new DataList();
$dlist->pageSize = 20;
$dlist->SetParameter("dopost","listArchives");
$dlist->SetParameter("keyword",$keyword);
$dlist->SetParameter("cid",$cid);
$dlist->SetParameter("arcrank",$arcrank);
$dlist->SetParameter("channelid",$channelid);
$dlist->SetSource($query);
include(dirname(__FILE__)."/templets/content_s_list.htm");
$dlist->Close();

ClearAllLink();
?>