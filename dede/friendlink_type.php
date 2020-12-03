<?php
require_once(dirname(__FILE__)."/config.php");
if(empty($dopost))
{
	$dopost = '';
}
//保存更改
if($dopost=="save")
{
	$startID = 1;
	$endID = $idend;
	for(;$startID<=$endID;$startID++)
	{
		$query = '';
		$tid = ${'ID_'.$startID};
		$pname =   ${'pname_'.$startID};
		if(isset(${'check_'.$startID}))
		{
			if($pname!='')
			{
				$query = "update #@__flinktype set typenamd='$pname_new'";
				$dsql->ExecuteNoneQuery($query);
			}
		}
		else
		{
			$query = "Delete From #@__flinktype where id='$tid' ";
			$dsql->ExecuteNoneQuery($query);
		}
	}
	//增加新记录
	if(isset($check_new) && $pname_new!='')
	{
		$query = "Insert Into #@__flinktype(typename) Values('{$pname_new}');";
		$dsql->ExecuteNoneQuery($query);
	}
	header("Content-Type: text/html; charset={$cfg_soft_lang}");
	echo "<script> alert('成功更新友情链接网站分类表！'); </script>";
}

include DedeInclude('templets/friendlink_type.htm');

?>