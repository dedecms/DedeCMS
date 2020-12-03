<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_plus');
$aid = ereg_replace("[^0-9]","",$aid);
if($dopost=="show")
{
	$dsql = new DedeSql(false);
	$dsql->SetQuery("update #@__plus set isshow=1 where aid='$aid';");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("成功启用一个插件,请刷新导航菜单!","plus_main.php");
	exit();
}
else if($dopost=="hide")
{
	$dsql = new DedeSql(false);
	$dsql->SetQuery("update #@__plus set isshow=0 where aid='$aid';");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("成功禁用一个插件,请刷新导航菜单!","plus_main.php");
	exit();
}
else if($dopost=="delete")
{
	if(empty($job)) $job="";
  if($job=="") //确认提示
  {
  	require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
  	$wintitle = "删除插件";
	  $wecome_info = "<a href='plus_main.php'>插件管理</a>::删除插件";
	  $win = new OxWindow();
	  $win->Init("plus_edit.php","js/blank.js","POST");
	  $win->AddHidden("job","yes");
	  $win->AddHidden("dopost",$dopost);
	  $win->AddHidden("aid",$aid);
	  $win->AddTitle("你确实要删除'".$title."'这个插件？");
	  $winform = $win->GetWindow("ok");
	  $win->Display();
	  exit();
  }
  else if($job=="yes") //操作
  {
  	$dsql = new DedeSql(false);
	  $dsql->SetQuery("Delete From #@__plus where aid='$aid';");
	  $dsql->ExecuteNoneQuery();
	  $dsql->Close();
	  ShowMsg("成功删除一个插件,请刷新导航菜单!","plus_main.php");
	  exit();
  }
}
else if($dopost=="saveedit") //保存更改
{
	$dsql = new DedeSql(false);
	$dsql->SetQuery("Update #@__plus set plusname='$plusname',menustring='$menustring',filelist='$filelist' where aid='$aid';");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("成功更改插件的配置!","plus_main.php");
  exit();
}
$dsql = new DedeSql(false);
$row = $dsql->GetOne("Select * From #@__plus where aid='$aid'");

require_once(dirname(__FILE__)."/templets/plus_edit.htm");

ClearAllLink();

?>