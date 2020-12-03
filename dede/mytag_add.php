<?php 
require(dirname(__FILE__)."/config.php");
CheckPurview('temp_Other');
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
if(empty($dopost)) $dopost = "";
//////////////////////////////////////////
if($dopost=="save")
{
	$tagname = trim($tagname);
	$dsql = new DedeSql(false);
	$row = $dsql->GetOne("Select typeid From #@__mytag where typeid='$typeid' And tagname like '$tagname'");
	if(is_array($row)){
		$dsql->Close();
		ShowMsg("在相同栏目下已经存在同名的标记！","-1");
		exit();
	}
	$starttime = GetMkTime($starttime);
	$endtime = GetMkTime($endtime);
	$inQuery = "
	 Insert Into #@__mytag(typeid,tagname,timeset,starttime,endtime,normbody,expbody)
	 Values('$typeid','$tagname','$timeset','$starttime','$endtime','$normbody','$expbody');
	";
	$dsql->SetQuery($inQuery);
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("成功增加一个自定义标记！","mytag_main.php");
	exit();
}
$startDay = time();
$endDay = AddDay($startDay,30);
$startDay = GetDateTimeMk($startDay);
$endDay = GetDateTimeMk($endDay);

require_once(dirname(__FILE__)."/templets/mytag_add.htm");

ClearAllLink();
?>