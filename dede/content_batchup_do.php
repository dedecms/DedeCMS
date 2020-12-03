<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_ArcBatch');
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
require_once(dirname(__FILE__)."/inc/inc_batchup.php");
//typeid,startid,endid,seltime,starttime,endtime,action,newtypeid
//批量操作
//check del move makehtml
//获取ID条件
//------------------------
if(empty($startid)) $startid = 0;
if(empty($endid)) $endid = 0;
if(empty($seltime)) $seltime = 0;
//生成HTML操作由其它页面处理
if($action=="makehtml")
{
	$jumpurl  = "makehtml_archives_action.php?endid=$endid&startid=$startid";
  $jumpurl .= "&typeid=$typeid&pagesize=20&seltime=$seltime";
  $jumpurl .= "&stime=".urlencode($starttime)."&etime=".urlencode($endtime);
	header("Location: $jumpurl");
	exit();
}
$gwhere = " where arcrank=0 ";
if($startid >0 ) $gwhere .= " And ID>= $startid ";
if($endid > $startid) $gwhere .= " And ID<= $endid ";
$dsql = new DedeSql(false);
$idsql = "";
if($typeid!=0){
	$GLOBALS['idArray'] = array();
	$idArrary = TypeGetSunTypes($typeid,$dsql,0);
	if(is_array($idArrary)){
	  foreach($idArrary as $tid){
		  if($idsql=="") $idsql .= " typeid=$tid ";
		  else $idsql .= " or typeid=$tid ";
	  }
	  $gwhere .= " And ( ".$idsql." ) ";
  }
}
if($seltime==1){
	$t1 = GetMkTime($starttime);
	$t2 = GetMkTime($endtime);
	$gwhere .= " And (senddate >= $t1 And senddate <= $t2) ";
}
//指量审核
if($action=='check')
{
	 if(empty($startid)||empty($endid)){
	 	 ShowMsg('该操作必须指定起始ID！','javascript:;');	
	 	 exit();
	 }
	 $jumpurl  = "makehtml_archives_action.php?endid=$endid&startid=$startid";
   $jumpurl .= "&typeid=$typeid&pagesize=20&seltime=$seltime";
   $jumpurl .= "&stime=".urlencode($starttime)."&etime=".urlencode($endtime);
	 $dsql->SetQuery("Select ID,arcrank From #@__archives $gwhere");
   $dsql->Execute('c');
	 while($row = $dsql->GetObject('c')){
	 	 if($row->arcrank==-1) $dsql->ExecuteNoneQuery("Update #@__archives set arcrank=0 where ID='{$row->ID}'");
	 }
	 $dsql->Close();
	 ShowMsg("完成数据库的审核处理，准备更新HTML...",$jumpurl);
	 exit();
}
//批量删除
else if($action=='del')
{
  if(empty($startid)||empty($endid)){
	 	 ShowMsg('该操作必须指定起始ID！','javascript:;');	
	 	 exit();
	}
  $dsql->SetQuery("Select ID From #@__archives $gwhere");
  $dsql->Execute('x');
  $tdd = 0;
  while($row = $dsql->GetObject('x')){ if(DelArc($row->ID)) $tdd++; }
  $dsql->Close();
	ShowMsg("成功删除 $tdd 条记录！","javascript:;");
	exit();
}
//批量移动
else if($action=='move')
{
  if(empty($typeid)){
	 	 ShowMsg('该操作必须指定栏目！','javascript:;');	
	 	 exit();
	}
  $typeold = $dsql->GetOne("Select * From #@__arctype where ID='$typeid'; ");
  $typenew = $dsql->GetOne("Select * From #@__arctype where ID='$newtypeid'; ");
  if(!is_array($typenew)){
  	$dsql->Close();
    ShowMsg("无法检测移动到的新栏目的信息，不能完成操作！","javascript:;");
	  exit();
  }
  if($typenew['ispart']!=0){
  	$dsql->Close();
    ShowMsg("你不能把数据移动到非最终列表的栏目！","javascript:;");
	  exit();
  }
  if($typenew->channeltype!=$typeold->channeltype){
  	$dsql->Close();
    ShowMsg("不能把数据移动到内容类型不同的栏目！","javascript:;");
	  exit();
  }
  $gwhere .= " And channel='".$typenew['channeltype']."'";
  $dsql->SetQuery("Select ID From #@__archives $gwhere");
  $dsql->Execute('m');
  $tdd = 0;
  while($row = $dsql->GetObject('m')){
	 	 $rs = $dsql->ExecuteNoneQuery("Update #@__archives set typeid='$newtypeid' where ID='{$row->ID}'");
	   if($rs) $tdd++;
	   DelArc($row->ID,true);
	}
  $dsql->Close();
  if($tdd>0)
  {
  	$jumpurl  = "makehtml_archives_action.php?endid=$endid&startid=$startid";
    $jumpurl .= "&typeid=$newtypeid&pagesize=20&seltime=$seltime";
    $jumpurl .= "&stime=".urlencode($starttime)."&etime=".urlencode($endtime);
  	ShowMsg("成功移动 $tdd 条记录，准备重新生成HTML...",$jumpurl);
  }
  else ShowMsg("完成操作，没移动任何数据...","javascript:;");
	exit();
}
ClearAllLink();
?>