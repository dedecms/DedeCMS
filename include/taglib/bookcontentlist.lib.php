<?php

if(!defined('DEDEINC')) exit('Request Error!');
require_once(DEDEINC.'/taglib/booklist.lib.php');

function lib_bookcontentlist(&$ctag, &$refObj)
{
	global $dsql, $envs, $cfg_dbprefix, $cfg_cmsurl;

	$attlist="row|12,booktype|-1,titlelen|30,orderby|lastpost,author|,keyword|";
	FillAttsDefault($ctag->CAttribute->Items,$attlist);
	extract($ctag->CAttribute->Items, EXTR_SKIP);

	if( !$dsql->IsTable("{$cfg_dbprefix}story_books") ) return '没安装连载模块';
	
	return lib_booklist($ctag, $refObj, 1);
	
}

?>