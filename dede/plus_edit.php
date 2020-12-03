<?
require_once(dirname(__FILE__)."/config.php");
SetPageRank(10);
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
?>