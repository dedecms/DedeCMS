<?php
require_once(dirname(__FILE__)."/config.php");
$cid = isset($cid) ? intval($cid) : 0;
$channelid = isset($channelid) ? intval($channelid) : 0;
$mid = isset($mid) ? intval($mid) : 0;
if(!isset($keyword))
{
	$keyword = '';
}
if(!isset($arcrank))
{
	$arcrank = '';
}

if(empty($cid) && empty($channelid))
{
	ShowMsg("该页面必须指定栏目ID或内容模型ID才能浏览！","javascript:;");
	exit();
}

//检查权限许可，总权限
CheckPurview('a_List,a_AccList,a_MyList');

//栏目浏览许可
if(TestPurview('a_List'))
{

}
else if(TestPurview('a_AccList'))
{
	if($cid==0)
	{
		$cid = $cuserLogin->getUserChannel();
	}
	else
	{
		CheckCatalog($cid,"你无权浏览非指定栏目的内容！");
	}
}

$adminid = $cuserLogin->getUserID();
$maintable = '#@__archives';
require_once(DEDEINC."/typelink.class.php");
require_once(DEDEINC."/datalistcp.class.php");
require_once(DEDEADMIN."/inc/inc_list_functions.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
$tl = new TypeLink($cid);
$listtable = trim($tl->TypeInfos['addtable']);
if( !empty($channelid) && !empty($ucid) && $tl->TypeInfos['channeltype'] != $channelid)
{
  ShowMsg('你没权限访问此页！','javascript:;');
  exit();
}

if($cid==0)
{
	$row = $tl->dsql->GetOne("Select typename,addtable From `#@__channeltype` where id='$channelid'");
	$positionname = $row['typename']." &gt; ";
	$listtable = $row['addtable'];
}
else
{
	$positionname = str_replace($cfg_list_symbol," &gt; ",$tl->GetPositionName())." &gt; ";
}

$optionarr = $tl->GetOptionArray($cid,$cuserLogin->getUserChannel(),$channelid);

$whereSql = $channelid==0 ? " where arc.channel < -1 " : " where arc.channel = '$channelid' ";

if(!empty($mid))
{
	$whereSql .= " And arc.mid = '$mid' ";
}

if($keyword!='')
{
	$whereSql .= " And (arc.title like '%$keyword%') ";
}

if($cid!=0)
{
	$whereSql .= " And arc.typeid in(".GetSonIds($cid).")";
}

if($arcrank!='')
{
	$whereSql .= " And arc.arcrank = '$arcrank' ";
	$CheckUserSend = "<input type='button' class='coolbg np' onClick=\"location='content_sg_list.php?cid={$cid}&channelid={$channelid}&dopost=listArchives';\" value='所有文档' />";
}
else
{
	$CheckUserSend = "<input type='button' class='coolbg np' onClick=\"location='content_sg_list.php?cid={$cid}&channelid={$channelid}&dopost=listArchives&arcrank=-1';\" value='稿件审核' />";
}

$query = "Select arc.aid,arc.aid as id,arc.typeid,arc.arcrank,arc.flag,arc.senddate,arc.channel,arc.title,arc.mid,arc.click,tp.typename,ch.typename as channelname,adm.userid as adminname
from `$listtable` arc
left join `#@__arctype` tp on tp.id=arc.typeid
left join `#@__channeltype` ch on ch.id=arc.channel
left join `#@__member` adm on adm.mid=arc.mid
$whereSql
order by arc.aid desc";

$dlist = new DataListCP();
$dlist->pageSize = 20;
$dlist->SetParameter("dopost","listArchives");
$dlist->SetParameter("keyword",$keyword);
$dlist->SetParameter("cid",$cid);
$dlist->SetParameter("channelid",$channelid);
$dlist->SetTemplate(DEDEADMIN."/templets/content_sg_list.htm");
$dlist->SetSource($query);
$dlist->Display();
$dlist->Close();

?>