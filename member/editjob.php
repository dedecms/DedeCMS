<?php
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/com_config.php");

$dsql = new DedeSql(false);
$memberid = $cfg_ml->M_ID;
$id =  intval($id);
if($step !=2){
	$query = "select * from #@__jobs where id=$id and memberID=$memberid limit 1;";
	$job = $dsql->Getone($query);
	$job['endtime'] = @ceil(($job['endtime']-$job['pubdate'])/86400);
	include(dirname(__FILE__)."/templets/editjob.htm");
}else{
	require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
	$endtime = intval($endtime);
	$nums = intval($nums);
	$salaries = intval($salaries);
	$endtime = $endtime * 86400;
	$name = trim($name);
	$job = trim($job);
	$nums = trim($nums);
	$department = trim($department);
	$address = trim($address);
	$sql = "update #@__jobs set name='$name', job='$job', nums='$nums', department='$department', address='$address',
	endtime=pubdate+$endtime, salaries='$salaries', message='$message' where id='$id' and memberID='$memberid'";
	if($dsql->ExecuteNoneQuery($sql))
	{
		$msg = "
		　　请选择你的后续操作：
		<a href='addjob.php'><u>继续发布招聘信息</u></a>
		&nbsp;&nbsp;
		<a href='job.php?id=$id' target='_blank'><u>查看招聘信息</u></a>
		&nbsp;&nbsp;
		<a href=\"editjob.php?id=$id\"><u>修改招聘信息</u></a>
		";

		$wintitle = "成功发布文章！";
		$wecome_info = "文章管理::发布文章";
		$win = new OxWindow();
		$win->AddTitle("成功发布文章：");
		$win->AddMsgItem($msg);
		$winform = $win->GetWindow("hand","&nbsp;",false);
		$win->Display();
	}else{
		$dsql->Close();
		ShowMsg("把数据保存到数据库表jobs时出错，请检查原因！","-1");
		exit();
	}



}








?>