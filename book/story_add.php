<?php

/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @rcsfile 	$RCSfile: story_add.php,v $
 * @revision 	$Revision: 1.1 $
 * @date 	$Date: 2009/08/04 04:06:24 $
 */

require_once(dirname(__FILE__)."/../member/config.php");
CheckRank(0,0);
$menutype = 'mydede';
if(!isset($action))
{
	$action = '';
}

//读取所有栏目
$dsql->SetQuery("Select id,classname,pid,rank,booktype From #@__story_catalog order by rank asc");
$dsql->Execute();
$ranks = Array();
$btypes = Array();
$stypes = Array();
$booktypes = Array();
while($row = $dsql->GetArray())
{
	if($row['pid']==0)
	{
		$btypes[$row['id']] = $row['classname'];
	}
	else
	{
		$stypes[$row['pid']][$row['id']] = $row['classname'];
	}
	$ranks[$row['id']] = $row['rank'];
	if($row['booktype']=='0')
	{
		$booktypes[$row['id']] = '小说';
	}
	else
	{
		$booktypes[$row['id']] = '漫画';
	}
}
$lastid = $row['id'];
$msg = '';
require_once(dirname(__FILE__)."/templets/book/story_add.htm");
?>