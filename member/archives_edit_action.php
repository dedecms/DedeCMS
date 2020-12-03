<?php 
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);

if($cfg_mb_sendall=='否'){
	ShowMsg("对不起，系统禁用了自定义模型投稿，因此无法使用此功能！","-1");
	exit();
}

require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
require_once(dirname(__FILE__)."/inc/inc_archives_all.php");
require_once(dirname(__FILE__)."/../include/inc_photograph.php");
require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
require_once(dirname(__FILE__)."/inc/inc_archives_functions.php");
$ID = ereg_replace("[^0-9]","",$ID);
$typeid = ereg_replace("[^0-9]","",$typeid);
$channelid = ereg_replace("[^0-9]","",$channelid);

if($typeid==0){
	ShowMsg("请指定文档隶属的栏目！","-1");
	exit();
}

if(!CheckChannel($typeid,$channelid)){
	ShowMsg("你所选择的栏目与当前模型不相符，或不支持投稿，请选择白色的选项！","-1");
	exit();
}

$dsql = new DedeSql(false);
//检测用户是否有权限操作这篇文档
//-------------------------------
$row = $dsql->GetOne("Select arcrank From #@__archives where memberID='".$cfg_ml->M_ID."' And ID='$ID'");
if(!is_array($row)){
   $dsql->Close();
   ShowMsg("你没权限更改这则信息！","-1");
   exit();
}

$cInfos = $dsql->GetOne("Select * From #@__channeltype where ID='$channelid'; ");	
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
$source = '';
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
ismake='$ismake',arcrank='$arcrank',typeid='$typeid',title='$title',
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

//----------------------------------
//更新附加表数据
//----------------------------------
$dtp = new DedeTagParse();
$dtp->SetNameSpace("field","<",">");
$dtp->LoadSource($cInfos['fieldset']);
$dede_addonfields = "";
if(is_array($dtp->CTags)){
    foreach($dtp->CTags as $tid=>$ctag){
        if($dede_addonfields=="") $dede_addonfields = $ctag->GetName().",".$ctag->GetAtt('type');
        else $dede_addonfields .= ";".$ctag->GetName().",".$ctag->GetAtt('type');
    }
}
$dede_addtablename = $cInfos['addtable'];
$addonfields = explode(";",$dede_addonfields);
$upfield = "";
foreach($addonfields as $v)
{
	if($v=="") continue;
	$vs = explode(",",$v);
	if($vs[1]=="textdata"){
		${$vs[0]} = GetFieldValue(${$vs[0]},$vs[1],$ID,'edit',${$vs[0].'_file'});
	}else{
		${$vs[0]} = GetFieldValue(${$vs[0]},$vs[1]);
	}
	if($upfield=="") $upfield .= $vs[0]." = '".${$vs[0]}."'";
	else $upfield .= ", ".$vs[0]." = '".${$vs[0]}."'";
}
$addQuery = "Update ".$dede_addtablename." set $upfield where aid='$ID'";
$dsql->SetQuery($addQuery);
$dsql->ExecuteNoneQuery();
$dsql->Close();

$artUrl = MakeArt($ID);

//---------------------------------
//返回成功信息
//----------------------------------

$msg = "
请选择你的后续操作：
<a href='archives_add.php?channelid=$channelid&cid=$typeid'><u>发布新信息</u></a>
&nbsp;&nbsp;
<a href='archives_edit.php?aid=".$ID."'><u>继续更改信息</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>预览信息</u></a>
&nbsp;&nbsp;
<a href='content_list.php?channelid=$channelid'><u>已发布信息管理</u></a>
&nbsp;&nbsp;
<a href='index.php'><u>会员主页</u></a>
";

$wintitle = "成功更改一则信息！";
$wecome_info = "文档管理::更改文档";
$win = new OxWindow();
$win->AddTitle("成功更改一则信息：");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();
?>