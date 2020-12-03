<?php
require_once(dirname(__FILE__)."/config.php");
$dsql = new DedeSql(false);

if(!isset($check)){
	$check = 0;
}
if(!isset($tid2)){
	$tid2 = 0;
}
if(!isset($tid)){
	$tid = 0;
}


if(empty($action)){
require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");

if($check == 1)
{
	$wheresql = "where  ifcheck=0";
}else{
	$wheresql = "where  ifcheck>=0";
}
if($tid2){
	$wheresql .= " and tid2=$tid2";
}elseif($tid){
	$wheresql .= " and tid=$tid";
}

$query = "select * from #@__askanswer $wheresql order by id desc";
updatecount();
$dlist = new DataList();
$dlist->pageSize = 20;

$dlist->SetParameter("tid",$tid);
$dlist->SetParameter("tid2",$tid2);
$dlist->SetParameter("check",$check);

$dlist->SetSource($query);
include(dirname(__FILE__)."/templets/answeradmin.htm");
$dlist->Close();

}elseif($action == 'delete'){

	CheckPurview('a_Del,a_AccDel,a_MyDel,sys_ArcBatch');
	require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
	if(empty($fmdo)) $fmdo = "";
	if($fmdo=="yes")
	{
	  if( $aid!="" && !ereg("(".$aid."`|`".$aid.")",$qstr) ) $qstr .= "`".$aid;
	  if($qstr==""){
	  	ShowMsg("参数无效！",'-1');
	  	exit();
	  }
	  $qstrs = explode("`",$qstr);
	  $okaids = Array();
	  $dsql = new DedeSql(false);
	  foreach($qstrs as $aid){
	    if(!isset($okaids[$aid])){
		  $dsql->SetQuery("delete from #@__askanswer where id='$aid'");
		  $dsql->ExecuteNoneQuery();
	    }else{
	    	$okaids[$aid] = 1;
	    }
    }
    updatecount();
    $dsql->Close();
    ShowMsg("成功删除指定的回答！",'answeradmin.php');
	  exit();
  }//确定刪除操作完成

  //删除确认消息
  //-----------------------
	$wintitle = "文档管理-删除答案";
	$wecome_info = "<a href='".$ENV_GOBACK_URL."'>文档管理</a>::删除答案";
	$win = new OxWindow();
	$win->Init("answeradmin.php","js/blank.js","POST");
	$win->AddHidden("fmdo","yes");
	$win->AddHidden("action",$action);
	$win->AddHidden("qstr",$qstr);
	$win->AddHidden("aid",$aid);
	$win->AddTitle("你确实要删除“ $qstr 和 $aid ”这些回答？");
	$winform = $win->GetWindow("ok");
	$win->Display();

}elseif($action == 'check'){
	CheckPurview('a_Commend,sys_ArcBatch');
	if( $aid!="" && !ereg("(".$aid."`|`".$aid.")",$qstr) ) $qstr .= "`".$aid;
	if($qstr==""){
	  ShowMsg("参数无效！",'-1');
	  exit();
	}
	$qstrs = explode("`",$qstr);
	$dsql = new DedeSql(false);
	foreach($qstrs as $aid){
	  $aid = ereg_replace("[^0-9]","",$aid);
	  if($aid=="") continue;
	  $dsql->SetQuery("Update #@__askanswer set ifcheck='1' where id='$aid' and ifcheck='0'");
	  $dsql->ExecuteNoneQuery();
	}
	$dsql->Close();
	ShowMsg("审核成功",'answeradmin.php');
	exit();

}


function updatecount()
{
	global $dsql;
	$dsql->SetQuery("select id, reid from #@__asktype");
	$dsql->Execute('asktype');
	while($row = $dsql->getarray('asktype'))
	{
		if($row['reid'] == 0){
			$dsql->SetQuery("select count(*) as dd from #@__ask where tid=$row[id]");
			$dsql->Execute('top');
			$asknum = $dsql->getarray('top');
			$dsql->ExecuteNoneQuery("update #@__asktype set asknum=".$asknum['dd']." where id=".$row['id']);
		}else{
			$dsql->SetQuery("select count(*) as dd from #@__ask where tid2=$row[id]");
			$dsql->Execute('sub');
			$asknum = $dsql->getarray('sub');
			$dsql->ExecuteNoneQuery("update #@__asktype set asknum=".$asknum['dd']." where id=".$row['id']);
		}
	}
}


ClearAllLink();

?>