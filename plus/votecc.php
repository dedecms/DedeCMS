<?php 
require_once(dirname(__FILE__)."/../include/config_base.php");
if(empty($aid)){
	ShowMsg("必须指定文档ID!","-1");
	exit();
}
$aid = intval($aid);
$dsql = new DedeSql(false);
$row = $dsql->GetOne("Select votecount From #@__addonvote where aid='$aid' ");
$dsql->Close();
$votecount = $row['votecount'];
echo "<!--\r\ndocument.write(\"{$votecount}\");\r\n-->";
?>