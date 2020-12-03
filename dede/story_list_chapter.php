<?php

/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @rcsfile 	$RCSfile: story_list_chapter.php,v $
 * @revision 	$Revision: 1.1 $
 * @date 	$Date: 2009/08/04 04:07:17 $
 */

require_once(dirname(__FILE__)."/config.php");
require_once DEDEINC.'/datalistcp.class.php';
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
CheckPurview('story_books');
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

$addquery = " id>0 ";
$orderby = " order by id desc ";
if($keyword!="")
{
	$addquery .= " And (bookname like '%$keyword%' Or chaptername like '%$keyword%') ";
}
if($bid!=0)
{
	$addquery .= " And bookid='$bid' ";
}

$query = "
   Select * From #@__story_chapter where $addquery $orderby
";
$dlist = new DataListCP();
$dlist->pageSize = 20;
$dlist->SetParameter("keyword",$keyword);
$dlist->SetParameter("bid",$bid);
$dlist->SetTemplate(DEDEADMIN.'/templets/story_list_chapter.htm');
$dlist->SetSource($query);
$dlist->Display();
?>