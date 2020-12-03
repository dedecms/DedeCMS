<?php
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);
require_once(DEDEINC."/datalistcp.class.php");
setcookie("ENV_GOBACK_URL",GetCurUrl(),time()+3600,"/");
$type = isset($type) ? trim($type) : '';

if($type == '')
{
	$sql = "Select * From `#@__member_stow` where mid='".$cfg_ml->M_ID."' order by id desc";
	$tpl = 'mystow';
}
else
{
	$sql = "select *,count(aid) as num from #@__member_stow group by aid order by num desc";
	$tpl = 'stowtop';
}

$dlist = new DataListCP();
$dlist->pageSize = 20;
$dlist->SetTemplate(DEDEMEMBER."/templets/$tpl.htm");
$dlist->SetSource($sql);
$dlist->Display();
?>