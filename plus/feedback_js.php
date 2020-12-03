<?php 
require(dirname(__FILE__)."/../include/config_base.php");
if(!empty($artID)) $arcID = $artID;
if(!isset($arcID)) $arcID = "";
$arcID = ereg_replace("[^0-9]","",$arcID);
if(empty($arcID)){ exit(); }

function cnw_left_safe($str,$len)
{
  $str = cnw_mid($str,0,$len);
  $str = ereg_replace("['\"\r\n]","",$str);
  return $str;
}

//返回的评论条数
//--------------
require_once(dirname(__FILE__)."/../include/pub_datalist.php");
$querystring = "select * from #@__feedback where aid='$arcID' and ischeck='1' order by dtime desc";
$dlist = new DataList();
$dlist->Init();
$dlist->SetSource($querystring);
$dlist->SetTemplet($cfg_basedir.$cfg_templets_dir."/plus/feedback_templet_js.htm");
$dlist->display();
$dlist->Close();
?>