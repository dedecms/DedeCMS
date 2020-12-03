<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('plus_站内新闻发布');
if(empty($dopost))
{
	$dopost = "";
}
$aid = ereg_replace("[^0-9]","",$aid);
if($dopost=="del")
{
	$dsql->ExecuteNoneQuery("Delete From #@__mynews where aid='$aid';");
	ShowMsg("成功删除一条站内新闻！","mynews_main.php");
	exit();
}
else if($dopost=="editsave")
{
	$inquery = "Update #@__mynews set title='$title',typeid='$typeid',writer='$writer',senddate='".GetMKTime($sdate)."',body='$body' where aid='$aid';";
	$dsql->ExecuteNoneQuery($inquery);
	ShowMsg("成功更改一条站内新闻！","mynews_main.php");
	exit();
}
$myNews = $dsql->GetOne("Select #@__mynews.*,#@__arctype.typename From #@__mynews left join #@__arctype on #@__arctype.id=#@__mynews.typeid where #@__mynews.aid='$aid';");
include DedeInclude('templets/mynews_edit.htm');

?>