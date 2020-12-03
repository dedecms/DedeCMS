<?php
if(!defined('DEDEMEMBER'))
{
	exit("dedecms");
}
require_once(DEDEINC."/image.func.php");
require_once(DEDEINC."/oxwindow.class.php");

$flag = '';
$typeid = isset($typeid) && is_numeric($typeid) ? $typeid : 0;
$userip = GetIP();
$ckhash = md5($aid.$cfg_cookie_encode);
if($ckhash!=$idhash)
{
	ShowMsg('校对码错误，你没权限修改此文档或操作不合法！','-1');
	exit();
}
if($typeid==0)
{
	ShowMsg('请指定文档隶属的栏目！','-1');
	exit();
}
$query = "Select tp.ispart,tp.channeltype,tp.issend,ch.issend as cissend,ch.sendrank,ch.arcsta,ch.addtable,ch.usertype
         From `#@__arctype` tp left join `#@__channeltype` ch on ch.id=tp.channeltype where tp.id='$typeid' ";
$cInfos = $dsql->GetOne($query);
$addtable = $cInfos['addtable'];

//检测栏目是否有投稿权限
if($cInfos['issend']!=1 || $cInfos['ispart']!=0|| $cInfos['channeltype']!=$channelid || $cInfos['cissend']!=1)
{
	ShowMsg("你所选择的栏目不支持投稿！","-1");
	exit();
}

//文档的默认状态
if($cInfos['arcsta']==0)
{
	$ismake = 0;
	$arcrank = 0;
}
else if($cInfos['arcsta']==1)
{
	$ismake = -1;
	$arcrank = 0;
}
else
{
	$ismake = 0;
	$arcrank = -1;
}

//对保存的内容进行处理
$title = cn_substrR(HtmlReplace($title,1),$cfg_title_maxlen);
$writer =  cn_substrR(HtmlReplace($writer,1),20);
$description = cn_substrR(HtmlReplace($description,1),250);
$keywords = cn_substrR(HtmlReplace($tags,1),30);
$mid = $cfg_ml->M_ID;

//处理上传的缩略图
$litpic = MemberUploads('litpic',$oldlitpic,$mid,'image','',$cfg_ddimg_width,$cfg_ddimg_height,false);
if($litpic!='')
{
	SaveUploadInfo($title,$litpic,1);
}
else
{
	$litpic =$oldlitpic;
}
?>