<?php
require_once(dirname(__FILE__).'/config.php');
AjaxHead();
if($openitem != 100)
{
	require(dirname(__FILE__).'/inc/inc_menu.php');
	require(DEDEADMIN.'/inc/inc_menu_func.php');
	GetMenus($cuserLogin->getUserRank(),'main');
	exit();
}
else
{
	$openitem = 0;
	require(dirname(__FILE__).'/inc/inc_menu_module.php');
	require(DEDEADMIN.'/inc/inc_menu_func.php');
	GetMenus($cuserLogin->getUserRank(),'module');
	exit();
}
?>