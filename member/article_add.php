<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/inc/inc_catalog_options.php");
require_once(dirname(__FILE__)."/../include/inc_imgbt.php");
CheckRank(0,0);

$dsql = new DedeSql(false);
$cInfos = $dsql->GetOne("Select sendrank From #@__channeltype  where ID='1'; ");	
if($cInfos['sendrank'] > $cfg_ml->M_Type){
	$row = $dsql->GetOne("Select membername From #@__arcrank where rank='".$cInfos['sendrank']."' ");
	$dsql->Close();
	ShowMsg("对不起，需要[".$row['membername']."]才能在这个频道发布文档！","-1","0",5000);
	exit();
}

require_once(dirname(__FILE__)."/templets/article_add.htm");

if(isset($dsql) && is_object($dsql)) $dsql->Close();
?>