<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_MakeHtml');
require_once(dirname(__FILE__)."/../include/inc_freelist_view.php");

if(empty($startid)) $startid = 0;
$ci = " aid >= $startid ";
if(!empty($endid) && $endid>=$startid){
	$ci .= " And aid <= $endid ";
}

header("Content-Type: text/html; charset={$cfg_ver_lang}");

$dsql = new DedeSql(false);
$dsql->SetQuery("Select aid From #@__freelist where $ci");
$dsql->Execute();
while($row=$dsql->GetArray()) $idArray[] = $row['aid'];
$dsql->Close();

if(!isset($pageno)) $pageno=0;
$totalpage=count($idArray);

if(isset($idArray[$pageno])){
	$lid = $idArray[$pageno];
}else{
	echo "完成所有文件创建！";
	exit();
}

$lv = new FreeList($lid);

$ntotalpage = $lv->TotalPage;

if(empty($mkpage)) $mkpage = 1;
if(empty($maxpagesize)) $maxpagesize = 50;

//如果栏目的文档太多，分多批次更新
if($ntotalpage<=$maxpagesize){
	$lv->MakeHtml();
	//echo 'dd';
	$finishType = true;
}else{
	$lv->MakeHtml($mkpage,$maxpagesize);
	//echo 'ee';
	$finishType = false;
	$mkpage = $mkpage + $maxpagesize;
	if( $mkpage >= ($ntotalpage+1) ) $finishType = true;
}

$lv->Close();

$nextpage = $pageno+1;

if($nextpage==$totalpage){
	echo "完成所有文件创建！";
}else{
	if($finishType){
	  $gourl = "makehtml_freelist_action.php?maxpagesize=$maxpagesize&startid=$startid&endid=$endid&pageno=$nextpage";
	  ShowMsg("成功创建列表：".$tid."，继续进行操作！",$gourl,0,100);
  }
  else
  {
  	$gourl = "makehtml_freelist_action.php?mkpage=$mkpage&maxpagesize=$maxpagesize&startid=$startid&endid=$endid&pageno=$pageno";
	  ShowMsg("列表：".$tid."，继续进行操作...",$gourl,0,100);
  }
}

ClearAllLink();
?>