<?php 
require_once(dirname(__FILE__)."/../config.php");
CheckPurview('a_New,a_AccNew');
require_once(dirname(__FILE__)."/../../include/inc_photograph.php");
require_once(dirname(__FILE__)."/../../include/pub_oxwindow.php");
require_once(dirname(__FILE__)."/../inc/inc_archives_functions.php");

if(!isset($iscommend)) $iscommend = 0;
if(!isset($isjump)) $isjump = 0;
if(!isset($isbold)) $isbold = 0;
if(!isset($isrm)) $isrm = 0;
if(!isset($ddisfirst)) $ddisfirst = 0;
if(!isset($ddisremote)) $ddisremote = 0;
$zipfile = (empty($zipfile) ? '' : $zipfile);
$formzip = (empty($formzip) ? 0 : $formzip);
$delzip = (empty($delzip) ? 0 : $delzip);
$formhtml = (empty($formhtml) ? 0 : $formhtml);

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
$title = trim($title);
if(empty($title)){
        ShowMsg("请输入标题","-1");
        exit();
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

$shorttitle = (empty($shorttitle) ? '' : cn_substr($shorttitle,36));
$color =  cn_substr($color,10);
$writer =  "";
$source = cn_substr($source,50);
$description = cn_substr($description,250);
if($keywords!="") $keywords = trim(cn_substr($keywords,60))." ";
if(!TestPurview('a_Check,a_AccCheck,a_MyCheck')){ $arcrank = -1; }

//处理上传的缩略图
$litpic = GetDDImage('litpic',$picname,$ddisremote);

$adminID = $cuserLogin->getUserID();

$imgurls = "{dede:pagestyle maxwidth='$maxwidth' ddmaxwidth='$ddmaxwidth' row='$row' col='$col' value='$pagestyle'/}\r\n";
$hasone = false;

//处理并保存所指定的图片从
//网上复制
//------------------------------
if($formhtml==1)
{
	$imagebody = stripslashes($imagebody);
	$imgurls .= GetCurContentAlbum($imagebody,$copysource,$litpicname);
	if($ddisfirst==1 && $litpic=="" && !empty($litpicname))
	{
	  $litpic = $litpicname;
	  $hasone = true;
	}
}
//ZIP中解压
//------------------------------
else if($formzip==1)
{
	include_once(DEDEADMIN."/../include/zip.lib.php");
	include_once(DEDEADMIN."/file_class.php");
	$zipfile = $cfg_basedir.str_replace($cfg_mainsite,'',$zipfile);
	$tmpzipdir = DEDEADMIN.'/module/ziptmp/'.cn_substr(md5(ExecTime()),16);
	$ntime = time();
	if(file_exists($zipfile))
	{
		 @mkdir($tmpzipdir,$GLOBALS['cfg_dir_purview']);
		 @chmod($tmpzipdir,$GLOBALS['cfg_dir_purview']);
		 $z = new zip();
		 $z->ExtractAll($zipfile,$tmpzipdir);
		 $fm = new FileManagement();
		 $imgs = array();
		 $fm->GetMatchFiles($tmpzipdir,"jpg|png|gif",$imgs);
		 $i = 0;

		 foreach($imgs as $imgold)
		 {
			   $i++;
			   $savepath = $cfg_image_dir."/".strftime("%Y-%m",$ntime);
         CreateDir($savepath);
         $iurl = $savepath."/".strftime("%d",$ntime).dd2char(strftime("%H%M%S",$ntime).'-'.$adminID."-{$i}".mt_rand(1000,9999));
         $iurl = $iurl.substr($imgold,-4,4);
			   $imgfile = $cfg_basedir.$iurl;
			   copy($imgold,$imgfile);
			   unlink($imgold);
			   if(is_file($imgfile))
			   {
			      $litpicname = GetImageMapDD($iurl,$ddmaxwidth);
				    $info = '';
				    $imginfos = GetImageSize($imgfile,$info);
				    $imgurls .= "{dede:img ddimg='$litpicname' text='' width='".$imginfos[0]."' height='".$imginfos[1]."'} $iurl {/dede:img}\r\n";
			      //把图片信息保存到媒体文档管理档案中
			      $inquery = "
               INSERT INTO #@__uploads(title,url,mediatype,width,height,playtime,filesize,uptime,adminid,memberid) 
                VALUES ('{$title}','{$iurl}','1','".$imginfos[0]."','".$imginfos[1]."','0','".filesize($imgfile)."','".$ntime."','$adminID','0');
             ";
            $dsql->ExecuteNoneQuery($inquery);
            if(!$hasone && $ddisfirst==1 
            && $litpic=="" && !empty($litpicname))
            {
	  	         if( file_exists($cfg_basedir.$litpicname) )
	  	         {
	  		          $litpic = $litpicname;
	  		          $hasone = true;
	  	         }
	          }
			   }
		 }
		 if($delzip==1) $fm->RmDirFiles($tmpzipdir);
	}
}
//正常上传或指定网址
//------------------------------
else {
for($i=1;$i<=120;$i++)
{
	if(isset(${'imgurl'.$i})||(isset($_FILES['imgfile'.$i]['tmp_name']) && is_uploaded_file($_FILES['imgfile'.$i]['tmp_name'])))
	{
		$iinfo = str_replace("'","`",stripslashes(${'imgmsg'.$i}));
		//非上传图片
		if(!is_uploaded_file($_FILES['imgfile'.$i]['tmp_name']))
		{
		    $iurl = stripslashes(${'imgurl'.$i});
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
		    }else if($iurl!=""){
			    $imgfile = $cfg_basedir.$iurl;
			    if(is_file($imgfile)){
			        $litpicname = GetImageMapDD($iurl,$ddmaxwidth);
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
			 $uptime = time();
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
			 
			 if(is_file($imgfile)){
				    $iurl = $filename;
				    $info = "";
				    $imginfos = GetImageSize($imgfile,$info);
			      $imgurls .= "{dede:img ddimg='$litpicname' text='$iinfo' width='".$imginfos[0]."' height='".$imginfos[1]."'} $iurl {/dede:img}\r\n";
			      //把新上传的图片信息保存到媒体文档管理档案中
			      $inquery = "
               INSERT INTO #@__uploads(title,url,mediatype,width,height,playtime,filesize,uptime,adminid,memberid) 
                VALUES ('$title".$i."','$filename','1','".$imginfos[0]."','".$imginfos[1]."','0','".filesize($imgfile)."','".time()."','$adminID','0');
             ";
            $dsql->ExecuteNoneQuery($inquery);
			 }
	  }
	  if(!$hasone && $ddisfirst==1 && $litpic=="" && !empty($litpicname))
	  {
	  	if( file_exists($cfg_basedir.$litpicname) )
	  	{
	  		$litpic = $litpicname;
	  		$hasone = true;
	  	}
	  }
	}//含有图片的条件
}//循环结束
}

$imgurls = addslashes($imgurls);

//写入数据库
//-----------------------------------
$dsql = new DedeSql(false);
$cts = GetChannelTable($dsql,$channelid);
$arcID = GetIndexKey($dsql,$typeid,$channelid);

//加入主档案表
//----------------------------------
$inQuery = "INSERT INTO `{$cts['maintable']}`(ID,typeid,typeid2,sortrank,iscommend,ismake,channel,arcrank,click,money,
title,shorttitle,color,writer,source,litpic,pubdate,senddate,arcatt,adminID,memberID,description,keywords,likeid) 
VALUES ('$arcID','$typeid','$typeid2','$sortrank','$iscommend','$ismake','$channelid','$arcrank','0','$money',
'$title','$shorttitle','$color','$writer','$source','$litpic','$pubdate','$senddate','$arcatt','$adminID','0',
'$description','$keywords','$likeid');
";

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
 INSERT INTO `{$cts['addtable']}`(aid,typeid,pagestyle,maxwidth,imgurls,row,col,isrm,ddmaxwidth{$inadd_f}) 
 Values('$arcID','$typeid','$pagestyle','$maxwidth','$imgurls','$row','$col','$isrm','$ddmaxwidth'{$inadd_v});
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
<a href='../album_add.php?cid=$typeid'><u>继续发布图片</u></a>
&nbsp;&nbsp;
<a href='../archives_do.php?aid=".$arcID."&dopost=editArchives'><u>更改图集</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>预览文档</u></a>
&nbsp;&nbsp;
<a href='../catalog_do.php?cid=$typeid&dopost=listArchives'><u>已发布图片管理</u></a>
&nbsp;&nbsp;
<a href='../catalog_main.php'><u>网站栏目管理</u></a>
";

$wintitle = "成功发布一个图集！";
$wecome_info = "文章管理::发布图集";
$win = new OxWindow();
$win->AddTitle("成功发布一个图集：");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();

ClearAllLink();
?>