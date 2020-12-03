<?php
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);

if($cfg_mb_album=='N'){
	ShowMsg("对不起，系统禁用了图集的功能，因此无法使用！","-1");
	exit();
}

$cfg_main_dftable = '#@__archives';
$cfg_add_dftable = '#@__addonimages';
$cfg_isalbum = true;
require_once(dirname(__FILE__)."/archives_addcheck.php");

$upscore = $cfg_send_score;

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

//对保存的内容进行处理
//--------------------------------
$sortrank = $senddate = $pubdate = mytime();
$shorttitle = $color= '';
$money = $arcatt = $typeid2 = 0;
$pagestyle = 2;

$title = ClearHtml($title);
$title = cn_substr($title,80);
$writer =  cn_substr(trim(ClearHtml($writer)),30);
$source = cn_substr(trim(ClearHtml($source)),50);
$description = cn_substr(trim(ClearHtml($description)),250);
$keywords = trim(cn_substr($keywords,60));
$userip = GetIP();
//处理上传的缩略图
if(!empty($litpic)) $litpic = GetUpImage('litpic',true,true);
else $litpic = "";
$adminID = 0;
$memberID = $cfg_ml->M_ID;

//加入主档案表
//----------------------------------
$inQuery = "INSERT INTO `$maintable`(
ID,typeid,typeid2,sortrank,iscommend,ismake,channel,
arcrank,click,money,title,shorttitle,color,writer,source,litpic,
pubdate,senddate,arcatt,adminID,memberID,description,keywords,mtype,userip)
VALUES ('$arcID','$typeid','$typeid2','$sortrank','0','$ismake','$channelid',
'$arcrank','0','$money','$title','$shorttitle','$color','$writer','$source','$litpic',
'$pubdate','$senddate','$arcatt','$adminID','$memberID','$description','$keywords','$mtype','$userip');";

if(!$dsql->ExecuteNoneQuery($inQuery)){
	$gerr = $dsql->GetError();
	$dsql->ExecuteNoneQuery("Delete From `#@__full_search` where aid='$arcID'");
	$dsql->Close();
	ShowMsg("把数据保存到数据库 `$maintable` 时出错，请联系管理员！".$gerr,"-1");
	exit();
}

//处理并保存所指定的图片
//------------------------------
$imgurls = "{dede:pagestyle maxwidth='$maxwidth' ddmaxwidth='' row='0' col='0' value='$pagestyle'/}\r\n";
for($i=1;$i<=120;$i++){
	if(isset(${'imgurl'.$i})||
	(isset($_FILES['imgfile'.$i]['tmp_name']) && is_uploaded_file($_FILES['imgfile'.$i]['tmp_name']))){
		$iinfo = str_replace("'","`",stripslashes(${'imgmsg'.$i}));
		//非上传图片
		if(!is_uploaded_file($_FILES['imgfile'.$i]['tmp_name'])){
		    $iurl = stripslashes(${'imgurl'.$i});
		    if(trim($iurl)=="") continue;
		    $iurl = trim(str_replace($cfg_basehost,"",$iurl));
		    if((eregi("^http://",$iurl) && !eregi($cfg_basehost,$iurl)) && $cfg_isUrlOpen && $iurl!="http://")
		    //远程图片
		    {
			    $reimgs = "";
			    if($cfg_isUrlOpen)
			    {
				     $reimgs = GetRemoteImage($iurl,$cfg_ml->M_ID);
			       if(is_array($reimgs)){
				        $imgurls .= "{dede:img text='$iinfo' width='".$reimgs[1]."' height='".$reimgs[2]."'} ".$reimgs[0]." {/dede:img}\r\n";
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
				      $info = "";
				      $imginfos = GetImageSize($imgfile,$info);
				      $imgurls .= "{dede:img text='$iinfo' width='".$imginfos[0]."' height='".$imginfos[1]."'} $iurl {/dede:img}\r\n";
			    }
		   }
	  //直接上传的图片
	  }else{
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

//加入附加表
//----------------------------------
$addQuery = "
INSERT INTO `$addtable`(aid,typeid,pagestyle,maxwidth,imgurls,row,col,isrm,ddmaxwidth{$inadd_f}) Values('$arcID','$typeid','$pagestyle','$maxwidth','$imgurls','0','0','0','0'{$inadd_v});
";

if(!$dsql->ExecuteNoneQuery($addQuery))
{
	 $gerr = $dsql->GetError();
	 $dsql->ExecuteNoneQuery("Delete From `$maintable` where ID='$arcID'");
	 $dsql->ExecuteNoneQuery("Delete From `#@__full_search` where aid='$arcID'");
	 $dsql->Close();
	 ShowMsg("把数据保存到附加表时出错，请联系管理员！".$gerr,"-1");
	 exit();
}

$dsql->ExecuteNoneQuery("Update `#@__member` set c2=c2+1,scores=scores+{$upscore} where ID='".$cfg_ml->M_ID."';");
$cfg_ml->FushCache();

$artUrl = MakeArt($arcID);

//更新全站搜索索引
$datas = array('aid'=>$arcID,'typeid'=>$typeid,'channelid'=>$channelid,'adminid'=>0,'mid'=>$memberID,'att'=>0,
               'title'=>$title,'url'=>$artUrl,'litpic'=>$litpic,'keywords'=>$keywords,
               'addinfos'=>$description,'uptime'=>$senddate,'arcrank'=>$arcrank,'mtype'=>$mtype);
WriteSearchIndex($dsql,$datas);
//写入Tag索引
InsertTags($dsql,$keywords,$arcID,$memberID,$typeid,$arcrank);
unset($datas);
$dsql->Close();

//---------------------------------
//返回成功信息
//----------------------------------

$msg = "
请选择你的后续操作：
<a href='album_add.php?channelid=$channelid'><u>继续发布新图集</u></a>
&nbsp;&nbsp;
<a href='album_edit.php?aid=".$arcID."'><u>更改图集</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>预览图集</u></a>
&nbsp;&nbsp;
<a href='content_list.php?channelid=$channelid'><u>已发布图集管理</u></a>
&nbsp;&nbsp;
<a href='index.php'><u>会员主页</u></a>
";

$wintitle = "成功发布一个图集！";
$wecome_info = "文档管理::发布图集";
$win = new OxWindow();
$win->mainTitle = "DedeCms发布文档成功提示";
$win->AddTitle("成功发布一个图集：");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();
?>