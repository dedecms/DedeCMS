<?php
if(!defined('DEDEINC'))
{
	exit("Request Error!");
}
function lib_softmsg(&$ctag,&$refObj)
{
	global $dsql;
	//$attlist="type|textall,row|24,titlelen|24,linktype|1";
	//FillAttsDefault($ctag->CAttribute->Items,$attlist);
	//extract($ctag->CAttribute->Items, EXTR_SKIP);
	$revalue = '';
	$row = $dsql->GetOne(" select * From `#@__softconfig` ");
	if(is_array($row)) $revalue = $row['downmsg'];
	return $revalue;
}
?>