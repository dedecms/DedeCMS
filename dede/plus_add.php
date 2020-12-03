<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_plus');
if(empty($dopost)) $dopost = "";
if($dopost=="save"){
	$plusname = str_replace("\\'","",$plusname);
	$link = str_replace("\\'","",$link);
	$target = str_replace("\\'","",$target);
	$menustring = "<m:item name=\\'$plusname\\' link=\\'$link\\' rank=\\'plus_$plusname\\' target=\\'$target\\' />";
  $dsql = new DedeSql(false);
  $dsql->SetQuery("Insert Into #@__plus(plusname,menustring,writer,isshow,filelist) Values('$plusname','$menustring','$writer','1','$filelist');");
  $dsql->Execute();
  $dsql->Close();
  ShowMsg("成功安装一个插件,请刷新导航菜单!","plus_main.php");
	exit();
}

require_once(dirname(__FILE__)."/templets/plus_add.htm");

ClearAllLink();
?>