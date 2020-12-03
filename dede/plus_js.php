<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_js');
$aid = ereg_replace("[^0-9]","",$aid);
$db = new DedeSql(false);
$row = $db->GetOne("Select * From #@__plus where aid='$aid'");

require_once(dirname(__FILE__)."/templets/plus_js.htm");
ClearAllLink();
?>