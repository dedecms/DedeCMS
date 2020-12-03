<?php
require(dirname(__FILE__)."/config.php");
if($cuserLogin->adminStyle=='dedecms')
{
	include DedeInclude('templets/index_top1.htm');
}
else
{
	include DedeInclude('templets/index_top2.htm');
}
?>
