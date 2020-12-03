<?php
if(!defined('DEDEINC'))
{
	exit("Request Error!");
}

function _FilterAll($fk,&$svar)
{
	global $cfg_notallowstr,$cfg_replacestr;
	if( is_array($svar) )
	{
		foreach($svar as $_k => $_v)
		{
			$svar[$_k] = _FilterAll($fk,$_v);
		}
	}
	else
	{
		if($cfg_notallowstr!='' && eregi($cfg_notallowstr,$svar))
		{
			ShowMsg(" $fk has not allow words!",'-1');
			exit();
		}
		if($cfg_replacestr!='')
		{
			$svar = eregi_replace($cfg_replacestr,"***",$svar);
		}
	}
	return $svar;
}

foreach(Array('_GET','_POST','_COOKIE') as $_request)
{
	foreach($$_request as $_k => $_v)
	{
		${$_k} = _FilterAll($_k,$_v);
	}
}
?>