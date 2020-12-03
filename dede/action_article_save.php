<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_photograph.php");
require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
require_once(dirname(__FILE__)."/inc/inc_archives_functions.php");

if(!isset($iscommend)) $iscommend = 0;
if(!isset($ispic)) $ispic = 0;
if(!isset($isbold)) $isbold = 0;
if(!isset($autokey)) $autokey = 0;
if(!isset($remote)) $remote = 0;
if(!isset($dellink)) $dellink = 0;
if(!isset($seltypeid)) $seltypeid = 0;

if(empty($channelid)){
	ShowMsg("文档为非指定的类型，请检查你增加内容时是否合法！","-1");
	exit();
}

//对保存的内容进行处理
//--------------------------------
$iscommend = $iscommend + $isbold;

$pubdate = GetMkTime($pubdate);
$senddate = time();
$sortrank = AddDay($senddate,$sortup);

if($ishtml==0) $ismake = -1;
else $ismake = 0;

if($typeid==0 && $seltypeid>0) $typeid = $seltypeid;

if($typeid==""||$typeid==0)
{
	ShowMsg('档案主栏目必须选择！','-1');
	exit();
}

$title = cn_substr($title,60);
$color =  cn_substr($color,10);
$writer =  cn_substr($writer,30);
$source = cn_substr($source,50);
$description = cn_substr($description,250);
$keywords = cn_substr($keywords,60);
if($cuserLogin->getUserRank() < 5){ $arcrank = -1; }

//处理上传的缩略图
//图片文件命名的方式为"/年月日/拼音部首的后6个字符+栏目ID+顺序ID+'.jpg'"
//如果顺序数超过一千，才会有可能出现重复
//------------------------------------------
if(is_uploaded_file($litpic))
{
  $istype = 0;
  $sparr = Array("image/pjpeg","image/jpeg","image/gif","image/png");
  $litpic_type = strtolower(trim($litpic_type));
  if(!in_array($litpic_type,$sparr)){
		ShowMsg("上传的图片格式错误，请使用JPEG、GIF、PNG格式的其中一种！","-1");
		exit();
	}
  $savepath = $ddcfg_image_dir."/".str_replace("-","",GetDateMk($pubdate));
  CreateDir($savepath);
  $rname = GetPinyin($title,1,0);
  $rndname = substr($rname,strlen($rname)-6,6).$typeid;
  $fullUrl = $savepath."/".$rndname;
  $spdd = 1;
  while(true){
  	if(!file_exists($cfg_basedir.$fullUrl."-".$spdd.".jpg")||$spdd>1000) break;
  	$spdd++;
  }
  $fullUrl = $fullUrl."-".$spdd.".jpg";
  move_uploaded_file($litpic,$cfg_basedir.$fullUrl);
	@unlink($litpic);
	$litpic = $fullUrl;
	ImageResize($cfg_basedir.$fullUrl,$cfg_ddimg_width,$cfg_ddimg_height);
	$litpic = $fullUrl;
}
else{
	if(!empty($picname)) $litpic = $picname;
}
if(empty($litpic)) $litpic = "";

//-------------------------------------------------
$body = stripslashes($body);
//把内容中远程的图片资源本地化
//------------------------------------
if($isUrlOpen && $remote==1){
	$body = GetCurContent($body);
}
//去除内容中的站外链接
//------------------------------------
if($dellink==1){
	$body = preg_replace("/(<a[ \t\r\n]{1,}href=[\"']{0,}http:\/\/[^\/]([^>]*)>)|(<\/a>)/isU","",$body);
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
	$keywords = addslashes($keywords);
}
$body = addslashes($body);
if($keywords!="") $keywords = trim(cn_substr($keywords,60))." ";
$adminID = $cuserLogin->getUserID();

//加入数据库的SQL语句
//----------------------------------
$inQuery = "INSERT INTO #@__archives(
typeid,typeid2,sortrank,iscommend,ismake,channel,
arcrank,click,money,title,color,writer,source,litpic,
pubdate,senddate,adminID,memberID,description,keywords) 
VALUES ('$typeid','$typeid2','$sortrank','$iscommend','$ismake','$channelid',
'$arcrank','0','$money','$title','$color','$writer','$source','$litpic',
'$pubdate','$senddate','$adminID','0','$description',' $keywords ');";

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