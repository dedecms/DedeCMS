<?php 
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);

if($cfg_mb_sendall=='N'){
	ShowMsg("对不起，系统禁用了自定义模型投稿，因此无法使用此功能！","-1");
	exit();
}

$cfg_add_dftable = '';
require_once(dirname(__FILE__)."/archives_editcheck.php");

$title = ClearHtml($title);
$writer =  cn_substr(trim(ClearHtml($writer)),30);
$source = '';
$description = cn_substr(trim(ClearHtml($description)),250);
$keywords = trim(cn_substr($keywords,60));
$userip = GetIP();

//处理上传的缩略图
if(!empty($litpic)){
	$litpic = GetUpImage('litpic',true,true);
	$litpicsql = " litpic='$litpic', ";
}else{
	$litpic = "";
	$litpicsql = '';
}

$memberID = $cfg_ml->M_ID;

//----------------------------------
//分析处理附加表数据
//----------------------------------
$inadd_f = '';
if(!empty($dede_addonfields))
{
  $addonfields = explode(";",$dede_addonfields);
  $inadd_f = "";
  if(is_array($addonfields))
  {
    foreach($addonfields as $v)
    {
	     if($v=="") continue;
	     $vs = explode(",",$v);
	     //HTML文本特殊处理
	     if($vs[1]=="htmltext"||$vs[1]=="textdata")
	     {
		     ${$vs[0]} = filterscript(stripslashes(${$vs[0]}));
         //自动摘要
         if($description==''){
    	      $description = cn_substr(html2text(${$vs[0]}),$cfg_auot_description);
	          $description = trim(preg_replace("/#p#|#e#/","",$description));
	          $description = addslashes($description);
         }
         ${$vs[0]} = addslashes(${$vs[0]});
         ${$vs[0]} = GetFieldValue(${$vs[0]},$vs[1],$ID,'add','','member');
	     }else{
		     ${$vs[0]} = GetFieldValueA(${$vs[0]},$vs[1],$ID);
	     }
	     $inadd_f .= ",`{$vs[0]}` = '".${$vs[0]}."'";
    }
  }
}

$inQuery = "
update `$maintable` set
ismake='$ismake',arcrank='$arcrank',typeid='$typeid',title='$title',
$litpic
description='$description',keywords='$keywords',userip='$userip'
where ID='$ID' And memberID='$memberID';
";
if(!$dsql->ExecuteNoneQuery($inQuery)){
	$gerr = $dsql->GetError();
	$dsql->Close();
	ShowMsg("把数据保存到数据库主表时出错，错误原因为：".$gerr,"javascript:;");
	exit();
}


$addQuery = "Update `{$addtable}` set typeid='$typeid'{$inadd_f} where aid='$ID'";
if(!$dsql->ExecuteNoneQuery($addQuery)){
     $gerr = $dsql->GetError();
     $dsql->Close();
     ShowMsg("把数据保存到数据库附加时出错，错误原因为：".$gerr,"javascript:;");
     exit();
}

$artUrl = MakeArt($ID);

//更新全站搜索索引
$datas = array('aid'=>$ID,'typeid'=>$typeid,'channelid'=>$channelid,'att'=>0,
               'title'=>$title,'url'=>$artUrl,'litpic'=>$litpic,'keywords'=>$keywords,
               'addinfos'=>$description,'arcrank'=>$arcrank,'mtype'=>$mtype);
if($litpic != '') $datas['litpic'] = $litpic;
UpSearchIndex($dsql,$datas);
//更新Tag索引
UpTags($dsql,$keywords,$ID,$memberID,$typeid,$arcrank);
unset($datas);
$dsql->Close();

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