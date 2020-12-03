<?php
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);
if($cfg_mb_lit=='Y')
{
	ShowMsg("由于系统开启了精简版会员空间，你访问的功能不可用！","-1");
	exit();
}
require_once(DEDEINC."/datalistcp.class.php");

if(!isset($ftype))
{
	$ftype = 0;
}
if(!isset($dopost))
{
	$dopost = '';
}

//更改好友状态
if($dopost=='upsta')
{
	$ids = ereg_replace("[^0-9,]","",$ids);
	if($sta=='good')
	{
		$upsta = " ftype=1 ";
	}
	else if($sta=='bad')
	{
		$upsta = " ftype=-1 ";
	}
	else
	{
		$upsta = " ftype=0 ";
	}
	$dsql->ExecuteNoneQuery("Update `#@__member_friends` set $upsta where id in($ids) And mid='{$cfg_ml->M_ID}' ");
	if($sta=='good')
	{
		ShowMsg("成功把指定好友设为关注好友！","myfriend.php?ftype=1");
	}
	else if($sta=='bad')
	{
		ShowMsg("成功把指定好友放入黑名单！","myfriend.php?ftype=-1");
	}
	else
	{
		ShowMsg("成功把指定好友转为普通好友！","myfriend.php");
	}
	exit();
}

//删除好友
else if($dopost=='del')
{
	$ids = ereg_replace("[^0-9,]","",$ids);
	$dsql->ExecuteNoneQuery("Delete From `#@__member_friends` where id in($ids) And mid='{$cfg_ml->M_ID}' ");
	ShowMsg("成功删除所选的好友！","myfriend.php?ftype=".$ftype);
	exit();
}

//浏览
else
{
	$wsql = '';
	if(empty($ftype))
	{
		$wsql = " mid='{$cfg_ml->M_ID}' And ftype <>  '-1' ";
		$tname = "所有好友";
	}
	else if($ftype==1)
	{
		$wsql = " mid='{$cfg_ml->M_ID}' And ftype =  '1' ";
		$tname = "特别关注";
	}
	else if($ftype==-1)
	{
		$wsql = " mid='{$cfg_ml->M_ID}' And ftype =  '-1' ";
		$tname = "黑名单";
	}

	$query = "Select * From `#@__member_friends` where  $wsql order by id desc";
	$dlist = new DataListCP();
	$dlist->pageSize = 20;
	$dlist->SetParameter("ftype",$ftype);
	$dlist->SetTemplate(dirname(__FILE__).'/templets/myfriend.htm');
	$dlist->SetSource($query);
	$dlist->Display();
}

function getUserInfo($uid,$_field = 'uname')
{
	global $dsql;
	$row = $dsql->GetOne("SELECT M.*,S.spacename,S.sign FROM #@__member AS M LEFT JOIN #@__member_space AS S ON M.mid=M.mid WHERE M.mid='$uid'");
	if(isset($row[$_field]))
	{
		if($_field == 'face')
		{
			$row[$_field] = empty($row[$_field]) ? 'images/dfboy.gif' : $row[$_field];
		}
		return $row[$_field];
	}
	else return '';
}
?>