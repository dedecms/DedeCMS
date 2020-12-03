<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_photograph.php");
require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
require_once(dirname(__FILE__)."/inc/inc_archives_functions.php");

if(!isset($iscommend)) $iscommend = 0;
if(!isset($ispic)) $ispic = 0;
if(!isset($isbold)) $isbold = 0;
if(!isset($autokey)) $autokey = 0;
if(!isset($remote)) $remote = 0;
if(!isset($dellink)) $dellink = 0;
//if(!isset($seltypeid)) $seltypeid = 0;

if(empty($channelid)||empty($ID)){
	ShowMsg("文档为非指定的类型，请检查你增加内容时是否合法！","-1");
	exit();
}

//对保存的内容进行处理
//--------------------------------
$iscommend = $iscommend + $isbold;

$pubdate = GetMkTime($pubdate);
$sortrank = AddDay($senddate,$sortup);//$pubdate + ($sortup * 24 * 3600);

if($ishtml==0) $ismake = -1;
else $ismake = 0;

//if($typeid==0 && $seltypeid>0) $typeid = $seltypeid;

$title = cn_substr($title,60);
$color =  cn_substr($color,10);
$writer =  cn_substr($writer,30);
$source = cn_substr($source,50);
$description = cn_substr($description,250);
if($keywords!="") $keywords = trim(cn_substr($keywords,60))." ";
if($cuserLogin->getUserRank() < 5){ $arcrank = -1; }

if(!empty($picname)) $litpic = $picname;
else $litpic = "";

$body = stripslashes($body);

//把内容中远程的图片资源本地化
//------------------------------------
if($isUrlOpen && $remote==1){
	$body = GetCurContent($body);
}

$body = addslashes($body);

$adminID = $cuserLogin->getUserID();

//更新数据库的SQL语句
//----------------------------------
$inQuery = "
update #@__archives set
typeid='$typeid',
typeid2='$typeid2',
sortrank='$sortrank',
iscommend='$iscommend',
ismake='$ismake',
arcrank='$arcrank',
money='$money',
title='$title',
color='$color',
writer='$writer',
source='$source',
litpic='$litpic',
pubdate='$pubdate',
description='$description',
keywords=' $keywords '
where ID='$ID'; ";

$dsql = new DedeSql();
$dsql->SetQuery($inQuery);
if(!$dsql->ExecuteNoneQuery()){
	$dsql->Close();
	ShowMsg("更新数据库archives表时出错，请检查！","-1");
	exit();
}

$row = $dsql->GetOne("Select aid,typeid From #@__addonarticle where aid='$ID'");
if(!is_array($row))
{
  $dsql->SetQuery("INSERT INTO #@__addonarticle(aid,typeid,body) Values('$ID','$typeid','$body')");
  if(!$dsql->ExecuteNoneQuery()){
	   $dsql->Close();
	   ShowMsg("把数据保存到数据库附加表addonarticle时出错，请检查原因！","-1");
	   exit();
  }
}
else
{
	$dsql->SetQuery("update #@__addonarticle set typeid='$typeid',body='$body' where aid='$ID'");
  if(!$dsql->ExecuteNoneQuery()){
	   $dsql->Close();
	   ShowMsg("更新附加表addonarticle时出错，请检查原因！","-1");
	   exit();
  }
}
$dsql->Close();

//生成HTML
//---------------------------------

$artUrl = MakeArt($ID,true);
if($artUrl=="") $artUrl = $cfg_plus_dir."/view.php?aid=$ID";

//---------------------------------
//返回成功信息
//----------------------------------

$msg = "
　　请选择你的后续操作：
<a href='article_add.php?cid=$typeid'><u>发布新文章</u></a>
&nbsp;&nbsp;
<a href='archives_do.php?aid=".$ID."&dopost=editArchives'><u>查看更改</u></a>
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
?>