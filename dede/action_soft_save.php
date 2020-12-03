<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_photograph.php");
require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
require_once(dirname(__FILE__)."/inc/inc_archives_functions.php");

if(!isset($iscommend)) $iscommend = 0;
if(!isset($ispic)) $ispic = 0;
if(!isset($isbold)) $isbold = 0;
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
if($keywords!="") $keywords = trim(cn_substr($keywords,60))." ";
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
  $rndname = substr($rname,0,6).$typeid;
  $fullUrl = $savepath."/".$rndname;
  $spdd = 1;
  while(true){
  	if(!file_exists($cfg_basedir.$fullUrl."-".$spdd.".jpg")||$spdd>1000) break;
  	$spdd++;
  }
  $fullUrl = $fullUrl."-".$spdd.".jpg";
  @move_uploaded_file($litpic,$cfg_basedir.$fullUrl);
	@unlink($litpic);
	$litpic = $fullUrl;
	ImageResize($cfg_basedir.$fullUrl,$cfg_ddimg_width,$cfg_ddimg_height);
	$litpic = $fullUrl;
}
else{
	if(!empty($picname)) $litpic = $picname;
}
if(empty($litpic)) $litpic = "";

$adminID = $cuserLogin->getUserID();

//加入主档案表

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

//软件链接列表
$softurl1 = stripslashes($softurl1);
if($softurl1!="") $urls = "{dede:link text='本地下载'} $softurl1 {/dede:link}\r\n";
else $urls = "";
for($i=2;$i<=9;$i++)
{
	if(!empty(${'softurl'.$i}))
	{ 
		$servermsg = stripslashes(${'servermsg'.$i});
	$softurl = stripslashes(${'softurl'.$i});
		if($servermsg=="") $servermsg = "下载地址".$i;
		if($softurl!="" && $softurl!="http://")
		{ $urls .= "{dede:link text='$servermsg'} $softurl {/dede:link}\r\n"; }
  }
}

$urls = addslashes($urls);

$softsize = $softsize.$unit;

//加入附加表
//----------------------------------
$arcID = $dsql->GetLastID();

$inQuery = "
INSERT INTO #@__addonsoft(aid,typeid,filetype,language,softtype,accredit,
os,softrank,officialUrl,officialDemo,softsize,softlinks,introduce) 
VALUES ('$arcID','$typeid','$filetype','$language','$softtype','$accredit',
'$os','$softrank','$officialUrl','$officialDemo','$softsize','$urls','$body');
";

$dsql->SetQuery($inQuery);
if(!$dsql->ExecuteNoneQuery()){
	$dsql->SetQuery("Delete From #@__archives where ID='$arcID'");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("把数据保存到数据库附加表 addonsoft 时出错，请检查原因！","-1");
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
<a href='soft_add.php?cid=$typeid'><u>继续发布软件</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>查看软件</u></a>
&nbsp;&nbsp;
<a href='catalog_do.php?cid=$typeid&dopost=listArchives'><u>已发布软件管理</u></a>
&nbsp;&nbsp;
<a href='catalog_main.php'><u>网站栏目管理</u></a>
";

$wintitle = "成功发布一个软件！";
$wecome_info = "文章管理::发布软件";
$win = new OxWindow();
$win->AddTitle("成功发布软件：");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();
?>