<?php

/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @rcsfile 	$RCSfile: story_edit.php,v $
 * @revision 	$Revision: 1.1 $
 * @date 	$Date: 2009/08/04 04:06:26 $
 */

require_once(dirname(__FILE__)."/../member/config.php");
CheckRank(0,0);
$menutype = 'mydede';
if(!isset($action))
{
	$action = '';
}
if(empty($dopost))
{
	//读取所有栏目
	$dsql->SetQuery("Select id,classname,pid,rank From #@__story_catalog order by rank asc");
	$dsql->Execute();
	$ranks = Array();
	$btypes = Array();
	$stypes = Array();
	while($row = $dsql->GetArray())
	{
		if($row['pid']==0)
		{
			$btypes[$row['id']] = $row['classname'];
		}
		else
		{
			$stypes[$row['pid']][$row['id']] = $row['classname'];
		}
		$ranks[$row['id']] = $row['rank'];
	}
	$lastid = $row['id'];
	$msg = '';
	$books = $dsql->GetOne("Select * From #@__story_books where bid='$bookid' and mid={$cfg_ml->M_ID} ");
	if(!is_array($books))
	{
		ShowMsg('图书ID错误，请返回','-1');
		exit;
	}
	if($cfg_book_ifcheck == 'Y' && $books['ischeck'] == 1)
	{
		ShowMsg('禁止修改，请返回','-1');
		exit;
	}
	require_once(dirname(__FILE__)."/templets/book/story_edit.htm");
}
elseif($dopost == 'edit')
{
	require_once(DEDEINC.'/image.func.php');
	require_once(DEDEINC.'/oxwindow.class.php');
	require_once(DEDEMEMBER."/inc/inc_archives_functions.php");
	if($catid==0)
	{
		ShowMsg("请指定图书所属栏目！","-1");
		exit();
	}
	
	//获得父栏目
	$nrow = $dsql->GetOne("Select * From #@__story_catalog where id='$catid' ");
	$bcatid = $nrow['pid'];
	$booktype = $nrow['booktype'];
	$pubdate = GetMkTime($pubdate);
	$bookname = addslashes(cn_substr(stripslashes($bookname),50));
	if($keywords!="")
	{
		$keywords = trim(addslashes(cn_substr(stripslashes($keywords),60)));
	}

	//处理上传的缩略图
	if(!empty($litpic))
	{
		$litpic = MemberUploads('litpic',$litpic,$cfg_ml->M_ID,'image','',$cfg_ddimg_width,$cfg_ddimg_height,false);
		$litpic = " litpic='$litpic', ";
	}
	else
	{
		$litpic = "";
	}
	$userip = getip();

	//自动摘要
	if($description=="" && $cfg_auot_description>0)
	{
		$description = stripslashes(cn_substr(html2text($body),$cfg_auot_description));
		$description = addslashes($description);
	}
	$status = $status == 1 ? 1 : 0;
	$upQuery = "
Update `#@__story_books`
set catid='$catid',
bcatid='$bcatid',
bookname='$bookname',
author='$author',
$litpic
pubdate='$pubdate',
lastpost='$pubdate',
description='$description',
status = '$status',
body='$body',
keywords='$keywords'
where bid='$bookid' and mid='{$cfg_ml->M_ID}'
";
	if(!$dsql->ExecuteNoneQuery($upQuery))
	{
		ShowMsg("更新数据库时出错，请检查！".$dsql->GetError(),"-1");
		exit();
	}

	//生成HTML
	require_once(dirname(__FILE__).'/include/story.view.class.php');
	$bv = new BookView($bookid,'book');
	$artUrl = $bv->MakeHtml();
	$bv->Close();

	//返回成功信息
	$msg = "
　　请选择你的后续操作：
<a href='story_edit.php?bookid={$bookid}'><u>继续修改</u></a>
&nbsp;&nbsp;
<a href='story_add.php?catid={$catid}'><u>发布新图书</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>预览图书</u></a>
&nbsp;&nbsp;
<a href='story_add_content.php?bookid={$bookid}'><u>增加图书内容</u></a>
&nbsp;&nbsp;
<a href='mybooks.php'><u>管理图书</u></a>
";
	$wintitle = "成功修改图书！";
	$wecome_info = "连载管理::修改图书";
	$win = new OxWindow();
	$win->AddTitle("成功修改一本图书：");
	$win->AddMsgItem($msg);
	$winform = $win->GetWindow("hand","&nbsp;",false);
	$win->Display();
}
?>