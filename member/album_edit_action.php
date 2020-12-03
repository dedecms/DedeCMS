<?php 
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);

if($cfg_mb_album=='N'){
	ShowMsg("对不起，系统禁用了图集的功能，因此无法使用！","-1");
	exit();
}

$cfg_add_dftable = '#@__addonimages';
require_once(dirname(__FILE__)."/archives_editcheck.php");

CheckUserSpace($cfg_ml->M_ID);

$title = ClearHtml($title);
$writer =  cn_substr(trim(ClearHtml($writer)),30);
$source = cn_substr(trim(ClearHtml($source)),50);
$description = cn_substr(trim(ClearHtml($description)),250);
$keywords = trim(cn_substr($keywords,60));
$userip = GetIP();

//处理上传的缩略图
if(!empty($litpic)){
	$litpic = GetUpImage('litpic',true,true);
	$litpicsql = " litpic='$litpic', ";
}else{
	$litpic = "";
	$litpicsql = '';
}

$memberID = $cfg_ml->M_ID;

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
		     ${$vs[0]} = filterscript(stripslashes(${$vs[0]}));
         //自动摘要
         if($description==''){
    	      $description = cn_substr(html2text(${$vs[0]}),$cfg_auot_description);
	          $description = trim(preg_replace("/#p#|#e#/","",$description));
	          $description = addslashes($description);
         }
         ${$vs[0]} = addslashes(${$vs[0]});
         ${$vs[0]} = GetFieldValue(${$vs[0]},$vs[1],$ID,'add','','member');
	     }else{
		     ${$vs[0]} = GetFieldValueA(${$vs[0]},$vs[1],$ID);
	     }
	     $inadd_f .= ",`{$vs[0]}` = '".${$vs[0]}."'";
    }
  }
}

//更新数据库的SQL语句
//----------------------------------
$inQuery = "
update `$maintable` set
ismake='$ismake',arcrank='$arcrank',typeid='$typeid',title='$title',source='$source',
$litpic
description='$description',keywords='$keywords',mtype='$mtype',userip='$userip'
where ID='$ID' And memberID='$memberID';
";
if(!$dsql->ExecuteNoneQuery($inQuery)){
	$gerr = $dsql->GetError();
	$dsql->Close();
	ShowMsg("把数据保存到数据库主表时出错，错误原因为：".$gerr,"javascript:;");
	exit();
}

//-------------------------------
//更新附加表
//------------------------------

//处理并保存所指定的图片
$pagestyle = 2;

$imgurls = "{dede:pagestyle maxwidth='$maxwidth' ddmaxwidth='' row='0' col='0' value='$pagestyle'/}\r\n";
for($i=1;$i<=120;$i++){
	if(isset(${'imgurl'.$i})|| 
	(isset($_FILES['imgfile'.$i]['tmp_name']) && is_uploaded_file($_FILES['imgfile'.$i]['tmp_name']))){
		$iinfo = str_replace("'","`",stripslashes(${'imgmsg'.$i}));
		//非上传图片
		if(!is_uploaded_file($_FILES['imgfile'.$i]['tmp_name'])){
		    $iurl = ${'imgurl'.$i};
		    if(trim($iurl)=="") continue;
		    $iurl = trim(str_replace($cfg_basehost,"",$iurl));
		    if((eregi("^http://",$iurl) && !eregi($cfg_basehost,$iurl)) && $cfg_isUrlOpen && $iurl!="http://")
		    //远程图片
		    {
			    $reimgs = "";
			    if($cfg_isUrlOpen){
				     $reimgs = GetRemoteImage($iurl,$cfg_ml->M_ID);
			       if(is_array($reimgs)){ $imgurls .= "{dede:img text='$iinfo' width='".$reimgs[1]."' height='".$reimgs[2]."'} ".$reimgs[0]." {/dede:img}\r\n"; }
			       else{ echo "下载：".$iurl." 失败，可能图片有反采集功能或http头不正确！<br />\r\n"; }
		      }else{
		  	     $imgurls .= "{dede:img text='$iinfo' width='' height=''} ".$iurl." {/dede:img}\r\n";
		      }
		    //站内图片
		    }else if($iurl!=""){
			    $imgfile = $cfg_basedir.$iurl;
			    if(is_file($imgfile)){
				      $info = "";
				      $imginfos = GetImageSize($imgfile,$info);
				      $imgurls .= "{dede:img text='$iinfo' width='".$imginfos[0]."' height='".$imginfos[1]."'} $iurl {/dede:img}\r\n";
			    }
		   }
	  //直接上传的图片
	  }else{
			 $oldiurl = ${'imgurl'.$i};
			 $oldimgfile = $cfg_basedir.$oldiurl;
			 if(file_exists($oldimgfile)){
			 	  $oldiurl = ereg_replace("[^0-9a-zA-Z\._-]","",$oldiurl);
			 	  $dsql->ExecuteNoneQuery("Delete From #@__uploads where url='$oldiurl' And memberid='".$cfg_ml->M_ID."';");
			 }
			 $iurl = GetUpImage('imgfile'.$i,false,false);
			 if($iurl!=''){
				    $imgfile = $cfg_basedir.$iurl;
				    $info = "";
				    $imginfos = GetImageSize($imgfile,$info);
				    $imgurls .= "{dede:img text='$iinfo' width='".$imginfos[0]."' height='".$imginfos[1]."'} $iurl {/dede:img}\r\n";
			 }
	  }
	}//含有图片的条件
}//循环结束
$imgurls = addslashes($imgurls);
CloseFtp();

//更新附加表
//----------------------------------
$addQuery = "Update `{$addtable}` set 
typeid = '$typeid',
maxwidth = '$maxwidth',
imgurls = '$imgurls'{$inadd_f}
where aid='$ID';
";

if(!$dsql->ExecuteNoneQuery($addQuery)){
   $gerr = $dsql->GetError();
   $dsql->Close();
   ShowMsg("把数据保存到数据库附加时出错，错误原因为：".$gerr,"javascript:;");
   exit();
}

$artUrl = MakeArt($ID);

//更新全站搜索索引
$datas = array('aid'=>$ID,'typeid'=>$typeid,'channelid'=>$channelid,'att'=>0,
               'title'=>$title,'url'=>$artUrl,'keywords'=>$keywords,
               'addinfos'=>$description,'arcrank'=>$arcrank,'mtype'=>$mtype);
if($litpic != '') $datas['litpic'] = $litpic;
UpSearchIndex($dsql,$datas);
//更新Tag索引
UpTags($dsql,$keywords,$ID,$memberID,$typeid,$arcrank);
unset($datas);
$dsql->Close();

//---------------------------------
//返回成功信息
//----------------------------------

$msg = "
请选择你的后续操作：
<a href='album_add.php?cid=$typeid'><u>继续发布新图集</u></a>
&nbsp;&nbsp;
<a href='album_edit.php?aid=$ID'><u>更改图集</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>预览图集</u></a>
&nbsp;&nbsp;
<a href='content_list.php?channelid=2'><u>已发布图集管理</u></a>
&nbsp;&nbsp;
<a href='index.php'><u>会员主页</u></a>
";

$wintitle = "成功更改一个图集！";
$wecome_info = "文档管理::更改图集";
$win = new OxWindow();
$win->AddTitle("成功更改一个图集：");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();
?>