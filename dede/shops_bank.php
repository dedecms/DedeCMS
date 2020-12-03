<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Bank');
require_once(DEDEINC."/datalistcp.class.php");

//配送方式设置
if(!isset($do))
{
	$do ='';
}
if($do == 'edit')
{
	$pid = intval($pid);
	$row = $dsql->GetOne("SELECT pid,paytype FROM #@__shops_paytype WHERE pid='$pid' LIMIT 0,1");
	if(!is_array($row))
	{
		ShowMsg("方式不存在!","shops_bank.php");
		exit();
	}
	$des = addslashes($des);
	$dsql->ExecuteNoneQuery("UPDATE #@__shops_paytype SET des='$des' WHERE pid='$pid'");
	ShowMsg("成功修改!","shops_bank.php");
	exit();
}
$infos = array();
$dsql->SetQuery("SELECT pid,des FROM #@__shops_paytype ORDER BY pid ASC");
$dsql->Execute();
while($row = $dsql->GetArray())
{
	if($row['pid'] > 2 && $row['pid'] < 5)
	{
		$infos[] = $row['des'];
	}
	else
	{
		continue;
	}
}
include(DEDEADMIN."/templets/shops_bank.htm");

?>