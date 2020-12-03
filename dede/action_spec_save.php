<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_photograph.php");
require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
require_once(dirname(__FILE__)."/inc/inc_archives_functions.php");

if(!isset($iscommend)) $iscommend = 0;
if(!isset($isbold)) $isbold = 0;

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
arcrank,click,title,color,writer,source,litpic,
pubdate,senddate,adminID,memberID,description,keywords) 
VALUES ('$typeid','$typeid2','$sortrank','$iscommend','$ismake','$channelid',
'$arcrank','0','$title','$color','$writer','$source','$litpic',
'$pubdate','$senddate','$adminID','0','$description',' $keywords ');";
$dsql = new DedeSql();
$dsql->SetQuery($inQuery);
if(!$dsql->ExecuteNoneQuery()){
	$dsql->Close();
	ShowMsg("把数据保存到数据库archives表时出错，请检查！","-1");
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
		$notename = trim(${'notename'.$i});
		$arcid = trim(${'arcid'.$i});
		$col = trim(${'col'.$i});
		$imgwidth = trim(${'imgwidth'.$i});
		$imgheight = trim(${'imgheight'.$i});
		$titlelen = trim(${'titlelen'.$i});
		$infolen = trim(${'infolen'.$i});
		$listtmp = trim(${'listtmp'.$i});

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
		infolen=\\'infolen\\' titlelen=\\'$titlelen\\' col=\\'$col\\' idlist=\\'$okids\\' name=\\'".addslashes($notename)."\\'}
		$listtmp
		{/dede:specnote}\r\n";
	}
}

//加入附加表
//----------------------------------
$arcID = $dsql->GetLastID();

$inQuery = "INSERT INTO #@__addonspec(aid,typeid,note) VALUES ('$arcID','$typeid','$notelist');";

$dsql->SetQuery($inQuery);
if(!$dsql->ExecuteNoneQuery()){
	$dsql->SetQuery("Delete From #@__archives where ID='$arcID'");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("把数据保存到数据库附加表 addonspec 时出错，请检查原因！","-1");
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
<a href='spec_add.php?cid=$typeid'><u>创建新专题</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>查看专题</u></a>
&nbsp;&nbsp;
<a href='content_s_list.php'><u>已发布专题管理</u></a>
";

$wintitle = "成功创建专题！";
$wecome_info = "文章管理::发布专题";
$win = new OxWindow();
$win->AddTitle("成功创建专题：");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();
?>