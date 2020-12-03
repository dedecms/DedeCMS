<?php
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);
require_once(DEDEINC."/datalistcp.class.php");
setcookie("ENV_GOBACK_URL",GetCurUrl(),time()+3600,"/");
$type = empty($type)? "sys" : trim($type);
$tpl = '';
$menutype = 'mydede';
$rank = empty($rank)? "" : $rank;
if($rank == 'top'){
	$sql = "select s.*,count(s.aid) as num,t.*  from #@__member_stow as s left join `#@__member_stowtype` as t on t.stowname=s.type where s.type='$type' group by s.aid order by num desc";
	$tpl = 'stowtop';
}else{
	$sql = "Select s.*,t.* From `#@__member_stow` as s left join `#@__member_stowtype` as t on t.stowname=s.type  where s.mid='".$cfg_ml->M_ID."' AND s.type='$type' order by s.id desc";
	$tpl = 'mystow';
}

$dsql->Execute('nn','Select indexname,stowname From `#@__member_stowtype`');
while($row = $dsql->GetArray('nn'))
{
	$rows[]=$row;
}

$dlist = new DataListCP();
$dlist->pageSize = 20;
$dlist->SetTemplate(DEDEMEMBER."/templets/$tpl.htm");
$dlist->SetSource($sql);
$dlist->Display();
?>