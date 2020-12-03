<?php
if(!defined('DEDEINC')) exit('Request Error!');

function plus_ask(&$atts,&$refObj,&$fields)
{
	global $dsql,$_vars;

	$attlist = "titlelen=40,row=8,typeid=0,sort=";
  FillAtts($atts,$attlist);
  FillFields($atts,$fields,$refObj);
	extract($atts, EXTR_OVERWRITE);

	$wheresql = ' 1 ';
	if($sort=='') {
		$orderby = 'order by id desc';
  }
	else if($sort=='commend')
	{
		$wheresql .= ' And digest=1';
		$orderby = ' order by dateline desc';
	}
	else if($sort=='ok')
	{
		$wheresql .= ' And status=1 ';
		$orderby = ' order by solvetime desc';
	}
	else if($sort=='expiredtime')
	{
		$wheresql .= ' And status=0 ';
		$orderby = ' order by expiredtime asc, dateline desc';
	}
	else if($sort=='reward')
	{
		$wheresql .= ' And status=0 ';
		$orderby = ' order by reward desc';
	}
	else
	{
		$wheresql .= ' And status=0 ';
		$orderby = ' order by disorder desc, dateline desc';
	}
	$query = "select id, tid, tidname, tid2, tid2name, title from `#@__ask` where $wheresql $orderby limit $row";
	$dsql->SetQuery($query);
	$dsql->Execute('an');
  $rearr = array();
  while($row = $dsql->GetArray('an'))
  {

    if($row['tid2'] != 0)
	    $row['typelink'] = $row['typedata'] = " <a href='browser.php?tid2={$row['tid2']}'>{$row['tid2name']}</a>\r\n";
    else
    	$row['typelink'] = $row['typedata'] = " <a href='browser.php?tid={$row['tid']}'>{$row['tidname']}</a>\r\n";
    $row['title'] = cn_substr($row['title'],$titlelen);
    $rearr[] = $row;
  }
	return $rearr;
}
?>