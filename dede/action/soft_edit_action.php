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

$shorttitle = cn_substr($shorttitle,36);
$color =  cn_substr($color,10);
$writer =  cn_substr($writer,30);
$source = cn_substr($source,50);
$description = cn_substr($description,250);
if($keywords!="") $keywords = trim(cn_substr($keywords,50))." ";
if(!TestPurview('a_Check,a_AccCheck,a_MyCheck')){ $arcrank = -1; }

//自动摘要
if($description=="" && $cfg_auot_description>0){
	$description = stripslashes(cn_substr(html2text($body),$cfg_auot_description));
	$description = addslashes($description);
}

//处理上传的缩略图
if(empty($ddisremote)) $ddisremote = 0;
$litpic = GetDDImage('none',$picname,$ddisremote);

//更新数据库的SQL语句
//----------------------------------
$dsql = new DedeSql(false);
$cts = GetChannelTable($dsql,$channelid);

//更新数据库的SQL语句
//----------------------------------
$inQuery = "
update `{$cts['maintable']}` set
typeid='$typeid',
typeid2='$typeid2',
redirecturl='$redirecturl',
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
keywords='$keywords',
shorttitle='$shorttitle',
arcatt='$arcatt',
likeid='$likeid'
where ID='$ID'; ";

if(!$dsql->ExecuteNoneQuery($inQuery)){
	$gerr = $dsql->GetError();
	$dsql->Close();
	ShowMsg("更新数据库主表 `{$cts['maintable']}` 时出错，请把相关信息提交给DedeCms官方。".$gerr,"javascript:;");
	exit();
}

//软件链接列表
$urls = "";
for($i=1;$i<=9;$i++)
{
	if(!empty(${'softurl'.$i}))
	{ 
		$servermsg = str_replace("'","",stripslashes(${'servermsg'.$i}));
		$softurl = stripslashes(${'softurl'.$i});
		if($servermsg=="") $servermsg = "下载地址".$i;
		if($softurl!="" && $softurl!="http://")
		{ $urls .= "{dede:link text='$servermsg'} $softurl {/dede:link}\r\n"; }
  }
}

$urls = addslashes($urls);

$softsize = $softsize;

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
//----------------------------------
$addQuery = "
Update `{$cts['addtable']}`
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
  introduce='$body'{$inadd_f}
where aid='$ID';
";
 
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
<a href='../soft_add.php?cid=$typeid'><u>发布新软件</u></a>
&nbsp;&nbsp;
<a href='../archives_do.php?aid=".$ID."&dopost=editArchives'><u>继续修改</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>查看软件</u></a>
&nbsp;&nbsp;
<a href='../catalog_do.php?cid=$typeid&dopost=listArchives'><u>已发布软件管理</u></a>
&nbsp;&nbsp;
<a href='../catalog_main.php'><u>网站栏目管理</u></a>
";

$wintitle = "成功发布一个软件！";
$wecome_info = "文章管理::发布软件";
$win = new OxWindow();
$win->AddTitle("成功发布软件：");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();

ClearAllLink();
?>