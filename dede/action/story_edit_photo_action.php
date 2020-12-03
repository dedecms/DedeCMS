<?php
require_once(dirname(__FILE__)."/../config.php");
CheckPurview('story_Edit');
require_once(dirname(__FILE__)."/../../include/inc_photograph.php");
require_once(dirname(__FILE__)."/../../include/pub_oxwindow.php");
require_once(dirname(__FILE__)."/../inc/inc_archives_functions.php");

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


$addtime = mytime();

//处理上传的缩略图
if(!isset($isremote)) $isremote = 0;
$bigpic = UploadOneImage('imgfile',$imgurl,$isremote);

$adminID = $cuserLogin->getUserID();


//----------------------------------
$inQuery = "
   Update `#@__story_content` set `title`='$title',`bookname`='$bookname',
   `chapterid`='$chapterid',`sortid`='$sortid',`bigpic`='$bigpic'
  where id='$cid'
";

if(!$dsql->ExecuteNoneQuery($inQuery)){
	ShowMsg("把数据保存到数据库时出错，请检查！".str_repolace("'","`",$dsql->GetError().$inQuery),"-1");
	$dsql->Close();
	exit();
}

$arcID = $cid;

//生成HTML
//---------------------------------

//$artUrl = MakeArt($arcID,true);
if($artcontentUrl=="") $artcontentUrl = $cfg_book_url."/show-photo.php?id=$arcID&bookid=$bookid&chapterid=$chapterid";

require_once(dirname(__FILE__).'/../../include/inc_arcbook_view.php');
$bv = new BookView($bookid,'book');
$artUrl = $bv->MakeHtml();
$bv->Close();

//---------------------------------
//返回成功信息
//----------------------------------
$msg = "
　　请选择你的后续操作：
<a href='../story_photo_edit.php?c={$cid}'><u>继续修改</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>预览漫画</u></a>
&nbsp;&nbsp;
<a href='$artcontentUrl' target='_blank'><u>预览内容</u></a>
&nbsp;&nbsp;
<a href='../story_list_content.php?bookid={$bookid}'><u>管理所有内容</u></a>
&nbsp;&nbsp;
<a href='../story_books.php'><u>管理所有图书</u></a>
";

$wintitle = "成功修改内容！";
$wecome_info = "连载管理::修改漫画内容";
$win = new OxWindow();
$win->AddTitle("成功发布漫画：");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();
ClearAllLink();
?>