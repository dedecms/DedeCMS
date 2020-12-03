<?php 
require_once(dirname(__FILE__)."/../config.php");
require_once(dirname(__FILE__)."/../../include/inc_photograph.php");
require_once(dirname(__FILE__)."/../../include/pub_oxwindow.php");
require_once(dirname(__FILE__)."/../inc/inc_archives_functions.php");

if(!TestPurview('spec_New')) {
	ShowMsg("对不起，你没有操作栏目 {$typeid} 的文档权限！");
	exit();
}

if(!isset($iscommend)) $iscommend = 0;
if(!isset($isbold)) $isbold = 0;

$channelid = -1;

//对保存的内容进行处理
//--------------------------------
$iscommend = $iscommend + $isbold;

$pubdate = GetMkTime($pubdate);
$senddate = mytime();
$sortrank = AddDay($senddate,$sortup);

if($ishtml==0) $ismake = -1;
else $ismake = 0;

$title = cn_substr($title,80);
$shorttitle = cn_substr($shorttitle,36);
$color =  cn_substr($color,10);
$writer =  cn_substr($writer,30);
$source = cn_substr($source,50);
$description = cn_substr($description,250);
if($keywords!="") $keywords = trim(cn_substr($keywords,60))." ";


//处理上传的缩略图
if(empty($ddisremote)) $ddisremote = 0;
$litpic = GetDDImage('litpic',$picname,$ddisremote);

$adminID = $cuserLogin->getUserID();

$dsql = new DedeSql(false);
$cts = GetChannelTable($dsql,$channelid);
$arcID = GetIndexKey($dsql,$typeid,$channelid);

//加入主档案表

//----------------------------------
$inQuery = "INSERT INTO `{$cts['maintable']}`(
ID,typeid,typeid2,sortrank,iscommend,ismake,channel,
arcrank,click,title,shorttitle,color,writer,source,litpic,
pubdate,senddate,arcatt,adminID,memberID,description,keywords,templet) 
VALUES ('$arcID','$typeid','$typeid2','$sortrank','$iscommend','$ismake','$channelid',
'$arcrank','0','$title','$shorttitle','$color','$writer','$source','$litpic',
'$pubdate','$senddate','$arcatt','$adminID','0','$description','$keywords','$templet');";

if(!$dsql->ExecuteNoneQuery($inQuery)){
	$gerr = $dsql->GetError();
	$dsql->Close();
	ShowMsg("把数据保存到数据库主表`{$cts['maintable']}`时出错，请检查！".$gerr,"javascript:;");
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
		
		$noteid = trim(${'noteid'.$i});
		$isauto = trim(${'isauto'.$i});
		$keywords = str_replace("'","",trim(${'keywords'.$i}));
		$typeid = trim(${'typeid'.$i});
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
keywords=\\'$keywords\\' typeid=\\'$typeid\\'}
	$listtmp
{/dede:specnote}\r\n";
	}
}

//----------------------------------
//分析处理附加表数据
//----------------------------------
$inadd_f = '';
$inadd_v = '';
if(!empty($dede_addonfields))
{
  $addonfields = explode(";",$dede_addonfields);
  $inadd_f = "";
  $inadd_v = "";
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
	     $inadd_f .= ",".$vs[0];
	     $inadd_v .= ",'".${$vs[0]}."'";
    }
  }
}

//加入附加表
//----------------------------------

$inQuery = "INSERT INTO `{$cts['addtable']}`(aid,typeid,note{$inadd_f}) VALUES ('$arcID','$typeid','$notelist'{$inadd_v});";

if(!$dsql->ExecuteNoneQuery($inQuery)){
	$gerr = $dsql->GetError();
	$dsql->ExecuteNoneQuery("Delete From {$cts['maintable']} where ID='$arcID'");
	$dsql->ExecuteNoneQuery("Delete From `#@__full_search` where aid='$arcID'");
	$dsql->Close();
	ShowMsg("把数据保存到数据库附加表 `{$cts['addtable']}` 时出错，请把相关信息提交给DedeCms官方。".$gerr,"javascript:;");
	exit();
}

//生成HTML
//---------------------------------
$artUrl = MakeArt($arcID,true);
if($artUrl=="") $artUrl = $cfg_plus_dir."/view.php?aid=$arcID";

//写入全站搜索索引
$datas = array('aid'=>$arcID,'typeid'=>$typeid,'channelid'=>$channelid,'adminid'=>$adminID,'mid'=>0,'att'=>$arcatt,
               'title'=>$title,'url'=>$artUrl,'litpic'=>$litpic,'keywords'=>$keywords,'pubdate'=>$pubdate,
               'addinfos'=>$description,'uptime'=>$senddate,'arcrank'=>$arcrank);
WriteSearchIndex($dsql,$datas);
unset($datas);
//写入Tag索引
InsertTags($dsql,$tag,$arcID,0,$typeid,$arcrank);

//---------------------------------
//返回成功信息
//----------------------------------
$msg = "
　　请选择你的后续操作：
<a href='../spec_add.php?cid=$typeid'><u>创建新专题</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>查看专题</u></a>
&nbsp;&nbsp;
<a href='../content_s_list.php'><u>已发布专题管理</u></a>
";

$wintitle = "成功创建专题！";
$wecome_info = "文章管理::发布专题";
$win = new OxWindow();
$win->AddTitle("成功创建专题：");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();

ClearAllLink();
?>