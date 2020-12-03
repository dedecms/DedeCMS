<?
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_MakeHtml');

require_once(dirname(__FILE__)."/../include/inc_arclist_view.php");

if(!isset($upnext)) $upnext = 1;

if($upnext==1)
{
	$dsql = new DedeSql(false);
	$idArrays = TypeGetSunTypes($typeid,$dsql,0);
	$dsql->Close();
	$i = 0;
  $idArray = "";
  foreach($idArrays as $k=>$v){
	  $idArray[$i] = $v; $i++;
  }
}
else
{
	$idArray[0] = $typeid;
}

if(!isset($pageno)) $pageno=0;
$totalpage=count($idArray);
if(isset($idArray[$pageno])) $tid = $idArray[$pageno];
else{
	echo "完成所有文件创建！";
	exit();
}

if($uptype=="all"||$uptype=="") $lv = new ListView($tid);
else $lv = new ListView($tid,$starttime);

if($lv->TypeLink->TypeInfos['ispart']==0
 && $lv->TypeLink->TypeInfos['isdefault']!=-1)
{ $ntotalpage = $lv->TotalPage; }
else{ $ntotalpage = 1; }

if(empty($mkpage)) $mkpage = 1;
if(empty($maxpagesize)) $maxpagesize = 50;

//如果栏目的文档太多，分多批次更新
if($ntotalpage<=$maxpagesize 
|| $lv->TypeLink->TypeInfos['ispart']!=0 
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


$lv->Close();

$nextpage = $pageno+1;
if($nextpage==$totalpage){
	echo "完成所有文件创建！";
}
else{
	if($finishType){
	  $gourl = "makehtml_list_action.php?maxpagesize=$maxpagesize&typeid=$typeid&pageno=$nextpage&uptype=$uptype&starttime=".urlencode($starttime);
	  ShowMsg("成功创建栏目：".$tid."，继续进行操作！",$gourl,0,100);
  }
  else
  {
  	$gourl = "makehtml_list_action.php?mkpage=$mkpage&maxpagesize=$maxpagesize&typeid=$typeid&pageno=$pageno&uptype=$uptype&starttime=".urlencode($starttime);
	  ShowMsg("栏目：".$tid."，继续进行操作...",$gourl,0,100);
  }
}
?>