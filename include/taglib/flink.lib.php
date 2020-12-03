<?php
if(!defined('DEDEINC'))
{
	exit("Request Error!");
}

function lib_flink(&$ctag,&$refObj)
{
	global $dsql;
	$attlist="type|textall,row|24,titlelen|24,linktype|1,typeid|0";
	FillAttsDefault($ctag->CAttribute->Items,$attlist);
	extract($ctag->CAttribute->Items, EXTR_SKIP);

	$totalrow = $row;
	$revalue = '';

	$wsql = " where ischeck >= '$linktype' ";
	if($typeid == 0)
	{
		$wsql .= '';
	}
	else
	{
		$wsql .= "And typeid = '$typeid'";
	}
	if($type=='image')
	{
		$wsql .= " And logo<>'' ";
	}
	else if($type=='text')
	{
		$wsql .= " And logo='' ";
	}

	$equery = "Select * from #@__flink $wsql order by sortrank asc limit 0,$totalrow";

	if(trim($ctag->GetInnerText())=='') $innertext = "<li>[field:link /]</li>";
	else $innertext = $ctag->GetInnerText();
	
	$dsql->SetQuery($equery);
	$dsql->Execute();
	while($dbrow=$dsql->GetObject())
	{
		if($type=='text'||$type=='textall')
		{
			$link = "<a href='".$dbrow->url."' target='_blank'>".cn_substr($dbrow->webname,$titlelen)."</a> ";
		}
		else if($type=='image')
		{
			$link = "<a href='".$dbrow->url."' target='_blank'><img src='".$dbrow->logo."' width='88' height='31' border='0'></a> ";
		}
		else
		{
			if($dbrow->logo=='')
			{
				$link = "<a href='".$dbrow->url."' target='_blank'>".cn_substr($dbrow->webname,$titlelen)."</a> ";
			}
			else
			{
				$link = "<a href='".$dbrow->url."' target='_blank'><img src='".$dbrow->logo."' width='88' height='31' border='0'></a> ";
			}
		}
		$rbtext = preg_replace("/\[field:url([\/\s]{0,})\]/isU", $row['url'], $innertext);
 		$rbtext = preg_replace("/\[field:webname([\/\s]{0,})\]/isU", $row['webname'], $rbtext);
 		$rbtext = preg_replace("/\[field:logo([\/\s]{0,})\]/isU", $row['logo'], $rbtext);
 		$rbtext = preg_replace("/\[field:link([\/\s]{0,})\]/isU", $link, $rbtext);
 		$revalue .= $rbtext;
	}
	return $revalue;
}
?>