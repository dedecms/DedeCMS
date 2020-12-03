<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_MakeHtml');
require_once(DEDEINC."/arc.rssview.class.php");
if(empty($tid))
{
	$tid = 0;
}
if(empty($maxrecord))
{
	$maxrecord = 50;
}
$row = $dsql->GetOne("Select id From `#@__arctype` where id>'$tid' And ispart<>2 order by id asc limit 0,1;");
if(!is_array($row))
{
	echo "完成所有文件更新！";
}
else
{
	$rv = new RssView($row['id'],$maxrecord);
	$rssurl = $rv->MakeRss();
	$tid = $row['id'];
	ShowMsg("成功更新".$rssurl."，继续进行操作！","makehtml_rss_action.php?tid=$tid&maxrecord=$maxrecord",0,100);
}

?>