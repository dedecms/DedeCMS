<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_ArcBatch');
require_once(DEDEINC."/typelink.class.php");
require_once(DEDEADMIN."/inc/inc_batchup.php");
@set_time_limit(0);

//typeid,startid,endid,seltime,starttime,endtime,action,newtypeid
//批量操作
//check del move makehtml
//获取ID条件
if(empty($startid))
{
	$startid = 0;
}
if(empty($endid))
{
	$endid = 0;
}
if(empty($seltime))
{
	$seltime = 0;
}
if(empty($typeid))
{
	$typeid = 0;
}
//生成HTML操作由其它页面处理
if($action=="makehtml")
{
	$jumpurl  = "makehtml_archives_action.php?endid=$endid&startid=$startid";
	$jumpurl .= "&typeid=$typeid&pagesize=20&seltime=$seltime";
	$jumpurl .= "&stime=".urlencode($starttime)."&etime=".urlencode($endtime);
	header("Location: $jumpurl");
	exit();
}

$gwhere = " where 1 ";
if($startid >0 )
{
	$gwhere .= " And id>= $startid ";
}
if($endid > $startid)
{
	$gwhere .= " And id<= $endid ";
}

$idsql = '';
if($typeid!=0)
{
	$ids = GetSonIds($typeid);
	$gwhere .= " And typeid in($ids) ";
}
if($seltime==1)
{
	$t1 = GetMkTime($starttime);
	$t2 = GetMkTime($endtime);
	$gwhere .= " And (senddate >= $t1 And senddate <= $t2) ";
}

//特殊操作
if(!empty($heightdone))
{
	$action=$heightdone;
}

//指量审核
if($action=='check')
{
	if(empty($startid) || empty($endid) || $endid < $startid)
	{
		ShowMsg('该操作必须指定起始ID！','javascript:;');
		exit();
	}
	$jumpurl  = "makehtml_archives_action.php?endid=$endid&startid=$startid";
	$jumpurl .= "&typeid=$typeid&pagesize=20&seltime=$seltime";
	$jumpurl .= "&stime=".urlencode($starttime)."&etime=".urlencode($endtime);
	$dsql->SetQuery("Select id,arcrank From `#@__arctiny` $gwhere");
	$dsql->Execute('c');
	while($row = $dsql->GetObject('c'))
	{
		if($row->arcrank==-1)
		{
			$dsql->ExecuteNoneQuery("Update `#@__arctiny` set arcrank=0 where id='{$row->id}'");
			$dsql->ExecuteNoneQuery("Update `#@__archives` set arcrank=0 where id='{$row->id}'");
		}
	}
	ShowMsg("完成数据库的审核处理，准备更新HTML...",$jumpurl);
	exit();
}

//批量删除
else if($action=='del')
{
	if(empty($startid) || empty($endid) || $endid < $startid)
	{
		ShowMsg('该操作必须指定起始ID！','javascript:;');
		exit();
	}
	$dsql->SetQuery("Select id From `#@__archives` $gwhere");
	$dsql->Execute('x');
	$tdd = 0;
	while($row = $dsql->GetObject('x'))
	{
		if(DelArc($row->id))
		{
			$tdd++;
		}
	}
	ShowMsg("成功删除 $tdd 条记录！","javascript:;");
	exit();
}

//删除空标题文档
else if($action=='delnulltitle')
{
	$dsql->SetQuery("Select id From `#@__archives` where trim(title)='' ");
	$dsql->Execute('x');
	$tdd = 0;
	while($row = $dsql->GetObject('x'))
	{
		if(DelArc($row->id))
		{
			$tdd++;
		}
	}
	ShowMsg("成功删除 $tdd 条记录！","javascript:;");
	exit();
}

//删除空内容文章
else if($action=='delnullbody')
{
	$dsql->SetQuery("Select aid From `#@__addonarticle` where LENGTH(body) < 10 ");
	$dsql->Execute('x');
	$tdd = 0;
	while($row = $dsql->GetObject('x'))
	{
		if(DelArc($row->aid))
		{
			$tdd++;
		}
	}
	ShowMsg("成功删除 $tdd 条记录！","javascript:;");
	exit();
}

//修正缩略图错误
else if($action=='modddpic')
{
	$dsql->ExecuteNoneQuery("Update `#@__archives` set litpic='' where trim(litpic)='litpic' ");
	ShowMsg("成功修正缩略图错误！","javascript:;");
	exit();
}

//批量移动
else if($action=='move')
{
	if(empty($typeid))
	{
		ShowMsg('该操作必须指定栏目！','javascript:;');
		exit();
	}
	$typeold = $dsql->GetOne("Select * From #@__arctype where id='$typeid'; ");
	$typenew = $dsql->GetOne("Select * From #@__arctype where id='$newtypeid'; ");
	if(!is_array($typenew))
	{
		ShowMsg("无法检测移动到的新栏目的信息，不能完成操作！","javascript:;");
		exit();
	}
	if($typenew['ispart']!=0)
	{
		ShowMsg("你不能把数据移动到非最终列表的栏目！","javascript:;");
		exit();
	}
	if($typenew['channeltype']!=$typeold['channeltype'])
	{
		ShowMsg("不能把数据移动到内容类型不同的栏目！","javascript:;");
		exit();
	}
	$gwhere .= " And channel='".$typenew['channeltype']."' And title like '%$keyword%'";

	$ch = $dsql->GetOne("Select addtable From `#@__channeltype` where id={$typenew['channeltype']} ");
	$addtable = $ch['addtable'];

	$dsql->SetQuery("Select id From `#@__archives` $gwhere");
	$dsql->Execute('m');
	$tdd = 0;
	while($row = $dsql->GetObject('m'))
	{
		$rs = $dsql->ExecuteNoneQuery("Update `#@__arctiny` set typeid='$newtypeid' where id='{$row->id}'");
		$rs = $dsql->ExecuteNoneQuery("Update `#@__archives` set typeid='$newtypeid' where id='{$row->id}'");
		if($addtable!='')
		{
			$dsql->ExecuteNoneQuery("Update `$addtable` set typeid='$newtypeid' where aid='{$row->id}' ");
		}
		if($rs)
		{
			$tdd++;
		}
		DelArc($row->id,true);
	}

	if($tdd>0)
	{
		$jumpurl  = "makehtml_archives_action.php?endid=$endid&startid=$startid";
		$jumpurl .= "&typeid=$newtypeid&pagesize=20&seltime=$seltime";
		$jumpurl .= "&stime=".urlencode($starttime)."&etime=".urlencode($endtime);
		ShowMsg("成功移动 $tdd 条记录，准备重新生成HTML...",$jumpurl);
	}
	else
	{
		ShowMsg("完成操作，没移动任何数据...","javascript:;");
	}
}

//删除空标题内容
else if($action=='delnulltitle')
{
	$dsql->SetQuery("Select id From #@__archives where trim(title)='' ");
	$dsql->Execute('x');
	$tdd = 0;
	while($row = $dsql->GetObject('x'))
	{
		if(DelArc($row->id))
		{
			$tdd++;
		}
	}
	ShowMsg("成功删除 $tdd 条记录！","javascript:;");
	exit();
}

//修正缩略图错误
else if($action=='modddpic')
{
	$dsql->ExecuteNoneQuery("Update #@__archives set litpic='' where trim(litpic)='litpic' ");
	ShowMsg("成功修正缩略图错误！","javascript:;");
	exit();
}
?>