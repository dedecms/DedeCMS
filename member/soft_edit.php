<?php
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);
require_once(DEDEINC."/dedetag.class.php");
require_once(DEDEINC."/customfields.func.php");
require_once(DEDEMEMBER."/inc/inc_catalog_options.php");
require_once(DEDEMEMBER."/inc/inc_archives_functions.php");
$channelid = isset($channelid) && is_numeric($channelid) ? $channelid : 3;
$aid = isset($aid) && is_numeric($aid) ? $aid : 0;

/*-------------
function _ShowForm(){  }
--------------*/
if(empty($dopost))
{
	//读取归档信息
	$arcQuery = "Select
    #@__channeltype.typename as channelname,
    #@__arcrank.membername as rankname,
    #@__archives.*
    From #@__archives
    left join #@__channeltype on #@__channeltype.id=#@__archives.channel
    left join #@__arcrank on #@__arcrank.rank=#@__archives.arcrank
    where #@__archives.id='$aid'";
	$dsql->SetQuery($arcQuery);
	$row = $dsql->GetOne($arcQuery);
	if(!is_array($row))
	{
		ShowMsg("读取档案基本信息出错!","-1");
		exit();
	}
	$query = "Select * From `#@__channeltype` where id='".$row['channel']."'";
	$cInfos = $dsql->GetOne($query);
	if(!is_array($cInfos))
	{
		ShowMsg("读取频道配置信息出错!","javascript:;");
		exit();
	}
	$addtable = $cInfos['addtable'];
	$addQuery = "Select * From `$addtable` where aid='$aid'";
	$addRow = $dsql->GetOne($addQuery);
	$newRowStart = 1;
	$nForm = '';
	if($addRow['softlinks']!='')
	{
		$dtp = new DedeTagParse();
		$dtp->LoadSource($addRow['softlinks']);
		if(is_array($dtp->CTags))
		{
			foreach($dtp->CTags as $ctag)
			{
				if($ctag->GetName()=='link')
				{
					$nForm .= "软件地址".$newRowStart."：<input class='text' type='text' name='softurl".$newRowStart."'  value='".trim($ctag->GetInnerText())."' />
            服务器名称：<input class='text' type='text' name='servermsg".$newRowStart."' value='".$ctag->GetAtt("text")."'  />
            <br />";
					$newRowStart++;
				}
			}
		}
		$dtp->Clear();
	}
	$channelid = $row['channel'];
	$tags = GetTags($aid);
	include(DEDEMEMBER."/templets/soft_edit.htm");
	exit();
}

/*------------------------------
function _SaveArticle(){  }
------------------------------*/
else if($dopost=='save')
{
	$description = '';
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

	//分析处理附加表数据
	$inadd_f = '';
	$inadd_v = '';
	if(!empty($dede_addonfields))
	{
		$addonfields = explode(';',$dede_addonfields);
		$inadd_f = '';
		$inadd_v = '';
		if(is_array($addonfields))
		{
			foreach($addonfields as $v)
			{
				if($v=='')
				{
					continue;
				}
				$vs = explode(',',$v);

				//HTML文本特殊处理
				if($vs[1]=='htmltext'||$vs[1]=='textdata')
				{
					${$vs[0]} = AnalyseHtmlBody(${$vs[0]},$description,$litpic,$keywords,$vs[1]);
				}
				else
				{
					if(!isset(${$vs[0]}))
					{
						${$vs[0]} = '';
					}
					${$vs[0]} = GetFieldValueA(${$vs[0]},$vs[1],$arcID);
				}
				$inadd_f .= ",`{$vs[0]}` = '".${$vs[0]}."'";
			}
		}
	}

	//更改主档案表
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
		ShowMsg("更新数据库archives表时出错，请检查！","-1");
		exit();
	}

	//软件链接列表
	$urls = '';
	for($i=1;$i<=9;$i++)
	{
		if(!empty(${'softurl'.$i}))
		{
			$servermsg = str_replace("'",'',stripslashes(${'servermsg'.$i}));
			$softurl = stripslashes(${'softurl'.$i});
			if($servermsg=='')
			{
				$servermsg = '下载地址'.$i;
			}
			if($softurl!='' && $softurl!='http://')
			{
				$urls .= "{dede:link text='$servermsg'} $softurl {/dede:link}\r\n";
			}
		}
	}
	$urls = addslashes($urls);

	//更新附加表
	$cts = $dsql->GetOne("Select addtable From `#@__channeltype` where id='$channelid' ");
	$addtable = trim($cts['addtable']);
	if($addtable!='')
	{
		$inQuery = "update `$addtable`
			set typeid ='$typeid',
			filetype ='$filetype',
			language ='$language',
			softtype ='$softtype',
			accredit ='$accredit',
			os ='$os',
			softrank ='$softrank',
			officialUrl ='$officialUrl',
			officialDemo ='$officialDemo',
			softsize ='$softsize',
			softlinks ='$urls',
			 userip='$userip',
			introduce='$body'{$inadd_f}
			where aid='$aid';
			";
		if(!$dsql->ExecuteNoneQuery($inQuery))
		{
			ShowMsg("更新数据库附加表 addonsoft 时出错，请检查原因！","-1");
			exit();
		}
	}
	UpIndexKey($aid,$arcrank,$typeid,$sortrank,$tags);
	$artUrl = MakeArt($aid,true);
	if($artUrl=='')
	{
		$artUrl = $cfg_phpurl."/view.php?aid=$aid";
	}

	//返回成功信息
	$msg = "　　请选择你的后续操作：
		<a href='soft_add.php?cid=$typeid'><u>发布新文章</u></a>
		&nbsp;&nbsp;
		<a href='soft_edit.php?channelid=$channelid&aid=".$aid."'><u>查看更改</u></a>
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