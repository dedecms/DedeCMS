<?php 
require_once(dirname(__FILE__)."/../config.php");
CheckPurview('a_Edit,a_AccEdit,a_MyEdit');
require_once(dirname(__FILE__)."/../../include/inc_photograph.php");
require_once(dirname(__FILE__)."/../../include/pub_oxwindow.php");
require_once(dirname(__FILE__)."/../inc/inc_archives_functions.php");

if(!isset($iscommend)) $iscommend = 0;
if(!isset($isjump)) $isjump = 0;
if(!isset($isbold)) $isbold = 0;

if($typeid==0){
	ShowMsg("请指定文档的栏目！","-1");
	exit();
}
if(empty($channelid)){
	ShowMsg("文档为非指定的类型，请检查你发布内容的表单是否合法！","-1");
	exit();
}
if(!CheckChannel($typeid,$channelid) || !CheckChannel($typeid2,$channelid)){
	ShowMsg("你所选择的栏目与当前模型不相符，请选择白色的选项！","-1");
	exit();
}
if(!TestPurview('a_Edit')) {
	if(TestPurview('a_AccEdit')) CheckCatalog($typeid,"对不起，你没有操作栏目 {$typeid} 的文档权限！");
	else CheckArcAdmin($ID,$cuserLogin->getUserID());
}

$arcrank = GetCoRank($arcrank,$typeid);

//对保存的内容进行处理
//--------------------------------
$iscommend = $iscommend + $isbold;

$pubdate = GetMkTime($pubdate);
$sortrank = AddDay($senddate,$sortup);

if($ishtml==0) $ismake = -1;
else $ismake = 0;

$title = cn_substr($title,80);
$shorttitle = cn_substr($shorttitle,36);
$color =  cn_substr($color,10);
$writer =  cn_substr($writer,30);
$source = cn_substr($source,50);
$description = cn_substr($description,250);
if($keywords!="") $keywords = trim(cn_substr($keywords,50))." ";
if(!TestPurview('a_Check,a_AccCheck,a_MyCheck')){ $arcrank = -1; }

//处理上传的缩略图
if(empty($ddisremote)) $ddisremote = 0;
$litpic = GetDDImage('none',$picname,$ddisremote);

$filesize = $filesize;
$playtime = $tms; 
$width  = GetAlabNum($width);
$height = GetAlabNum($height);

//处理远程的Flash
//------------------
if(empty($downremote)) $downremote = 0;
$rmflash = "";
if(eregi("^http://",$flashurl) 
  && !eregi($cfg_basehost,$flashurl) && $downremote==1)
{
	if($cfg_isUrlOpen) $rmflash = GetRemoteFlash($flashurl,$adminID);
}
if($width==0)  $width  = "500";
if($height==0) $height = "350";
if($rmflash!="") $flashurl = $rmflash;

if($flashurl==""){
	ShowMsg("Flash地址不正确，或远程采集出错！","-1");
	exit();
}

$dsql = new DedeSql(false);
$tables = GetChannelTable($dsql,$ID,'arc');

//更新数据库的SQL语句
//----------------------------------
$inQuery = "
update `{$tables['maintable']}` set
typeid='$typeid',
typeid2='$typeid2',
sortrank='$sortrank',
redirecturl='$redirecturl',
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
keywords='$keywords',
shorttitle='$shorttitle',
arcatt='$arcatt',
likeid='$likeid'
where ID='$ID'; ";

if(!$dsql->ExecuteNoneQuery($inQuery)){
	$gerr = $dsql->GetError();
	$dsql->Close();
	ShowMsg("更新数据库主表 `{$tables['maintable']}` 时出错，请把相关信息提交给DedeCms官方。".$gerr,"javascript:;");
	exit();
}


//----------------------------------
//分析处理附加表数据
//----------------------------------
$inadd_f = '';
if(!empty($dede_addonfields))
{
  $addonfields = explode(";",$dede_addonfields);
  $inadd_f = "";
  if(is_array($addonfields))
  {
    foreach($addonfields as $v)
    {
	     if($v=="") continue;
	     $vs = explode(",",$v);
	     //HTML文本特殊处理
	     if($vs[1]=="htmltext"||$vs[1]=="textdata")
	     {
		     include_once(DEDEADMIN.'/inc/inc_arc_makeauto.php');
	     }else{
		     ${$vs[0]} = GetFieldValueA(${$vs[0]},$vs[1],$arcID);
	     }
	     $inadd_f .= ",`{$vs[0]}` = '".${$vs[0]}."'";
    }
  }
}

//更新附加表
$addQuery = "
  update `{$tables['addtable']}`
  set typeid ='$typeid',
  filesize ='$filesize',
  playtime ='$playtime',
  flashtype ='$flashtype',
  flashrank ='$flashrank',
  width ='$width',
  height ='$height',
  flashurl ='$flashurl'{$inadd_f}
  where aid='$ID';
";
if(!$dsql->ExecuteNoneQuery($addQuery)){
	$gerr = $dsql->GetError();
	$dsql->Close();
	ShowMsg("更新数据库附加表 `{$addtable}` 时出错，请把相关信息提交给DedeCms官方。".$gerr,"javascript:;");
	exit();
}

//生成HTML
//---------------------------------
$artUrl = getfilenameonly($ID, $typeid, $senddate, $title, $ismake, $arcrank, $money);

//更新全站搜索索引
$datas = array('aid'=>$ID,'typeid'=>$typeid,'channelid'=>$channelid,'adminid'=>$edadminid,'mid'=>$memberid,'att'=>$arcatt,
               'title'=>$title,'url'=>$artUrl,'litpic'=>$litpic,'keywords'=>$keywords,'pubdate'=>$pubdate,
               'addinfos'=>$description,'uptime'=>time(),'arcrank'=>$arcrank);
UpSearchIndex($dsql,$datas);
unset($datas);
//更新Tag索引
UpTags($dsql,$tag,$ID,0,$typeid,$arcrank);
MakeArt($ID,true);
//---------------------------------
//返回成功信息
//----------------------------------

$msg = "
　　请选择你的后续操作：
<a href='../flash_add.php?cid=$typeid'><u>发布新Flash作品</u></a>
&nbsp;&nbsp;
<a href='../archives_do.php?aid=".$ID."&dopost=editArchives'><u>查看更改</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>查看文档</u></a>
&nbsp;&nbsp;
<a href='../catalog_do.php?cid=$typeid&dopost=listArchives'><u>已发布Flash管理</u></a>
&nbsp;&nbsp;
<a href='../catalog_main.php'><u>网站栏目管理</u></a>
";

$wintitle = "成功发布一个Flash作品！";
$wecome_info = "文章管理::更改Flash";
$win = new OxWindow();
$win->AddTitle("成功更改一个Flash！");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();

ClearAllLink();
?>