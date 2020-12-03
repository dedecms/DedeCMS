<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Log');
require_once(DEDEINC."/datalistcp.class.php");
require_once(DEDEINC."/common.func.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
$sql = $where = "";
$dtime=(empty($dtime))? 0 : $dtime;
$ischeck=(empty($ischeck))? "" : $ischeck;
$dopost=(empty($dopost))? "" : $dopost;
if($type=="feed"){
	 $table="#@__member_feed";
	 $id="aid";
	 $tpl=DEDEADMIN."/templets/member_feed_main.htm";
}else{
	 $table="#@__member_msg";
	 $id="id";
	 $tpl=DEDEADMIN."/templets/member_mood_main.htm";
}
if(in_array($ischeck,array('-1', '1'))){
  $type = array('-1'=>'0', '1'=>'1');
	if($dtime>0){
		$nowtime = time();
		$starttime = $nowtime - ($dtime*24*3600);
		$where .= " And dtime>'$starttime' And ischeck='$type[$ischeck]' ";
	}else{
		$where .= " And ischeck='$type[$ischeck]' ";
	}
}elseif($dtime>0){
	$nowtime = time();
	$starttime = $nowtime - ($dtime*24*3600);
	$where .= " And dtime>'$starttime' ";
}elseif($dopost=='pall'){

	$where .= " And mid='$mid' ";
}

//获得是否审核的表述
function IsChecklog($ischeck)
{
  $s = '';
  $s=($ischeck=='1')? "<font color=blue>已审核</font>" : "<font color=red>未审核</font>";
  return $s;
}

function JstrimJajxLog($str,$len)
{
	$str = cn_substr($str,$len);
	$str = str_replace('&#039;', '"', $str);
	$str = str_replace('&lt;', '<', $str);
	$str = str_replace('&gt;', '>', $str);
	return $str;
}


$row=$dsql->GetOne("SELECT COUNT($id) AS dd FROM $table");
$totalnum=$row['dd'];
$rows=$dsql->GetOne("SELECT COUNT($id) AS dd FROM $table WHERE ischeck=1");
$checknum=$rows['dd'];
$rowss=$dsql->GetOne("SELECT COUNT($id) AS dd FROM $table WHERE ischeck=0");
$ischecknum=$rowss['dd'];
$sql = "SELECT * FROM $table WHERE 1=1 $where ORDER BY dtime DESC";
$dlist = new DataListCP();
$dlist->pageSize = 20;
$dlist->SetParameter("totalnum",$totalnum);
$dlist->SetParameter("checknum",$checknum);
$dlist->SetParameter("ischecknum",$ischecknum);
$dlist->SetTemplate($tpl);
$dlist->SetSource($sql);
$dlist->Display();
?>