<?php
require(dirname(__FILE__)."/config.php");
CheckPurview('plus_投票模块');
require_once(DEDEINC."/dedetag.class.php");
if(empty($dopost))
{
	$dopost="";
}
$aid = isset($aid) && is_numeric($aid) ? $aid : 0;
$ENV_GOBACK_URL = empty($_COOKIE['ENV_GOBACK_URL']) ? "vote_main.php" : $_COOKIE['ENV_GOBACK_URL'];
if($dopost=="delete")
{
	if($dsql->ExecuteNoneQuery("Delete From #@__vote where aid='$aid'"))
	{
		ShowMsg('成功删除一组投票!',$ENV_GOBACK_URL);
	}
	else
	{
		ShowMsg('指定删除投票不存在!',$ENV_GOBACK_URL);
	}
}
else if($dopost=="saveedit")
{
	$starttime = GetMkTime($starttime);
	$endtime = GetMkTime($endtime);
	$query = "Update #@__vote set votename='$votename',
		starttime='$starttime',
		endtime='$endtime',
		totalcount='$totalcount',
		ismore='$ismore',
		votenote='$votenote' where aid='$aid'
		";
	if($dsql->ExecuteNoneQuery($query))
	{
		ShowMsg('成功更改一组投票!',$ENV_GOBACK_URL);
	}
	else
	{
		ShowMsg('更改一组投票失败!',$ENV_GOBACK_URL);
	}
}
else
{
	$row = $dsql->GetOne("Select * From #@__vote where aid='$aid'");
	if(!is_array($row))
	{
		ShowMsg('指定投票不存在！','-1');
		exit();
	}
	include DedeInclude('templets/vote_edit.htm');
}

?>