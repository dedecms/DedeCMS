<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_User');
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
if(empty($dopost)) $dopost = "";
$ID = ereg_replace("[^0-9]","",$ID);
//----------------------------
$ntime = time();
function TjMonth($uid){
	global $dsql,$ntime;
	$y = strftime("%Y",$ntime);
	$m = strftime("%m",$ntime);
	$stime = GetMkTime("{$y}-{$m}-01");
	$row = $dsql->GetOne("Select count(ID) as dd From #@__archives where adminid='$uid' And senddate>=$stime; ");
	return $row['dd'];
}

function TjQuar($uid){
	global $dsql,$ntime;
	$y = strftime("%Y",$ntime);
	$m = strftime("%m",$ntime);
	$j = floor($m%3)+1;
	$m = ($j * 3) - 2;
	$stime = GetMkTime("{$y}-{$m}-01");
	$row = $dsql->GetOne("Select count(ID) as dd From #@__archives where adminid='$uid' And senddate>=$stime; ");
	return $row['dd'];
}

function TjYear($uid){
	global $dsql,$ntime;
	$y = strftime("%Y",$ntime);
	$stime = GetMkTime("{$y}-01-01");
	$row = $dsql->GetOne("Select count(ID) as dd From #@__archives where adminid='$uid' And senddate>=$stime; ");
	return $row['dd'];
}

function TjAll($uid){
	global $dsql,$ntime;
	$row = $dsql->GetOne("Select count(ID) as dd From #@__archives where adminid='$uid'; ");
	return $row['dd'];
}

function SumMonth($uid){
	global $dsql,$ntime;
	$y = strftime("%Y",$ntime);
	$m = strftime("%m",$ntime);
	$stime = GetMkTime("{$y}-{$m}-01");
	$row = $dsql->GetOne("Select sum(click) as dd From #@__archives where adminid='$uid' And senddate>=$stime; ");
	return $row['dd'];
}

function SumQuar($uid){
	global $dsql,$ntime;
	$y = strftime("%Y",$ntime);
	$m = strftime("%m",$ntime);
	$j = floor($m%3)+1;
	$m = ($j * 3) - 2;
	$stime = GetMkTime("{$y}-{$m}-01");
	$row = $dsql->GetOne("Select sum(click) as dd From #@__archives where adminid='$uid' And senddate>=$stime; ");
	return $row['dd'];
}

function SumYear($uid){
	global $dsql,$ntime;
	$y = strftime("%Y",$ntime);
	$stime = GetMkTime("{$y}-01-01");
	$row = $dsql->GetOne("Select sum(click) as dd From #@__archives where adminid='$uid' And senddate>=$stime; ");
	return $row['dd'];
}

function SumAll($uid){
	global $dsql,$ntime;
	$row = $dsql->GetOne("Select sum(click) as dd From #@__archives where adminid='$uid'; ");
	return $row['dd'];
}

$dsql = new DedeSql(false);
$row = $dsql->GetOne("Select * From #@__admin where ID='$ID'");
require_once(dirname(__FILE__)."/templets/sys_admin_user_tj.htm");

ClearAllLink();
?>