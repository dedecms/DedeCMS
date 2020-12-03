<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_ArcTj');
$row1 = $dsql->GetOne("Select count(*) as dd From `#@__arctiny` ");
$row2 = $dsql->GetOne("Select count(*) as dd From `#@__feedback` ");
$row3 = $dsql->GetOne("Select count(*) as dd From `#@__member` ");

function GetArchives($dsql,$ordertype)
{
	$starttime = time() - (24*3600*30);
	if($ordertype=='monthFeedback' ||$ordertype=='monthHot')
	{
		$swhere = " where senddate>$starttime ";
	}
	else
	{
		$swhere = "";
	}
	if(eregi('feedback',$ordertype))
	{
		$ordersql = " order by scores desc ";
	}
	else
	{
		$ordersql = " order by click desc ";
	}
	$query = "Select id,title,click,scores From #@__archives $swhere $ordersql limit 0,20 ";
	$dsql->SetQuery($query);
	$dsql->Execute('ga');
	while($row = $dsql->GetObject('ga'))
	{
		if(eregi('feedback',$ordertype))
		{
			$moreinfo = "[<a target='_blank' href='".$GLOBALS['cfg_phpurl']."/feedback.php?aid={$row->id}'><u>评论：{$row->scores}</u></a>]";
		}
		else
		{
			$moreinfo = "[点击：{$row->click}]";
		}
		echo "·<a href='archives_do.php?aid={$row->id}&dopost=viewArchives' target='_blank'>";
		echo cn_substr($row->title,30)."</a>{$moreinfo}<br/>\r\n";
	}
}
include DedeInclude('templets/content_tj.htm');

?>