<?php 
require(dirname(__FILE__)."/../include/config_base.php");
$aid = ereg_replace("[^0-9]","",$aid);
$dsql = new DedeSql(false);
$row = $dsql->GetOne("Select * From #@__myad where aid='$aid'");
$dsql->Close();
if($row['timeset']==0) $adbody = $row['normbody'];
else{
	$ntime = time();
	if($ntime>$row['endtime']||$ntime<$row['starttime']){ $adbody = $row['expbody']; }
	else{ $adbody = $row['normbody']; }
}
$adbody = str_replace('"','\"',$adbody);
$adbody = str_replace("\r","\\r",$adbody);
$adbody = str_replace("\n","\\n",$adbody);
echo "<!--\r\n";
echo "document.write(\"{$adbody}\");\r\n";
echo "-->\r\n";
?>