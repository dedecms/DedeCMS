<?php

/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @rcsfile 	$RCSfile: story_list_chapter.php,v $
 * @revision 	$Revision: 1.1 $
 * @date 	$Date: 2009/08/04 04:06:27 $
 */

require_once(dirname(__FILE__)."/../member/config.php");
require_once(DEDEINC."/datalistcp.class.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
CheckRank(0,0);
$menutype = 'mydede';
if(!isset($action))
{
	$action = '';
}
if(!isset($keyword))
{
	$keyword = "";
}
if(!isset($bid))
{
	$bid = 0;
}
if(!empty($bookid))
{
	$bid = $bookid;
}
$addquery = " mid='$cfg_ml->M_ID' and id>0 ";
$orderby = " order by id desc ";
if($keyword!="")
{
	$addquery .= " And (bookname like '%$keyword%' Or chaptername like '%$keyword%') ";
}
if($bid!=0)
{
	$addquery .= " And bookid='$bid' ";
}
if(empty($bookname))
{
	$bookname = '';
}
if(empty($booktype))
{
	$booktype = '0';
}
$row = $dsql->GetOne("SELECT bookname,booktype FROM #@__story_books WHERE bid = '$bookid'");
if(is_array($row)){
$bookname = $row['bookname'];
$booktype = $row['booktype'];
}
$query = "Select * From #@__story_chapter where $addquery $orderby";

$dlist = new DataListCP();
$dlist->pageSize = 20;
$dlist->SetParameter("keyword",$keyword);
$dlist->SetParameter("bid",$bid);
$dlist->SetTemplate(dirname(__FILE__)."/templets/book/story_list_chapter.htm");
$dlist->SetSource($query);
$dlist->Display();
$dlist->Close();
?>