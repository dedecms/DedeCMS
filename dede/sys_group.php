<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Group');
if(empty($dopost))
{
	$dopost = "";
}
include DedeInclude('templets/sys_group.htm');

?>