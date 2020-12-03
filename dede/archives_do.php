<?php
require_once(dirname(__FILE__).'/config.php');
require_once(DEDEADMIN.'/inc/inc_batchup.php');
require_once(DEDEADMIN.'/inc/inc_archives_functions.php');
require_once(DEDEINC.'/typelink.class.php');
require_once(DEDEINC.'/arc.archives.class.php');
$ENV_GOBACK_URL = (empty($_COOKIE['ENV_GOBACK_URL']) ? 'content_list.php' : $_COOKIE['ENV_GOBACK_URL']);

if(empty($dopost) || empty($aid))
{
	ShowMsg('对不起，你没指定运行参数！','-1');
	exit();
}
$aid = ereg_replace('[^0-9]','',$aid);

/*--------------------------
//编辑文档
function editArchives(){ }
---------------------------*/
if($dopost=='editArchives')
{
	$query = "Select arc.id,arc.typeid,ch.maintable,ch.editcon
           From `#@__arctiny` arc
           left join `#@__arctype` tp on tp.id=arc.typeid
           left join `#@__channeltype` ch on ch.id=arc.channel
          where arc.id='$aid' ";
	$row = $dsql->GetOne($query);
	$gurl = $row['editcon'];
	if($gurl=='')
	{
		$gurl='article_edit.php';
	}
	header("location:{$gurl}?aid=$aid");
	exit();
}

/*--------------------------
//浏览文档
function viewArchives(){ }
---------------------------*/
else if($dopost=="viewArchives")
{
	$aid = ereg_replace('[^0-9]','',$aid);

	//获取主表信息
	$query = "Select arc.*,ch.maintable,ch.addtable,ch.issystem,ch.editcon,
	          tp.typedir,tp.typename,tp.corank,tp.namerule,tp.namerule2,tp.ispart,tp.moresite,tp.sitepath,tp.siteurl
           From `#@__arctiny` arc
           left join `#@__arctype` tp on tp.id=arc.typeid
           left join `#@__channeltype` ch on ch.id=tp.channeltype
           where arc.id='$aid' ";
	$trow = $dsql->GetOne($query);
	$trow['maintable'] = ( trim($trow['maintable'])=='' ? '#@__archives' : trim($trow['maintable']) );
	if($trow['issystem'] != -1)
	{
		$arcQuery = "Select arc.*,tp.typedir,tp.typename,tp.corank,tp.namerule,tp.namerule2,tp.ispart,tp.moresite,tp.sitepath,tp.siteurl
		           from `{$trow['maintable']}` arc left join `#@__arctype` tp on arc.typeid=tp.id
		           left join `#@__channeltype` ch on ch.id=arc.channel where arc.id='$aid' ";
		$arcRow = $dsql->GetOne($arcQuery);
		if($arcRow['ismake']==-1 || $arcRow['corank']!=0 || $arcRow['arcrank']!=0 || $arcRow['typeid']==0 || $arcRow['money']>0)
		{
			echo "<script language='javascript'>location.href='{$cfg_phpurl}/view.php?aid={$aid}';</script>";
			exit();
		}
	}
	else
	{
		$arcRow['id'] = $aid;
		$arcRow['typeid'] = $trow['typeid'];
		$arcRow['senddate'] = $trow['senddate'];
		$arcRow['title'] = '';
		$arcRow['ismake'] = 1;
		$arcRow['arcrank'] = $trow['corank'];
		$arcRow['namerule'] = $trow['namerule'];
		$arcRow['typedir'] = $trow['typedir'];
		$arcRow['money'] = 0;
		$arcRow['filename'] = '';
		$arcRow['moresite'] = $trow['moresite'];
		$arcRow['siteurl'] = $trow['siteurl'];
		$arcRow['sitepath'] = $trow['sitepath'];
	}
	$arcurl  = GetFileUrl($arcRow['id'],$arcRow['typeid'],$arcRow['senddate'],$arcRow['title'],$arcRow['ismake'],$arcRow['arcrank'],
	$arcRow['namerule'],$arcRow['typedir'],$arcRow['money'],$arcRow['filename'],$arcRow['moresite'],$arcRow['siteurl'],$arcRow['sitepath']);
	$arcfile = GetFileUrl($arcRow['id'],$arcRow['typeid'],$arcRow['senddate'],$arcRow['title'],
	$arcRow['ismake'],$arcRow['arcrank'],$arcRow['namerule'],$arcRow['typedir'],$arcRow['money'],$arcRow['filename']);
	if(eregi('^http:',$arcfile))
	{
		$arcfile = eregi_replace("^http://([^/]*)/",'/',$arcfile);
	}
	$truefile = GetTruePath().$arcfile;
	if(!file_exists($truefile))
	{
		MakeArt($aid,true);
	}
	echo "<script language='javascript'>location.href='$arcurl"."?".time()."';</script>";
	exit();
}

/*--------------------------
//推荐文档
function commendArchives(){ }
---------------------------*/
else if($dopost=="commendArchives")
{
	CheckPurview('a_Commend,sys_ArcBatch');
	if( !empty($aid) && empty($qstr) )
	{
		$qstr = $aid;
	}
	if($qstr=='')
	{
		ShowMsg("参数无效！",$ENV_GOBACK_URL);
		exit();
	}
	$arcids = ereg_replace('[^0-9,]','',ereg_replace('`',',',$qstr));
	$query = "Select arc.id,arc.typeid,ch.issystem,ch.maintable,ch.addtable From `#@__arctiny` arc
           left join `#@__arctype` tp on tp.id=arc.typeid
           left join `#@__channeltype` ch on ch.id=tp.channeltype
          where arc.id in($arcids) ";
	$dsql->SetQuery($query);
	$dsql->Execute();
	while($row = $dsql->GetArray())
	{
		$aid = $row['id'];
		if($row['issystem']!=-1)
		{
			$maintable = ( trim($row['maintable'])=='' ? '#@__archives' : trim($row['maintable']) );
			$arr = $dsql->GetOne("Select flag From `{$maintable}` where id='$aid' ");
			$flag = ($arr['flag']=='' ? 'c' : $arr['flag'].',c');
			$dsql->ExecuteNoneQuery(" Update `{$maintable}` set `flag`='$flag' where id='{$aid}' ");
		}
		else
		{
			$maintable = trim($row['addtable']);
			$arr = $dsql->GetOne("Select flag From `{$maintable}` where aid='$aid' ");
			$flag = ($arr['flag']=='' ? 'c' : $arr['flag'].',c');
			$dsql->ExecuteNoneQuery(" Update `{$maintable}` set `flag`='$flag' where aid='{$aid}' ");
		}
	}
	ShowMsg("成功把所选的文档设为推荐！",$ENV_GOBACK_URL);
	exit();
}

/*--------------------------
//生成HTML
function makeArchives();
---------------------------*/
else if($dopost=="makeArchives")
{
	CheckPurview('sys_MakeHtml,sys_ArcBatch');
	if( !empty($aid) && empty($qstr) )
	{
		$qstr = $aid;
	}
	if($qstr=='')
	{
		ShowMsg('参数无效！',$ENV_GOBACK_URL);
		exit();
	}
	require_once(DEDEADMIN.'/inc/inc_archives_functions.php');
	$qstrs = explode('`',$qstr);
	$i = 0;
	foreach($qstrs as $aid)
	{
		$i++;
		$pageurl = MakeArt($aid,false);
	}
	ShowMsg("成功更新指定 $i 个文件...",$ENV_GOBACK_URL);
	exit();
}

/*--------------------------
//审核文档
function checkArchives() {   }
---------------------------*/
else if($dopost=="checkArchives")
{
	CheckPurview('a_Check,a_AccCheck,sys_ArcBatch');
	require_once(DEDEADMIN."/inc/inc_archives_functions.php");
	if( !empty($aid) && empty($qstr) )
	{
		$qstr = $aid;
	}
	if($qstr=='')
	{
		ShowMsg("参数无效！",$ENV_GOBACK_URL);
		exit();
	}
	$arcids = ereg_replace('[^0-9,]','',ereg_replace('`',',',$qstr));
	$query = "Select arc.id,arc.typeid,ch.issystem,ch.maintable,ch.addtable From `#@__arctiny` arc
           	left join `#@__arctype` tp on tp.id=arc.typeid
            left join `#@__channeltype` ch on ch.id=tp.channeltype
            where arc.id in($arcids) ";
	$dsql->SetQuery($query);
	$dsql->Execute('ckall');
	while($row = $dsql->GetArray('ckall'))
	{
		$aid = $row['id'];
		//print_r($row);
		$maintable = ( trim($row['maintable'])=='' ? '#@__archives' : trim($row['maintable']) );
		$dsql->ExecuteNoneQuery("Update `#@__arctiny` set arcrank='0' where id='$aid' ");
		if($row['issystem']==-1)
		{
			$dsql->ExecuteNoneQuery("Update `".trim($row['addtable'])."` set arcrank='0' where aid='$aid' ");
		}
		else
		{
			$dsql->ExecuteNoneQuery("Update `$maintable` set arcrank='0' where id='$aid' ");
		}
		$pageurl = MakeArt($aid,false);
	}
	ShowMsg("成功审核指定的文档！",$ENV_GOBACK_URL);
	exit();
}

/*--------------------------
//删除文档
function delArchives(){ }
---------------------------*/
else if($dopost=="delArchives")
{
	CheckPurview('a_Del,a_AccDel,a_MyDel,sys_ArcBatch');
	require_once(DEDEINC."/oxwindow.class.php");
	if(empty($fmdo))
	{
		$fmdo = '';
	}

	//确定刪除操作完成
	if($fmdo=='yes')
	{
		if( !empty($aid) && empty($qstr) )
		{
			$qstr = $aid;
		}
		if($qstr=='')
		{
			ShowMsg("参数无效！",$ENV_GOBACK_URL);
			exit();
		}
		$qstrs = explode("`",$qstr);
		$okaids = Array();

		foreach($qstrs as $aid)
		{
			if(!isset($okaids[$aid]))
			{
				DelArc($aid);
			}
			else
			{
				$okaids[$aid] = 1;
			}
		}
		ShowMsg("成功删除指定的文档！",$ENV_GOBACK_URL);
		exit();
	}

	//删除确认提示
	else
	{
		$wintitle = "文档管理-删除文档";
		$wecome_info = "<a href='".$ENV_GOBACK_URL."'>文档管理</a>::删除文档";
		$win = new OxWindow();
		$win->Init("archives_do.php","js/blank.js","POST");
		$win->AddHidden("fmdo","yes");
		$win->AddHidden("dopost",$dopost);
		$win->AddHidden("qstr",$qstr);
		$win->AddHidden("aid",$aid);
		$win->AddTitle("你确实要删除“ $qstr 和 $aid ”这些文档？");
		$winform = $win->GetWindow("ok");
		$win->Display();
	}
}

/*-----------------------------
function moveArchives(){ }
------------------------------*/
else if($dopost=='moveArchives')
{
	CheckPurview('sys_ArcBatch');
	if(empty($totype))
	{
		require_once(DEDEINC."/oxwindow.class.php");
		require_once(DEDEINC."/typelink.class.php");
		$tl = new TypeLink($aid);
		$typeOptions = $tl->GetOptionArray(0,$cuserLogin->getUserChannel(),0);
		$typeOptions = "<select name='totype' style='width:350px'>
		<option value='0'>请选择移动到的位置...</option>\r\n
		$typeOptions
		</select>";
		$wintitle = "文档管理-移动文档";
		$wecome_info = "<a href='".$ENV_GOBACK_URL."'>文档管理</a>::移动文档";
		$win = new OxWindow();
		$win->Init("archives_do.php","js/blank.js","POST");
		$win->AddHidden("fmdo","yes");
		$win->AddHidden("dopost",$dopost);
		$win->AddHidden("qstr",$qstr);
		$win->AddHidden("aid",$aid);
		$win->AddTitle("你目前的操作是移动文档，请选择目标栏目：");
		$win->AddMsgItem($typeOptions,"30","1");
		$win->AddMsgItem("你选中的文档ID是： $qstr <br>移动的栏目必须和选定的文档频道类型一致，否则程序会自动勿略不符合的文档。","30","1");
		$winform = $win->GetWindow("ok");
		$win->Display();
	}
	else
	{
		$totype = ereg_replace('[^0-9]','',$totype);
		$typeInfos = $dsql->GetOne("Select tp.channeltype,tp.ispart,tp.channeltype,ch.maintable,ch.addtable From `#@__arctype` tp left join `#@__channeltype` ch on ch.id=tp.channeltype where tp.id='$totype' ");
		if(!is_array($typeInfos))
		{
			ShowMsg('参数错误！','-1');
			exit();
		}
		if($typeInfos['ispart']!=0)
		{
			ShowMsg('文档保存的栏目必须为最终列表栏目！','-1');
			exit();
		}
		if(empty($typeInfos['maintable']))
		{
			$typeInfos['maintable'] = '#@__archives';
		}
		$arcids = ereg_replace('[^0-9,]','',ereg_replace('`',',',$qstr));
		$arc = '';
		$j = 0;
		$okids = array();
		$dsql->SetQuery("Select id,typeid From `{$typeInfos['maintable']}` where id in($arcids) And channel='{$typeInfos['channeltype']}' ");
		$dsql->Execute();
		while($row = $dsql->GetArray())
		{
			if($row['typeid']!=$totype)
			{
				$dsql->ExecuteNoneQuery("Update `#@__arctiny`  Set typeid='$totype' where id='{$row['id']}' ");
				$dsql->ExecuteNoneQuery("Update `{$typeInfos['maintable']}` Set typeid='$totype' where id='{$row['id']}' ");
				$dsql->ExecuteNoneQuery("Update `{$typeInfos['addtable']}` Set typeid='$totype' where aid='{$row['id']}' ");
				$okids[] = $row['id'];
				$j++;
			}
		}
		//更新HTML
		foreach($okids as $aid)
		{
			$arc = new Archives($aid);
			$arc->MakeHtml();
		}
		ShowMsg("成功移动 $j 个文档！",$ENV_GOBACK_URL);
		exit();
	}
}

//还原文档；
else if($dopost=='return')
{
	CheckPurview('a_Del,a_AccDel,a_MyDel,sys_ArcBatch');
	require_once(DEDEINC."/oxwindow.class.php");

	if( !empty($aid) && empty($qstr) )
	{
		$qstr = $aid;
	}
	if($qstr=='')
	{
		ShowMsg("参数无效！","recycling.php");
		exit();
	}
	$qstrs = explode("`",$qstr);
	foreach($qstrs as $aid)
	{
		$dsql->ExecuteNoneQuery("Update `#@__archives` set arcrank='-1',ismake='0' where id='$aid'");
		$dsql->ExecuteNoneQuery("Update `#@__arctiny` set `arcrank` = '-1' where id = '$aid'; ");
	}
	ShowMsg("成功还原指定的文档！","recycling.php");
	exit();
}

//清空文档；
else if($dopost=='clear')
{
	CheckPurview('a_Del,a_AccDel,a_MyDel,sys_ArcBatch');
	require_once(DEDEINC."/oxwindow.class.php");
	if(empty($fmdo))
	{
		$fmdo = '';
	}

	//确定刪除操作完成
	if($fmdo=='yes')
	{
		if( !empty($aid) && empty($qstr) )
		{
			$qstr = $aid;
		}
		if($qstr=='')
		{
			ShowMsg("参数无效！","recycling.php");
			exit();
		}
		$qstrs = explode(",",$qstr);
		$okaids = Array();
		foreach($qstrs as $aid)
		{
			if(!isset($okaids[$aid]))
			{
				DelArc($aid,"OK");
			}
			else
			{
				$okaids[$aid] = 1;
			}
		}
		ShowMsg("成功删除指定的文档！","recycling.php");
		exit();
	}
	else
	{
		$dsql->SetQuery("SELECT id FROM `#@__archives` WHERE `arcrank` = '-2'");
		$dsql->Execute();
		$qstr = '';
		while($row = $dsql->GetArray())
		{
			$qstr .= $row['id'].",";
			$aid = $row['id'];
		}
		$num = $dsql->GetTotalRow();
		if(empty($num))
		{
			ShowMsg("对不起，未发现相关文档！","recycling.php");
			exit();
		}
		$wintitle = "文档管理-清空所有文档";
		$wecome_info = "<a href='recycling.php'>文档回收站</a>::清空所有文档";
		$win = new OxWindow();
		$win->Init("archives_do.php","js/blank.js","POST");
		$win->AddHidden("fmdo","yes");
		$win->AddHidden("dopost",$dopost);
		$win->AddHidden("qstr",$qstr);
		$win->AddHidden("aid",$aid);
		$win->AddTitle("本次操作将清空回收站<font color='#FF0000'>所有共 $num 篇文档</font><br>你确实要永久删除“ $qstr ”这些文档？");
		$winform = $win->GetWindow("ok");
		$win->Display();
	}

}

//清除文档；
else if($dopost=='del')
{
	CheckPurview('a_Del,a_AccDel,a_MyDel,sys_ArcBatch');
	require_once(DEDEINC."/oxwindow.class.php");
	if(empty($fmdo))
	{
		$fmdo = '';
	}

	//确定刪除操作完成
	if($fmdo=='yes')
	{
		if( !empty($aid) && empty($qstr) )
		{
			$qstr = $aid;
		}
		if($qstr=='')
		{
			ShowMsg("参数无效！","recycling.php");
			exit();
		}
		$qstrs = explode("`",$qstr);
		$okaids = Array();

		foreach($qstrs as $aid)
		{
			if(!isset($okaids[$aid]))
			{
				DelArc($aid,"OK");
			}
			else
			{
				$okaids[$aid] = 1;
			}
		}
		ShowMsg("成功删除指定的文档！","recycling.php");
		exit();
	}

	//删除确认提示
	else
	{
		$wintitle = "文档管理-删除文档";
		$wecome_info = "<a href='recycling.php'>文档管理</a>::删除文档";
		$win = new OxWindow();
		$win->Init("archives_do.php","js/blank.js","POST");
		$win->AddHidden("fmdo","yes");
		$win->AddHidden("dopost",$dopost);
		$win->AddHidden("qstr",$qstr);
		$win->AddHidden("aid",$aid);
		$win->AddTitle("你确实要永久删除“ $qstr 和 $aid ”这些文档？");
		$winform = $win->GetWindow("ok");
		$win->Display();
	}
}
?>