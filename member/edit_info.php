<?php 
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);
$dsql=new DedeSql();
$row=$dsql->GetOne("select  * from #@__member where ID='".$cfg_ml->M_ID."'");
require_once(dirname(__FILE__)."/templets/edit_info.htm");
?>