<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Log');
if(empty($dopost))
{
	ShowMsg("你没指定任何参数！","javascript:;");
	exit();
}
if(empty($dellog))
{
	$dellog = 0;
}

//删除选定状态
if($dopost=="del")
{
	$bkurl = isset($_COOKIE['ENV_GOBACK_URL']) ? $_COOKIE['ENV_GOBACK_URL'] : "member_msg_main.php";
	$ids = explode('`',$ids);
	$dquery = "";
	foreach($ids as $id)
	{
		if($dquery=="")
		{
			$dquery .= "id='$id' ";
		}
		else
		{
			$dquery .= " Or id='$id' ";
		}
	}
	if($dquery!="") $dquery = " where ".$dquery;
	$dsql->ExecuteNoneQuery("DELETE FROM #@__member_msg $dquery");
	ShowMsg("成功删除指定的记录！",$bkurl);
	exit();
}
//审核选定状态
else if($dopost=="check")
{
	$bkurl = isset($_COOKIE['ENV_GOBACK_URL']) ? $_COOKIE['ENV_GOBACK_URL'] : "member_msg_main.php";
	$ids = explode('`',$ids);
	$dquery = "";
	foreach($ids as $id)
	{
		if($dquery=="")
		{
			$dquery .= " id='$id' ";
		}
		else
		{
			$dquery .= " Or id='$id' ";
		}
	}
	if($dquery!="") $dquery = " where ".$dquery;
	$dsql->ExecuteNoneQuery("UPDATE #@__member_msg SET ischeck=1 $dquery");
	ShowMsg("成功审核指定的记录！",$bkurl);
	exit();
}
else
{
	ShowMsg("无法识别你的请求！","javascript:;");
	exit();
}

?>