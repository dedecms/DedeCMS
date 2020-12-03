<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_MakeHtml');
require_once(DEDEINC."/arc.partview.class.php");
if(empty($typeid))
{
	$typeid = 0;
}
if(empty($templet))
{
	$templet = "plus/js.htm";
}
if(empty($uptype))
{
	$uptype = "all";
}
if($uptype == "all")
{
	$row = $dsql->GetOne("Select id From #@__arctype where id>'$typeid' And ispart<>2 order by id asc limit 0,1;");
	if(!is_array($row))
	{
		echo "完成所有文件更新！";
		exit();
	}
	else
	{
		$pv = new PartView($row['id']);
		$pv->SetTemplet($cfg_basedir.$cfg_templets_dir."/".$templet);
		$pv->SaveToHtml($cfg_basedir.$cfg_cmspath."/data/js/".$row['id'].".js");
		$typeid = $row['id'];
		ShowMsg("成功更新".$cfg_cmspath."/data/js/".$row['id'].".js，继续进行操作！","makehtml_js_action.php?typeid=$typeid",0,100);
		exit();
	}
}
else
{
	$pv = new PartView($typeid);
	$pv->SetTemplet($cfg_basedir.$cfg_templets_dir."/".$templet);
	$pv->SaveToHtml($cfg_basedir.$cfg_cmspath."/data/js/".$typeid.".js");
	echo "成功更新".$cfg_cmspath."/data/js/".$typeid.".js！";
	echo "预览：";
	echo "<hr>";
	echo "<script src='".$cfg_cmspath."/data/js/".$typeid.".js'></script>";
	exit();
}

?>