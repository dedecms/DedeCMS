<?php

require_once(dirname(__FILE__)."/config.php");

if(!isset($status))
{
	$status = 0;
}
if(!isset($tid2))
{
	$tid2 = 0;
}
if(!isset($tid))
{
	$tid = 0;
}

if(empty($action))
{
	require_once(DEDEINC.'/datalistcp.class.php');
	$wheresql = $status == -1 ? " where  status=-1 " : " where  status>=-1 ";
	if($tid2)
	{
		$wheresql .= " and tid2='$tid2' ";
	}
	else if($tid)
	{
		$wheresql .= " and tid='$tid' ";
	}

	$query = "select * from `#@__ask` $wheresql order by id desc";
	updatecount();
	$dlist = new DataListCP();
	$dlist->pageSize = 20;
	$dlist->SetParameter("tid",$tid);
	$dlist->SetParameter("tid2",$tid2);
	$dlist->SetParameter("status",$status);
	$dlist->SetTemplet(DEDEADMIN."/templets/ask_admin.htm");
	$dlist->SetSource($query);
	$dlist->Display();
}
else if($action == 'delete')
{
	CheckPurview('a_Del,a_AccDel,a_MyDel,sys_ArcBatch');
	require_once(DEDEINC."/oxwindow.class.php");
	if(empty($fmdo))
	{
		$fmdo = "";
	}
	if($fmdo=="yes")
	{
		if( $aid!='' && !ereg("(".$aid."`|`".$aid.")",$qstr) )
		{
			$qstr .= "`".$aid;
		}
		if($qstr=='')
		{
			ShowMsg("参数无效！",'-1');
			exit();
		}
		$qstrs = explode("`",$qstr);
		$okaids = Array();
		foreach($qstrs as $aid)
		{
			if(!isset($okaids[$aid]))
			{
				$dsql->ExecuteNoneQuery("delete from `#@__ask` where id='$aid' ");
				$dsql->ExecuteNoneQuery("delete from `#@__askanswer` where askid='$aid' ");
			}else{
				$okaids[$aid] = 1;
			}
		}
		updatecount();
		ShowMsg("成功删除指定的问题！",'ask_admin.php');
		exit();
	}//确定刪除操作完成

	//删除确认消息
	$wintitle = "文档管理-删除问题";
	$wecome_info = "<a href='".$ENV_GOBACK_URL."'>文档管理</a>::删除问题";
	$win = new OxWindow();
	$win->Init("ask_admin.php","js/blank.js","POST");
	$win->AddHidden("fmdo","yes");
	$win->AddHidden("action",$action);
	$win->AddHidden("qstr",$qstr);
	$win->AddHidden("aid",$aid);
	$win->AddTitle("你确实要删除“ $qstr 和 $aid ”这些问题？");
	$winform = $win->GetWindow("ok");
	$win->Display();
	exit();
}
else if($action == 'digest')
{
	CheckPurview('a_Commend,sys_ArcBatch');
	if( $aid!='' && !ereg("(".$aid."`|`".$aid.")",$qstr) )
	{
		$qstr .= "`".$aid;
	}
	if($qstr=='')
	{
		ShowMsg("参数无效！",'-1');
		exit();
	}
	$qstrs = explode("`",$qstr);
	foreach($qstrs as $aid)
	{
		$aid = ereg_replace("[^0-9]","",$aid);
		if($aid=="")
		{
			continue;
		}
		$dsql->SetQuery("Update `#@__ask` set digest='1' where id='$aid' ");
		$dsql->ExecuteNoneQuery();
	}
	ShowMsg("成功把所选的问题设为推荐！",'ask_admin.php');
	exit();
}
else if($action == 'check')
{
	CheckPurview('a_Commend,sys_ArcBatch');
	if( $aid!="" && !ereg("(".$aid."`|`".$aid.")",$qstr) )
	{
		$qstr .= "`".$aid;
	}
	if($qstr=="")
	{
		ShowMsg("参数无效！",'-1');
		exit();
	}
	$qstrs = explode("`",$qstr);
	foreach($qstrs as $aid)
	{
		$aid = ereg_replace("[^0-9]","",$aid);
		if($aid=="")
		{
			continue;
		}
		$dsql->SetQuery("Update `#@__ask` set status='0' where id='$aid' and status=-1 ");
		$dsql->ExecuteNoneQuery();
	}
	ShowMsg("问题审核成功！",'ask_admin.php');
	exit();
}

function updatecount()
{
	global $dsql;
	$dsql->SetQuery("select id, reid from `#@__asktype` ");
	$dsql->Execute('asktype');
	while($row = $dsql->getarray('asktype'))
	{
		if($row['reid'] == 0)
		{
			$dsql->SetQuery("select count(*) as dd from `#@__ask` where tid='{$row['id']}' ");
			$dsql->Execute('top');
			$asknum = $dsql->getarray('top');
			$dsql->ExecuteNoneQuery("update `#@__asktype` set asknum='{$asknum['dd']}' where id='{$row['id']}' ");
		}
		else
		{
			$dsql->SetQuery("select count(*) as dd from `#@__ask` where tid2='{$row['id']}' ");
			$dsql->Execute('sub');
			$asknum = $dsql->getarray('sub');
			$dsql->ExecuteNoneQuery("update `#@__asktype` set asknum='{$asknum['dd']}' where id='{$row['id']}' ");
		}
	}
}

?>