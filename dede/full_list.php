<?php
require_once(dirname(__FILE__)."/config.php");

if(!isset($cid)) $cid = 0;
if(!isset($keyword)) $keyword = "";
if(!isset($channelid)) $channelid = 0;
if(!isset($arcrank)) $arcrank = "";
if(!isset($adminid)) $adminid = 0;
if(!isset($ismember)) $ismember = 0;
if(!isset($USEListStyle)) $USEListStyle = '';
//检查权限许可，总权限
CheckPurview('a_List,a_AccList,a_MyList');

$cids = '';
//栏目浏览许可
if(TestPurview('a_List')){
	;
}
else if(TestPurview('a_AccList'))
{
	 if($cid==0)
	 {
	 	 $cids = MyCatalogInArr();
	 	 if(!empty($cids) && !ereg(',',$cids)){ $cid = $cids; $cids = ''; }
	 }
	 else{
	 	 CheckCatalog($cid,"你无权浏览非指定栏目的内容！");
	 }
}else
{
	 $adminid = $cuserLogin->getUserID();
}
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
require_once(dirname(__FILE__)."/inc/inc_list_functions.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");

//初始化频道信息
//------------------------------------
$seltypeids = 0;
//if(empty($cid) && empty($channelid)) $channelid = 1;
$tl = new TypeLink($cid);
if($cid>0) $channelid = $tl->TypeInfos['channeltype'];
$tables = GetChannelTable($tl->dsql,$channelid,'channel');
if($cid>0){
	$positionname = str_replace($cfg_list_symbol,"&gt;",$tl->GetPositionName())."&gt;";
	$seltypeids = $tl->dsql->GetOne("Select ID,typename,channeltype From #@__arctype where ID='$cid' ",MYSQL_ASSOC);
}
else if($channelid>0){
	$row = $tl->dsql->GetOne(" Select typename From #@__channeltype where ID='$channelid' ");
	$positionname = '所有'.$row[0]."&gt;";
}else{
	$positionname = '';
}
//---------------------------------------

$opall=1;
if(is_array($seltypeids)){
	$optionarr = GetTypeidSel('form3','cid','selbt1',0,$seltypeids['ID'],$seltypeids['typename']);
}else{
	$optionarr = GetTypeidSel('form3','cid','selbt1',0,0,'请选择栏目...');
}


if($channelid==0) $whereSql = " where a.channelid > 0 ";
else $whereSql = " where a.channelid = '$channelid' ";

if($ismember==1) $whereSql .= " And a.mid > 0 ";

if(!empty($memberid)) $whereSql .= " And a.mid = '$memberid' ";
else $memberid = 0;

if(!empty($cids)){
	$whereSql .= " And a.typeid in ($cids) ";
}

if($keyword!=""){
	$whereSql .= " And a.title like '%$keyword%' ";
}

if($cid!=0){
	$tlinkids = $tl->GetSunID($cid,'',0,true);
	if($tlinkids != -1){
		$whereSql .= " And a.typeid in($tlinkids) ";
	}
}

if($adminid>0){ $whereSql .= " And a.adminid = '$adminid' "; }

if($arcrank!=''){
	$whereSql .= " And a.arcrank = '$arcrank' ";
	$CheckUserSend = "<input type='button' onClick=\"location='full_list.php?channelid=$channelid';\" value='所有文档' class='inputbut'>";
}
else{
	$whereSql .= " And a.arcrank >-1 ";
	$CheckUserSend = "<input type='button' onClick=\"location='full_list.php?arcrank=-1&channelid=$channelid';\" value='稿件审核' class='inputbut'>";
}

if(empty($orderby)) $orderby = "aid";

$query = "
select a.aid,a.adminid,a.typeid,a.uptime,a.channelid,a.arcrank,a.click,a.title,a.litpic,a.adminid,a.mid,
t.typename,c.typename as channelname,adm.uname as adminname
from `#@__full_search` a
left join `#@__arctype` t on t.ID=a.typeid
left join `#@__channeltype` c on c.ID=a.channelid
left join `#@__admin` adm on adm.ID=a.adminid
$whereSql order by a.aid desc
";
$dsql = new DedeSql(false);
$dlist = new DataList();
$dlist->pageSize = 20;
$dlist->SetParameter("dopost","listArchives");
$dlist->SetParameter("keyword",$keyword);
$dlist->SetParameter("adminid",$adminid);
$dlist->SetParameter("memberid",$memberid);
$dlist->SetParameter("cid",$cid);
$dlist->SetParameter("arcrank",$arcrank);
$dlist->SetParameter("channelid",$channelid);
$dlist->SetParameter("ismember",$ismember);
$dlist->SetParameter("orderby",$orderby);
$dlist->SetSource($query);
include_once(dirname(__FILE__)."/templets/full_list.htm");
$dlist->Close();
ClearAllLink();
?>