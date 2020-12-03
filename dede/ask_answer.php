<?php

require_once(dirname(__FILE__).'/config.php');

if(!isset($check))
{
	$check = 0;
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
	$wheresql = $check == 1 ? "where  ifcheck=0" : "where  ifcheck>=0";
	if($tid2)
	{
		$wheresql .= " and tid2=$tid2";
	}
	else if($tid)
	{
		$wheresql .= " and tid=$tid";
	}
	$query = "select * from `#@__askanswer` $wheresql order by id desc";
	updatecount();
	$dlist = new DataListCP();
	$dlist->pageSize = 20;
	$dlist->SetParameter("tid",$tid);
	$dlist->SetParameter("tid2",$tid2);
	$dlist->SetParameter("check",$check);
	$dlist->SetTemplet(DEDEADMIN."/templets/ask_answer.htm");
	$dlist->SetSource($query);
	$dlist->Display();
	exit();
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
		$okaids = Array();
		foreach($qstrs as $aid)
		{
			if(!isset($okaids[$aid]))
			{
				$dsql->SetQuery("delete from #@__askanswer where id='$aid'");
				$dsql->ExecuteNoneQuery();
			}else{
				$okaids[$aid] = 1;
			}
		}
		updatecount();
		ShowMsg("成功删除指定的回答！",'ask_answer.php');
		exit();
	}//确定刪除操作完成

	//删除确认消息
	$wintitle = "文档管理-删除答案";
	$wecome_info = "<a href='ask_answer.php'>答案管理</a>::删除答案";
	$win = new OxWindow();
	$win->Init("ask_answer.php","js/blank.js","POST");
	$win->AddHidden("fmdo","yes");
	$win->AddHidden("action",$action);
	$win->AddHidden("qstr",$qstr);
	$win->AddHidden("aid",$aid);
	$win->AddTitle("你确实要删除“ $qstr 和 $aid ”这些回答？");
	$winform = $win->GetWindow("ok");
	$win->Display();

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
		$dsql->SetQuery("Update #@__askanswer set ifcheck='1' where id='$aid' and ifcheck='0'");
		$dsql->ExecuteNoneQuery();
	}
	ShowMsg("审核成功",'ask_answer.php');
	exit();
}

function updatecount()
{
	global $dsql;
	$dsql->SetQuery("select id, reid from #@__asktype");
	$dsql->Execute('asktype');
	while($row = $dsql->getarray('asktype'))
	{
		if($row['reid'] == 0){
			$dsql->SetQuery("select count(*) as dd from #@__ask where tid=$row[id]");
			$dsql->Execute('top');
			$asknum = $dsql->getarray('top');
			$dsql->ExecuteNoneQuery("update #@__asktype set asknum=".$asknum['dd']." where id=".$row['id']);
		}else{
			$dsql->SetQuery("select count(*) as dd from #@__ask where tid2=$row[id]");
			$dsql->Execute('sub');
			$asknum = $dsql->getarray('sub');
			$dsql->ExecuteNoneQuery("update #@__asktype set asknum=".$asknum['dd']." where id=".$row['id']);
		}
	}
}

?>