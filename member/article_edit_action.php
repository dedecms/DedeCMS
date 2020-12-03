<?php 
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);

require_once(dirname(__FILE__)."/../include/inc_photograph.php");
require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
require_once(dirname(__FILE__)."/inc/inc_archives_functions.php");

$typeid = ereg_replace("[^0-9]","",$typeid);
$channelid = 1;
$ID = ereg_replace("[^0-9]","",$ID);

if($typeid==0){
	ShowMsg("请指定文档隶属的栏目！","-1");
	exit();
}

if(!CheckChannel($typeid,$channelid)){
	ShowMsg("你所选择的栏目与当前模型不相符，或不支持投稿，请选择白色的选项！","-1");
	exit();
}

$dsql = new DedeSql(false);

//检测用户是否有权限操作这篇文章
//--------------------------------
$cInfos = $dsql->GetOne("Select arcsta From #@__channeltype  where ID='1'; ");

$row = $dsql->GetOne("Select arcrank From #@__archives where memberID='".$cfg_ml->M_ID."' And ID='$ID'");

if(!is_array($row)){
   $dsql->Close();
   ShowMsg("你没权限更改这篇文章！","-1");
   exit();
}else if($row['arcrank']>=0 && $cInfos['arcsta']==-1){
   $dsql->Close();
   ShowMsg("这篇文章已被审核，你没权限更改！","-1");
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

$body = eregi_replace("<(iframe|script)","",$body);
//更新附加表
//----------------------------------

$dsql->SetQuery("Update #@__addonarticle set typeid='$typeid',body='$body' where aid='$ID'; ");
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
<a href='article_add.php?cid=$typeid'><u>发表新文章</u></a>
&nbsp;&nbsp;
<a href='article_edit.php?aid=".$ID."'><u>更改文章</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>预览文章</u></a>
&nbsp;&nbsp;
<a href='content_list.php?channelid=1'><u>已发布文章管理</u></a>
&nbsp;&nbsp;
<a href='index.php'><u>会员主页</u></a>
";

$wintitle = "成功修改一个文章！";
$wecome_info = "文档管理::修改文章";
$win = new OxWindow();
$win->AddTitle("成功修改一个文章：");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();

?>