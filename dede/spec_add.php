<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('spec_New');
require_once(DEDEADMIN."/inc/inc_catalog_options.php");
require_once(DEDEADMIN."/inc/inc_archives_functions.php");

$channelid = -1;
if(empty($cid)) $cid = 0;
$dsql = new DedeSql(false);

$query = " Select * From  #@__channeltype where ID='$channelid'";
$cInfos = $dsql->GetOne($query);
$channelid = $cInfos['ID'];
$addtable = $cInfos['addtable'];

require_once(DEDEADMIN."/templets/spec_add.htm");

ClearAllLink();

?>