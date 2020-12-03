<?php

/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @rcsfile 	$RCSfile: story_add_action.php,v $
 * @revision 	$Revision: 1.1 $
 * @date 	$Date: 2009/08/04 04:06:24 $
 */

require_once(dirname(__FILE__)."/../member/config.php");
CheckRank(0,0);
include_once(DEDEINC."/image.func.php");
include_once(DEDEINC."/oxwindow.class.php");
require_once(DEDEMEMBER."/inc/inc_archives_functions.php");
if(!isset($iscommend))
{
	$iscommend = 0;
}
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
$litpic = MemberUploads('litpic',$litpic,$cfg_ml->M_ID,'image','',$cfg_ddimg_width,$cfg_ddimg_height,false);
$userip = getip();

//自动摘要
if($description=="" && $cfg_auot_description>0)
{
	$description = stripslashes(cn_substr(html2text($body),$cfg_auot_description));
	$description = addslashes($description);
}
if($cfg_book_ifcheck == 'Y')
{
	$ischeck = 0;
}
else
{
	$ischeck = 1;
}
$tim = time();
$inQuery = "
INSERT INTO `#@__story_books`(`catid`,`bcatid`,`ischeck`,`booktype`,`click`,`bookname`,
`author`,`mid`,`litpic`,`pubdate`,`status`,
`lastpost`,`postnum`,`lastfeedback`,`feedbacknum`,`weekcc`,`monthcc`,`weekup`,`monthup`,
`description`,`body`,`keywords`,`userip`,`senddate` )
VALUES ('$catid','$bcatid','$ischeck','$booktype', '0', '$bookname',
 '$author', '{$cfg_ml->M_ID}', '$litpic', '$pubdate', '0',
 '0', '0', '0', '0', '0', '0', '0', '0',
  '$description' , '$body' , '$keywords', '$userip','$tim');
";
if(!$dsql->ExecuteNoneQuery($inQuery))
{
	$gerr = $dsql->GetError();
	$dsql->Close();
	ShowMsg("把数据保存到数据库时出错，请检查！".$gerr."<hr>".$inQuery,"javascript:;");
	exit();
}
$arcID = $dsql->GetLastID();

//生成HTML
if($cfg_book_ifcheck == 'N')
{
	require_once(dirname(__FILE__).'./include/story.view.class.php');
	$bv = new BookView($arcID,'book');
	$artUrl = $bv->MakeHtml();
	$bv->Close();
}
else
{
	$artUrl = "../book/book.php?id=$arcID";
}

//返回成功信息
$msg = "
　　请选择你的后续操作：
<a href='story_add.php?catid=$catid'><u>继续发布图书</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>查看图书</u></a>
&nbsp;&nbsp;
<a href='story_add_content.php?bookid={$arcID}'><u>增加图书内容</u></a>
&nbsp;&nbsp;
<a href='mybooks.php'><u>管理图书</u></a>
";
$wintitle = "成功发布图书！";
$wecome_info = "连载管理::发布图书";
$win = new OxWindow();
$win->AddTitle("成功发布一本图书：");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();
?>