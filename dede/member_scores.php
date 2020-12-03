<?php
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
require_once(dirname(__FILE__)."/../include/inc_functions.php");
CheckPurview('member_Scores');
$db = new DedeSql(false);
if(!isset($action)) $action = '';
if($action=='save'){
	if(!empty($add_integral)&&!empty($add_icon)&&!empty($add_titles)){
		$integral = ereg_replace("[^0-9]","",$add_integral);
		$add_icon = ereg_replace("[^0-9]","",$add_icon);
		$add_titles = cn_substr($add_titles,15);
		$db->ExecuteNoneQuery("INSERT INTO #@__scores(integral,icon,titles,isdefault) VALUES('$integral','$add_icon','$add_titles','$add_isdefault')");
	}
	foreach($_POST as $rk=>$rv){
		if(ereg('-',$rk))
		{
			$ID = ereg_replace("[^1-9]","",$rk);
			$fildes = ereg_replace("[^a-z]","",$rk);
			$k = $$rk;
			if(empty($k)) $k = 0;
			$sql = $fildes."='".$k."'";
			$db->ExecuteNoneQuery("UPDATE #@__scores SET ".$sql." WHERE id='{$ID}'");
			if(ereg('Ids-',$rk)) {
				if($k) $db->ExecuteNoneQuery("DELETE FROM #@__scores WHERE id='$ID'");
			}
		}
	}	
}

$Scores = array();
$db->SetQuery("SELECT * FROM #@__scores ORDER BY id ASC");
$db->Execute();
while($rs = $db->GetArray()) array_push ($Scores,$rs);

require_once(dirname(__FILE__)."/templets/member_scores.htm");

$db->Close();

ClearAllLink();
?>