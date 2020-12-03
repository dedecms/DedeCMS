<?php
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);

if($cfg_mb_sendall=='N'){
	ShowMsg("对不起，系统禁用了自定义模型投稿，因此无法使用此功能！","-1");
	exit();
}

$cfg_main_dftable = '#@__archives';
$cfg_add_dftable = '';
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
$title = cn_substr($title,80);
$writer =  cn_substr(trim(ClearHtml($writer)),30);
$source = '';
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
'$pubdate','$senddate','$arcatt','$adminID','$memberID','$description','$keywords','0','$userip');";

if(!$dsql->ExecuteNoneQuery($inQuery)){
	$gerr = $dsql->GetError();
	$dsql->ExecuteNoneQuery("Delete From `#@__full_search` where aid='$arcID'");
	$dsql->Close();
	ShowMsg("把数据保存到数据库 `$maintable` 时出错，请联系管理员！".$gerr,"-1");
	exit();
}

if($addtable!="" && $inadd_f!="")
{
  $addQuery = "INSERT INTO `$addtable`(aid,typeid{$inadd_f}) Values('$arcID','$typeid'{$inadd_v})";
  if(!$dsql->ExecuteNoneQuery($addQuery))
  {
	   $gerr = $dsql->GetError();
	   $dsql->ExecuteNoneQuery("Delete From `$maintable` where ID='$arcID'");
	   $dsql->ExecuteNoneQuery("Delete From `#@__full_search` where aid='$arcID'");
	   $dsql->Close();
	   ShowMsg("把数据保存到附加表时出错，请联系管理员！".$gerr,"-1");
	   exit();
  }
}

$dsql->ExecuteNoneQuery("Update `#@__member` set c3=c3+1,scores=scores+{$upscore} where ID='".$cfg_ml->M_ID."';");
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
<a href='archives_add.php?channelid=$channelid&cid=$typeid'><u>继续发布信息</u></a>
&nbsp;&nbsp;
<a href='archives_edit.php?aid=".$arcID."'><u>更改信息</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>预览信息</u></a>
&nbsp;&nbsp;
<a href='content_list.php?channelid=$channelid'><u>已发布信息管理</u></a>
&nbsp;&nbsp;
<a href='index.php'><u>会员主页</u></a>
";

$wintitle = "成功发布一则信息！";
$wecome_info = "文档管理::发布文档";
$win = new OxWindow();
$win->mainTitle = "DedeCms发布文档成功提示";
$win->AddTitle("成功发布一则信息：");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();
?>