<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_MakeHtml');
require_once(dirname(__FILE__)."/../include/inc_arclist_view.php");

/*
makehtml_list_action.php?
typeid=118
uptype=all
starttime=2007-01-16+15%3A24%3A05
maxpagesize=100
upnext=1
*/

if(!isset($upnext)) $upnext = 1;
if(empty($gotype)) $gotype = '';
if(empty($uptype)) $uptype = '';
if(empty($pageno)) $pageno=0;
if(empty($mkpage)) $mkpage = 1;
if(empty($typeid)) $typeid = 0;
if(empty($starttime)) $starttime = '';
if(empty($maxpagesize)) $maxpagesize = 50;
$adminID = $cuserLogin->getUserID();

header("Content-Type: text/html; charset={$cfg_ver_lang}");

$dsql = new DedeSql(false);
//普通生成
if($gotype=='')
{
  if($upnext==1 || $typeid==0){
    $tidss = TypeGetSunID($typeid,$dsql,"",0,true);
    $idArray = explode(',',$tidss);
  }else{
  	$idArray = array();
  	$idArray[] = $typeid;
  }
}
//一键更新
else if($gotype=='mkall')
{
	$mkcachefile = DEDEADMIN."/../data/mkall_cache_{$adminID}.php";
	$idArray = array();
	if(file_exists($mkcachefile)) include_once($mkcachefile);
}

$totalpage=count($idArray);
if(isset($idArray[$pageno])){
	$tid = $idArray[$pageno];
}else{
	if($gotype==''){
	  echo "完成所有文件创建！";
	  ClearAllLink();
	  exit();
	}else if($gotype=='mkall')
	{
		ShowMsg("完成所有栏目列表更新，现在作最后数据优化！","makehtml_all.php?action=make&step=10");
		ClearAllLink();
	  exit();
	}
}

//更新数组所记录的栏目
if(!empty($tid))
{	
  if($uptype=='all'||$uptype=='') $lv = new ListView($tid);
  else $lv = new ListView($tid,$starttime);

  if($lv->TypeLink->TypeInfos['ispart']==0 
  && $lv->TypeLink->TypeInfos['isdefault']!=-1)
  {
  	$ntotalpage = $lv->TotalPage;
  }else{

  	$ntotalpage = 1;
  }

  //如果栏目的文档太多，分多批次更新
  if($ntotalpage<=$maxpagesize || $lv->TypeLink->TypeInfos['ispart']!=0 
  || $lv->TypeLink->TypeInfos['isdefault']==-1)
  {
 	
	  $lv->MakeHtml();
	  $finishType = true;
  }
  else
  {

	   $lv->MakeHtml($mkpage,$maxpagesize);
	   $finishType = false;
	   $mkpage = $mkpage + $maxpagesize;
	   if( $mkpage >= ($ntotalpage+1) ) $finishType = true;
  }

}//!empty

$nextpage = $pageno+1;

if($nextpage >= $totalpage && $finishType)
{
	if($gotype=='')
	{
	   echo "<br>完成所有文件创建！";
	   ClearAllLink();
	   exit();
	}else if($gotype=='mkall')
	{
		 ShowMsg("完成所有栏目列表更新，现在作最后数据优化！","makehtml_all.php?action=make&step=10");
		 ClearAllLink();
	   exit();
	}
}else
{
	if($finishType){
	   $gourl = "makehtml_list_action.php?gotype={$gotype}&maxpagesize=$maxpagesize&typeid=$typeid&pageno=$nextpage&uptype=$uptype&starttime=".urlencode($starttime);
	   ClearAllLink();
	   ShowMsg("成功创建栏目：".$tid."，继续进行操作！",$gourl,0,100);
	   exit();
  }else{
  	 $gourl = "makehtml_list_action.php?gotype={$gotype}&mkpage=$mkpage&maxpagesize=$maxpagesize&typeid=$typeid&pageno=$pageno&uptype=$uptype&starttime=".urlencode($starttime);
	   ClearAllLink();
	   ShowMsg("栏目：".$tid."，继续进行操作...",$gourl,0,100);
	   exit();
  }
}
?>