<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
require_once(dirname(__FILE__)."/inc/inc_batchup.php");
CheckPurview('sys_ArcBatch');
@set_time_limit(0);
if($action=='delnulltitle')
{
  $dsql = new DedeSql(false);
  $dsql->SetQuery("Select ID From #@__archives where trim(title)='' ");
  $dsql->Execute('x');
  $tdd = 0;
  while($row = $dsql->GetObject('x')){ if(DelArc($row->ID)) $tdd++; }
  $dsql->Close();
	ShowMsg("³É¹¦É¾³ý $tdd Ìõ¼ÇÂ¼£¡","javascript:;");
	exit();
}
//É¾³ý¿ÕÄÚÈÝÎÄÕÂ
else if($action=='delnullbody')
{
  $dsql = new DedeSql(false);
  $dsql->SetQuery("Select aid From #@__addonarticle where LENGTH(body) < 10 ");
  $dsql->Execute('x');
  $tdd = 0;
  while($row = $dsql->GetObject('x')){ if(DelArc($row->aid)) $tdd++; }
  $dsql->Close();
	ShowMsg("³É¹¦É¾³ý $tdd Ìõ¼ÇÂ¼£¡","javascript:;");
	exit();
}
//ÐÞÕýËõÂÔÍ¼´íÎó
else if($action=='modddpic')
{
	$dsql = new DedeSql(false);
  $dsql->ExecuteNoneQuery("Update #@__archives set litpic='' where trim(litpic)='litpic' ");
  $dsql->Close();
	ShowMsg("³É¹¦ÐÞÕýËõÂÔÍ¼´íÎó£¡","javascript:;");
	exit();
}
?>