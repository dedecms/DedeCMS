<?php

if(!defined('DEDEINC')) exit('Request Error!');

function lib_mytag(&$ctag, &$refObj)
{
	$attlist = "typeid|0,name|,ismake|no";
	FillAttsDefault($ctag->CAttribute->Items,$attlist);
	extract($ctag->CAttribute->Items, EXTR_SKIP);

	if(trim($ismake)=='') $ismake = 'no';
	$body = lib_GetMyTagT($refObj, $typeid, $name, '#@__mytag');
	//编译
	if($ismake=='yes')
	{
		require_once(DEDEINC.'/arc.partview.class.php');
		$pvCopy = new PartView($typeid);
		$pvCopy->SetTemplet($body,"string");
		$body = $pvCopy->GetResult();
	}
	return $body;
}

function lib_GetMyTagT(&$refObj, $typeid,$tagname,$tablename)
{
	global $dsql;
	if($tagname=='') return '';
	if(trim($typeid)=='') $typeid=0;
	if( !empty($refObj->Fields['typeid']) && $typeid==0) $typeid = $refObj->Fields['typeid'];
	
	$typesql = $row = '';
	if($typeid > 0) $typesql = " And typeid in(0,".GetTopids($typeid).") ";
	
	$row = $dsql->GetOne(" Select * From $tablename where tagname like '$tagname' $typesql order by typeid desc ");
	if(!is_array($row)) return '';

	$nowtime = time();
	if($row['timeset']==1 
	  && ($nowtime<$row['starttime'] || $nowtime>$row['endtime']) )
	{
		$body = $row['expbody'];
	}
	else
	{
		$body = $row['normbody'];
	}
	
	return $body;
}
?>