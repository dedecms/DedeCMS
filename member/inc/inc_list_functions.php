<?php
if(!defined('DEDEMEMBER'))
{
	exit("dedecms");
}

//获得是否推荐的表述
function IsCommendArchives($iscommend)
{
  $s = '';
  if(ereg('c',$iscommend))
  {
  	$s .= '推荐';
  }
  else if(ereg('h',$iscommend))
  {
  	$s .= ' 头条';
  }
  else if(ereg('p',$iscommend))
  {
  	$s .= ' 图片';
  }
  else if(ereg('j',$iscommend))
  {
  	$s .= ' 跳转';
  }
  return $s;
}

//获得推荐的标题
function GetCommendTitle($title,$iscommend)
{
	if(ereg('c',$iscommend))
	{
		$title = "$title<font color='red'>(推荐)</font>";
	}
	return "$title";
}

//更换颜色
$GLOBALS['RndTrunID'] = 1;
function GetColor($color1,$color2)
{
	$GLOBALS['RndTrunID']++;
	if($GLOBALS['RndTrunID']%2==0)
	{
		return $color1;
	}
	else
	{
		return $color2;
	}
}

//检查图片是否存在
function CheckPic($picname)
{
	if($picname!="")
	{
		return $picname;
	}
	else
	{
		return "img/dfpic.gif";
	}
}

//判断内容是否生成HTML
function IsHtmlArchives($ismake)
{
	if($ismake==1)
	{
		return "已生成";
	}
	else if($ismake==-1)
	{
		return "仅动态";
	}
	else
	{
		return "<font color='red'>未生成</font>";
	}
}

//获得内容的限定级别名称
function GetRankName($arcrank)
{
	global $arcArray;
	if(!is_array($arcArray))
	{
		$dsql->SetQuery("Select * from #@__arcrank");
		$dsql->Execute();
		while($row = $dsql->GetObject())
		{
			$arcArray[$row->rank]=$row->membername;
		 }
	}
	if(isset($arcArray[$arcrank]))
	{
		return $arcArray[$arcrank];
	}
	else
	{
		return "不限";
	}
}

//判断内容是否为图片文章
function IsPicArchives($picname)
{
	if($picname!="")
	{
		return "<font color='red'>(图)</font>";
	}
	else
	{
		return "";
	}
}
?>