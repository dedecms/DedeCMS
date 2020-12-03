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
$senddate = mytime();
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
$litpic = GetDDImage('litpic',$picname,$ddisremote);

$adminID = $cuserLogin->getUserID();

//自动摘要
if($description=="" && $cfg_auot_description>0){
	$description = stripslashes(cn_substr(html2text($body),$cfg_auot_description));
	$description = addslashes($description);
}

//加入主档案表

//写入数据库
//-----------------------------------
$dsql = new DedeSql(false);
$cts = GetChannelTable($dsql,$channelid);
$arcID = GetIndexKey($dsql,$typeid,$channelid);

//----------------------------------
$inQuery = "INSERT INTO `{$cts['maintable']}`(ID,typeid,typeid2,sortrank,iscommend,ismake,channel,
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

//软件链接列表
$softurl1 = stripslashes($softurl1);
$urls = "";
if($softurl1!="") $urls .= "{dede:link text='本地下载'} $softurl1 {/dede:link}\r\n";
for($i=2;$i<=9;$i++)
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

$softsize = $softsize.$unit;

$inadd_f = '';
$inadd_v = '';
//----------------------------------
//分析处理附加表数据
//----------------------------------
if(!empty($cts['addtable']))
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
 INSERT INTO `{$cts['addtable']}`(aid,typeid,filetype,language,softtype,accredit,
os,softrank,officialUrl,officialDemo,softsize,softlinks,introduce{$inadd_f}) 
VALUES ('$arcID','$typeid','$filetype','$language','$softtype','$accredit',
'$os','$softrank','$officialUrl','$officialDemo','$softsize','$urls','$body'{$inadd_v});
";

if(!$dsql->ExecuteNoneQuery($query))
{
	$gerr = $dsql->GetError();
	$dsql->ExecuteNoneQuery("Delete From `{$cts['maintable']}` where ID='$arcID'");
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
<a href='../soft_add.php?cid=$typeid'><u>继续发布软件</u></a>
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