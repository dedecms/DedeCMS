<?php
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(DEDEINC.'/arc.partview.class.php');

if(isset($arcID)) $aid = $arcID;
$arcID = $aid = (isset($aid) && is_numeric($aid)) ? $aid : 0;
if($aid==0) die(" document.write('Request Error!'); ");

$pv = new PartView();
$row = $pv->dsql->GetOne(" Select * From `#@__mytag` where aid='$aid' ");
if(!is_array($row)) {
	exit(" document.write('Not found input!'); ");
}
$tagbody = '';
if($row['timeset']==0)
{
	$tagbody = $row['normbody'];
}
else
{
	$ntime = time();
	if($ntime>$row['endtime'] || $ntime<$row['starttime'])
	{
		$tagbody = $row['expbody'];
	}
	else
	{
		$tagbody = $row['normbody'];
	}
}
$pv->SetTemplet($tagbody,'string');
$myvalues  = $pv->GetResult();

$myvalues = str_replace('"','\"',$myvalues);
$myvalues = str_replace("\r","\\r",$myvalues);
$myvalues = str_replace("\n","\\n",$myvalues);
echo "<!--\r\n";
echo "document.write(\"{$myvalues}\");\r\n";
echo "-->\r\n";
?>