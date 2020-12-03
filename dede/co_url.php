<?php
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEINC."/datalistcp.class.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
$where = "";
if(!isset($nid))
{
	$nid="";
}
if(!empty($nid))
{
	$where = " where cu.nid='$nid' ";
}
if(empty($small))
{
	$small = 0;
}
if($nid!='')
{
	$exportbt = "
	<input type='button' name='b0' value='导出内容' class='coolbg np'
	style='width:80px' onClick=\"location.href='co_export.php?nid=$nid';\" />
	<input type='button' name='b0' value='采集本节点' class='coolbg np'
	style='width:80px' onClick=\"location.href='co_gather_start.php?nid=$nid';\" />
	";
}
else
{
	$exportbt = "";
}
$sql = "Select cu.aid,cu.nid,cu.isexport as isex,cu.title,cu.url,cu.dtime,cu.isdown,cn.notename,tp.typename From `#@__co_htmls` cu
left join `#@__co_note` cn on cn.nid=cu.nid
left join `#@__arctype` tp on tp.id=cu.typeid
$where order by cu.aid desc";
$dlist = new DataListCP();
$dlist->SetParameter("nid",$nid);
$dlist->SetParameter("small",$small);
if($small==0)
{
	$dlist->SetTemplate(DEDEADMIN."/templets/co_url.htm");
}
else
{
	$dlist->SetTemplate(DEDEADMIN."/templets/co_url_2.htm");
}
$dlist->SetSource($sql);
$dlist->display();

function IsDownLoad($isd)
{
	if($isd=="0")
	{
		return "未下载";
	}
	else
	{
		return "已下载";
	}
}

function IsExData($isex)
{
	if($isex==0)
	{
		return "未导出";
	}
	else
	{
		return "已导出";
	}
}

?>