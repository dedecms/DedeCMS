<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Group');
if(!empty($dopost))
{
	$row = $dsql->GetOne("Select * From #@__admintype where rank='".$rankid."'");
	if(is_array($row))
	{
		ShowMsg("你所创建的组别的级别值已存在，不允许重复!","-1");
		exit();
	}
	$AllPurviews = "";
	if(is_array($purviews))
	{
		foreach($purviews as $pur)
		{
			$AllPurviews = $pur.' ';
		}
		$AllPurviews = trim($AllPurviews);
	}
	$dsql->ExecuteNoneQuery("INSERT INTO #@__admintype(rank,typename,system,purviews) VALUES ('$rankid','$groupname', 0, '$AllPurviews');");
	ShowMsg("成功创建一个新的用户组!","sys_group.php");
	exit();
}
include DedeInclude('templets/sys_group_add.htm');

?>