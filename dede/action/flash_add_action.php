<?php
require_once(dirname(__FILE__)."/../config.php");
CheckPurview('a_New,a_AccNew');
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
if(!TestPurview('a_New')) {
	CheckCatalog($typeid,"对不起，你没有操作栏目 {$typeid} 的权限！");
	if($typeid2!=0) CheckCatalog($typeid2,"对不起，你没有操作栏目 {$typeid2} 的权限！");
}

$arcrank = GetCoRank($arcrank,$typeid);

//对保存的内容进行处理
//--------------------------------
$iscommend = $iscommend + $isbold;

$pubdate = GetMkTime($pubdate);
$senddate = time();
$sortrank = AddDay($senddate,$sortup);

if($ishtml==0) $ismake = -1;
else $ismake = 0;

$shorttitle = cn_substr($shorttitle,36);
$color =  cn_substr($color,10);
$writer =  cn_substr($writer,30);
$source = cn_substr($source,50);
$description = cn_substr($description,250);
if($keywords!="") $keywords = trim(cn_substr($keywords,50)." ");
if(!TestPurview('a_Check,a_AccCheck,a_MyCheck')){ $arcrank = -1; }

//处理上传的缩略图
if(empty($ddisremote)) $ddisremote = 0;
$litpic = GetDDImage('litpic',$picname,$ddisremote);

$adminID = $cuserLogin->getUserID();

$filesize = $filesize.$unit;
$playtime = "$tm 分 $ts 秒";
$width  = GetAlabNum($width);
$height = GetAlabNum($height);
//$flashurl = "";


//处理远程的Flash
//------------------
$rmflash = "";
if(empty($downremote)) $downremote = 0;

//直接从远程粘贴
if(eregi("embed",$remoteflash)){
	$remoteflash = stripslashes($remoteflash);
	require_once(dirname(__FILE__)."/../../include/pub_dedehtml2.php");
	$dml = new DedeHtml2();
	$dml->GetLinkType = "media";
	$dml->SetSource($remoteflash,"",false);
	$marr = $dml->Medias;
	$rmfalsh = "";
	if(!is_array($marr)) $rmfalsh = "";
	else{
		if(count($marr)==1)
		{
			foreach($marr as $k=>$v){
			  $rmfalsh = $k;
			  break;
		  }
		}
		else
		{
			foreach($marr as $k=>$v){
			  $rmfalsh = $k;
			  if(GetAlabNum($dml->MediaInfos[$rmfalsh][0])>300&&
			  GetAlabNum($dml->MediaInfos[$rmfalsh][1])>250)
			  { break; }
		  }
		}
		$width  = GetAlabNum($dml->MediaInfos[$rmfalsh][0]);
		$height = GetAlabNum($dml->MediaInfos[$rmfalsh][1]);
	}
	$dml->Clear();
	if($cfg_isUrlOpen && $downremote==1) $rmflash = GetRemoteFlash($rmfalsh,$adminID);
}
//Flash Url 为远程地址
else if(eregi("^http://",$flashurl)
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
$cts = GetChannelTable($dsql,$channelid);
$arcID = GetIndexKey($dsql,$typeid,$channelid);


//加入数据库的SQL语句
//----------------------------------
$inQuery = "INSERT INTO `{$cts['maintable']}`(
ID,typeid,typeid2,sortrank,iscommend,ismake,channel,
arcrank,click,money,title,shorttitle,color,writer,source,litpic,
pubdate,senddate,arcatt,adminID,memberID,description,keywords,likeid)
VALUES ('$arcID','$typeid','$typeid2','$sortrank','$iscommend','$ismake','$channelid',
'$arcrank','0','$money','$title','$shorttitle','$color','$writer','$source','$litpic',
'$pubdate','$senddate','$arcatt','$adminID','0','$description','$keywords','$likeid');";

if(!$dsql->ExecuteNoneQuery($inQuery)){
	$gerr = $dsql->GetError();
	$dsql->Close();
	ShowMsg("把数据保存到数据库主表 `{$cts['maintable']}` 时出错，请把相关信息提交给DedeCms官方。".$gerr,"javascript:;");
	exit();
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

$query = "
INSERT INTO `{$cts['addtable']}`(aid,typeid,filesize,playtime,flashtype,flashrank,width,height,flashurl{$inadd_f})
VALUES ('$arcID','$typeid','$filesize','$playtime','$flashtype','$flashrank','$width','$height','$flashurl'{$inadd_v});
";

$dsql->SetQuery($query);
if(!$dsql->ExecuteNoneQuery()){
	$gerr = $dsql->GetError();
	$dsql->ExecuteNoneQuery("Delete From {$cts['maintable']} where ID='$arcID'");
	$dsql->ExecuteNoneQuery("Delete From `#@__full_search` where aid='$arcID'");
	$dsql->Close();
	ShowMsg("把数据保存到数据库附加表 `{$cts['addtable']}` 时出错，请把相关信息提交给DedeCms官方。".$gerr,"javascript:;");
	exit();
}
$artUrl = getfilenameonly($arcID, $typeid, $senddate, $title, $ismake, $arcrank, $money);

//写入全站搜索索引
$datas = array('aid'=>$arcID,'typeid'=>$typeid,'channelid'=>$channelid,'adminid'=>$adminID,'mid'=>0,'att'=>$arcatt,
               'title'=>$title,'url'=>$artUrl,'litpic'=>$litpic,'keywords'=>$keywords,'pubdate'=>$pubdate,
               'addinfos'=>$description,'uptime'=>$senddate,'arcrank'=>$arcrank);
WriteSearchIndex($dsql,$datas);
unset($datas);
//写入Tag索引
InsertTags($dsql,$tag,$arcID,0,$typeid,$arcrank);
//生成HTML
//---------------------------------
MakeArt($arcID,true);

//---------------------------------
//返回成功信息
//----------------------------------

$msg = "
　　请选择你的后续操作：
<a href='../flash_add.php?cid=$typeid'><u>继续发布Flash作品</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>查看文档</u></a>
&nbsp;&nbsp;
<a href='../catalog_do.php?cid=$typeid&dopost=listArchives'><u>已发布Flash管理</u></a>
&nbsp;&nbsp;
<a href='../catalog_main.php'><u>网站栏目管理</u></a>
";

$wintitle = "成功发布一个Flash作品！";
$wecome_info = "文章管理::发布Flash";
$win = new OxWindow();
$win->AddTitle("成功发布作品：");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();

ClearAllLink();
?>