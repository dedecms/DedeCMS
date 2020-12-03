<?php
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEINC."/typeunit.class.menu.php");
$userChannel = $cuserLogin->getUserChannel();
if(empty($opendir))
{
	$opendir=-1;
}
if($userChannel>0)
{
	$opendir=$userChannel;
}
if($cuserLogin->adminStyle=='dedecms')
{
	include DedeInclude('templets/catalog_menu.htm');
	exit();
}
else
{
	include DedeInclude('templets/catalog_menu2.htm');
	exit();
}

?>