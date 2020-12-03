<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
$dsql = new DedeSql(false);

require_once(dirname(__FILE__)."/templets/freelist_add.htm");

ClearAllLink();
?>