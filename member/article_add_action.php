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
$channelid = 1;
$typeid = ereg_replace("[^0-9]","",$typeid);

if($typeid==0){
	ShowMsg("请指定文档隶属的栏目！","-1");
	exit();
}

if(!CheckChannel($typeid,$channelid)){
	ShowMsg("你所选择的栏目与当前模型不相符，或不支持投稿，请选择白色的选项！","-1");
	exit();
}

$dsql = new DedeSql(false);

$cInfos = $dsql->GetOne("Select sendrank,arcsta From #@__channeltype  where ID='1'; ");	
if($cInfos['sendrank'] > $cfg_ml->M_Type){
	$row = $dsql->GetOne("Select membername From #@__arcrank where rank='".$cInfos['sendrank']."' ");
	$dsql->Close();
	ShowMsg("对不起，需要[".$row['membername']."]才能在这个频道发布文档！","-1","0",5000);
	exit();
}
//对保存的内容进行处理
//--------------------------------
$typeid2 = 0;
$pubdate = mytime();
$senddate = $pubdate;
$sortrank = $pubdate;

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

$body = eregi_replace("<(iframe|script)","",$body);

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

//加入附加表
//----------------------------------
$dsql->SetQuery("INSERT INTO #@__addonarticle(aid,typeid,body) Values('$arcID','$typeid','$body')");
if(!$dsql->ExecuteNoneQuery()){
	    $dsql->SetQuery("Delete From #@__archives where ID='$arcID'");
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
<a href='article_add.php?cid=$typeid'><u>继续发表文章</u></a>
&nbsp;&nbsp;
<a href='article_edit.php?aid=".$arcID."'><u>更改文章</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>预览文章</u></a>
&nbsp;&nbsp;
<a href='content_list.php?channelid=1'><u>已发布文章管理</u></a>
&nbsp;&nbsp;
<a href='index.php'><u>会员主页</u></a>
";

$wintitle = "成功发布一个文章！";
$wecome_info = "文档管理::发布文章";
$win = new OxWindow();
$win->AddTitle("成功发布一个文章：");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();
?>