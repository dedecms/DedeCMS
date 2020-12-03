<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_ArcTj');
$dsql = new DedeSql(false);
$row1 = $dsql->GetOne("Select count(*) as dd From `#@__full_search`");
$row2 = $dsql->GetOne("Select count(*) as dd From `#@__feedback`");
$row3 = $dsql->GetOne("Select count(*) as dd From `#@__member`");

require_once(dirname(__FILE__)."/templets/content_tj.htm");

ClearAllLink();

?>