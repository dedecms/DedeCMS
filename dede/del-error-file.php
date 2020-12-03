<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
require_once(dirname(__FILE__)."/inc/inc_batchup.php");
$squery = "select aid from #@__addonarticle where body like '%/plus/img/etag.gif%' ";

$dsql = new DedeSql(false);

$dsql->SetQuery($squery);
$dsql->Execute();

while($row = $dsql->GetArray()){
   $aid = $row['aid'];
   DelArc($aid);
   echo "删除 $aid OK<br>";
}

ClearAllLink();

?>