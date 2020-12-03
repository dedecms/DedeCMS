<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_photograph.php");
require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
require_once(dirname(__FILE__)."/inc/inc_archives_functions.php");

if(!isset($iscommend)) $iscommend = 0;
if(!isset($ispic)) $ispic = 0;
if(!isset($isbold)) $isbold = 0;

if(empty($channelid)||empty($ID)){
	ShowMsg("文档为非指定的类型，请检查你增加内容时是否合法！","-1");
	exit();
}

//对保存的内容进行处理
//--------------------------------
$iscommend = $iscommend + $isbold;

$pubdate = GetMkTime($pubdate);
$sortrank = AddDay($senddate,$sortup);

if($ishtml==0) $ismake = -1;
else $ismake = 0;

$title = cn_substr($title,60);
$color =  cn_substr($color,10);
$writer =  cn_substr($writer,30);
$source = cn_substr($source,50);
$description = cn_substr($description,250);
if($keywords!="") $keywords = trim(cn_substr($keywords,50))." ";
if($cuserLogin->getUserRank() < 5){ $arcrank = -1; }

if(!empty($picname)) $litpic = $picname;
else $litpic = "";

$filesize = $filesize;
$playtime = $tms; 
$width  = GetAlabNum($width);
$height = GetAlabNum($height);
//$flashurl = "";

//处理远程的Flash
//------------------
if(empty($downremote)) $downremote = 0;
$rmflash = "";
if(eregi("^http://",$flashurl) 
  && !eregi($cfg_basehost,$flashurl) && $downremote==1)
{
	if($isUrlOpen) $rmflash = GetRemoteFlash($flashurl,$adminID);
}
if($width==0)  $width  = "500";
if($height==0) $height = "350";
if($rmflash!="") $flashurl = $rmflash;

if($flashurl==""){
	ShowMsg("Flash地址不正确，或远程采集出错！","-1");
	exit();
}

//更新主档案表
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
source='$source',
writer='$writer',
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

//更新附加表
//----------------------------------

$row = $dsql->GetOne("Select aid,typeid From #@__addonflash where aid='$ID'");
if(!is_array($row))
{
  $query = "
  INSERT INTO #@__addonflash(aid,typeid,filesize,playtime,flashtype,flashrank,width,height,flashurl) 
  VALUES ('$ID','$typeid','$filesize','$playtime','$flashtype','$flashrank','$width','$height','$flashurl');
  ";
  $dsql->SetQuery($query);
  if(!$dsql->ExecuteNoneQuery()){
	  $dsql->Close();
	  ShowMsg("把数据保存到数据库附加表 addonflash 时出错，请检查原因！","-1");
	  exit();
  }
}
else
{
	$query = "
  update #@__addonflash
  set typeid ='$typeid',
  filesize ='$filesize',
  playtime ='$playtime',
  flashtype ='$flashtype',
  flashrank ='$flashrank',
  width ='$width',
  height ='$height',
  flashurl ='$flashurl'
  where aid='$ID';
  ";
  $dsql->SetQuery($query);
  if(!$dsql->ExecuteNoneQuery()){
	  $dsql->Close();
	  ShowMsg("更新数据库附加表 addonflash 时出错，请检查原因！","-1");
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
<a href='flash_add.php?cid=$typeid'><u>发布新Flash作品</u></a>
&nbsp;&nbsp;
<a href='archives_do.php?aid=".$ID."&dopost=editArchives'><u>查看更改</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>查看文档</u></a>
&nbsp;&nbsp;
<a href='catalog_do.php?cid=$typeid&dopost=listArchives'><u>已发布Flash管理</u></a>
&nbsp;&nbsp;
<a href='catalog_main.php'><u>网站栏目管理</u></a>
";

$wintitle = "成功发布一个Flash作品！";
$wecome_info = "文章管理::更改Flash";
$win = new OxWindow();
$win->AddTitle("成功更改一个Flash！");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();
?>