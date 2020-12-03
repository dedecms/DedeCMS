<?php
if(!defined('DEDEINC'))
{
	exit("Request Error!");
}
function lib_demotag(&$ctag,&$refObj)
{
	global $dsql,$envs;
	
	//属性处理
	$attlist="row|12,titlelen|24";
	FillAttsDefault($ctag->CAttribute->Items,$attlist);
	extract($ctag->CAttribute->Items, EXTR_SKIP);
	$revalue = '';
	
	//你需编写的代码，不能用echo之类语法，把最终返回值传给$revalue
	//------------------------------------------------------
	
	$revalue = 'Hello Word!';
	
	//------------------------------------------------------
	return $revalue;
}
?>