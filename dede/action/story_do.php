<?php 
require_once(dirname(__FILE__)."/../config.php");
CheckPurview('story_Del');
require_once(dirname(__FILE__)."/../../include/pub_oxwindow.php");
if(empty($action)){
	ShowMsg("你没指定任何参数！","-1");
	exit();
}

/*--------------------
function DelBook()
删除整本图书
-------------------*/
if($action=='delbook')
{
	$dsql = new DedeSql(false);
	$bids = explode(',',$bid);
	$i = 0;
	foreach($bids as $bid){
	  if(intval($bid)<=0) continue;
	  $i++;
	  $row = $dsql->GetOne("Select booktype From #@__story_books where id='$bid' ");
	  $dsql->ExecuteNoneQuery("Delete From #@__story_books where id='$bid' ");
	  $dsql->ExecuteNoneQuery("Delete From #@__story_chapter  where bookid='$bid' ");
	  //删除图片
	  if($row['booktype']==1){
		  $dsql->SetQuery("Select bigpic From #@__story_content where bookid='$bid' ");
		  $dsql->Execute();
		  while($row = $dsql->GetArray()){
			  $bigpic = $row['bigpic'];
			  if( $bigpic!="" && !eregi('^http://',$bigpic) ) @unlink($cfg_basedir.$bigpic);
		  }
	  }
	  $dsql->ExecuteNoneQuery("Delete From #@__story_content where bookid='$bid' ");
  }
	$dsql->Close();
	if(empty($ENV_GOBACK_URL)) $ENV_GOBACK_URL = 'story_books.php';
	ShowMsg("成功删除 {$i} 本图书！",$ENV_GOBACK_URL);
	exit();
}
/*--------------------
   function DelStoryContent()
   删除图书内容
-------------------*/
else if($action=='delcontent')
{
	$dsql = new DedeSql();
	$row = $dsql->GetOne("Select bigpic,chapterid,bookid From #@__story_content where id='$cid' ");
	$chapterid = $row['chapterid'];
	$bookid = $row['bookid'];
	
	//如果图片不为空，先删除图片
	if( $row['bigpic']!="" && !eregi('^http://',$row['bigpic']) ) @unlink($cfg_basedir.$row['bigpic']);
	
	$dsql->ExecuteNoneQuery(" Delete From #@__story_content where id='$cid' ");
	
	//更新图书记录
	$row = $dsql->GetOne("Select count(id) as dd From #@__story_content where bookid='$bookid' ");
	$dsql->ExecuteNoneQuery("Update #@__story_books set postnum='{$row['dd']}' where id='$bookid' ");
	
	//更新章节记录
	$row = $dsql->GetOne("Select count(id) as dd From #@__story_content where chapterid='$chapterid' ");
	$dsql->ExecuteNoneQuery("Update #@__story_chapter set postnum='{$row['dd']}' where id='$chapterid' ");
	
	$dsql->Close();
	ShowMsg("成功删除指定内容！",$ENV_GOBACK_URL);
	exit();
}
/*--------------------
   function EditChapter()
   保存章节信息
-------------------*/
else if($action=='editChapter')
{
	$dsql = new DedeSql();
	$dsql->ExecuteNoneQuery("Update #@__story_chapter set chaptername='$chaptername',chapnum='$chapnum' where id='$cid' ");
	AjaxHead();
	echo "<font color='red'>成功更新章节：{$chaptername} ！ [<a href=\"javascript:CloseLayer('editchapter')\">关闭提示</a>]</font> <br /><br /> 提示：修改章节名称或章节序号直接在左边修改，然后点击右边的 [更新] 会保存。 ";
	$dsql->Close();
	exit();
}
/*--------------------
   function DelChapter()
   删除章节信息
-------------------*/
else if($action=='delChapter')
{
	$dsql = new DedeSql();
	$row = $dsql->GetOne("Select c.bookid,b.booktype From #@__story_chapter c left join  #@__story_books b on b.id=c.bookid where c.id='$cid' ");
	$bookid = $row['bookid'];
	$booktype = $row['booktype'];
	$dsql->ExecuteNoneQuery("Delete From #@__story_chapter where id='$cid' ");
	//删除图片
	if($booktype==1)
	{
		$dsql->SetQuery("Select bigpic From #@__story_content where bookid='$bookid' ");
		$dsql->Execute();
		while($row = $dsql->GetArray()){
			$bigpic = $row['bigpic'];
			if( $bigpic!="" && !eregi('^http://',$bigpic) ) @unlink($cfg_basedir.$bigpic);
		}
	}
	$dsql->ExecuteNoneQuery("Delete From #@__story_content where chapterid='$cid' ");
	//更新图书记录
	$row = $dsql->GetOne("Select count(id) as dd From #@__story_content where bookid='$bookid' ");
	$dsql->ExecuteNoneQuery("Update #@__story_books set postnum='{$row['dd']}' where id='$bookid' ");
	$dsql->Close();
	ShowMsg("成功删除指定章节！",$ENV_GOBACK_URL);
	exit();
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
		$dsql = new DedeSql();
	  foreach($ids as $cid){
	    $chaptername = ${'chaptername_'.$cid};
	    $chapnum= ${'chapnum_'.$cid};
	    $dsql->ExecuteNoneQuery("Update #@__story_chapter set chaptername='$chaptername',chapnum='$chapnum' where id='$cid' ");
	  }
	  $dsql->Close();
	}
	ShowMsg("成功更新指定章节信息！",$ENV_GOBACK_URL);
	exit();
}

ClearAllLink();
?>