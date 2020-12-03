<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Group');
if(empty($dopost)) $dopost = "";
$dsql = new DedeSql(false);

require_once(dirname(__FILE__)."/templets/sys_group.htm");

ClearAllLink();

?>