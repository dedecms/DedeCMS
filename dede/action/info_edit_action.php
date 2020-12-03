<?php
require_once(dirname(__FILE__)."/../config.php");
CheckPurview('a_Edit,a_AccEdit,a_MyEdit');
require_once(DEDEADMIN."/inc/inc_archives_functions.php");

if(!isset($iscommend)) $iscommend = 0;
if(!isset($isjump)) $isjump = 0;
if(!isset($isbold)) $isbold = 0;
if(!isset($autokey)) $autokey = 0;
if(!isset($remote)) $remote = 0;
if(!isset($autolitpic)) $autolitpic = 0;

if(!isset($smalltypeid)) $smalltypeid = 0;

if(!isset($sectorchange)){
	$sectorid = $oldsectorid;
	$sectorid2 = $oldsectorid2;
}
if(!isset($areachange)){
	$areaid = $oldareaid;
	$areaid2 = $oldareaid2;
}


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
$endtime = $senddate + 3600 * 24 * $endtime;


$title = cn_substr($title,80);

if($keywords!="") $keywords = trim(cn_substr($keywords,60))." ";

//处理上传的缩略图
if(empty($ddisremote)) $ddisremote = 0;
$litpic = GetDDImage('none',$picname,$ddisremote);

$body = stripslashes($body);

//自动摘要
if($description=="" && $cfg_auot_description>0){
	$description = stripslashes(cn_substr(html2text($body),$cfg_auot_description));
	$description = trim(preg_replace("/#p#|#e#/","",$description));
	$description = addslashes($description);
}
//把内容中远程的图片资源本地化
//------------------------------------
if($cfg_isUrlOpen && $remote==1){
	$body = GetCurContent($body);
}
//自动获取关键字
//----------------------------------
if($autokey==1){
	require_once(DEDEADMIN."/../include/pub_splitword_www.php");
	$keywords = "";
	$sp = new SplitWord();
	$titleindexs = explode(" ",trim($sp->GetIndexText($sp->SplitRMM($title))));
	$allindexs = explode(" ",trim($sp->GetIndexText($sp->SplitRMM(Html2Text($body)),200)));
	if(is_array($allindexs) && is_array($titleindexs)){
		foreach($titleindexs as $k){
			if(strlen($keywords)>=50) break;
			else $keywords .= $k." ";
		}
		foreach($allindexs as $k){
			if(strlen($keywords)>=50) break;
			else if(!in_array($k,$titleindexs)) $keywords .= $k." ";
	  }
	}
	$sp->Clear();
	unset($sp);
	$keywords = preg_replace("/#p#|#e#/","",$keywords);
	$keywords = addslashes($keywords);
}

//自动获取缩略图
if($autolitpic==1 && $litpic==''){
  $litpic = GetDDImgFromBody($body);
}

$message = addslashes($body);

$dsql = new DedeSql(false);
$cts = GetChannelTable($dsql,$channelid);

//更新数据库的SQL语句
//----------------------------------
$inQuery = "
update `{$cts['maintable']}` set
typeid=$typeid,
smalltypeid=$smalltypeid,
areaid=$areaid,
areaid2=$areaid2,
sectorid=$sectorid,
sectorid2=$sectorid2,
sortrank=$sortrank,
pubdate=$pubdate,
endtime=$endtime,
title='$title',
iscommend='$iscommend',
keywords='$keywords',
litpic='$litpic',
description='$description',
arcatt='$arcatt'
where ID='$ID'; ";

if(!$dsql->ExecuteNoneQuery($inQuery)){
	$gerr = $dsql->GetError();
	$dsql->Close();
	ShowMsg("更新数据库主表 `{$cts['maintable']}` 时出错，请把相关信息提交给DedeCms官方。".$gerr,"javascript:;");
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

$addQuery = "Update `{$cts['addtable']}` set typeid='$typeid',
message='$message',contact='$contact',phone='$phone',
fax='$fax',email='$email',qq='$qq',msn='$msn',address='$address'{$inadd_f}
where aid='{$ID}' ";

if(!$dsql->ExecuteNoneQuery($addQuery)){
	$gerr = $dsql->GetError();
	$dsql->Close();
	ShowMsg("更新数据库附加表 `{$cts['addtable']}` 时出错，请把相关信息提交给DedeCms官方。".$gerr,"javascript:;");
	exit();
}

//生成HTML
//---------------------------------
$artUrl = getfilenameonly($ID, $typeid, $senddate, $title, $ismake, $arcrank, $money);

//更新全站搜索索引
$datas = array('aid'=>$ID,'typeid'=>$typeid,'channelid'=>$channelid,'adminid'=>$edadminid,'att'=>$arcatt,
               'title'=>$title,'url'=>$artUrl,'litpic'=>$litpic,'keywords'=>$keywords,'pubdate'=>$pubdate,
               'addinfos'=>$description,'uptime'=>time(),'arcrank'=>0);
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
<a href='../archives_do.php?aid=".$ID."&dopost=editArchives&channelid=-2'><u>查看更改</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>预览文档</u></a>
&nbsp;&nbsp;
<a href='../catalog_do.php?cid=$typeid&dopost=listArchives'><u>管理信息</u></a>
&nbsp;&nbsp;
<a href='../catalog_main.php'><u>网站栏目管理</u></a>
";

$wintitle = "成功更改信息！";
$wecome_info = "文章管理::更改信息";
$win = new OxWindow();
$win->AddTitle("成功更改信息：");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();

ClearAllLink();
?>


