<?php
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);
require_once(DEDEINC."/datalistcp.class.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
$keyword = empty($keyword) ? '' : FilterSearch($keyword);
$addsql = " where mid='".$cfg_ml->M_ID."' And title like '%$keyword%' ";
if(empty($mediatype))
{
	$mediatype = 0;
}
$mediatype = intval($mediatype);
if($mediatype>0)
{
	$addsql .= " And mediatype='$mediatype' ";
}
$sql = "Select * From `#@__uploads` $addsql order by aid desc";
$dlist = new DataListCP();
$dlist->pageSize = 5;
$dlist->SetParameter("mediatype",$mediatype);
$dlist->SetParameter("keyword",$keyword);
$dlist->SetTemplate(DEDEMEMBER."/templets/uploads.htm");
$dlist->SetSource($sql);
$dlist->Display();

function MediaType($tid,$nurl)
{
	if($tid==1)
	{
		return "图片";
	}
	else if($tid==2)
	{
		return "FLASH";
	}
	else if($tid==3)
	{
		return "视频/音频";
	}
	else
	{
		return "附件/其它";
	}
}
function GetFileSize($fs)
{
	$fs = $fs/1024;
	return sprintf("%10.1f",$fs)." K";
}
function GetImageView($furl,$mtype)
{
	if($mtype==1)
	{
		return "<img class='litPic' width='80' height='80' src='$furl'  border='0' /><br />";
	}
}
?>