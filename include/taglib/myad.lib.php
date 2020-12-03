<?php

if(!defined('DEDEINC')) exit('Request Error!');
require_once(DEDEINC.'/taglib/mytag.lib.php');

function lib_myad(&$ctag, &$refObj)
{
	$attlist = "typeid|0,name|";
	FillAttsDefault($ctag->CAttribute->Items,$attlist);
	extract($ctag->CAttribute->Items, EXTR_SKIP);

	$body = lib_GetMyTagT($refObj, $typeid, $name, '#@__myad');
	
	return $body;
}

?>