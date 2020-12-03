<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Task');
if(empty($dopost))	$dopost = '';

//删除
if($dopost=='del')
{
	$dsql->ExecuteNoneQuery("Delete From `#@__sys_task` where id='$id' ");
	ShowMsg("成功删除一个任务！", "sys_task.php");
	exit();
}

include DedeInclude('templets/sys_task.htm');

?>