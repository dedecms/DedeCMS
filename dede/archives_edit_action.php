<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('a_Edit,a_AccEdit,a_MyEdit');
require_once(dirname(__FILE__)."/../include/inc_photograph.php");
require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
require_once(dirname(__FILE__)."/inc/inc_archives_functions.php");
require_once(dirname(__FILE__)."/inc/inc_archives_all.php");

if(!isset($iscommend)) $iscommend = 0;
if(!isset($isjump)) $isjump = 0;
if(!isset($isbold)) $isbold = 0;
if(!isset($autokey)) $autokey = 0;
if(!isset($remote)) $remote = 0;
if(!isset($dellink)) $dellink = 0;

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

$shorttitle = cn_substr($shorttitle,36);
$color =  cn_substr($color,10);
$writer =  cn_substr($writer,30);
$source = cn_substr($source,50);
$description = cn_substr($description,250);
if($keywords!="") $keywords = trim(cn_substr($keywords,60))." ";
if(!TestPurview('a_Check,a_AccCheck,a_MyCheck')){ $arcrank = -1; }

//处理上传的缩略图
if(empty($ddisremote)) $ddisremote = 0;
$litpic = GetDDImage('none',$picname,$ddisremote);

$adminID = $cuserLogin->getUserID();

//更新数据库的SQL语句
//----------------------------------
$inQuery = "
update #@__archives set
typeid='$typeid',
typeid2='$typeid2',
sortrank='$sortrank',
iscommend='$iscommend',
redirecturl='$redirecturl',
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
keywords='$keywords',
shorttitle='$shorttitle',
arcatt='$arcatt'
where ID='$ID'; ";

$dsql = new DedeSql();
$dsql->SetQuery($inQuery);
if(!$dsql->ExecuteNoneQuery()){
	$dsql->Close();
	ShowMsg("更新数据库archives表时出错，请检查！","-1");
	exit();
}

//----------------------------------
//分析处理附加表数据
//----------------------------------
if(empty($dede_addtablename)) $dede_addtablename = "";
if(empty($dede_addonfields)) $dede_addonfields = "";
$addonfields = explode(";",$dede_addonfields);
$upfield = "";
foreach($addonfields as $v)
{
	if($v=="") continue;
	$vs = explode(",",$v);
	if($vs[1]=="textdata"){
		${$vs[0]} = GetFieldValue(${$vs[0]},$vs[1],$ID,'edit',${$vs[0].'_file'});
	}else{
		${$vs[0]} = GetFieldValue(${$vs[0]},$vs[1]);
	}
	if($upfield=="") $upfield .= $vs[0]." = '".${$vs[0]}."'";
	else $upfield .= ", ".$vs[0]." = '".${$vs[0]}."'";
}
$addQuery = "Update ".$dede_addtablename." set $upfield where aid='$ID'";
$dsql->SetQuery($addQuery);
$dsql->ExecuteNoneQuery();
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
<a href='archives_add.php?cid=$typeid'><u>发布新文档</u></a>
&nbsp;&nbsp;
<a href='archives_do.php?aid={$ID}&dopost=editArchives'><u>查看更改</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>预览文档</u></a>
&nbsp;&nbsp;
<a href='catalog_do.php?cid=$typeid&dopost=listArchives'><u>管理文档</u></a>
&nbsp;&nbsp;
<a href='catalog_main.php'><u>网站栏目管理</u></a>
";

$wintitle = "成功更改文档！";
$wecome_info = "文章管理::更改文档";
$win = new OxWindow();
$win->AddTitle("成功更改文档：");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();
?>