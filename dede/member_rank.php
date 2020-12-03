<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('member_Type');
if(empty($dopost))
{
	$dopost = '';
}

//保存更改
if($dopost=='save')
{
	$startID = 1;
	$endID = $idend;
	for(;$startID <= $endID;$startID++)
	{
		
		$query = '';
		$id = ${"ID_".$startID};
		$name = ${"name_".$startID};
		$rank = ${"rank_".$startID};
		$money = ${"money_".$startID};
		$scores = ${"scores_".$startID};
		if(isset(${"check_".$startID}))
		{
			if($rank>0)
			{
				$query = "update `#@__arcrank` set membername='$name',money='$money',rank='$rank',scores='$scores' where id='$id' ";			
			}
		}
		else
		{
			$query = "Delete From `#@__arcrank` where id='$id' And rank<>10";
		}
		if($query!='')
		{
			$dsql->ExecuteNoneQuery($query);
		}
	}
	if(isset($check_new))
	{
		if($rank_new > 0 && $name_new != '' && $rank_new > 10)
		{
			$inquery = "Insert Into `#@__arcrank`(`rank`,`membername`,`adminrank`,`money`,`scores`,`purviews`) Values('$rank_new','$name_new','5','$money_new','$scores',''); ";
			$dsql->ExecuteNoneQuery($inquery);
		}
	}
	echo "<script> alert('成功更新会员等级表！'); </script>";
}
if($dopost == 'del')
{
	$dsql->ExecuteNoneQuery("Delete From `#@__arcrank` where id='$id' And rank<>10");
	ShowMsg("删除成功！","member_rank.php");
	exit();
}

$dsql->SetQuery("Select * From `#@__arcrank` where rank>0 order by rank");
$dsql->Execute();
include DedeInclude('templets/member_rank.htm');

?>