<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('plus_站内新闻发布');
if(empty($dopost)) $dopost = "";
$aid = ereg_replace("[^0-9]","",$aid);
$dsql = new DedeSql(false);
if($dopost=="del")
{
	 $dsql->SetQuery("Delete From #@__mynews where aid='$aid';");
	 $dsql->ExecuteNoneQuery();
	 $dsql->Close();
	 ShowMsg("成功删除一条站内新闻！","mynews_main.php");
	 exit();
}
else if($dopost=="editsave")
{
	$dsql->SetQuery("Update #@__mynews set title='$title',typeid='$typeid',writer='$writer',senddate='".GetMKTime($sdate)."',body='$body' where aid='$aid';");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("成功更改一条站内新闻！","mynews_main.php");
	exit();
}
$myNews = $dsql->GetOne("Select #@__mynews.*,#@__arctype.typename From #@__mynews left join #@__arctype on #@__arctype.ID=#@__mynews.typeid where #@__mynews.aid='$aid';");

require_once(dirname(__FILE__)."/templets/mynews_edit.htm");

ClearAllLink();
?>