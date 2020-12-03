<?php 
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);

if($cfg_mb_album=='否'){
	ShowMsg("对不起，系统禁用了图集的功能，因此无法使用！","-1");
	exit();
}


$svali = GetCkVdValue();
if(strtolower($vdcode)!=$svali || $svali==""){
  ShowMsg("验证码错误！","-1");
  exit();
}
require_once(dirname(__FILE__)."/../include/inc_photograph.php");
require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
require_once(dirname(__FILE__)."/inc/inc_archives_functions.php");

if(!isset($iscommend)) $iscommend = 0;
if(!isset($isjump)) $isjump = 0;
if(!isset($isbold)) $isbold = 0;
if(!isset($isrm)) $isrm = 0;
if(!isset($ddisfirst)) $ddisfirst = 0;
if(!isset($ddisremote)) $ddisremote = 0;
$channelid = 2;
$typeid = ereg_replace("[^0-9]","",$typeid);

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

$cInfos = $dsql->GetOne("Select sendrank,arcsta From #@__channeltype  where ID='2'; ");	
if($cInfos['sendrank'] > $cfg_ml->M_Type){
	$row = $dsql->GetOne("Select membername From #@__arcrank where rank='".$cInfos['sendrank']."' ");
	$dsql->Close();
	ShowMsg("对不起，需要[".$row['membername']."]才能在这个频道发布文档！","-1","0",5000);
	exit();
}
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
$typeid2 = 0;
$pubdate = mytime();
$senddate = $pubdate;
$sortrank = $pubdate;
$shorttitle = '';
$color =  '';
$money = 0;
$arcatt = 0;
$pagestyle = 2;

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
if(!empty($litpic)) $litpic = GetUpImage('litpic',true,true);
else $litpic = "";
$adminID = 0;
$memberID = $cfg_ml->M_ID;

//加入主档案表
//----------------------------------
$inQuery = "INSERT INTO #@__archives(
typeid,typeid2,sortrank,iscommend,ismake,channel,
arcrank,click,money,title,shorttitle,color,writer,source,litpic,
pubdate,senddate,arcatt,adminID,memberID,description,keywords,mtype,userip) 
VALUES ('$typeid','$typeid2','$sortrank','$iscommend','$ismake','$channelid',
'$arcrank','0','$money','$title','$shorttitle','$color','$writer','$source','$litpic',
'$pubdate','$senddate','$arcatt','$adminID','$memberID','$description','$keywords','$mtype','$userip');";
$dsql->SetQuery($inQuery);
if(!$dsql->ExecuteNoneQuery()){
	$dsql->Close();
	ShowMsg("把数据保存到数据库archives表时出错，请检查！","-1");
	exit();
}
$arcID = $dsql->GetLastID();
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
		    if((eregi("^http://",$iurl) && !eregi($cfg_basehost,$iurl)) && $isUrlOpen && $iurl!="http://")
		    //远程图片
		    {
			    $reimgs = "";
			    if($isUrlOpen)
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
$query = "
INSERT INTO #@__addonimages(aid,typeid,pagestyle,maxwidth,imgurls,row,col,isrm,ddmaxwidth) Values('$arcID','$typeid','$pagestyle','$maxwidth','$imgurls','0','0','0','0');
";
$dsql->SetQuery($query);
if(!$dsql->ExecuteNoneQuery()){
	$dsql->SetQuery("Delete From #@__archives where ID='$arcID'");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("把数据保存到数据库附加表 addonimages 时出错，请检查原因！","-1");
	exit();
}

$dsql->ExecuteNoneQuery("Update #@__member set c2=c2+1 where ID='".$cfg_ml->M_ID."';");

$dsql->Close();

$artUrl = MakeArt($arcID);

//---------------------------------
//返回成功信息
//----------------------------------

$msg = "
请选择你的后续操作：
<a href='album_add.php?cid=$typeid'><u>继续发布新图集</u></a>
&nbsp;&nbsp;
<a href='album_edit.php?aid=".$arcID."'><u>更改图集</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>预览图集</u></a>
&nbsp;&nbsp;
<a href='content_list.php?channelid=2'><u>已发布图集管理</u></a>
&nbsp;&nbsp;
<a href='index.php'><u>会员主页</u></a>
";

$wintitle = "成功发布一个图集！";
$wecome_info = "文档管理::发布图集";
$win = new OxWindow();
$win->AddTitle("成功发布一个图集：");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();
?>