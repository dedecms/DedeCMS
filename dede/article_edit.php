<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('a_Edit,a_AccEdit,a_MyEdit');
require_once(DEDEINC."/customfields.func.php");
require_once(DEDEADMIN."/inc/inc_archives_functions.php");
if(empty($dopost))
{
	$dopost = '';
}
$aid = isset($aid) && is_numeric($aid) ? $aid : 0;
if($dopost!='save')
{
	require_once(DEDEADMIN."/inc/inc_catalog_options.php");
	require_once(DEDEINC."/dedetag.class.php");

	//读取归档信息
	$query = "Select ch.typename as channelname,ar.membername as rankname,arc.*
    From `#@__archives` arc
    left join `#@__channeltype` ch on ch.id=arc.channel
    left join `#@__arcrank` ar on ar.rank=arc.arcrank where arc.id='$aid' ";
	$arcRow = $dsql->GetOne($query);
	if(!is_array($arcRow))
	{
		ShowMsg("读取档案基本信息出错!","-1");
		exit();
	}
	$query = "Select * From `#@__channeltype` where id='".$arcRow['channel']."'";
	$cInfos = $dsql->GetOne($query);
	if(!is_array($cInfos))
	{
		ShowMsg("读取频道配置信息出错!","javascript:;");
		exit();
	}
	$addtable = $cInfos['addtable'];
	$addRow = $dsql->GetOne("Select * From `$addtable` where aid='$aid'");
	if(!is_array($addRow))
	{
		ShowMsg("读取附加信息出错!","javascript:;");
		exit();
	}
	$channelid = $arcRow['channel'];
	$tags = GetTags($aid);
	include DedeInclude("templets/article_edit.htm");
	exit();
}

/*--------------------------------
function __save(){  }
-------------------------------*/
else if($dopost=='save')
{
	require_once(DEDEINC.'/image.func.php');
	require_once(DEDEINC.'/oxwindow.class.php');
	$flag = isset($flags) ? join(',',$flags) : '';
	$notpost = isset($notpost) && $notpost == 1 ? 1: 0;
	
	if(empty($typeid2)) $typeid2 = 0;
	if(!isset($autokey)) $autokey = 0;
	if(!isset($remote)) $remote = 0;
	if(!isset($dellink)) $dellink = 0;
	if(!isset($autolitpic)) $autolitpic = 0;

	if(empty($typeid))
	{
		ShowMsg("请指定文档的栏目！","-1");
		exit();
	}
	if(empty($channelid))
	{
		ShowMsg("文档为非指定的类型，请检查你发布内容的表单是否合法！","-1");
		exit();
	}
	if(!CheckChannel($typeid,$channelid))
	{
		ShowMsg("你所选择的栏目与当前模型不相符，请选择白色的选项！","-1");
		exit();
	}
	if(!TestPurview('a_Edit'))
	{
		if(TestPurview('a_AccEdit'))
		{
			CheckCatalog($typeid,"对不起，你没有操作栏目 {$typeid} 的文档权限！");
		}
		else
		{
			CheckArcAdmin($id,$cuserLogin->getUserID());
		}
	}


	//对保存的内容进行处理
	$pubdate = GetMkTime($pubdate);
	$sortrank = AddDay($pubdate,$sortup);
	$ismake = $ishtml==0 ? -1 : 0;
	$title = htmlspecialchars(cn_substrR($title,$cfg_title_maxlen));
	$shorttitle = cn_substrR($shorttitle,36);
	$color =  cn_substrR($color,7);
	$writer =  cn_substrR($writer,20);
	$source = cn_substrR($source,30);
	$description = cn_substrR($description,250);
	$keywords = trim(cn_substrR($keywords,30));
	$filename = trim(cn_substrR($filename,40));
	if(!TestPurview('a_Check,a_AccCheck,a_MyCheck'))
	{
		$arcrank = -1;
	}
	$adminid = $cuserLogin->getUserID();

	//处理上传的缩略图
	if(empty($ddisremote))
	{
		$ddisremote = 0;
	}
	$litpic = GetDDImage('none',$picname,$ddisremote);

	//分析body里的内容
	$body = AnalyseHtmlBody($body,$description,$litpic,$keywords,'htmltext');

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
				if($vs[1]=='htmltext'||$vs[1]=='textdata') //HTML文本特殊处理
				{
					${$vs[0]} = AnalyseHtmlBody(${$vs[0]},$description,$litpic,$keywords,$vs[1]);
				}else
				{
					if(!isset(${$vs[0]}))
					{
						${$vs[0]} = '';
					}
					${$vs[0]} = GetFieldValueA(${$vs[0]},$vs[1],$id);
				}
				$inadd_f .= ",`{$vs[0]}` = '".${$vs[0]}."'";
			}
		}
	}

	//处理图片文档的自定义属性
	if($litpic!='' && !ereg('p',$flag))
	{
		$flag = ($flag=='' ? 'p' : $flag.',p');
	}
	if($redirecturl!='' && !ereg('j',$flag))
	{
		$flag = ($flag=='' ? 'j' : $flag.',j');
	}

	//更新数据库的SQL语句
	$query = "update #@__archives set
    typeid='$typeid',
    typeid2='$typeid2',
    sortrank='$sortrank',
    flag='$flag',
    ismake='$ismake',
    arcrank='$arcrank',
    money='$money',
    title='$title',
    color='$color',
    writer='$writer',
    source='$source',
    litpic='$litpic',
    pubdate='$pubdate',
    notpost='$notpost',
    description='$description',
    keywords='$keywords',
    shorttitle='$shorttitle',
    filename='$filename'
    where id='$id'; ";

	if(!$dsql->ExecuteNoneQuery($query))
	{
		ShowMsg('更新数据库archives表时出错，请检查',-1);
		exit();
	}
	$cts = $dsql->GetOne("Select addtable From `#@__channeltype` where id='$channelid' ");
	$addtable = trim($cts['addtable']);
	if($addtable!='')
	{
		$useip = GetIP();
		$iquery = "update `$addtable` set typeid='$typeid',body='$body'{$inadd_f},redirecturl='$redirecturl',userip='$useip' where aid='$id'";
		if(!$dsql->ExecuteNoneQuery($iquery))
		{
			ShowMsg("更新附加表 `$addtable`  时出错，请检查原因！","javascript:;");
			exit();
		}
	}

	//生成HTML
	UpIndexKey($id,$arcrank,$typeid,$sortrank,$tags);
	$artUrl = MakeArt($id,true,true);
	if($artUrl=='')
	{
		$artUrl = $cfg_phpurl."/view.php?aid=$id";
	}

	//返回成功信息
	$msg = "
    　　请选择你的后续操作：
    <a href='article_add.php?cid=$typeid'><u>发布新文章</u></a>
    &nbsp;&nbsp;
    <a href='archives_do.php?aid=".$id."&dopost=editArchives'><u>查看更改</u></a>
    &nbsp;&nbsp;
    <a href='$artUrl' target='_blank'><u>查看文章</u></a>
    &nbsp;&nbsp;
    <a href='catalog_do.php?cid=$typeid&dopost=listArchives'><u>管理文章</u></a>
    &nbsp;&nbsp;
    <a href='catalog_main.php'><u>网站栏目管理</u></a>
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