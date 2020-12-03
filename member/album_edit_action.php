<?php 
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);

if($cfg_mb_album=='否'){
	ShowMsg("对不起，系统禁用了图集的功能，因此无法使用！","-1");
	exit();
}

require_once(dirname(__FILE__)."/../include/inc_photograph.php");
require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
require_once(dirname(__FILE__)."/inc/inc_archives_functions.php");

$typeid = ereg_replace("[^0-9]","",$typeid);
$channelid = 2;
$ID = ereg_replace("[^0-9]","",$ID);

if($typeid==0){
	ShowMsg("请指定文档隶属的栏目！","-1");
	exit();
}

if(!CheckChannel($typeid,$channelid)){
	ShowMsg("你所选择的栏目与当前模型不相符，或不支持投稿，请选择白色的选项！","-1");
	exit();
}

CheckUserSpace($cfg_ml->M_ID);

$dsql = new DedeSql(false);

//检测用户是否有权限操作这篇文档
//--------------------------------

$row = $dsql->GetOne("Select arcrank From #@__archives where memberID='".$cfg_ml->M_ID."' And ID='$ID'");
if(!is_array($row)){
   $dsql->Close();
   ShowMsg("你没权限更改这个图集！","-1");
   exit();
}

$cInfos = $dsql->GetOne("Select arcsta From #@__channeltype  where ID='2'; ");
if($cInfos['arcsta']==0){
	$ismake = 0;
	$arcrank = 0;
}
else if($cInfos['arcsta']==1){
	$ismake = -1;
	$arcrank = 0;
}
else{
	$ismake = 0;
	$arcrank = -1;
}

$title = ClearHtml($title);
$writer =  cn_substr(trim(ClearHtml($writer)),30);
$source = cn_substr(trim(ClearHtml($source)),50);
$description = cn_substr(trim(ClearHtml($description)),250);
if($keywords!=""){
	$keywords = ereg_replace("[,;]"," ",trim(ClearHtml($keywords)));
	$keywords = trim(cn_substr($keywords,60))." ";
}
$userip = GetIP();

//处理上传的缩略图
if(!empty($litpic)){
	$litpic = GetUpImage('litpic',true,true);
	$litpic = " litpic='$litpic', ";
}else{
	$litpic = "";
}

$memberID = $cfg_ml->M_ID;

//更新数据库的SQL语句
//----------------------------------
$inQuery = "
update #@__archives set
ismake='$ismake',arcrank='$arcrank',typeid='$typeid',title='$title',source='$source',
$litpic
description='$description',keywords='$keywords',mtype='$mtype',userip='$userip'
where ID='$ID' And memberID='$memberID';
";
$dsql->SetQuery($inQuery);
if(!$dsql->ExecuteNoneQuery()){
	$dsql->Close();
	ShowMsg("把数据保存到数据库archives表时出错，请检查！","-1");
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
		    if((eregi("^http://",$iurl) && !eregi($cfg_basehost,$iurl)) && $isUrlOpen && $iurl!="http://")
		    //远程图片
		    {
			    $reimgs = "";
			    if($isUrlOpen){
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
$query = "
update #@__addonimages set 
typeid = '$typeid',
maxwidth = '$maxwidth',
imgurls = '$imgurls'
where aid='$ID';
";
$dsql->SetQuery($query);
if(!$dsql->ExecuteNoneQuery()){
	$dsql->Close();
	ShowMsg("把数据保存到数据库附加表 addonimages 时出错，请检查原因！","-1");
	exit();
}
$dsql->Close();

$artUrl = MakeArt($ID);

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