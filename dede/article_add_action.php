<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('a_New,a_AccNew');
require_once(dirname(__FILE__)."/../include/inc_photograph.php");
require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
require_once(dirname(__FILE__)."/inc/inc_archives_functions.php");

if(!isset($iscommend)) $iscommend = 0;
if(!isset($isjump)) $isjump = 0;
if(!isset($isbold)) $isbold = 0;
if(!isset($autokey)) $autokey = 0;
if(!isset($remote)) $remote = 0;
if(!isset($dellink)) $dellink = 0;
if(!isset($autolitpic)) $autolitpic = 0;

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
$keywords = cn_substr($keywords,60);
if(!TestPurview('a_Check,a_AccCheck,a_MyCheck')){ $arcrank = -1; }

//处理上传的缩略图
if(empty($ddisremote)) $ddisremote = 0;
$litpic = GetDDImage('litpic',$picname,$ddisremote);

$body = stripslashes($body);

//自动摘要
if($description=="" && $cfg_auot_description>0){
	$description = stripslashes(cn_substr(html2text($body),$cfg_auot_description));
	$description = trim(preg_replace("/#p#|#e#/","",$description));
	$description = addslashes($description);
}
//把内容中远程的图片资源本地化
//------------------------------------
if($isUrlOpen && $remote==1){
	$body = GetCurContent($body);
}
//去除内容中的站外链接
//------------------------------------
if($dellink==1){
	$body = str_replace($cfg_basehost,'#basehost#',$body);
	$body = preg_replace("/(<a[ \t\r\n]{1,}href=[\"']{0,}http:\/\/[^\/]([^>]*)>)|(<\/a>)/isU","",$body);
  $body = str_replace('#basehost#',$cfg_basehost,$body);
}
//自动获取文章中的关键字
//----------------------------------
if($autokey==1||$keywords==""){
	require_once(dirname(__FILE__)."/../include/pub_splitword_www.php");
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
//自动分页
if($sptype=="auto"){
	$body = SpLongBody($body,$spsize*1024,"#p#分页标题#e#");
}

//自动获取缩略图
if($autolitpic==1 && $litpic==''){
  $cfg_medias_dir = str_replace('/','\/',$cfg_medias_dir);
  $picname = preg_replace("/.+?".$cfg_medias_dir."(.*)( |\"|').*$/isU",$cfg_medias_dir."$1",$body);
  if(eregi("\.(jpg|gif|png)$",$picname)){
  	 if(ereg("_lit\.",$picname)) $litpic = $picname;
  	 else $litpic = GetDDImage('ddfirst',$picname,0);
  }
}

$body = addslashes($body);
if($keywords!="") $keywords = trim(cn_substr($keywords,60))." ";
$adminID = $cuserLogin->getUserID();

//加入数据库的SQL语句
//----------------------------------
$inQuery = "INSERT INTO #@__archives(
typeid,typeid2,sortrank,iscommend,ismake,channel,
arcrank,click,money,title,shorttitle,color,writer,source,litpic,
pubdate,senddate,arcatt,adminID,memberID,description,keywords,templet,redirecturl) 
VALUES ('$typeid','$typeid2','$sortrank','$iscommend','$ismake','$channelid',
'$arcrank','0','$money','$title','$shorttitle','$color','$writer','$source','$litpic',
'$pubdate','$senddate','$arcatt','$adminID','0','$description','$keywords','$templet','$redirecturl');";

$dsql = new DedeSql();
$dsql->SetQuery($inQuery);
if(!$dsql->ExecuteNoneQuery()){
	$dsql->Close();
	ShowMsg("把数据保存到数据库archives表时出错，请检查！","-1");
	exit();
}
$arcID = $dsql->GetLastID();
$dsql->SetQuery("INSERT INTO #@__addonarticle(aid,typeid,body) Values('$arcID','$typeid','$body')");
if(!$dsql->ExecuteNoneQuery()){
	$dsql->SetQuery("Delete From #@__archives where ID='$arcID'");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("把数据保存到数据库附加表addonarticle时出错，请检查原因！","-1");
	exit();
}
$dsql->Close();

//生成HTML
//---------------------------------

$artUrl = MakeArt($arcID,true);
if($artUrl=="") $artUrl = $cfg_plus_dir."/view.php?aid=$arcID";

//---------------------------------
//返回成功信息
//----------------------------------

$msg = "
　　请选择你的后续操作：
<a href='article_add.php?cid=$typeid'><u>继续发布文章</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>查看文章</u></a>
&nbsp;&nbsp;
<a href='archives_do.php?aid=".$arcID."&dopost=editArchives'><u>更改文章</u></a>
&nbsp;&nbsp;
<a href='catalog_do.php?cid=$typeid&dopost=listArchives'><u>已发布文章管理</u></a>
&nbsp;&nbsp;
<a href='catalog_main.php'><u>网站栏目管理</u></a>
";

$wintitle = "成功发布文章！";
$wecome_info = "文章管理::发布文章";
$win = new OxWindow();
$win->AddTitle("成功发布文章：");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();
?>