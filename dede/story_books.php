<?php

/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @rcsfile 	$RCSfile: story_books.php,v $
 * @revision 	$Revision: 1.1 $
 * @date 	$Date: 2009/08/04 04:06:44 $
 */

require_once(dirname(__FILE__)."/config.php");
require_once DEDEINC.'/datalistcp.class.php';
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
CheckPurview('story_list');
if(!isset($action))
{
	$action = '';
}
if(!isset($catid))
{
	$catid = 0;
}
if(!isset($keyword))
{
	$keyword = "";
}
if(!isset($orderby))
{
	$orderby = 0;
}
if(!isset($ischeck))
{
	$ischeck = 0;
}
if(!isset($cid))
{
	$cid = 0;
}
if($action == 'checked')
{
	$id = intval($id);
	$query="UPDATE #@__story_books SET ischeck=1 WHERE bid='$id'";
	if($dsql->ExecuteNoneQuery($query))
	{
		showmsg('审核成功','story_books.php');
		exit();
	}
	else
	{
		showmsg('审核失败','story_books.php');
		exit();
	}
}

//读取所有栏目列表
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
$addquery = "";
if($ischeck == 1)
{
	$addquery .= " and ischeck=0 ";
}
$orderby = " order by b.bid desc ";
if($catid!=0)
{
	$addquery .= " And (b.bcatid='$catid' Or b.catid='$catid') ";
}
if($keyword!="")
{
	$addquery .= " And (b.bookname like '%$keyword%' Or b.author like '%$keyword%') ";
}
$query = "
   Select b.bid,b.catid,b.bookname,b.booktype,b.litpic,b.ischeck,b.postnum,b.senddate,c.id as cid,c.classname From #@__story_books b
   left join #@__story_catalog c on c.id = b.catid where b.bid>0 $addquery $orderby
";
$dlist = new DataListCP();
$dlist->pageSize = 20;
$dlist->SetParameter("keyword",$keyword);
$dlist->SetParameter("catid",$cid);
$dlist->SetParameter("orderby",$orderby);
$dlist->SetTemplate(DEDEADMIN.'/templets/story_books.htm');
$dlist->SetSource($query);
$dlist->Display();
?>