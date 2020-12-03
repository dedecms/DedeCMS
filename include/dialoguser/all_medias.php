<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../pub_datalist_dm.php");
require_once(dirname(__FILE__)."/../inc_functions.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");

if(empty($f)) $f = '';
if(empty($v)) $v ='';
if(empty($mediatype)) $mediatype = 0;

function MediaType($tid,$nurl)
{
	if($tid==1) return "图片<a href=\"javascript:;\" onClick=\"ChangeImage('$nurl');\"><img src='../dialog/img/picviewnone.gif' name='picview' border='0' alt='预览'></a>";
	else if($tid==2) return "FLASH";
	else if($tid==3) return "视频/音频";
	else return "附件/其它";
}

function GetFileSize($fs){
	$fs = $fs/1024;
	return sprintf("%10.1f",$fs)." K";
}


if(empty($keyword)) $keyword = "";
$addsql = " where (title like '%$keyword%' Or url like '%$keyword%') ";

if($mediatype==2) $addsql .= " And (mediatype='2' OR mediatype='3') ";
else if($mediatype>0) $addsql .= " And mediatype='$mediatype' ";

$addsql .= " And memberid='{$cfg_ml->M_ID}' ";

$sql = "Select aid,title,url,mediatype,filesize,uptime From #@__uploads $addsql order by aid desc";

//echo $sql;

$dlist = new DataList();
$dlist->pageSize = 20;
$dlist->Init();
$dlist->SetParameter("mediatype",$mediatype);
$dlist->SetParameter("keyword",$keyword);
$dlist->SetParameter("f",$f);
$dlist->SetSource($sql);
include(dirname(__FILE__)."/all_medias.htm");
$dlist->Close();
?>