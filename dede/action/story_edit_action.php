<?php
require_once(dirname(__FILE__)."/../config.php");
CheckPurview('story_New');
require_once(dirname(__FILE__)."/../../include/inc_photograph.php");
require_once(dirname(__FILE__)."/../../include/pub_oxwindow.php");
require_once(dirname(__FILE__)."/../inc/inc_archives_functions.php");

if(!isset($iscommend)) $iscommend = 0;

if($catid==0){
	ShowMsg("请指定图书所属栏目！","-1");
	exit();
}

$dsql = new DedeSql(false);
//获得父栏目
$nrow = $dsql->GetOne("Select * From #@__story_catalog where id='$catid' ");
$bcatid = $nrow['pid'];
$booktype = $nrow['booktype'];

$pubdate = GetMkTime($pubdate);

$bookname = cn_substr($bookname,50);

if($keywords!="") $keywords = trim(cn_substr($keywords,60));

//处理上传的缩略图
$litpic = GetDDImage('litpic',$litpicname,0);

$adminID = $cuserLogin->getUserID();

//自动摘要
if($description=="" && $cfg_auot_description>0){
	$description = stripslashes(cn_substr(html2text($body),$cfg_auot_description));
	$description = addslashes($description);
}

//----------------------------------
$upQuery = "
Update `#@__story_books`
set catid='$catid',
bcatid='$bcatid',
iscommend='$iscommend',
click='$click',
freenum='$freenum',
bookname='$bookname',
author='$author',
litpic='$litpic',
pubdate='$pubdate',
description='$description',
body='$body',
keywords='$keywords',
status='$status',
ischeck='$ischeck'
where id='$bookid' ";


if(!$dsql->ExecuteNoneQuery($upQuery)){
	ShowMsg("更新数据库时出错，请检查！".$dsql->GetError(),"-1");
	$dsql->Close();
	exit();
}

$dsql->Close();

//生成HTML
//---------------------------------

require_once(dirname(__FILE__).'/../../include/inc_arcbook_view.php');
$bv = new BookView($bookid,'book');
$artUrl = $bv->MakeHtml();
$bv->Close();

//---------------------------------
//返回成功信息
//----------------------------------
$msg = "
　　请选择你的后续操作：
<a href='../story_edit.php?bookid={$bookid}'><u>继续修改</u></a>
&nbsp;&nbsp;
<a href='../story_add.php?catid={$catid}'><u>发布新图书</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>预览图书</u></a>
&nbsp;&nbsp;
<a href='../story_add_content.php?bookid={$bookid}'><u>增加图书内容</u></a>
&nbsp;&nbsp;
<a href='../story_books.php'><u>管理图书</u></a>
";

$wintitle = "成功修改图书！";
$wecome_info = "连载管理::修改图书";
$win = new OxWindow();
$win->AddTitle("成功修改一本图书：");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();

ClearAllLink();
?>