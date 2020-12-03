<?php
require(dirname(__FILE__).'/config.php');
require(DEDEADMIN.'/inc/inc_menu.php');
require(DEDEADMIN.'/inc/inc_menu_func.php');
$openitem = (empty($openitem) ? 1 : $openitem);
if($cuserLogin->adminStyle=='dedecms')
{
	include DedeInclude('templets/index_menu1.htm');
}
else
{
	include DedeInclude('templets/index_menu2.htm');
}
?>
