<?php 
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);

require_once(dirname(__FILE__)."/../include/inc_photograph.php");
require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
require_once(dirname(__FILE__)."/inc/inc_archives_functions.php");

$typeid = ereg_replace("[^0-9]","",$typeid);
$channelid = 1;
$ID = ereg_replace("[^0-9]","",$ID);


$dsql = new DedeSql(false);

//检测用户是否有权限操作这篇文章
//--------------------------------

$row = $dsql->GetOne("Select arcrank From #@__archives where memberID='".$cfg_ml->M_ID."' And ID='$ID'");

if(!is_array($row)){
   $dsql->Close();
   ShowMsg("你没权限更改这个作品！","-1");
   exit();
}else if($row['arcrank']>0){
   $dsql->Close();
   ShowMsg("这篇作品已被锁定，你没权限更改！","-1");
   exit();
}

$ismake = 0;
$arcrank = -1;

$title = ClearHtml($title);
$writer =  cn_substr(trim(ClearHtml($writer)),30);
$source = "Dedecms 官方";
$description = cn_substr(trim(ClearHtml($description)),250);
$keywords = "";
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
update #@__archives set typeid='$typeid',title='$title',source='$source',
$litpic
description='$description',keywords='$keywords',userip='$userip'
where ID='$ID' And memberID='$memberID';
";

$dsql->SetQuery($inQuery);
if(!$dsql->ExecuteNoneQuery()){
	$dsql->Close();
	ShowMsg("把数据保存到数据库archives表时出错，请检查！","-1");
	exit();
}

//更新附加表
//----------------------------------

$dsql->SetQuery("Update #@__addonvote set upload='$upload',bigpic='$bigpic' where aid='$ID'; ");
if(!$dsql->ExecuteNoneQuery()){
   $dsql->Close();
   ShowMsg("把数据保存到数据库附时出错，请联系管理员！","-1");
   exit();
}
$dsql->Close();

$artUrl = MakeArt($ID);

//返回成功信息
//----------------------------------

$msg = "
请选择你的后续操作：
<a href='vote_add.php?cid=$typeid'><u>发表新作品</u></a>
&nbsp;&nbsp;
<a href='vote_edit.php?aid=".$ID."'><u>更改作品</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>预览作品</u></a>
&nbsp;&nbsp;
<a href='content_list.php?channelid=15'><u>已发布作品管理</u></a>
&nbsp;&nbsp;
<a href='index.php'><u>会员主页</u></a>
";

$wintitle = "成功修改一个作品！";
$wecome_info = "文档管理::修改作品";
$win = new OxWindow();
$win->AddTitle("成功作品一个文章：");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();

?>