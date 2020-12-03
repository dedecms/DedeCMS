<?php
require_once('config.php');
CheckPurview('t_Move');
require_once(DEDEINC.'/oxwindow.class.php');
require_once(DEDEINC.'/typelink.class.php');
$typeid = isset($typeid) ? intval($typeid) : 0;
if(empty($dopost))
{
	$dopost = 'movelist';
}
$row  = $dsql->GetOne(" Select reid,typename,channeltype From `#@__arctype` where id='$typeid' ");
$typename = $row['typename'];
$reid = $row['reid'];
$channelid = $row['channeltype'];

//移动栏目
if($dopost=='moveok')
{
	if($typeid==$movetype)
	{
		ShowMsg('移对对象和目标位置相同！','catalog_main.php');
		exit();
	}
	if(IsParent($movetype,$typeid,$dsql))
	{
		ShowMsg('不能从父类移动到子类！','catalog_main.php');
		exit();
	}
	$dsql->ExecuteNoneQuery("Update `#@__arctype` set reid='$movetype' where id='$typeid'");
	UpDateCatCache();
	ShowMsg('成功移动目录！','catalog_main.php');
	exit();
}

function IsParent($myid,$topid,$dsql)
{
	$row = $dsql->GetOne("select id,reid,topid from #@__arctype where topid='$myid' or id='$myid'");
	if($row['reid']==$topid)
	{
		return true;
	}
	else if($row['reid']==0)
	{
		return false;
	}
	else
	{
		return IsParent($row['reid'],$topid,$dsql);
	}
}

$tl = new TypeLink($typeid);
$typeOptions = $tl->GetOptionArray(0,0,$channelid);
$wintitle = "移动栏目";
$wecome_info = "<a href='catalog_main.php'>栏目管理</a> &gt;&gt; 移动栏目";
$win = new OxWindow();
$win->Init('catalog_move.php','js/blank.js','POST');
$win->AddHidden('typeid',$typeid);
$win->AddHidden('dopost','moveok');
$win->AddTitle("移动目录时不会删除原来已创建的列表，移动后需重新对栏目创建HTML。");
$win->AddItem('你选择的栏目是：',"$typename($typeid)");
$win->AddItem('你希望移动到那个栏目？',"<select name='movetype'>\r\n<option value='0'>移动为顶级栏目</option>\r\n$typeOptions\r\n</select>");
$win->AddItem('注意事项：','不允许从父级移动到子级目录，只允许子级到更高级或同级或不同父级的情况。');
$winform = $win->GetWindow('ok');
$win->Display();

?>