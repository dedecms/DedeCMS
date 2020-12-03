<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('temp_Other');
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
$dsql = new DedeSql(false);
$row  = $dsql->GetOne("Select * From #@__homepageset");
$dsql->Close();

require_once(dirname(__FILE__)."/templets/tag_test.htm");

ClearAllLink();
?>