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
   header("Content-Type: text/html; charset={$cfg_ver_lang}");
   echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset={$cfg_ver_lang}\">\r\n";
   echo "删除 $aid OK<br>";
}

ClearAllLink();

?>