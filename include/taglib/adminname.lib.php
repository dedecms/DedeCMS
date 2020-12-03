<?php
if(!defined('DEDEINC')) exit('Request Error!');

function lib_adminname(&$ctag, &$refObj)
{
	global $dsql;
	if(empty($refObj->Fields['dutyadmin']))
	{
		$dutyadmin = $GLOBALS['cfg_df_dutyadmin'];
	}
	else
	{
		$row = $dsql->GetOne("Select uname From `#@__admin` where id='{$refObj->Fields['dutyadmin']}' ");
		$dutyadmin = isset($row['uname']) ? $row['uname'] : $GLOBALS['cfg_df_dutyadmin'];
	}
	return $dutyadmin;
}

?>