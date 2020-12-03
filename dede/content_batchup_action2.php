<?php
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
require_once(dirname(__FILE__)."/inc/inc_batchup.php");
CheckPurview('sys_ArcBatch');
@set_time_limit(0);
$dsql = new DedeSql(false);
if($action=='modddpic')
{
	$query = "select ID,maintable from #@__channeltype";
	$dsql->setquery($query);
	$dsql->execute();
	$channels = array();
	while($row = $dsql->getarray())
	{
		$channels[$row['ID']] = $row['maintable'];
	}
	$query = "select aid, litpic,channelid from #@__full_search where litpic<>''";
	$dsql->setquery($query);
	$dsql->execute('litpic');
	while($row = $dsql->getarray('litpic'))
	{
		if(!preg_match("/^http:\/\//i",$row['litpic'])){
			if(!file_exists($row['litpic'])){
				$query = "update #@__full_search set litpic='' where aid=".$row['aid']." limit 1";
				$dsql->executenonequery($query);
				$maintable = $channels[$row['channelid']];
				$query = "update ".$maintable." set litpic='' where aid=".$row['aid']." limit 1";
				$dsql->executenonequery($query);
			}
		}
	}
	$dsql->Close();
	ShowMsg("成功修正缩略图错误！","javascript:;");
	exit();
}elseif($action == 'delerrdata')
{
	$query = "select ID from #@__channeltype";
	$dsql->setquery($query);
	$dsql->execute();
	$channelids = 0;
	while($row = $dsql->getarray())
	{
		$channelids .= ','.$row['ID'];
	}
	$query = "select ID from #@__arctype";
	$dsql->setquery($query);
	$dsql->execute();
	$tids = '';
	$tidarr = array();
	while($row = $dsql->getarray())
	{
		$tidarr[]= $row['ID'];
	}
	//print_r($tidarr);exit;
	$tids = implode(',', $tidarr);
	$dsql->executenonequery("delete from #@__full_search where NOT(typeid in($tids))");
	$dsql->executenonequery("delete from #@__full_search where NOT(channelid in($channelids))");
	$dsql->executenonequery("OPTIMIZE TABLE `#@__full_search`");
	$dsql->Close();
	ShowMsg("成功清除错误数据！","javascript:;");
	exit();
}