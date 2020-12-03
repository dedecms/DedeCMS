<?php
require(dirname(__FILE__)."/config.php");
CheckPurview('sys_Task');
if(empty($dopost)) $dopost = '';

if($dopost=='save')
{
	$starttime = empty($starttime) ? 0 : GetMkTime($starttime);
	$endtime = empty($endtime) ? 0 : GetMkTime($endtime);
	$runtime = $h.':'.$m;
	$query = "Update `#@__sys_task`
	set `taskname` = '$taskname',
	`dourl` = '$dourl',
	`islock` = '$nislock',
	`runtype` = '$runtype',
	`runtime` = '$runtime',
	`starttime` = '$starttime',
	`endtime` = '$endtime',
	`freq` = '$freq',
	`description` = '$description',
	`parameter` = '$parameter'
	where id='$id' ";
	$rs = $dsql->ExecuteNoneQuery($query);
	if($rs) 
	{
		ShowMsg('成功修改一个任务!', 'sys_task.php');
	}
	else
	{
		ShowMsg('修改任务失败!'.$dsql->GetError(), 'javascript:;');
	}
	exit();
}

$row = $dsql->GetOne("Select * From `#@__sys_task` where id='$id' ");
include DedeInclude('templets/sys_task_edit.htm');

?>