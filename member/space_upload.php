<?php 
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);
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

function GetImageView($furl,$mtype){
	if($mtype==1){
		return "<img src='$furl' width='80' border='0' /><br />";
	}
}


if(empty($keyword)) $keyword = "";
else{
	$keyword = cn_substr(trim(ereg_replace($cfg_egstr,"",stripslashes($keyword))),30);
	$keyword = addslashes($keyword);
}

$addsql = " where memberID='".$cfg_ml->M_ID."' And title like '%$keyword%' ";

if(empty($mediatype)) $mediatype = 0;
$mediatype = ereg_replace("[^0-9]","",$mediatype);
if($mediatype>1) $addsql .= " And mediatype='$mediatype' ";

$sql = "Select * From #@__uploads $addsql order by aid desc";

$dlist = new DataList();
$dlist->pageSize = 5;
$dlist->Init();
$dlist->SetParameter("mediatype",$mediatype);
$dlist->SetParameter("keyword",$keyword);
$dlist->SetSource($sql);
include(dirname(__FILE__)."/templets/space_upload.htm");
$dlist->Close();
?>