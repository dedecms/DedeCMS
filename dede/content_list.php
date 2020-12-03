<?php

/**
 *
 * content_s_list.php、content_i_list.php、content_select_list.php
 * 均使用本文件作为实际处理代码，只是使用的模板不同，如有相关变动，只需改本文件及相关模板即可
 *
 */
 
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
if(!isset($dopost))
{
	$dopost = '';
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

//
//在不指定排序条件和关键字的情况下直接统计微表
//
if(empty($totalresult) && empty($keyword) && empty($orderby))
{
	$tinyQuerys = array();
	
	if(!empty($channelid) && empty($cid))
	{
		$tinyQuerys[] = " channel='$channelid' ";
	}
	else
	{
		$tinyQuerys[] = " channel>0 ";
	}
	
	if(!empty($arcrank))
	{
		$tinyQuerys[] = " arcrank='$arcrank' ";
	}
	else
	{
		$tinyQuerys[] = " arcrank > -2 ";
	}
	
	if(!empty($mid))
	{
		$tinyQuerys[] = " mid='$mid' ";
	}
	
	if(!empty($cid))
	{
		$tinyQuerys[] = " typeid in(".GetSonIds($cid).") ";
	}
	
	if(count($tinyQuerys)>0)
	{
		$tinyQuery = "where ".join(' And ',$tinyQuerys);
	}
	
	$arr = $dsql->GetOne("Select count(*) as dd From `#@__arctiny` $tinyQuery ");

	$totalresult = $arr['dd'];
}

if($cid==0)
{
	if($channelid==0)
	{
		$positionname = '所有栏目&gt;';
	}
	else
	{
		$row = $tl->dsql->GetOne("Select id,typename,maintable From `#@__channeltype` where id='$channelid'");
		$positionname = $row['typename']." &gt; ";
		$maintable = $row['maintable'];
		$channelid = $row['id'];
	}
}
else
{
	$positionname = str_replace($cfg_list_symbol," &gt; ",$tl->GetPositionName())." &gt; ";
}

//当选择的是单表模型栏目时，直接跳转到单表模型管理区
if(empty($channelid) 
  && isset($tl->TypeInfos['channeltype']))
{
	$channelid = $tl->TypeInfos['channeltype'];
}
if($channelid < -1 )
{
	header("location:content_sg_list.php?cid=$cid&channelid=$channelid&keyword=$keyword");
	exit();
}

$optionarr = $tl->GetOptionArray($cid,$cuserLogin->getUserChannel(),$channelid);
$whereSql = empty($channelid) ? " where arc.channel > 0  And arc.arcrank > -2 " : " where arc.channel = '$channelid' And arc.arcrank > -2 ";
if(!empty($mid))
{
	$whereSql .= " And arc.mid = '$mid' ";
}
if($keyword!='')
{
	$whereSql .= " And ( CONCAT(arc.title,arc.writer) like '%$keyword%') ";
}
if($cid!=0)
{
	$whereSql .= " And arc.typeid in (".GetSonIds($cid).")";
}
if($arcrank!='')
{
	$whereSql .= " And arc.arcrank = '$arcrank' ";
	$CheckUserSend = "<input type='button' class='coolbg np' onClick=\"location='catalog_do.php?cid=".$cid."&dopost=listArchives&gurl=content_list.php';\" value='所有文档' />";
}
else
{
	$CheckUserSend = "<input type='button' class='coolbg np' onClick=\"location='catalog_do.php?cid=".$cid."&dopost=listArchives&arcrank=-1&gurl=content_list.php';\" value='稿件审核' />";
}

$query = "Select arc.id,arc.typeid,arc.senddate,arc.flag,arc.ismake,
arc.channel,arc.arcrank,arc.click,arc.title,arc.color,arc.litpic,arc.pubdate,arc.mid,
tp.typename,ch.typename as channelname,mb.uname as adminname
from `$maintable` arc
left join `#@__arctype` tp on tp.id=arc.typeid
left join `#@__channeltype` ch on ch.id=arc.channel
left join `#@__member` mb on mb.mid=arc.mid
$whereSql
order by arc.id desc";

if(empty($f) || !ereg('form', $f)) $f = 'form1.arcid1';
$dlist = new DataListCP();
$dlist->pageSize = 30;
$dlist->SetParameter("dopost","listArchives");
$dlist->SetParameter("keyword",$keyword);
$dlist->SetParameter("cid",$cid);
$dlist->SetParameter("arcrank",$arcrank);
$dlist->SetParameter("channelid",$channelid);
$dlist->SetParameter("f", $f);
if(empty($s_tmplets))
{
	$s_tmplets = "templets/content_list.htm";
}
$dlist->SetTemplate(DEDEADMIN."/".$s_tmplets);
$dlist->SetSource($query);
$dlist->Display();
$dlist->Close();

?>