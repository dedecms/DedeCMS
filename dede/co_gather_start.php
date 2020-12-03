<?php
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEINC."/dedecollection.class.php");
if(!empty($nid))
{
	$ntitle = '采集指定节点：';
	$nid = intval($nid);
	$co = new DedeCollection();
	$co->LoadNote($nid);
	$row = $dsql->GetOne("Select count(aid) as dd From `#@__co_htmls` where nid='$nid'; ");
	if($row['dd']==0)
	{
		$unum = "没有记录或从来没有采集过这个节点！";
	}
	else
	{
		$unum = "共有 {$row['dd']} 个历史种子网址！<a href='javascript:SubmitNew();'>[<u>更新种子网址，并采集</u>]</a>";
	}
}
else
{
	$ntitle = '监控式采集：';
	$unum = "没指定采集节点，将使用检测新内容采集模式！";
}
include DedeInclude('templets/co_gather_start.htm');

?>