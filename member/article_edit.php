<?php
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);
require_once(DEDEINC."/dedetag.class.php");
require_once(DEDEINC."/customfields.func.php");
require_once(DEDEMEMBER."/inc/inc_catalog_options.php");
require_once(DEDEMEMBER."/inc/inc_archives_functions.php");
$channelid = isset($channelid) && is_numeric($channelid) ? $channelid : 1;
$aid = isset($aid) && is_numeric($aid) ? $aid : 0;
$mtypesid = isset($mtypesid) && is_numeric($mtypesid) ? $mtypesid : 0;

/*-------------
function _ShowForm(){  }
--------------*/
if(empty($dopost))
{
	//读取归档信息
	$arcQuery = "Select arc.*,ch.addtable,ch.fieldset,m.mtypeid
       From `#@__archives` arc left join `#@__channeltype` ch on ch.id=arc.channel
       left join `#@__member_archives` as m on arc.id = m.id
       where arc.id='$aid' And arc.mid='".$cfg_ml->M_ID."'; ";
	$row = $dsql->GetOne($arcQuery);
	if(!is_array($row))
	{
		ShowMsg("读取文章信息出错!","-1");
		exit();
	}
	else if($row['arcrank']>=0 && $row['arcsta']==-1)
	{
		$dtime = time();
		$maxtime = $cfg_mb_editday * 24 *3600;
		if($dtime - $row['senddate'] > $maxtime)
		{
			ShowMsg("这篇文档已经锁定，你不能再修改它！","-1");
			exit();
		}
	}
	$addRow = $dsql->GetOne("Select * From `{$row['addtable']}` where aid='$aid'; ");
	include(DEDEMEMBER."/templets/article_edit.htm");
	exit();
}

/*------------------------------
function _SaveArticle(){  }
------------------------------*/
else if($dopost=='save')
{
	include(DEDEMEMBER.'/inc/archives_check_edit.php');

	//分析处理附加表数据
	$inadd_f = '';
	if(!empty($dede_addonfields))
	{
		$addonfields = explode(';',$dede_addonfields);
		if(is_array($addonfields))
		{
			foreach($addonfields as $v)
			{
				if($v=='')
				{
					continue;
				}
				$vs = explode(',',$v);
				if(!isset(${$vs[0]}))
				{
					${$vs[0]} = '';
				}
				${$vs[0]} = GetFieldValueA(${$vs[0]},$vs[1],$aid);
				$inadd_f .= ','.$vs[0]." ='".${$vs[0]}."' ";
			}
		}
	}
	$body = AnalyseHtmlBody($body,$description);
	$body = HtmlReplace($body,-1);

	//处理图片文档的自定义属性
	if($litpic!='')
	{
		$flag = 'p';
	}

	//更新数据库的SQL语句
	$upQuery = "Update `#@__archives` set
             ismake='$ismake',
             arcrank='$arcrank',
             typeid='$typeid',
             title='$title',
             litpic='$litpic',
             description='$description',
             keywords='$keywords',            
             flag='$flag'
      where id='$aid' And mid='$mid'; ";
	if(!$dsql->ExecuteNoneQuery($upQuery))
	{
		ShowMsg("把数据保存到数据库主表时出错，请联系管理员！".$dsql->GetError(),"-1");
		exit();
	}
	if($addtable!='')
	{
		$upQuery = "Update `$addtable` set typeid='$typeid',body='$body'{$inadd_f},userip='$userip' where aid='$aid' ";
		if(!$dsql->ExecuteNoneQuery($upQuery))
		{
			ShowMsg("更新附加表 `$addtable`  时出错，请联系管理员！","javascript:;");
			exit();
		}
		$dsql->ExecuteNoneQuery("Update `#@__member_archives` set mtypeid = '$mtypesid' WHERE id = '$aid'");
	}
	UpIndexKey($aid,$arcrank,$typeid,$sortrank,$tags);
	$artUrl = MakeArt($aid,true);
	if($artUrl=='')
	{
		$artUrl = $cfg_phpurl."/view.php?aid=$aid";
	}

	//返回成功信息
	$msg = "　　请选择你的后续操作：
		<a href='article_add.php?cid=$typeid'><u>发布新文章</u></a>
		&nbsp;&nbsp;
		<a href='archives_do.php?channelid=$channelid&aid=".$aid."&dopost=edit'><u>查看更改</u></a>
		&nbsp;&nbsp;
		<a href='$artUrl' target='_blank'><u>查看文章</u></a>
		&nbsp;&nbsp;
		<a href='content_list.php?channelid=$channelid'><u>管理文章</u></a>
		";
	$wintitle = "成功更改文章！";
	$wecome_info = "文章管理::更改文章";
	$win = new OxWindow();
	$win->AddTitle("成功更改文章：");
	$win->AddMsgItem($msg);
	$winform = $win->GetWindow("hand","&nbsp;",false);
	$win->Display();
}
?>