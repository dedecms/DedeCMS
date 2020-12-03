<?php 
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);

$cfg_main_dftable = '#@__archives';
$cfg_add_dftable = '#@__addonarticle';
require_once(dirname(__FILE__)."/archives_addcheck.php");

//对保存的内容进行处理
//--------------------------------
$sortrank = $senddate = $pubdate = mytime();

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

$color =  $shorttitle = '';
$arcatt = $money = $typeid2 = 0;


$title = ClearHtml($title);
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

$body = eregi_replace("<(iframe|script)","",$body);

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

//加入附加表
//----------------------------------
$addQuery = "INSERT INTO `$addtable`(aid,typeid,body{$inadd_f}) Values('$arcID','$typeid','$body'{$inadd_v})";
if(!$dsql->ExecuteNoneQuery($addQuery))
{
	 $gerr = $dsql->GetError();
	 $dsql->ExecuteNoneQuery("Delete From `$maintable` where ID='$arcID'");
	 $dsql->ExecuteNoneQuery("Delete From `#@__full_search` where aid='$arcID'");
	 $dsql->Close();
	 ShowMsg("把数据保存到附加表时出错，请联系管理员！".$gerr,"-1");
	 exit();
}

$dsql->ExecuteNoneQuery("Update `#@__member` set c1=c1+1,scores=scores+{$upscore} where ID='".$cfg_ml->M_ID."';");
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
<a href='article_add.php?channelid=$channelid'><u>继续发表文章</u></a>
&nbsp;&nbsp;
<a href='article_edit.php?aid=".$arcID."'><u>更改文章</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>预览文章</u></a>
&nbsp;&nbsp;
<a href='content_list.php?channelid=$channelid'><u>已发布文章管理</u></a>
&nbsp;&nbsp;
<a href='index.php'><u>会员主页</u></a>
";

$wintitle = "成功发布一个文章！";
$wecome_info = "文档管理::发布文章";
$win = new OxWindow();
$win->mainTitle = "DedeCms发布文档成功提示";
$win->AddTitle("成功发布一个文章：");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();
?>