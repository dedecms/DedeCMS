<?php
require(dirname(__FILE__)."/config.php");
CheckPurview('co_EditNote');
if(empty($job))
{
	$job='';
}
if($job=='')
{
	require_once(DEDEINC."/oxwindow.class.php");
	$wintitle = "更改采集规则";
	$wecome_info = "<a href='co_main.php'><u>采集点管理</u></a>::更改采集规则 - 专家更改模式";
	$win = new OxWindow();
	$win->Init("co_edit_text.php","js/blank.js","POST");
	$win->AddHidden("job","yes");
	$win->AddHidden("nid",$nid);
	$row = $dsql->GetOne("Select * From `#@__co_note` where nid='$nid' ");
	$win->AddTitle("索引与基本信息配置：");
	$win->AddMsgItem("<textarea name='listconfig' style='width:100%;height:200px'>{$row['listconfig']}</textarea>");
	$win->AddTitle("字段配置：");
	$win->AddMsgItem("<textarea name='itemconfig' style='width:100%;height:300px'>{$row['itemconfig']}</textarea>");
	$winform = $win->GetWindow("ok");
	$win->Display();
	exit();
}
else
{
	CheckPurview('co_EditNote');
	$query = "update `#@__co_note` set listconfig='$listconfig',itemconfig='$itemconfig' where nid='$nid' ";
	$rs = $dsql->ExecuteNoneQuery($query);
	ShowMsg("成功修改一个规则!","co_main.php");
	exit();
}
?>