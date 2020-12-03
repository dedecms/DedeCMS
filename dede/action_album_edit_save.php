<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_photograph.php");
require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
require_once(dirname(__FILE__)."/inc/inc_archives_functions.php");

if(!isset($iscommend)) $iscommend = 0;
if(!isset($ispic)) $ispic = 0;
if(!isset($isbold)) $isbold = 0;

if(empty($channelid)||empty($ID)){
	ShowMsg("文档为非指定的类型，请检查你更改内容时是否合法！","-1");
	exit();
}

//对保存的内容进行处理
//--------------------------------
$iscommend = $iscommend + $isbold;

$pubdate = GetMkTime($pubdate);
$sortrank = AddDay($senddate,$sortup);

if($ishtml==0) $ismake = -1;
else $ismake = 0;

$title = cn_substr($title,60);
$color =  cn_substr($color,10);
$source = cn_substr($source,50);
$description = cn_substr($description,250);
if($keywords!="") $keywords = trim(cn_substr($keywords,60))." ";
if($cuserLogin->getUserRank() < 5){ $arcrank = -1; }

if(!empty($picname)) $litpic = $picname;
else $litpic = "";


//更新数据库的SQL语句
//----------------------------------
$inQuery = "
update #@__archives set
typeid='$typeid',
typeid2='$typeid2',
sortrank='$sortrank',
iscommend='$iscommend',
ismake='$ismake',
arcrank='$arcrank',
money='$money',
title='$title',
color='$color',
source='$source',
litpic='$litpic',
pubdate='$pubdate',
description='$description',
keywords=' $keywords '
where ID='$ID'; ";


$dsql = new DedeSql();
$dsql->SetQuery($inQuery);
if(!$dsql->ExecuteNoneQuery()){
	$dsql->Close();
	ShowMsg("更新数据库archives表时出错，请检查！","-1");
	exit();
}

//处理并保存所指定的图片
//------------------------------

$imgurls = "{dede:pagestyle maxwidth='$maxwidth' value='$pagestyle'/}\r\n";

//旧图片
for($i=1;$i<=40;$i++){
	if(isset(${'oimgurl'.$i})){
		$iurl = stripslashes(${'oimgurl'.$i});
		if(trim($iurl)=="") continue;
		$iinfo = stripslashes(${'oimgmsg'.$i});
		$imgfile = $cfg_basedir.$iurl;
		if(is_file($imgfile)){
				$info = "";
				$imginfos = GetImageSize($imgfile,$info);
				$imgurls .= "{dede:img text='$iinfo' width='".$imginfos[0]."' height='".$imginfos[1]."'} $iurl {/dede:img}\r\n";
		}
	}
}
//新增图片
for($i=1;$i<=40;$i++){
	if(isset(${'imgurl'.$i})){
		$iurl = stripslashes(${'imgurl'.$i});
		if(trim($iurl)=="") continue;
		$iinfo = stripslashes(${'imgmsg'.$i});
		$iurl = trim(str_replace($cfg_basehost,"",$iurl));
		if(eregi("^http://",$iurl) && $isUrlOpen) //下载远程图片
		{
			$reimgs = "";
			if($isUrlOpen)
			{
				$reimgs = GetRemoteImage($iurl,$adminID);
			  if(is_array($reimgs)){
				  $imgurls .= "{dede:img text='$iinfo' width='".$reimgs[1]."' height='".$reimgs[2]."'} ".$reimgs[0]." {/dede:img}\r\n";
			  }
		  }
		  else
		  {
		  	$imgurl = "{dede:img text='' width='' height=''} ".$iurl." {/dede:img}\r\n";
		  }
		}
		else if($iurl!="")  //站内图片
		{
			$imgfile = $cfg_basedir.$iurl;
			if(is_file($imgfile)){
				$info = "";
				$imginfos = GetImageSize($imgfile,$info);
				$imgurls .= "{dede:img text='$iinfo' width='".$imginfos[0]."' height='".$imginfos[1]."'} $iurl {/dede:img}\r\n";
			}
		}
	}
}
$imgurls = addslashes($imgurls);
//更新加入附加表
//----------------------------------
$row = $dsql->GetOne("Select aid,typeid From #@__addonimages where aid='$ID'");
if(!is_array($row))
{
  $query = "
  INSERT INTO #@__addonimages(aid,typeid,pagestyle,maxwidth,imgurls) Values('$ID','$typeid','$pagestyle','$maxwidth','$imgurls');
  ";
  $dsql->SetQuery($query);
  if(!$dsql->ExecuteNoneQuery()){
	  $dsql->Close();
	  ShowMsg("把数据保存到数据库附加表 addonimages 时出错，请检查原因！","-1");
	  exit();
  }
}
else
{
	$query = "
  Update #@__addonimages
  set typeid='$typeid',
  pagestyle='$pagestyle',
  maxwidth = '$maxwidth',
  imgurls='$imgurls'
  where aid='$ID';";
  $dsql->SetQuery($query);
  if(!$dsql->ExecuteNoneQuery()){
	  $dsql->Close();
	  ShowMsg("更新附加表 addonimages 时出错，请检查原因！","-1");
	  exit();
  }
}

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
<a href='album_add.php?cid=$typeid'><u>继续发布图片</u></a>
&nbsp;&nbsp;
<a href='archives_do.php?aid=".$ID."&dopost=editArchives'><u>查看更改</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>查看文档</u></a>
&nbsp;&nbsp;
<a href='catalog_do.php?cid=$typeid&dopost=listArchives'><u>管理已发布图片</u></a>
&nbsp;&nbsp;
<a href='catalog_main.php'><u>网站栏目管理</u></a>
";

$wintitle = "成功更改图集！";
$wecome_info = "文章管理::更改图集";
$win = new OxWindow();
$win->AddTitle("成功更改一个图集：");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();
?>