<?php 
require_once(dirname(__FILE__)."/../config.php");
require_once(dirname(__FILE__)."/../../include/inc_photograph.php");
require_once(dirname(__FILE__)."/../../include/pub_oxwindow.php");
require_once(dirname(__FILE__)."/../inc/inc_archives_functions.php");

if(!isset($iscommend)) $iscommend = 0;
if(!isset($ispic)) $ispic = 0;
if(!isset($isbold)) $isbold = 0;

if( empty($channelid)||empty($ID) ){
	ShowMsg("文档为非指定的类型，请检查你增加内容时是否合法！","-1");
	exit();
}
if(!TestPurview('spec_Edit')) {
	ShowMsg("对不起，你没有操作栏目 {$typeid} 的文档权限！");
	exit();
}

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

//处理上传的缩略图
if(empty($ddisremote)) $ddisremote = 0;
$litpic = GetDDImage('none',$picname,$ddisremote);

$adminID = $cuserLogin->getUserID();

$dsql = new DedeSql(false);
$cts = GetChannelTable($dsql,$channelid);
//更改主档案表
//----------------------------------

$inQuery = "
update `{$cts['maintable']}` set
typeid='$typeid',
sortrank='$sortrank',
iscommend='$iscommend',
ismake='$ismake',
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
templet='$templet'
where ID='$ID'; ";

if(!$dsql->ExecuteNoneQuery($inQuery)){
	$gerr = $dsql->GetError();
	$dsql->Close();
	ShowMsg("更新数据库主表 `{$cts['maintable']}` 时出错，请把相关信息提交给DedeCms官方。".$gerr,"javascript:;");
	exit();
}

//专题节点列表
//--------------------------------
$arcids = "";
$notelist = "";
for($i=1;$i<=$cfg_specnote;$i++)
{
	if(!empty(${'notename'.$i}))
	{
		$notename = str_replace("'","",trim(${'notename'.$i}));
		$arcid = trim(${'arcid'.$i});
		$col = trim(${'col'.$i});
		$imgwidth = trim(${'imgwidth'.$i});
		$imgheight = trim(${'imgheight'.$i});
		$titlelen = trim(${'titlelen'.$i});
		$infolen = trim(${'infolen'.$i});
		$listtmp = trim(${'listtmp'.$i});
		
	  if(isset(${'noteid'.$i})) $noteid = trim(${'noteid'.$i});
		else $noteid = $i;
		
		if(isset(${'isauto'.$i})) $isauto = trim(${'isauto'.$i});
		else $isauto = 0;
		
		if(isset(${'keywords'.$i})) $keywordsn = str_replace("'","",trim(${'keywords'.$i}));
		else $keywordsn = "";
		
		if(!empty(${'typeid'.$i})) $typeidn = trim(${'typeid'.$i});
		else $typeidn = 0;
		
		if(!empty(${'rownum'.$i})) $rownum = trim(${'rownum'.$i});
		else $rownum = 0;

		$arcid = ereg_replace("[^0-9,]","",$arcid);
		$ids = explode(",",$arcid);
		$okids = "";
		if(is_array($ids)){
		foreach($ids as $mid)
		{
			$mid = trim($mid);
			if($mid=="") continue;
			if(!isset($arcids[$mid])){
				if($okids=="") $okids .= $mid;
				else $okids .= ",".$mid; 
				$arcids[$mid] = 1;
			}
		}}
		$notelist .= "{dede:specnote imgheight=\\'$imgheight\\' imgwidth=\\'$imgwidth\\' 
infolen=\\'$infolen\\' titlelen=\\'$titlelen\\' col=\\'$col\\' idlist=\\'$okids\\' 
name=\\'$notename\\' noteid=\\'$noteid\\' isauto=\'$isauto\' rownum=\\'$rownum\\' 
keywords=\\'$keywordsn\\' typeid=\\'$typeidn\\'} 
	$listtmp
{/dede:specnote}\r\n";
	}
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
//----------------------------------
$addQuery = "update `{$cts['addtable']}` set typeid ='$typeid',note='$notelist'{$inadd_f} where aid='$ID';";
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
$datas = array('aid'=>$ID,'typeid'=>$typeid,'channelid'=>$channelid,'adminid'=>$adminID,'mid'=>0,'att'=>$arcatt,
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
<a href='../soft_add.php?cid=$typeid'><u>发布新专题</u></a>
&nbsp;&nbsp;
<a href='../archives_do.php?aid=".$ID."&dopost=editArchives'><u>继续修改</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>预览专题</u></a>
&nbsp;&nbsp;
<a href='../content_s_list.php'><u>已发布专题管理</u></a>
";

$wintitle = "成功更改一个专题！";
$wecome_info = "专题管理::更改专题";
$win = new OxWindow();
$win->AddTitle("成功更改专题！");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();

ClearAllLink();
?>