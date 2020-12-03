<?php

/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @rcsfile 	$RCSfile: story_edit.php,v $
 * @revision 	$Revision: 1.1 $
 * @date 	$Date: 2009/08/04 04:07:17 $
 */

require_once(dirname(__FILE__)."/config.php");
CheckPurview('story_Edit');
if(!isset($action))
{
	$action = '';
}

//读取所有栏目
$dsql->SetQuery("Select id,classname,pid,rank From #@__story_catalog order by rank asc");
$dsql->Execute();
$ranks = Array();
$btypes = Array();
$stypes = Array();
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
}
$lastid = $row['id'];
$msg = '';
$books = $dsql->GetOne("Select S.*,A.membername From #@__arcrank as A left join #@__story_books as S on A.rank=S.arcrank where S.bid='$bookid' ");
require_once(DEDEADMIN.'/templets/story_edit.htm');
?>