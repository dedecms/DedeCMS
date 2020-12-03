<?php
require(dirname(__FILE__)."/config.php");
CheckPurview('plus_广告管理');
require_once DEDEINC."/typelink.class.php";
if(empty($dopost))
{
	$dopost = "";
}

if($dopost=="save")
{
	//timeset tagname typeid normbody expbody
	$tagname = trim($tagname);
	$row = $dsql->GetOne("Select typeid From #@__myad where typeid='$typeid' And tagname like '$tagname'");
	if(is_array($row))
	{
		ShowMsg("在相同栏目下已经存在同名的标记！","-1");
		exit();
	}
	$starttime = GetMkTime($starttime);
	$endtime = GetMkTime($endtime);
	if($normbody['style']=='code')
	{
		$normbody = $normbody['htmlcode'];
	}
	elseif($normbody['style']=='txt')
	{
		$normbody = "<a href=\"{$normbody['link']}\" font-size=\"{$normbody['size']}\" color=\"{$normbody['color']}\">{$normbody['title']}</a>";
	}
	elseif($normbody['style']=='img')
	{
		if(empty($normbody['width']))
		{
			$width = "";
		}
		else
		{
			$width = " width=\"{$normbody['width']}\"";
		}
		if (empty($normbody['height']))
		{
			$height = "";
		}
		else
		{
			$height = "height=\"{$normbody['height']}\"";
		}
		$normbody = "<a href=\"{$normbody['link']}\"><img src=\"{$normbody['url']}\"$width $height border=\"0\" /></a>";
	}
	else
	{
		if(empty($normbody['width']))
		{
			$width = "";
		}
		else
		{
			$width = " width=\"{$normbody['width']}\"";
		}
		if (empty($normbody['height']))
		{
			$height = "";
		}
		else
		{
			$height = "height=\"{$normbody['height']}\"";
		}
		$normbody = "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.Macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0\"$width $height><param name=\"movie\" value=\"{$normbody['link']}\"/><param name=\"quality\" value=\"high\"/></object>";
	}
	$query = "
	 Insert Into #@__myad(typeid,tagname,adname,timeset,starttime,endtime,normbody,expbody)
	 Values('$typeid','$tagname','$adname','$timeset','$starttime','$endtime','$normbody','$expbody');
	";
	$dsql->ExecuteNoneQuery($query);
	ShowMsg("成功增加一个广告！","ad_main.php");
	exit();
}
$startDay = time();
$endDay = AddDay($startDay,30);
$startDay = GetDateTimeMk($startDay);
$endDay = GetDateTimeMk($endDay);
include DedeInclude('templets/ad_add.htm');

?>