<?php
//系统设置为维护状态可访问
$cfg_IsCanView = true;
require(dirname(__FILE__)."/../include/config_base.php");
if(!empty($artID)) $arcID = $artID;
if(!isset($arcID)) $arcID = "";
if(!isset($arcurl)) $arcurl = '';
//echo $arcurl;exit;
$arcID = intval($arcID);
if(empty($arcID) && empty($arcurl)) exit();

require_once(dirname(__FILE__)."/../include/pub_datalist.php");

$dlist = new DataList();

$urlindex = 0;
if(empty($arcID))
{
	$row = $dlist->dsql->GetOne("Select id From `#@__cache_feedbackurl` where url='$arcurl' ");
	if(is_array($row)) $urlindex = $row['id'];
}
if(empty($arcID) && empty($urlindex)) exit();
//Javascript内容屏蔽函数
function cnw_left_safe($str,$len)
{
  $str = cnw_left($str,$len);
  $str = ereg_replace("['\"\r\n]","",$str);
  return $str;
}

//返回的评论条数
//--------------
if(empty($arcID)) $wq = " urlindex = '$urlindex' "; 
else $wq = " aid='$arcID' ";
 $querystring = "select * from `#@__feedback` where $wq and ischeck='1' order by dtime desc";
$dlist->Init();
$dlist->SetSource($querystring);
$dlist->SetTemplet($cfg_basedir.$cfg_templets_dir."/plus/feedback_templet_js.htm");
$dlist->display();
$dlist->Close();
?>