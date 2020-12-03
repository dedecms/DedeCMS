<?php 
require_once(dirname(__FILE__)."/config.php");
empty($_COOKIE['ENV_GOBACK_URL']) ? $ENV_GOBACK_URL = "-1" : $ENV_GOBACK_URL=$_COOKIE['ENV_GOBACK_URL'];
CheckPurview('sys_Keyword');
$keyword = trim($keyword);
$rank = ereg_replace("[^0-9]","",$rank);
if(ereg(" ",$keyword)||$keyword=="")
{
	ShowMsg("关键字不能带有空格或为空！",-1);
	exit();
}
$dsql = new DedeSql(false);
$row = $dsql->GetOne("Select * From #@__keywords where keyword like '$keyword'");
if(is_array($row))
{
	$dsql->Close();
	ShowMsg("关键字已存在库中！","-1");
	exit();
}
$inquery = "
INSERT INTO #@__keywords(keyword,rank,sta,rpurl) VALUES ('$keyword','$rank','1','$rpurl');
";
$dsql->SetQuery($inquery);
$dsql->ExecuteNoneQuery();
ClearAllLink();
ShowMsg("成功增加一个关键字！",$ENV_GOBACK_URL);
?>