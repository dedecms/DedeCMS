<?php
require_once(dirname(__FILE__)."/../include/common.inc.php");

if(isset($arcID))
{
	$aid = $arcID;
}
$arcID = $aid = (isset($aid) && is_numeric($aid)) ? $aid : 0;
if($aid==0)
{
	die(" Request Error! ");
}

$row = $dsql->GetOne("Select * From `#@__myad` where aid='$aid' ");
$adbody = '';
if($row['timeset']==0)
{
	$adbody = $row['normbody'];
}
else
{
	$ntime = time();
	if($ntime>$row['endtime'] || $ntime<$row['starttime'])
	{
		$adbody = $row['expbody'];
	}
	else
	{
		$adbody = $row['normbody'];
	}
}
$adbody = str_replace('"','\"',$adbody);
$adbody = str_replace("\r","\\r",$adbody);
$adbody = str_replace("\n","\\n",$adbody);
echo "<!--\r\n";
echo "document.write(\"{$adbody}\");\r\n";
echo "-->\r\n";
?>