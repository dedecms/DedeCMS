<?php

/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @rcsfile 	$RCSfile: index.php,v $
 * @revision 	$Revision: 1.1 $
 * @date 	$Date: 2009/08/04 04:06:20 $
 */

require_once dirname(__FILE__).'/include/common.inc.php';
require_once dirname(__FILE__).'/include/asktype.inc.php';
$nav = $sitename;

$solvingnum = 0; //未解决的问题数
$solvednum = 0;  //已解决的问题数
$query = "select status,count(status) as dd from `#@__ask` group by status ";
$dsql->Execute('me',$query);
while($tmparr = $dsql->GetArray())
{
	if($tmparr['status']==0)
	{
		$solvingnum = $tmparr['dd'];
	}
	else
	{
		$solvednum += $tmparr['dd'];
	}
}

$dtp = new DedeTemplate();
$dtp->LoadTemplate(DEDEASK.'template/default/index.htm');
if($cfg_ask_rewrite=='Y')
{
	$dtp->Display();
	myecho();
	exit();
}
else
{
	$dtp->Display();
}
?>