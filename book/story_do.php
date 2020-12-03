<?php

/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @rcsfile 	$RCSfile: story_do.php,v $
 * @revision 	$Revision: 1.1 $
 * @date 	$Date: 2009/08/04 04:06:25 $
 */

require_once(dirname(__FILE__)."/../member/config.php");
CheckRank(0,0);
require_once(DEDEINC.'/oxwindow.class.php');
if(empty($action))
{
	ShowMsg("你没指定任何参数！","-1");
	exit();
}

/*--------------------
function DelBook()
删除整本图书
-------------------*/
if($action=='delbook')
{
	$row = $dsql->GetOne("Select booktype From #@__story_books where id='$bid' and mid='$cfg_ml->M_ID' ");
	if(is_array($row))
	{
		$dsql->ExecuteNoneQuery("Delete From #@__story_books where id='$bid' and mid='$cfg_ml->M_ID' ");
		$dsql->ExecuteNoneQuery("Delete From #@__story_chapter  where bookid='$bid' and mid='$cfg_ml->M_ID' ");
		$dsql->ExecuteNoneQuery("Delete From #@__story_content where bookid='$bid' and mid='$cfg_ml->M_ID' ");
		if(empty($ENV_GOBACK_URL))
		{
			$ENV_GOBACK_URL = 'mybooks.php';
		}
		ShowMsg("成功删除一本图书！",$ENV_GOBACK_URL);
		exit();
	}
	else
	{
		ShowMsg("对不起，你没有权限！","-1");
		exit();
	}
}

/*--------------------
function DelStoryContent()
删除图书内容
-------------------*/
else if($action=='delcontent')
{
	$row = $dsql->GetOne("Select bigpic,chapterid,bookid From #@__story_content where id='$cid' and mid='$cfg_ml->M_ID' ");
	if(!is_array($row))
	{
		ShowMsg("对不起，你没有权限！","-1");
		exit();
	}
	$chapterid = $row['chapterid'];
	$bookid = $row['bookid'];
	$dsql->ExecuteNoneQuery(" Delete From #@__story_content where id='$cid' and mid='$cfg_ml->M_ID' ");

	//更新图书记录
	$row = $dsql->GetOne("Select count(id) as dd From #@__story_content where bookid='$bookid' and mid='$cfg_ml->M_ID' ");
	$dsql->ExecuteNoneQuery("Update #@__story_books set postnum='{$row['dd']}' where id='$bookid' and mid='$cfg_ml->M_ID' ");

	//更新章节记录
	$row = $dsql->GetOne("Select count(id) as dd From #@__story_content where chapterid='$chapterid' and mid='$cfg_ml->M_ID' ");
	$dsql->ExecuteNoneQuery("Update #@__story_chapter set postnum='{$row['dd']}' where id='$chapterid' and mid='$cfg_ml->M_ID' ");
	ShowMsg("成功删除指定内容！",$ENV_GOBACK_URL);
	exit();
}

/*--------------------
function EditChapter()
保存章节信息
-------------------*/
else if($action=='editChapter')
{
	$dsql->ExecuteNoneQuery("Update #@__story_chapter set chaptername='$chaptername',chapnum='$chapnum' where id='$cid' and mid='$cfg_ml->M_ID' ");
	AjaxHead();
	echo "<font color='red'>成功更新章节：{$chaptername} ！ [<a href=\"javascript:CloseLayer('editchapter')\">关闭提示</a>]</font> <br /><br /> 提示：修改章节名称或章节序号直接在左边修改，然后点击右边的 [更新] 会保存。 ";
	exit();
}

/*--------------------
function DelChapter()
删除章节信息
-------------------*/
else if($action=='delChapter')
{
	$row = $dsql->GetOne("Select c.bookid,b.booktype From #@__story_chapter c left join  #@__story_books b on b.id=c.bookid where c.id='$cid' and c.mid='$cfg_ml->M_ID' ");
	if(!is_array($row))
	{
		ShowMsg("对不起，你没有权限！","-1");
		exit();
	}
	$bookid = $row['bookid'];
	$booktype = $row['booktype'];
	$dsql->ExecuteNoneQuery("Delete From #@__story_chapter where id='$cid' and mid='$cfg_ml->M_ID' ");
	$dsql->ExecuteNoneQuery("Delete From #@__story_content where chapterid='$cid' and mid='$cfg_ml->M_ID' ");

	//更新图书记录
	$row = $dsql->GetOne("Select count(id) as dd From #@__story_content where bookid='$bookid' and mid='$cfg_ml->M_ID' ");
	$dsql->ExecuteNoneQuery("Update #@__story_books set postnum='{$row['dd']}' where id='$bookid' and mid='$cfg_ml->M_ID' ");
	ShowMsg("成功删除指定章节！",$ENV_GOBACK_URL);
	exit();
}

/*---------------
function EditChapterAll()
批量修改章节
-------------------*/
else if($action=='upChapterSort')
{
	if(isset($ids) && is_array($ids))
	{
		foreach($ids as $cid)
		{
			$chaptername = ${'chaptername_'.$cid};
			$chapnum= ${'chapnum_'.$cid};
			$dsql->ExecuteNoneQuery("Update #@__story_chapter set chaptername='$chaptername',chapnum='$chapnum' where id='$cid' and mid='$cfg_ml->M_ID' ");
		}
	}
	ShowMsg("成功更新指定章节信息！",$ENV_GOBACK_URL);
	exit();
}

/*---------------
function EditChapterAll()
批量修改章节
-------------------*/
else if($action == "delstorychapter")
{
	if(isset($ids) && is_array($ids)) foreach($ids as $cid)
	{
		$dsql->ExecuteNoneQuery("Delete From #@__story_chapter where id='$cid' and mid='$cfg_ml->M_ID' ");
	}
	ShowMsg("删除成功！",$ENV_GOBACK_URL);
	exit();
}
?>