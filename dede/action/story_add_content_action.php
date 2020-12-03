<?php
require_once(dirname(__FILE__)."/../config.php");
CheckPurview('story_New');
require_once(dirname(__FILE__)."/../../include/pub_oxwindow.php");
require_once(dirname(__FILE__)."/../../include/inc_bookfunctions.php");

if( empty($chapterid)
|| (!empty($addchapter) && !empty($chapternew)) )
{
	if(empty($chapternew))
	{
		 ShowMsg("由于你发布的内容没选择章节，系统拒绝发布！","-1");
		 exit();
	}
	$dsql = new DedeSql();
	$row = $dsql->GetOne("Select * From #@__story_chapter where bookid='$bookid' order by chapnum desc");
	if(is_array($row)) $nchapnum = $row['chapnum']+1;
	else $nchapnum = 1;
	$query = "INSERT INTO `#@__story_chapter`(`bookid`,`catid`,`chapnum`,`memberid`,`chaptername`,`bookname`)
            VALUES ('$bookid', '$catid', '$nchapnum', '0', '$chapternew','$bookname');";
	$rs = $dsql->ExecuteNoneQuery($query);
	if($rs){
		$chapterid = $dsql->GetLastID();
	}
	else
  {
  	ShowMsg("增加章节失败，请检查原因！","-1");
		exit();
  }
}else
{
	$dsql = new DedeSql();
}

//获得父栏目
$nrow = $dsql->GetOne("Select * From #@__story_catalog where id='$catid' ");
$bcatid = $nrow['pid'];
$booktype = $nrow['booktype'];

if(empty($bcatid)) $bcatid = 0;
if(empty($booktype)) $booktype = 0;


$addtime = time();

//处理上传的缩略图
//$litpic = GetDDImage('litpic',$litpicname,0);

$adminID = $cuserLogin->getUserID();

//本章最后一个小说的排列顺次序
$lrow = $dsql->GetOne("Select sortid From #@__story_content where bookid='$bookid' And chapterid='$chapterid' order by sortid desc");
if(empty($lrow)) $sortid = 1;
else $sortid = $lrow['sortid']+1;


//----------------------------------
$inQuery = "
INSERT INTO `#@__story_content`(`title`,`bookname`,`chapterid`,`catid`,`bcatid`,`bookid`,`booktype`,`sortid`,
`memberid`,`bigpic`,`body`,`addtime`,`adminid` )
VALUES ('$title','$bookname', '$chapterid', '$catid','$bcatid', '$bookid','$booktype','$sortid', '0', '' , '', '$addtime','$adminID');";

if(!$dsql->ExecuteNoneQuery($inQuery)){
	ShowMsg("把数据保存到数据库时出错，请检查！".$dsql->GetError().$inQuery,"-1");
	$dsql->Close();
	exit();
}

$arcID = $dsql->GetLastID();

WriteBookText($arcID,addslashes($body));

//更新图书的内容数
$row = $dsql->GetOne("Select count(id) as dd From #@__story_content  where bookid = '$bookid' ");
$dsql->ExecuteNoneQuery("Update #@__story_books set postnum='{$row['dd']}',lastpost='".time()."' where id='$bookid' ");
//更新章节的内容数
$row = $dsql->GetOne("Select count(id) as dd From #@__story_content  where bookid = '$bookid' And chapterid='$chapterid' ");
$dsql->ExecuteNoneQuery("Update #@__story_chapter set postnum='{$row['dd']}' where id='$chapterid' ");


//生成HTML
//---------------------------------

//$artUrl = MakeArt($arcID,true);
if($artcontentUrl=="") $artcontentUrl = $cfg_book_path."/story.php?id=$arcID";

require_once(dirname(__FILE__).'/../../include/inc_arcbook_view.php');
$bv = new BookView($bookid,'book');
$artUrl = $bv->MakeHtml();
$bv->Close();

//---------------------------------
//返回成功信息
//----------------------------------
$msg = "
　　请选择你的后续操作：
<a href='../story_add_content.php?bookid={$bookid}'><u>继续发布</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>预览小说</u></a>
&nbsp;&nbsp;
<a href='$artcontentUrl' target='_blank'><u>预览内容</u></a>
&nbsp;&nbsp;
<a href='../story_list_content.php?bookid={$bookid}'><u>管理所有内容</u></a>
&nbsp;&nbsp;
<a href='../story_books.php'><u>管理所有图书</u></a>
";

$wintitle = "成功发布文章！";
$wecome_info = "连载管理::发布文章";
$win = new OxWindow();
$win->AddTitle("成功发布文章：");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();

ClearAllLink();
?>