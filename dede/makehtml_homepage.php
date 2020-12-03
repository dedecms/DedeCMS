<?php 
require_once(dirname(__FILE__)."/config.php");
$dsql = new DedeSql(false);
$row  = $dsql->GetOne("Select * From #@__homepageset");

require_once(dirname(__FILE__)."/templets/makehtml_homepage.htm");

ClearAllLink();
?>