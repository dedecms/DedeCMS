<?php 
require_once("config.php");
CheckPurview('t_Move');
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
if(empty($typeid)) $typeid="";
if(empty($job)) $job="movelist";
$typeid = ereg_replace("[^0-9]","",$typeid);
$dsql = new DedeSql(false);
$row  = $dsql->GetOne("Select reID,typename,channeltype From #@__arctype where ID='$typeid'");
$typename = $row['typename'];
$reID = $row['reID'];
$channelid = $row['channeltype'];
//移动栏目
//------------------
if($job=="moveok")
{
	if($typeid==$movetype)
	{
		$dsql->Close();
		ShowMsg("移对对象和目标位置相同！","catalog_main.php");
	  exit();
	}
	if(IsParent($movetype,$typeid,$dsql))
	{
		$dsql->Close();
		ShowMsg("不能从父类移动到子类！","catalog_main.php");
	  exit();
	}
	$dsql->SetQuery("Update #@__arctype set reID='$movetype' where ID='$typeid'");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	//更新树形菜单
   $rndtime = time();
   $uptreejs = "<script language='javascript'>
   if(window.navigator.userAgent.indexOf('MSIE')>=1){
     if(top.document.frames.menu.location.href.indexOf('catalog_menu.php')>=1)
     { top.document.frames.menu.location = 'catalog_menu.php?$rndtime'; }
   }else{
  	 if(top.document.getElementById('menu').src.indexOf('catalog_menu.php')>=1)
     { top.document.getElementById('menu').src = 'catalog_menu.php?$rndtime'; }
   }
   </script>";
   echo $uptreejs;
	 ShowMsg("成功移动目录！","catalog_main.php");
	 exit();
}
function IsParent($myid,$topid,$dsql)
{
	$row = $dsql->GetOne("select ID,reID from #@__arctype where ID='$myid'");
	if($row['reID']==$topid) return true;
	else if($row['reID']==0) return false;
	else return IsParent($row['reID'],$topid,$dsql);
}
///////////////////////////////////////////////////


$tl = new TypeLink($typeid);
$typeOptions = $tl->GetOptionArray(0,0,$channelid);
$tl->Close();

require_once(dirname(__FILE__)."/templets/catalog_move.htm");

ClearAllLink();
?>