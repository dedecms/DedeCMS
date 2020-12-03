<?php 
require_once(dirname(__FILE__)."/../config.php");
CheckPurview('a_Edit,a_AccEdit,a_MyEdit');
require_once(dirname(__FILE__)."/../../include/inc_photograph.php");
require_once(dirname(__FILE__)."/../../include/pub_oxwindow.php");
require_once(dirname(__FILE__)."/../inc/inc_archives_functions.php");

if(!isset($iscommend)) $iscommend = 0;
if(!isset($isjump)) $isjump = 0;
if(!isset($isbold)) $isbold = 0;
if(!isset($isrm)) $isrm = 0;

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
$source = cn_substr($source,50);
$description = cn_substr($description,250);
if($keywords!="") $keywords = trim(cn_substr($keywords,60))." ";
if(!TestPurview('a_Check,a_AccCheck,a_MyCheck')){ $arcrank = -1; }

//处理上传的缩略图
if(empty($ddisremote)) $ddisremote = 0;
$litpic = GetDDImage('none',$picname,$ddisremote);

$adminID = $cuserLogin->getUserID();
//处理并保存所指定的图片
//------------------------------
$imgurls = "{dede:pagestyle maxwidth='$maxwidth' ddmaxwidth='$ddmaxwidth' row='$row' col='$col' value='$pagestyle'/}\r\n";

for($i=1;$i<=120;$i++)
{
	if(isset(${'imgurl'.$i})||(isset($_FILES['imgfile'.$i]['tmp_name']) && is_uploaded_file($_FILES['imgfile'.$i]['tmp_name'])))
	{
		$iinfo = str_replace("'","`",stripslashes(${'imgmsg'.$i}));
		//非上传图片
		if(!is_uploaded_file($_FILES['imgfile'.$i]['tmp_name']))
		{
		    $iurl = stripslashes(${'imgurl'.$i});
		    $ioldurl = @stripslashes(${'imgurlold'.$i});
		    $ioldddimg = @stripslashes(${'oldddimg'.$i});
		    if(trim($iurl)=="") continue;
		    $iurl = trim(str_replace($cfg_basehost,"",$iurl));
		    if((eregi("^http://",$iurl) && !eregi($cfg_basehost,$iurl)) && $cfg_isUrlOpen)
		    //远程图片
		    {
			    $reimgs = "";
			    if($cfg_isUrlOpen && $isrm==1)
			    {
				     $reimgs = GetRemoteImage($iurl,$adminID);
			       if(is_array($reimgs)){
				        $litpicname = GetImageMapDD($reimgs[0],$ddmaxwidth);
				        $imgurls .= "{dede:img ddimg='$litpicname' text='$iinfo' width='".$reimgs[1]."' height='".$reimgs[2]."'} ".$reimgs[0]." {/dede:img}\r\n";
			       }else{
			       	  echo "下载：".$iurl." 失败，可能图片有反采集功能或http头不正确！<br />\r\n";
			       }
		      }else{
		  	     $imgurls .= "{dede:img text='$iinfo' width='' height=''} ".$iurl." {/dede:img}\r\n";
		      }
		    //站内图片
		    }else if($iurl!="")
		    {
			    $imgfile = $cfg_basedir.$iurl;
			    if(is_file($imgfile))
			    {
			        if($ioldurl!=$iurl || !is_file($cfg_basedir.$ioldddimg)){
			        	$litpicname = GetImageMapDD($iurl,$ddmaxwidth);
				      }
				      $info = "";
				      $imginfos = GetImageSize($imgfile,$info);
				      $imgurls .= "{dede:img ddimg='$litpicname' text='$iinfo' width='".$imginfos[0]."' height='".$imginfos[1]."'} $iurl {/dede:img}\r\n";
			    }
		   }
	  //直接上传的图片
	  }else
	  {
			 $sparr = Array("image/pjpeg","image/jpeg","image/gif","image/png","image/x-png","image/wbmp");
			 if(!in_array($_FILES['imgfile'.$i]['type'],$sparr)){
			 	  continue;
			 }
			 $uptime = mytime();
			 $imgPath = $cfg_image_dir."/".strftime("%y%m%d",$uptime);
	  	 MkdirAll($cfg_basedir.$imgPath,$GLOBALS['cfg_dir_purview']);
			 CloseFtp();
			 $filename = $imgPath."/".dd2char($cuserLogin->getUserID().strftime("%H%M%S",$uptime).mt_rand(1000,9999))."-{$i}";
			 $fs = explode(".",$_FILES['imgfile'.$i]['name']);
	     $filename = $filename.".".$fs[count($fs)-1];
			 @move_uploaded_file($_FILES['imgfile'.$i]['tmp_name'],$cfg_basedir.$filename);
			 
			 //缩图
			 $litpicname = GetImageMapDD($filename,$ddmaxwidth);
			 
			 //水印
			 $imgfile = $cfg_basedir.$filename;
			 @WaterImg($imgfile,'up');
			 
			 if(is_file($imgfile))
			 {
				    $iurl = $filename;
				    $info = "";
				    $imginfos = GetImageSize($imgfile,$info);
			      $imgurls .= "{dede:img ddimg='$litpicname' text='$iinfo' width='".$imginfos[0]."' height='".$imginfos[1]."'} $iurl {/dede:img}\r\n";
			      //把新上传的图片信息保存到媒体文档管理档案中
			      $inquery = "
               INSERT INTO #@__uploads(title,url,mediatype,width,height,playtime,filesize,uptime,adminid,memberid) 
                VALUES ('$title".$i."','$filename','1','".$imginfos[0]."','".$imginfos[1]."','0','".filesize($imgfile)."','".mytime()."','$adminID','0');
             ";
            $dsql->ExecuteNoneQuery($inquery);
			 }
	  }
	}//含有图片的条件
}//循环结束

$imgurls = addslashes($imgurls);

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
  set typeid='$typeid',pagestyle='$pagestyle',maxwidth = '$maxwidth',
  imgurls='$imgurls',row='$row',col='$col',isrm='$isrm'{$inadd_f}
where aid='$ID';";

if(!$dsql->ExecuteNoneQuery($addQuery)){
	$gerr = $dsql->GetError();
	$dsql->Close();
	ShowMsg("更新数据库附加表 `{$cts['addtable']}` 时出错，请把相关信息提交给DedeCms官方。".$gerr,"javascript:;");
	exit();
}

//生成HTML
//---------------------------------
$artUrl = MakeArt($ID,true);
if($artUrl=="") $artUrl = $cfg_plus_dir."/view.php?aid=$ID";

//更新全站搜索索引
$datas = array('aid'=>$ID,'typeid'=>$typeid,'channelid'=>$channelid,'adminid'=>$edadminid,'mid'=>$memberid,'att'=>$arcatt,
               'title'=>$title,'url'=>$artUrl,'litpic'=>$litpic,'keywords'=>$keywords,'pubdate'=>$pubdate,
               'addinfos'=>$description,'uptime'=>mytime(),'arcrank'=>$arcrank);
UpSearchIndex($dsql,$datas);
unset($datas);
//更新Tag索引
UpTags($dsql,$tag,$ID,0,$typeid,$arcrank);

//---------------------------------
//返回成功信息
//----------------------------------

$msg = "
　　请选择你的后续操作：
<a href='../album_add.php?cid=$typeid'><u>继续发布图片</u></a>
&nbsp;&nbsp;
<a href='../archives_do.php?aid=".$ID."&dopost=editArchives'><u>查看更改</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>预览文档</u></a>
&nbsp;&nbsp;
<a href='../catalog_do.php?cid=$typeid&dopost=listArchives'><u>管理已发布图片</u></a>
&nbsp;&nbsp;
<a href='../catalog_main.php'><u>网站栏目管理</u></a>
";

$wintitle = "成功更改图集！";
$wecome_info = "文章管理::更改图集";
$win = new OxWindow();
$win->AddTitle("成功更改一个图集：");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();

ClearAllLink();
?>