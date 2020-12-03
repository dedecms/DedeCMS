<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('c_New');
$dsql = new DedeSql(false);
$row = $dsql->GetOne("Select ID From #@__channeltype order by ID desc limit 0,1 ");
$newid = $row['ID']+1;
if($newid<10) $newid = $newid+10;
require_once(dirname(__FILE__)."/templets/mychannel_add.htm");
ClearAllLink();
?>