<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('member_Scores');
if(!isset($action))
{
	$action = '';
}
if($action=='save')
{
	if(!empty($add_integral)&&!empty($add_icon)&&!empty($add_titles))
	{
		$integral = ereg_replace("[^0-9]","",$add_integral);
		$add_icon = ereg_replace("[^0-9]","",$add_icon);
		$add_titles = cn_substr($add_titles,15);
		$dsql->ExecuteNoneQuery("INSERT INTO `#@__scores`(integral,icon,titles,isdefault) VALUES('$integral','$add_icon','$add_titles','$add_isdefault')");
	}
	foreach($_POST as $rk=>$rv)
	{
		if(ereg('-',$rk))
		{
			$ID = ereg_replace("[^0-9]","",$rk);
			$fildes = ereg_replace("[^a-z]","",$rk);
			$k = $$rk;
			if(empty($k))
			{
				$k = 0;
			}
			$sql = $fildes."='".$k."'";
			$dsql->ExecuteNoneQuery("UPDATE `#@__scores` SET ".$sql." WHERE id='{$ID}'");
			if(ereg('Ids-',$rk))
			{
				if($k) $dsql->ExecuteNoneQuery("DELETE FROM `#@__scores` WHERE id='$ID'");
			}
		}
	}
}
$Scores = array();
$dsql->SetQuery("SELECT * FROM `#@__scores` ORDER BY id ASC");
$dsql->Execute();
while($rs = $dsql->GetArray())
{
	array_push($Scores,$rs);
}
include DedeInclude('templets/member_scores.htm');

?>