<?php
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/com_config.php");
$step = (empty($step) ? '' : $step);
$dsql = new DedeSql(false);
if($step !=2){
$com = $dsql->getone("select comname from #@__member_cominfo where id={$cfg_ml->M_ID}");
$comname = $com['comname'];
include(dirname(__FILE__)."/templets/addjob.htm");

}else{
	require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");

	$memberid = $cfg_ml->M_ID;
	$pubdate = mytime();
	$endtime = $pubdate + $endtime * 86400;
	$name = trim(filterscript($name));
	$job = trim(filterscript($job));
	$nums = intval($nums);
	$department = trim(filterscript($department));
	$address = trim(filterscript($address));
	$salaries = intval($salaries);

	if($name == ''){
		showmsg('公司名称不能为空','-1');
		exit();
	}
	if($address == ''){
		showmsg('工作地址不能为空','-1');
		exit();
	}
	if($message == ''){
		showmsg('职位描述不能为空','-1');
		exit();
	}


	$sql = "insert into #@__jobs(name, job, nums, department, address, pubdate, endtime, salaries, message, memberID)
	values('$name', '$job', '$nums', '$department', '$address', '$pubdate', '$endtime', '$salaries', '$message', '$memberid')";

	if($dsql->ExecuteNoneQuery($sql))
	{
		$id = $dsql->GetLastID();
		$msg = "
		　　请选择你的后续操作：
		<a href='addjob.php'><u>继续发布招聘信息</u></a>
		&nbsp;&nbsp;
		<a href='index.php?id=$memberid&type=job&jobid=$id' target='_blank'><u>查看招聘信息</u></a>
		&nbsp;&nbsp;
		<a href='editjob.php?id=".$id."'><u>修改招聘信息</u></a>
		";
		$wintitle = "成功发布招聘信息！";
		$wecome_info = "文章管理::发布招聘信息";
		$win = new OxWindow();
		$win->AddTitle("成功发布招聘信息：");
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