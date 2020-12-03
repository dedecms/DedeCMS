<?php 
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);

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
$channelid = 15;
//$typeid = 51;

$dsql = new DedeSql(false);

//对保存的内容进行处理
//--------------------------------
$typeid2 = 0;
$pubdate = mytime();
$senddate = $pubdate;
$sortrank = $pubdate;

$ismake = 0;
$arcrank = -1;

$shorttitle = '';
$color =  '';
$money = 0;
$arcatt = 0;
$pagestyle = 2;

$title = ClearHtml($title);
$writer =  cn_substr(trim(ClearHtml($writer)),30);
$source = "Dedecms 官方";
$description = cn_substr(trim(ClearHtml($description)),250);
$keywords = "";
$userip = GetIP();
//处理上传的缩略图
if(!empty($litpic)) $litpic = GetUpImage('litpic',true,true);
else $litpic = "";
$adminID = 0;
$memberID = $cfg_ml->M_ID;
$mtype = 0;
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
	echo mysql_error();
	$dsql->Close();
	//ShowMsg("把数据保存到数据库archives表时出错，请检查！","-1");
	exit();
}
$arcID = $dsql->GetLastID();

//加入附加表
//----------------------------------
$dsql->SetQuery("INSERT INTO #@__addonvote(aid,typeid,upload,bigpic,votecount) Values('$arcID','$typeid','$upload','$bigpic','0'); ");
if(!$dsql->ExecuteNoneQuery()){
	    $dsql->SetQuery("Delete From #@__addonvote where ID='$arcID'");
	    $dsql->ExecuteNoneQuery();
	    $dsql->Close();
	    ShowMsg("把数据保存到数据库附时出错，请联系管理员！","-1");
	    exit();
}

$dsql->ExecuteNoneQuery("Update #@__member set c1=c1+1 where ID='".$cfg_ml->M_ID."';");

$dsql->Close();

$artUrl = MakeArt($arcID);

//---------------------------------
//返回成功信息
//----------------------------------

$msg = "
请选择你的后续操作：
<a href='vote_add.php?cid=$typeid'><u>继续发表作品</u></a>
&nbsp;&nbsp;
<a href='vote_edit.php?aid=".$arcID."'><u>更改作品</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>预览作品</u></a>
&nbsp;&nbsp;
<a href='content_list.php?channelid=1'><u>已发布作品管理</u></a>
&nbsp;&nbsp;
<a href='index.php'><u>会员主页</u></a>
";

$wintitle = "成功发布一个作品！";
$wecome_info = "文档管理::发布作品";
$win = new OxWindow();
$win->AddTitle("成功发布一个作品：");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();
?>