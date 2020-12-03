<?php 
require(dirname(__FILE__)."/config.php");
CheckPurview('temp_Other');
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
if(empty($dopost)) $dopost = "";
$aid = ereg_replace("[^0-9]","",$aid);
if( empty($_COOKIE['ENV_GOBACK_URL']) ) $ENV_GOBACK_URL = "mytag_main.php";
else $ENV_GOBACK_URL = $_COOKIE['ENV_GOBACK_URL'];
//////////////////////////////////////////
if($dopost=="delete")
{
	$dsql = new DedeSql(false);
	$dsql->SetQuery("Delete From #@__mytag where aid='$aid'");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("成功删除一个自定义标记！",$ENV_GOBACK_URL);
	exit();
}
else if($dopost=="saveedit")
{
	$dsql = new DedeSql(false);
	$starttime = GetMkTime($starttime);
	$endtime = GetMkTime($endtime);
	$query = "
	 Update #@__mytag
	 set
	 typeid='$typeid',
	 timeset='$timeset',
	 starttime='$starttime',
	 endtime='$endtime',
	 normbody='$normbody',
	 expbody='$expbody'
	 where aid='$aid'
	";
	$dsql->SetQuery($query);
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("成功更改一个自定义标记！",$ENV_GOBACK_URL);
	exit();
}
else if($dopost=="getjs")
{
	require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
	$jscode = "<script src='{$cfg_plus_dir}/mytag_js.php?aid=$aid' language='javascript'></script>";
	$showhtml = "<xmp style='color:#333333;background-color:#ffffff'>\r\n\r\n$jscode\r\n\r\n</xmp>";
  $showhtml .= "预览：<iframe name='testfrm' frameborder='0' src='mytag_edit.php?aid={$aid}&dopost=testjs' id='testfrm' width='100%' height='200'></iframe>";
  $wintitle = "宏标记定义-获取JS";
	$wecome_info = "<a href='ad_main.php'><u>宏标记定义</u></a>::获取JS";
  $win = new OxWindow();
  $win->Init();
  $win->AddTitle("以下为选定宏标记的JS调用代码：");
  $winform = $win->GetWindow("hand",$showhtml);
  $win->Display();
	exit();
}
else if($dopost=="testjs")
{
	header("Content-Type: text/html; charset={$cfg_ver_lang}");
	echo "<script src='{$cfg_plus_dir}/mytag_js.php?aid=$aid' language='javascript'></script>";
	exit();
}
$dsql = new DedeSql(false);
$row = $dsql->GetOne("Select * From #@__mytag where aid='$aid'");

require_once(dirname(__FILE__)."/templets/mytag_edit.htm");

ClearAllLink();
?>