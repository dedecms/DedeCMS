<?php

/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @rcsfile 	$RCSfile: story_stow.php,v $
 * @revision 	$Revision: 1.1 $
 * @date 	$Date: 2009/08/04 04:06:27 $
 */

require_once(dirname(__FILE__)."/../member/config.php");
require_once(DEDEINC."/datalistcp.class.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
CheckRank(0,0);
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
if(!isset($cid))
{
	$cid = 0;
}

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
$addquery = "";
$orderby = " order by b.id desc ";
if($catid!=0)
{
	$addquery .= " And (b.bcatid='$catid' Or b.catid='$catid') ";
}
if($keyword!="")
{
	$addquery .= " And (b.bookname like '%$keyword%' Or b.author like '%$keyword%') ";
}
$query = "
   Select b.id,b.catid,b.bookname,b.booktype,b.litpic,b.postnum,b.senddate,b.ischeck, c.id as cid,c.classname From #@__story_books b
   left join #@__story_catalog c on c.id = b.catid where mid={$cfg_ml->M_ID} and b.id>0 $addquery $orderby
";
$dlist = new DataListCP();
$dlist->pageSize = 20;
$dlist->SetParameter("keyword",$keyword);
$dlist->SetParameter("catid",$cid);
$dlist->SetParameter("orderby",$orderby);
$dlist->SetTemplate(dirname(__FILE__)."/templets/book/story_stow.htm");
$dlist->SetSource($query);
$dlist->Display();
$dlist->Close();
?>