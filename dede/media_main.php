<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
require_once(dirname(__FILE__)."/../include/inc_functions.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");

function MediaType($tid,$nurl)
{
	if($tid==1) return "图片";
	else if($tid==2) return "FLASH";
	else if($tid==3) return "视频/音频";
	else return "附件/其它";
}

function GetFileSize($fs){
	$fs = $fs/1024;
	return sprintf("%10.1f",$fs)." K";
}

function UploadAdmin($adminid,$memberid)
{
	if($adminid!='') return $adminid;
	else return $memberid;
}


if(empty($keyword)) $keyword = "";
$addsql = " where (u.title like '%$keyword%' Or u.url like '%$keyword%') ";

if(empty($membertype)) $membertype = 0;
if($membertype==1) $addsql .= " And u.adminID>0 ";
else if($membertype==2) $addsql .= " And u.memberID>0 ";

if(empty($mediatype)) $mediatype = 0;
if($mediatype>1) $addsql .= " And u.mediatype='$membertype' ";

if(!empty($memberid)) $addsql .= " And u.memberID='$memberid' ";
else $memberid = 0;

if(!empty($memberid)) $addsql .= " And u.memberid='$memberid' ";

$sql = "Select u.aid,u.title,u.url,u.mediatype,u.filesize,u.memberID,u.uptime
,a.userid as adminname,m.userid as membername
From #@__uploads u
Left join #@__admin a on  a.ID = u.adminID
Left join #@__member m on m.ID = u.memberID
$addsql order by u.aid desc";

$dlist = new DataList();
$dlist->pageSize = 10;
$dlist->SetParameter("mediatype",$mediatype);
$dlist->SetParameter("keyword",$keyword);
$dlist->SetParameter("membertype",$membertype);
$dlist->SetParameter("memberid",$memberid);
$dlist->SetSource($sql);
include(dirname(__FILE__)."/templets/media_main.htm");
$dlist->Close();

ClearAllLink();
?>