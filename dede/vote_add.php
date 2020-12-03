<?php 
require(dirname(__FILE__)."/config.php");
CheckPurview('plus_投票模块');
if(empty($dopost)) $dopost = "";
//////////////////////////////////////////
if($dopost=="save")
{
	//$ismore,$votename
	$starttime = GetMkTime($starttime);
	$endtime = GetMkTime($endtime);
	$voteitems = "";
	$j=0;
	for($i=1;$i<=15;$i++)
	{
		if(!empty(${"voteitem".$i})){
			$j++;
			$voteitems .= "<v:note id=\\'$j\\' count=\\'0\\'>".${"voteitem".$i}."</v:note>\r\n";
		}
	}
	$dsql = new DedeSql(false);
	$inQuery = "
	insert into #@__vote(votename,starttime,endtime,totalcount,ismore,votenote) 
	Values('$votename','$starttime','$endtime','0','$ismore','$voteitems');
	";
	$dsql->SetQuery($inQuery);
	if(!$dsql->ExecuteNoneQuery())
	{
		$dsql->Close();
		ShowMsg("增加投票失败，请检查数据是否非法！","-1");
		exit();
	}
	$dsql->Close();
	ShowMsg("成功增加一组投票！","vote_main.php");
	exit();
}
$startDay = time();
$endDay = AddDay($startDay,30);
$startDay = GetDateTimeMk($startDay);
$endDay = GetDateTimeMk($endDay);

require_once(dirname(__FILE__)."/templets/vote_add.htm");

ClearAllLink();
?>