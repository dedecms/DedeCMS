<?php
require_once(dirname(__FILE__)."/../include/common.inc.php");
require_once(DEDEINC.'/datalistcp.class.php');
require_once(DEDEINC."/enums.func.php");
require_once(DEDEDATA.'/enums/nativeplace.php');

/** 定义模板路径 **/
$skin = 'default';

define('TPL_DIR', dirname(__FILE__)."/tpl/$skin/");

/** 行业数据 **/
$topVocation = getTopData('vocation');

$cname = isset($cname) ? addslashes(FilterSearch(stripslashes($cname))) : '';
$town = isset($town) ? addslashes(FilterSearch(stripslashes($town))) : '';

$vocation = isset($vocation) && is_numeric($vocation) ? $vocation : 0;
$nativeplace = isset($nativeplace) && is_numeric($nativeplace) ? $nativeplace : 0;

$province = isset($province) && is_numeric($province) ? $province : 0;
if($province > 0) $nativeplace = $province;

$wheresql = '1';

if( $nativeplace%500 != 0 )
{
	$wheresql .= " and company.place = '$nativeplace'";
}
else
{
	if($nativeplace!=0)
	{
		$min = $nativeplace - 1;
		$max = $nativeplace + 500;
		$wheresql .= " and (company.place = '$nativeplace' or (company.place >= '$min' and company.place < '$max'))";
	}
}

if($vocation > 0)
{
	if($vocation%500 != 0)
	{
		$wheresql .= " and company.vocation = '$vocation' ";
	}
	else
	{
		$max = ceil(($vocation+1)/500)*500;
		$min = $max - 500;
		$wheresql .= " and company.vocation >= '$min' and company.vocation < '$max'";
	}
}

$cname = FilterSearch(stripslashes($cname));
$cname = addslashes(cn_substr($cname, 30));
if($cname != '')
{
	$wheresql .= " and (company.company like '%$cname%' or company.product like '%$cname%')";
}

/*
//如果要允许搜索具体地址，在模板增加搜索地址的文本框name='town'，然后去除此注解即可。
$town = FilterSearch(stripslashes($town));
$town = addslashes(cn_substr($town, 20));
if($town != '')
{
	$wheresql .= " and company.address like '%$town%'";
}
*/

$sql = "select company.*, member.userid from `#@__member_company` company
			 left join `#@__member` member on member.mid=company.mid
			 where $wheresql order by uptime desc, mid desc ";

$dl = new DataListCP();
$dl->pageSize = 10;
$dl->SetParameter('cname', $cname);
$dl->SetParameter('nativeplace', $nativeplace);
$dl->SetParameter('vocation', $vocation);

$dl->SetTemplate(TPL_DIR.'/index.tpl.htm');
$dl->SetSource($sql);
$dl->Display();

?>