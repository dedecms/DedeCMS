<?php
require_once(dirname(__FILE__)."/config.php");

if(!isset($cid)) $cid = 0;
if(!isset($keyword)) $keyword = "";
if(!isset($channelid)) $channelid = 0;
if(!isset($arcrank)) $arcrank = "";
if(!isset($adminid)) $adminid = 0;
if(!isset($ismember)) $ismember = 0;
if(!isset($USEListStyle)) $USEListStyle = '';
if(empty($defaultPageSize)) $defaultPageSize = 20;
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
if(empty($cid) && empty($channelid)) $channelid = 1;
$tl = new TypeLink($cid);
if($cid>0) $channelid = $tl->TypeInfos['channeltype'];
$tables = GetChannelTable($tl->dsql,$channelid,'channel');
if($cid==0){
	$row = $tl->dsql->GetOne(" Select typename From #@__channeltype where ID='$channelid' ");
	$positionname = '所有'.$row[0]."&gt;";
}else{
	$positionname = str_replace($cfg_list_symbol,"&gt;",$tl->GetPositionName())."&gt;";
	$seltypeids = $tl->dsql->GetOne("Select ID,typename,channeltype From #@__arctype where ID='$cid' ",MYSQL_ASSOC);
}
//---------------------------------------

if($channelid<-1) $USEListStyle='infos';

$opall=1;
if(is_array($seltypeids)){
	$optionarr = GetTypeidSel('form3','cid','selbt1',0,$seltypeids['ID'],$seltypeids['typename']);
}else{
	$optionarr = GetTypeidSel('form3','cid','selbt1',0,0,'请选择栏目...');
}


if($channelid==0) $whereSql = " where a.channel > 0 ";
else $whereSql = " where a.channel = '$channelid' ";

if($ismember==1) $whereSql .= " And a.memberID > 0 ";

if(!empty($memberid)) $whereSql .= " And a.memberID = '$memberid' ";
else $memberid = 0;

if(!empty($cids)){
	$whereSql .= " And a.typeid in ($cids) ";
}

if($keyword!=""){
	$whereSql .= " And a.title like '%$keyword%' ";
}

if($cid!=0){
	$tlinkSql = $tl->GetSunID($cid,"a",0);
	$whereSql .= " And $tlinkSql ";
}

if($adminid>0){ $whereSql .= " And a.adminID = '$adminid' "; }

if($arcrank!=""){
	$whereSql .= " And a.arcrank = '$arcrank' ";
	$CheckUserSend = "<input type='button' onClick=\"location='catalog_do.php?channelid={$channelid}&cid={$cid}&dopost=listArchives&gurl=content_list.php';\" value='所有文档' class='inputbut'>";
}
else{
	//$whereSql .= " And a.arcrank >-1 ";
	$CheckUserSend = "<input type='button' onClick=\"location='catalog_do.php?channelid={$channelid}&cid={$cid}&dopost=listArchives&arcrank=-1&gurl=content_list.php';\" value='稿件审核' class='inputbut'>";
}

if(empty($orderby)) $orderby = "ID";

$query = "
select a.ID,a.adminID,a.typeid,a.senddate,a.iscommend,a.ismake,a.channel,a.endtime,a.writer,
a.arcrank,a.click,a.title,a.color,a.litpic,a.pubdate,a.adminID,a.memberID,
t.typename,c.typename as channelname,adm.uname as adminname
from `{$tables['maintable']}` a
left join #@__arctype t on t.ID=a.typeid
left join #@__channeltype c on c.ID=a.channel
left join #@__admin adm on adm.ID=a.adminID
$whereSql order by a.{$orderby} desc
";
$dsql = new DedeSql(false);
$dlist = new DataList();
$dlist->pageSize = $defaultPageSize;
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
if($USEListStyle=='pic') include_once(dirname(__FILE__)."/templets/content_i_list.htm");
else if($USEListStyle=='infos') include_once(dirname(__FILE__)."/templets/info_list.htm");
else include_once(dirname(__FILE__)."/templets/content_list.htm");
$dlist->Close();
ClearAllLink();
?>