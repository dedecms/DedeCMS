<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('co_Export');
require_once(dirname(__FILE__)."/../include/pub_collection.php");
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
require_once(dirname(__FILE__)."/inc/inc_catalog_options.php");
$dsql = new DedeSql(false);
$mrow = $dsql->GetOne("Select count(*) as dd From #@__courl where nid='$nid'");
$totalcc = $mrow['dd'];
$rrow = $dsql->GetOne("Select typeid From #@__conote where nid='$nid'");
$ruleid = $rrow['typeid'];
$rrow = $dsql->GetOne("Select channelid From #@__co_exrule where aid='$ruleid'");
$channelid = $rrow['channelid'];

require_once(dirname(__FILE__)."/templets/co_export.htm");

ClearAllLink();
?>