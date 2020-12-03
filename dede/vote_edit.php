<?php 
require(dirname(__FILE__)."/config.php");
CheckPurview('plus_投票模块');
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
if(empty($dopost)) $dopost="";
if(empty($aid)) $aid="";
$aid = trim(ereg_replace("[^0-9]","",$aid));
if($aid==""){
	ShowMsg('你没有指定投票ID！','-1');
	exit();
}
if(!empty($_COOKIE['ENV_GOBACK_URL'])) $ENV_GOBACK_URL = $_COOKIE['ENV_GOBACK_URL'];
else $ENV_GOBACK_URL = "vote_main.php";
///////////////////////////////////////
if($dopost=="delete")
{
	$dsql = new DedeSql(false);
	$dsql->SetQuery("Delete From #@__vote where aid='$aid'");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg('成功删除一组投票!',$ENV_GOBACK_URL);
	exit();
}
else if($dopost=="saveedit")
{
	$dsql = new DedeSql(false);
	$starttime = GetMkTime($starttime);
	$endtime = GetMkTime($endtime);
	$query = "Update #@__vote set votename='$votename',
	starttime='$starttime',
	endtime='$endtime',
	totalcount='$totalcount',
	ismore='$ismore',
	votenote='$votenote' where aid='$aid'";
	$dsql->SetQuery($query);
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg('成功更改一组投票!',$ENV_GOBACK_URL);
	exit();
}
$dsql = new DedeSql(false);
$row = $dsql->GetOne("Select * From #@__vote where aid='$aid'");

require_once(dirname(__FILE__)."/templets/vote_edit.htm");

ClearAllLink();
?>