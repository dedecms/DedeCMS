<?php
if(!defined('DEDEINC'))
{
	exit("Request Error!");
}

function lib_adminname(&$ctag,&$refObj)
{
	global $dsql;
	$row = '';
	if(empty($GLOBALS['envs']['adminid']))
	{
		return '';
	}
	else
	{
		$row = $dsql->GetOne("Select uname From `#@__admin` where id='{$GLOBALS['envs']['adminid']}' ");
	}
	if(is_array($row))
	{
		return $row['uname'];
	}
	else
	{
		return '';
	}
}

?>