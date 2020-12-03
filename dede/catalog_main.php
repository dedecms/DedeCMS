<?php
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_typeunit_admin.php");
$userChannel = $cuserLogin->getUserChannel();
require_once(dirname(__FILE__)."/templets/catalog_main.htm");
ClearAllLink();
?>